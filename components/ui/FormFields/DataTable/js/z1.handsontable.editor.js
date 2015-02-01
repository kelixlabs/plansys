
(function (Handsontable) {

    /*************** RELATION TYPE *******************/
    function relationRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.AutocompleteCell.renderer.apply(this, arguments);

        return td;
    }
    Handsontable.renderers.registerRenderer('relation', relationRenderer);

    var RelationEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();
    RelationEditor.prototype.open = function () {
        this.cellProperties.opened = true;
        Handsontable.editors.AutocompleteEditor.prototype.open.apply(this, arguments);
    };
    RelationEditor.prototype.close = function () {
        this.cellProperties.opened = false;
        Handsontable.editors.AutocompleteEditor.prototype.close.apply(this, arguments);
    };
    RelationEditor.prototype.checkRel = function (value, callback) {
        var $scope = this.$scope;
        var relList = this.relList;

        if (typeof relList[value] == "undefined") {
            var name = this.name;
            var originalRow = this.row;
            var s = $scope.ht.getSelected();
            var row = s[0];
            var col = s[1];
            var opt = $scope.columns[col];
            $scope.$http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                's': value,
                'm': $scope.modelClass,
                'f': $scope.name,
                'c': name,
                'p': opt.relParams
            }).success(function (data) {
                // cache query
                var labels = [];
                for (i in data) {
                    if (!data[i].label)
                        continue;

                    labels.push(data[i].label);
                    relList[data[i].label.trim('"')] = data[i].value;
                }

                if (labels.indexOf(value) >= 0) {
                    callback(true);
                } else {
                    $scope.data[originalRow][name] = '';
                    callback(false);
                }
            });
        } else {
            $scope.data[this.row][this.name] = relList[value];
            callback(true);
        }
    }
    RelationEditor.prototype.handleChange = function ($scope, c) {
        var row = $scope.data[c[0]];
        switch (row['__dt_flg']) {
            case "Z":
                var dsrow = row['__dt_row'];
                if (!!$scope.datasource.data[dsrow]) {
                    $scope.datasource.data[dsrow][c[1]] = c[3];
                }
                break;
            case "G":
                var rows = $scope.dtGroups.findRows(row);
                var col = $scope.dtGroups.groupCols[row['__dt_lvl']];

                rows.forEach(function (r) {
                    r[col] = angular.copy(row[col]);
                    var dsrow = r['__dt_row'];
                    $scope.datasource.data[dsrow][col] = angular.copy(row[col]);
                });
                break;
        }
    }
    RelationEditor.prototype.search = function (query, process) {
        if (!this.opened) {
            process([]);
            return;
        }

        var $scope = this.$scope;
        var $q = $scope.$q;
        var $http = $scope.$http;
        if (!$scope.triggerRelationWatch) {
            return false;
        }
        var relList = this.relList;
        var s = this.instance.getSelected();
        if (s) {
            var row = s[0];
            var col = s[1];
            var opt = $scope.columns[col];
            if ($scope.dtGroups && $scope.data[row]['__dt_flg'] == "G") {
                var prop = $scope.dtGroups.groupCols[$scope.data[row]['__dt_lvl']];
                opt = $scope.dtGroups.groupColOpts[prop + $scope.relSuffix];
            }

            if (opt.columnType != "relation")
                return;
            for (i in opt.relParams) {
                var p = opt.relParams[i];
                if (p.indexOf('js:') === 0) {
                    var value = $scope.$eval(p.replace('js:', ''));
                    opt.relParams[i] = value;
                }
            }
            if ($scope.httpRequest) {
                $scope.httpRequest.resolve();
            }
            $scope.httpRequest = $q.defer();
            $http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                's': query,
                'm': $scope.modelClass,
                'f': $scope.name,
                'c': opt.name,
                'p': opt.relParams
            }, {
                timeout: $scope.httpRequest.promise
            }).success(function (data) {
                // cache query
                var labels = [];
                for (i in data) {
                    if (!data[i].label)
                        continue;

                    labels.push(data[i].label);
                    relList[data[i].label.trim('"')] = data[i].value;
                }

                if (labels.length && labels.length > 0) {
                    process(labels);
                }
            });
        }
    }
    Handsontable.editors.RelationEditor = RelationEditor;
    Handsontable.editors.registerEditor('relation', RelationEditor);

    /*************** DATE TIME  *******************/
    function formatDate(val, format, $filter, td) {
        var oldval = val;
        if (typeof oldval != "string")
            oldval = "";

        if (typeof val == "string") {
            var t = val.split(/[- :]/);
            if (t.length > 3) {
                val = new Date(t[0], t[1] - 1, t[2], t[3], t[4]);
            } else if (t.length == 3) {
                if (val.indexOf(':') > 0) {
                    val = new Date(1, 0, 0, t[0], t[1], t[2]);
                } else {
                    val = new Date(t[0], t[1] - 1, t[2], 0, 0);
                }
            } else if (t.length == 2) {
                val = new Date();
                val.setMinutes(t[0]);
                val.setMinutes(t[1]);
            }
            if (val == "Invalid Date" || (typeof val == 'object' && val.getFullYear() < 1900)) {
                val = "";
            }
        } else {
            val = "";
        }

        switch (format) {
            case "99/99/9999":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy'));
                } else {
                    if (oldval.split(/\//).length == 3)
                        return oldval;

                    if (td) {
                        val = "dd/mm/yyyy";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99/99/9999 99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy HH:mm'));
                } else {

                    if (td) {
                        val = "dd/mm/yyyy hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'HH:mm'));
                } else {

                    if (td) {
                        val = "hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
        }
        return val;
    }

    function dateTimeRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);
        var options = instance.getSettings().columns[col];
        Handsontable.Dom.fastInnerHTML(td, formatDate(value, options.inputMask, options.filter, td));
        return td;
    }
    Handsontable.renderers.registerRenderer('datetime', dateTimeRenderer);

    /*************** STRING ALIAS RENDERER *******************/
    function stringAliasRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);
        var options = instance.getSettings().columns[col];
        var val = value;

        angular.forEach(options.stringAlias, function (alias, idx) {
            if (idx == value) {
                val = alias;
            }
        });

        if (typeof options.options.style == "string") {
            td.setAttribute('style', options.options.style);
        }
        Handsontable.Dom.fastInnerHTML(td, val);

        return td;
    }
    Handsontable.renderers.registerRenderer('stringalias', stringAliasRenderer);

    /*************** INPUT MASK RENDERER *******************/
    var InputMaskEditor = Handsontable.editors.TextEditor.prototype.extend();
    InputMaskEditor.prototype.createElements = function () {
        Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

        var row = this.instance.getSelected()[0];
        var col = this.instance.getSelected()[1];
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        this.TEXTAREA = document.createElement('input');
        this.TEXTAREA.setAttribute('type', 'text');
        this.TEXTAREA.className = 'handsontableInput';
        this.textareaStyle = this.TEXTAREA.style;
        this.textareaStyle.width = 0;
        this.textareaStyle.height = 0;
        this.$textarea = $(this.TEXTAREA);

        Handsontable.Dom.empty(this.TEXTAREA_PARENT);
        this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

        $(this.TEXTAREA)
                .bind('focus', function () {
                    var val = $(this).val();
                    $(this).val(formatDate(val, options.inputMask, options.filter));
                    $(this).mask(options.inputMask);
                })

    };

    InputMaskEditor.prototype.close = function () {
        var ed = this.instance.getActiveEditor();
        var row = ed.row;
        var col = ed.col;
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        var val = $(this.TEXTAREA).val();
        var $filter = options.filter;

        Handsontable.editors.TextEditor.prototype.close.apply(this, arguments);

        switch (options.inputMask) {
            case "99/99/9999":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99/99/9999 99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0], t[3], t[4]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date();
                d.setHours(t[0]);
                d.setMinutes(t[1]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
        }

    };
    Handsontable.editors.InputMaskEditor = InputMaskEditor;
    Handsontable.editors.registerEditor('mask', InputMaskEditor);

    /*************** GROUPS RENDERER *******************/
    function groupsRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);

        if (value && col == 0) {
            var row = cellProperties.$scope.data[row];

            switch (row['__dt_flg']) {
                case "Z":
                    Handsontable.Dom.fastInnerHTML(td, value);
                    break;
                case "G":
                    var gidx = row['__dt_lvl'];
                    var lvstr = "";
                    for (var ll = 0; ll < gidx; ll++) {
                        lvstr += "    ";
                    }
                    lvstr += '◢  ';
                    Handsontable.Dom.fastInnerHTML(td, lvstr + value);
                    break;
            }
        }

        return td;
    }
    Handsontable.renderers.registerRenderer('groups', groupsRenderer);

})(Handsontable);

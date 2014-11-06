<?php

/**
 * Class DropDownList
 * @author rizky
 */
class DropDownList extends FormField {

    /**
     * @return array me-return array property DropDown.
     */
    public function getFieldProperties() {
        return [
            [
                'label' => 'Field Name',
                'name' => 'name',
                'options' => [
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    
                ],
                'list' => [],
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ],
            [
                'label' => 'Label',
                'name' => 'label',
                'options' => [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => 500,
                ],
                'type' => 'TextField',
            ],
            [
                'label' => 'Layout',
                'name' => 'layout',
                'options' => [
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ],
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ],
            [
                'totalColumns' => '4',
                'column1' => [
                    '<column-placeholder></column-placeholder>',
                ],
                'column2' => [
                    [
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => [
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => 500,
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ],
                        'type' => 'TextField',
                    ],
                    '<column-placeholder></column-placeholder>',
                ],
                'column3' => [
                    [
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => [
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => 500,
                        ],
                        'type' => 'TextField',
                    ],
                    '<column-placeholder></column-placeholder>',
                ],
                'type' => 'ColumnField',
            ],
            '<hr/>',
            [
                'label' => 'Searchable',
                'name' => 'searchable',
                'options' => [
                    'ng-model' => 'active.searchable',
                    'ng-change' => 'save()',
                ],
                'listExpr' => 'array(\\"Yes\\",\\"No\\")',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
            [
                'label' => 'Show \\"Other\\" Item',
                'name' => 'showOther',
                'options' => [
                    'ng-model' => 'active.showOther',
                    'ng-change' => 'save()',
                ],
                'listExpr' => 'array(\\\'Yes\\\',\\\'No\\\')',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
            [
                'label' => '\\"Other\\" Item Label',
                'name' => 'otherLabel',
                'labelWidth' => '6',
                'fieldWidth' => '4',
                'options' => [
                    'ng-model' => 'active.otherLabel',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ng-show' => 'active.showOther == \'Yes\'',
                ],
                'type' => 'TextField',
            ],
            [
                'label' => 'DropDown Item',
                'name' => 'list',
                'show' => 'Show',
                'options' => [
                    'ng-hide' => 'active.listExpr != \'\' || active.options[\'ps-list\'] != null',
                ],
                'allowEmptyKey' => 'Yes',
                'allowSpaceOnKey' => 'Yes',
                'type' => 'KeyValueGrid',
            ],
            [
                'label' => 'List Expression',
                'fieldname' => 'listExpr',
                'options' => [
                    'ng-hide' => 'active.options[\'ps-list\'] != null',
                    'ps-valid' => 'active.list = result;save();',
                ],
                'desc' => '<i class=\\"fa fa-warning\\"></i> WARNING: Using List Expression will replace <i>DropDown Item</i> with expression result',
                'type' => 'ExpressionField',
            ],
            [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
            [
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ],
            [
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ],
        ];
    }

    /** @var string $label */
    public $label = '';

    /** @var string $name */
    public $name = '';

    /** @var string $value digunakan pada function checked */
    public $value = '';

    /** @var array $options */
    public $options = [];

    /** @var array $fieldOptions */
    public $fieldOptions = [];

    /** @var array $labelOptions */
    public $labelOptions = [];

    /** @var string $list */
    public $list = '';

    /** @var string $listExpr digunakan pada function processExpr */
    public $listExpr = '';

    /** @var string $layout */
    public $layout = 'Horizontal';

    /** @var integer $labelWidth */
    public $labelWidth = 4;

    /** @var integer $fieldWidth */
    public $fieldWidth = 8;

    /** @var string $searchable */
    public $searchable = 'No';

    /** @var string $showOther */
    public $showOther = 'No';

    /** @var string $otherLabel */
    public $otherLabel = 'Lainnya';

    /** @var string $toolbarName */
    public static $toolbarName = "Drop Down List";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-caret-square-o-down";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['drop-down-list.js'];
    }

    /**
     * @return array me-return array hasil proses expression.
     */
    public function processExpr() {

        if ($this->listExpr != "") {
            if (FormField::$inEditor) {
                $this->list = '';
                return ['list' => ''];
            }

            ## evaluate expression
            $this->list = $this->evaluate($this->listExpr, true);

            if (is_array($this->list) && !Helper::is_assoc($this->list)) {
                if (!is_array($this->list[0])) {
                    $this->list = Helper::toAssoc($this->list);
                }
            }
        } else if (is_array($this->list) && !Helper::is_assoc($this->list)) {
            $this->list = Helper::toAssoc($this->list);
        }

        return [
            'list' => $this->list
        ];
    }

    /**
     * checked
     * Fungsi ini untuk mengecek value dari field
     * @param string $value
     * @return boolean me-return true atau false
     */
    public function checked($value) {
        if ($this->value == $value)
            return true;
        else
            return false;
    }

    /**
     * getLayoutClass
     * Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout
     * @return string me-return string Class layout yang digunakan
     */
    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    /**
     * @return string me-return string Class error jika terdapat error pada satu atau banyak attribute.
     */
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    /**
     * getlabelClass
     * Fungsi ini akan mengecek $layout untuk menentukan layout yang digunakan
     * dan meload option label dari property $labelOptions
     * @return string me-return string Class label
     */
    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    /**
     * @return string me-return string class
     */
    public function getFieldClass() {
        return "btn-group btn-block";
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field DropDownList dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('btn dropdown-toggle btn-sm btn-block btn-dropdown-field', 'fieldOptions');
        $btn_class = ['btn-primary', 'btn-default', 'btn-success', 'btn-danger', 'btn-warning'];
        if (!in_array($this->fieldOptions['class'], $btn_class)) {
            $this->addClass('btn-default', 'fieldOptions');
        }

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        $this->processExpr();
        return $this->renderInternal('template_render.php');
    }

}

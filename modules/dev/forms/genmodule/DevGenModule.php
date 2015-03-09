<?php

class DevGenModule extends Form {

    public $name;
    public $alias;
    public $path;
    public $classPath;
    public $module;
    public $imports = [];
    public $error = '';

    public function create($module) {
        $m = explode(".", $module);
        if (count($m) == 2) {
            $name = lcfirst($m[1]);
            $class = ucfirst($name) . "Module";
            $basePath = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $alias = ($m[0] == "app" ? 'app' : 'application') . ".modules.{$name}.{$class}";
            $path = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";

            $this->name = $name;
            $this->alias = $alias;
            $this->path = $path;
            $this->classPath = $classPath;

            if (!is_file($this->classPath)) {
                $this->module = ModuleGenerator::init($alias);
                $this->imports = $this->module->listImport();
                if (is_null($this->module)) {
                    $this->error = 'Failed to create module
Invalid Class Name "' . $name . '"';
                }
            } else {
                $this->module = null;
                $this->error = 'Failed to create module
Module "' . $name . '" already exist';
            }
        }
    }

    public function delete() {
        $dirPath = $this->path;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($dirPath);
    }

    public function load($module) {
        $m = explode(".", $module);
        if (count($m) == 2 && $m[1] != '') {
            $name = lcfirst($m[1]);
            $class = ucfirst($name) . "Module";
            $basePath = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $alias = ($m[0] == "app" ? 'app' : 'application') . ".modules.{$name}.{$class}";
            $path = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";

            $this->name = $name;
            $this->alias = $alias;
            $this->path = $path;
            $this->classPath = $classPath;

            if (is_file($this->classPath)) {
                $this->module = ModuleGenerator::init($alias);
                $this->imports = $this->module->listImport();
            } else {
                $this->module = null;
            }
        }
    }

    public function getControllers() {
        if (isset($this->module)) {
            return $this->module->getControllers();
        } else {
            return null;
        }
    }

    public function getForm() {
        return array (
            'title' => 'Generate Module',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenModule',
                        'title' => 'Module',
                        'icon' => 'fa-empire',
                        'inlineJS' => 'GenModule.js',
                        'menuOptions' => array (),
                    ),
                    'col2' => array (
                        'type' => 'mainform',
                        'name' => 'col2',
                        'sizetype' => '%',
                    ),
                ),
            ),
            'inlineJS' => 'GenModule.js',
            'includeJS' => array (),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<!-- EMPTY MODULE -->
<div ng-if=\'!model.name\'>
    <div class=\"empty-box-container\">
        <div class=\"message\">
            Please select item on right sidebar
        </div>
    </div>

</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<!-- MODULE INFO TAB -->
<tabset class=\'tab-set\' ng-if=\'model.name\'>
<tab heading=\"Module Info\">',
                'type' => 'Text',
            ),
            array (
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Module Name',
                        'name' => 'name',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module Alias',
                        'name' => 'alias',
                        'type' => 'LabelField',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Class Path',
                        'name' => 'classPath',
                        'labelOptions' => array (
                            'style' => 'text-align:left;',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module Directory',
                        'name' => 'path',
                        'labelOptions' => array (
                            'style' => 'text-align:left;',
                        ),
                        'type' => 'LabelField',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'title' => '<i class=\\\'fa fa-empire\\\'></i> Import Initialization <span ng-bind-html=\\\'importStatus\\\'></span>',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'label' => 'Generate Import',
                        'buttonType' => 'success',
                        'icon' => 'refresh',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;
margin:-50px -45px 0px 0px;',
                            'href' => 'url:/dev/genModule/genImport?active={params.active}',
                            'confirm' => 'WARNING: Current code will be REPLACED. Are you sure?',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'value' => '
<div style=\'margin:-25px -50px -25px -40px;\'>
    <div id=\"import-editor\"
     style=\"width:100%;
     height:400px;\"
     ng-model=\"model.imports\"
     ng-change=\"saveImport()\"
     ng-delay=\"500\"
     ui-ace=\"aceConfig({inline:true})\"></div>
</div>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'title' => '<i class=\\\'fa fa-cubes\\\'></i> Controllers',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'label' => 'Add New Controller',
                        'buttonType' => 'success',
                        'icon' => 'plus-circle',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;
margin:-50px -45px 0px 0px;',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'value' => '    <table class=\"table table-condensed table-bordered table-small\">
        
        <tr ng-repeat=\"c in params.controllers track by $index\">
            <td>{{ c.class }}</td>
        </tr>
    </table>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'value' => '<!-- ACCESS CONTROL TAB -->
</tab><tab heading=\"Access Control\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<!-- TAB CLOSER -->
</tab></tabset>',
                'type' => 'Text',
            ),
        );
    }

}
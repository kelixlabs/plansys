<?php

class InstallDbForm extends Form {

    public $host;
    public $username;
    public $password;
    public $dbname;
    public $resetdb = "yes";

    public function rules() {
        return [
            ['host, username, dbname', 'required']
        ];
    }

    public function getForm() {
        return array(
            'title' => 'Plansys Installer - Database Information',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<div class=\"install-pane\" style=\"width:350px;\">
    <div class=\"install-pane-head\">
        <img src=\"<?= Yii::app()->controller->staticUrl(\"/img/logo.png\"); ?>\" alt=\"Logo Plansys\" />
    </div>
    
    <div ng-if=\"!params.error\" style=\"margin-top:15px;\" class=\"alert alert-info\"><?= Setting::t(\"Please enter your MySQL server information \") ?></div>
    
    <div ng-if=\"params.error\" style=\"margin-top:15px;\" class=\"alert alert-danger\">{{params.error}}</div>
    ',
                'type' => 'Text',
            ),
            array (
                'label' => 'Host:',
                'name' => 'host',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Username:',
                'name' => 'username',
                'layout' => 'Vertical',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password:',
                'name' => 'password',
                'fieldType' => 'password',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Database name:',
                'name' => 'dbname',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'name' => 'resetdb',
                'list' => array (
                    'yes' => 'Reset Plansys table',
                ),
                'labelWidth' => '0',
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'type' => 'CheckboxList',
            ),
            array (
                'value' => '<div class=\"info text-left\" style=\"margin:-20px 0px 0px -3px\">
    only tables with prefix p_ (e.g. p_user, p_role, etc)<br/> that will be reset.
</div>

<br/>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Next Step',
                'buttonType' => 'success',
                'buttonSize' => '',
                'type' => 'SubmitButton',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}
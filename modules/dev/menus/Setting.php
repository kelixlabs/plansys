<?php 

## AUTOGENERATED OPTIONS - DO NOT EDIT
$options = array (
    'mode' => 'normal',
    'layout' => array (
        'size' => '200',
        'sizetype' => 'px',
        'type' => 'menu',
        'name' => 'col1',
        'file' => 'application.modules.dev.menus.Setting',
        'icon' => 'fa-sliders',
        'title' => 'Main Setting',
        'menuOptions' => array (),
    ),
);
## END OF AUTOGENERATED OPTIONS

return array (
    array (
        'label' => 'Application Setting',
        'icon' => 'fa-home',
        'url' => '/dev/setting/app',
        'formattedUrl' => '/coba/index.php?r=dev/setting/app',
    ),
    array (
        'label' => 'Database Setting',
        'icon' => 'fa-database',
        'url' => '/dev/setting/database',
        'formattedUrl' => '/coba/index.php?r=dev/setting/database',
    ),
    array (
        'label' => 'Email Setting',
        'icon' => 'fa-envelope',
        'url' => '/dev/setting/email',
        'formattedUrl' => '/coba/index.php?r=dev/setting/email',
    ),
    array (
        'label' => 'LDAP Setting',
        'icon' => 'fa-users',
        'url' => '/dev/setting/ldap',
        'formattedUrl' => '/coba/index.php?r=dev/setting/ldap',
    ),
);
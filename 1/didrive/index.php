<?php

require( $_SERVER['DOCUMENT_ROOT'].DS.'0.site'.DS.'exe'.DS.'shop'.DS.'class.php' );

$vv['sys']['ckeditor'] = 112;
$vv['sys']['ckeditor_in'][] = 'editor1';

$vv['mnum'] = array(
    'index' => array(
        'name' => 'Информация'
        )
    ,'adds' => array(
        'name' => 'Загрузить файлы CSV'
        )
    
    /*
    ,'items' => array(
        'name' => 'Добавить'
        )
    ,'clients' => array(
        'name' => 'Клиенты'
        )
    ,'orders' => array(
        'name' => 'Заказы'
        )
    */
    
    );

$vv['now_option'] = ( isset($_REQUEST['option']{1}) && isset($vv['mnum'][$_REQUEST['option']]) ) ? $_REQUEST['option'] : 'index' ;

    if( file_exists( didr_f.'_'.$vv['now_option'].'.php' ) )
    {
    require( didr_f.'_'.$vv['now_option'].'.php' );
    $vv['body2tpl'] = didr_tpl.'_'.$vv['now_option'].'.htm';
    }
    else
    {
    $vv['body2tpl'] = didr_tpl.'empty.htm';
    }

    
$vv['tpl_body'] = didr_tpl.'body.htm';
//echo __FILE__.' ['.__LINE.']';
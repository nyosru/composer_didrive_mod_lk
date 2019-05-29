<?php

    if( isset( $_POST['name'] ) )
    {
        
        if( !function_exists('translit') )
        require( $_SERVER['DOCUMENT_ROOT'].DS.'0.all'.DS.'f'.DS.'txt.php');

        if( !function_exists('get_file_ext') )
        require( $_SERVER['DOCUMENT_ROOT'].DS.'0.all'.DS.'f'.DS.'file.php');

    $r = $_POST;
    $r['name_eng'] = translit( $_POST['name_eng'] );
    
        if( strlen( $r['name_eng'] ) < 2 )
        $r['name_eng'] = translit( $_POST['name'] );

    //$status = '';
    $c1 = $db->sql_query('SELECT
            `name_eng`
        FROM
            `shop_cat`
        WHERE 
            `folder` = \''.$vv['folder'].'\' 
            AND `name_eng` = \''.$r['name_eng'].'\'
        LIMIT 1
        ;');
    //echo $status;

        if( $db -> sql_numrows($c1) == 1 )
        {
        $vv['warn'] .= '<div class=warn2 >Каталог не добавлен, название по английски не должно повторяться, повторите добавление каталога</div>';
        }
        else
        {
        
            if( 
                isset($_FILES['img'])
                && $_FILES['img']['error'] == 0 
                && $_FILES['img']['size'] > 10
                )
            {

                if( !is_dir($_SERVER['DOCUMENT_ROOT'].DS.'9.site'.DS.$vv['folder'].DS.'download'.DS.'shop_cats') )
                mkdir($_SERVER['DOCUMENT_ROOT'].DS.'9.site'.DS.$vv['folder'].DS.'download'.DS.'shop_cats', 0755);

            $nd = $_SERVER['DOCUMENT_ROOT'].DS.'9.site'.DS.$vv['folder'].DS.'download'.DS.'shop_cats'.DS;
            $nf = 'cat_'.$r['name_eng'].'.'.get_file_ext($_FILES['img']['name']);

                if( copy( $_FILES['img']['tmp_name'], $nd.$nf ) )
                {
                $r['img'] = $nf;
                }

            }

        db2_insert( $db, 'shop_cat', $r );
        $vv['warn'] .= '<div class=warn >Каталог добавлен</div>';
        }
    }
    elseif( isset( $_REQUEST['delitem'] ) )
    {
    $db->sql_query( 'DELETE FROM `shop_cat` 
        WHERE 
            `folder` = \''.addslashes($vv['folder']).'\' 
            AND (
                `name_eng` = \''.addslashes($_REQUEST['delitem']).'\' 
                OR 
                `up` = \''.addslashes($_REQUEST['delitem']).'\' 
                )
        ;' );
    header( 'HTTP/1.1 301 Moved Permanently' );
    header( 'Refresh: 2; URL=http://'.$_SERVER['HTTP_HOST'].'/i.didrive.php?level='.$_REQUEST['level'].'&option='.$_REQUEST['option'] );
    die( '<h3 style="color:red" >Каталог(и) удалён</h3>' );
    }

//$status = '';
$c1 = $db->sql_query('SELECT *
    FROM `shop_cat`
    WHERE 
        `folder` = \''.$vv['folder'].'\' 
    ORDER BY 
        `sort` DESC
        ,`name` DESC
    ;');
//echo $status;

$vv['cats'] = array();

    if( $db -> sql_numrows($c1) > 0 )
    {

        while( $d = $db->sql_fr($c1) )
        {
        $vv['cats'][] = $d;
        $vv['cats_sort'][$d['name_eng']] = $d;
        }

    }
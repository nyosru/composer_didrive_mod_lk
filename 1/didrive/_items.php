<?php

//$vv['cats'] = $n_shop3 -> get_cat( $db );

    if( isset( $_POST['name'] ) )
    {

    // echo '<pre>'; print_r( $_POST ); echo '</pre>';
    // echo '<pre>'; print_r( $_FILES ); echo '</pre>';
        
        if( !function_exists('translit') )
        require( $_SERVER['DOCUMENT_ROOT'].DS.'0.all'.DS.'f'.DS.'txt.php');

    $v = $_POST;
    $v['name_eng'] = substr(translit( $_POST['name_eng'] ),0,47);

        if( strlen( $v['name_eng'] ) < 2 )
        $v['name_eng'] = substr( translit( $_POST['name'] ),0,47 );

        if( $n_shop3->check_item( $db, $vv['folder'], $v['name_eng'] ) === true )
        {
        $v['name_eng'] .= '_'.rand(0,99);

            if( $n_shop3->check_item( $db, $vv['folder'], $v['name_eng'] ) === true )
            $v['name_eng'] = substr($v['name_eng'],0,47).'_'.rand(0,99);

        }

        if( !is_dir( SD.'shop_items' ) )
        mkdir( SD.'shop_items', 0755 );

    $dir_i = SD.'shop_items'.DS.translit( $v['cat'] );

        if( !is_dir( $dir_i ) )
        mkdir( $dir_i, 0755 );

    $qn = 1;

        if( !function_exists('get_file_ext') )
        require( $_SERVER['DOCUMENT_ROOT'].DS.'0.all'.DS.'f'.DS.'file.php');

        for( $q = 1; $q < 5; $q++ )
        {

            if( 
                isset( $_FILES['img']['tmp_name'][$q] )
                && $_FILES['img']['error'][$q] == 0
                && $_FILES['img']['size'][$q] > 10
            )
            {
            $ff = $v['name_eng'].'__'.$qn.'.'.get_file_ext(strtolower($_FILES['img']['name'][$q]));
            copy( $_FILES['img']['tmp_name'][$q], $dir_i.DS.$ff );
            echo $_FILES['img']['tmp_name'][$q] .' '.$dir_i.DS.$ff.' <br/>';
            $v['img'.$qn] = $ff;
            $qn++;
            }

        }

    $_SESSION['last_cat'] = $v['cat'];

    //$status = '';
    db2_insert( $db, 'shop_item', $v, 'da' );
    //echo $status;

    header( 'HTTP/1.1 301 Moved Permanently' );
    header( 'Refresh: 1; URL=http://'.$_SERVER['HTTP_HOST'].'/i.didrive.php?level='.$_REQUEST['level'].'&option='.$_REQUEST['option'] );

    die( '<h3 style="color:red" >Товар добавлен</h3>' );
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
    
    
    if( isset( $_REQUEST['showitems']{1} ) )
    {
    // $status = '';
    $c1 = $db->sql_query('SELECT *
        FROM
            `shop_item`
        WHERE 
            `folder` = \''.$vv['folder'].'\' 
            AND `cat` = \''.addslashes($_REQUEST['showitems']).'\' 
        ORDER BY 
            `sort` DESC
            ,`name` DESC
        ;');
    // echo $status;

    $vv['items'] = array();

        if( $db -> sql_numrows($c1) > 0 )
        {

            while( $d = $db->sql_fr($c1) )
            {
            $d['img_small'] = $vv['sd'].'shop_item/'.$d['cat'].'/'.$d['img1'];
            $vv['items'][] = $d;
            }

        }
        
     echo '<pre>'; print_r( $vv['items'] ); echo '</pre>';
    }
    
    
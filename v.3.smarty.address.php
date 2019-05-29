<?php

/**
 * работа с адресами
 */
if (isset($_POST['submit']) && isset($_POST['new_adress'])) {

    $new_id = \f\db\db2_insert($db, 'gm_user_adress', array(
        'user' => $_SESSION['now_user']['id'],
        'd' => 'NOW'
            ), true, 'last_id');

    $indb = array();

    foreach ($_POST['new_adress'] as $k => $v) {
        $indb[] = array(
            'name' => $k,
            'value' => $v
        );
    }

    \f\db\sql_insert_mnogo2($db, 'gm_user_adress_option', $indb, array('id_adress' => $new_id));

    $vv['warn'] .= 'Аddress added';
    
    if( isset($_POST['goto']{1}) ){
        \f\redirect( '', $_POST['goto']);
    }
    
}
elseif( isset( $_GET['del_adres'] ) && is_numeric( $_GET['del_adres'] ) ){

$db->sql_query('UPDATE `gm_user_adress` SET `status` = \'delete\' WHERE id = \''.$_GET['del_adres'].'\';');
\f\redirect( '/', $_GET['level'].'/'.$_GET['option'].'/' );

}
elseif( isset( $_GET['set_primary'] ) && is_numeric( $_GET['set_primary'] ) ){

$db->sql_query('UPDATE `gm_user_adress` SET `status` = \'ok\' WHERE `status` = \'ok-primary\' AND user = \''.$_SESSION['now_user']['id'].'\' ;');
$db->sql_query('UPDATE `gm_user_adress` SET `status` = \'ok-primary\' WHERE id = \''.$_GET['set_primary'].'\'  AND user = \''.$_SESSION['now_user']['id'].'\' ;');
\f\redirect( '/', $_GET['level'].'/'.$_GET['option'].'/'.rand(10,999).'/' );

}
// echo '#<br/>#' . __LINE__;
$vv['tpl_0body'] = \f\like_tpl('body_lk.address', $dir_mod, $dir_mod_site);

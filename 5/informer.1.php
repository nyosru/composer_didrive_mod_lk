<?php


/**
информер
**/





//?
//uid=5903492
//&first_name=Серёжа
//&last_name=Бакланов
//&photo=https://sun3-12.userapi.com/c845121/v845121518/116ace/-M-1eSE0niA.jpg%3Fava=1
//&photo_rec=https://sun3-13.userapi.com/c845121/v845121518/116ad1/Ealie_Hev_8.jpg%3Fava=1
//&hash=a6abdb3c907cbc87c75c56a1a48feac8

if( !empty( $_REQUEST['uid']) ){
    
    \f\pa($_REQUEST);
    
    die();
    
}



/*
//f\pa($vv['now_inf_cfg']);

if (isset($vv['now_inf_cfg']['load_inf']) && $vv['now_inf_cfg']['load_inf'] == 'da') {
    //$vv['inf']['items'][$vv['now_inf_cfg']['cfg.level']] = \Nyos\mod\items::getItems($db, $vv['folder'], $vv['now_inf_cfg']['cfg.level']);
    try{
        $vv['inf']['items'][$vv['now_inf_cfg']['cfg.level']] = \Nyos\mod\items::getItems2($db, $vv['folder'], $vv['now_inf_cfg']['cfg.level']);
    } catch ( Exception $e ){
        // ошибку писать в лог
    }
}

if (1 == 2) {
// echo '11<pre>'; print_r($q); echo '</pre>';
// echo '22<pre>'; print_r($w); echo '</pre>';
// $w['folder'] => jobs

    if (isset($w['folder']) && is_dir($_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . folder . DS . 'download' . DS . $w['folder'])) {

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . folder . DS . '_smartydata.' . $w['cfg.level'] . '.json')) {
            //echo __FILE__.'<br/>';

            $vv['informer'][$w['cfg.level']] = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . folder . DS . '_smartydata.' . $w['cfg.level'] . '.json'), true);
            // echo '<pre>'; print_r($vv['informer'][$w['cfg.level']]); echo '</pre>';
            //  = date
        }
    }
}
*/



    // \f\pa(dir_site_module_nowlev_tpl_inf ) ;
    // \f\pa(dir_mods_mod_vers_tpl_inf ) ;

    // \f\pa($dir_tpl_site);
    // \f\pa($w);

    // $vv['inf_enter_form'] = \f\like_tpl( 'enter.reg.form', $dir_tpl_site, '/vendor/didrive_mod/'.$w['type'].'/'.$w['version'].'/tpl.inf/', DR ) ;
    $vv['inf_enter_form'] = DS.'sites'.DS. \Nyos\Nyos::$folder_now . DS . 'module'.DS.$w['cfg.level'].DS.'tpl.inf'.DS.'enter.reg.form.htm';
    // \f\pa($vv['inf_enter_form']);
    // exit;
    
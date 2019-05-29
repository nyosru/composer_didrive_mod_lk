<?php

$vv['_inf']['lk'][4]['forms'] = \f\like_tpl( 'enter.reg.form', dirname(__FILE__).'/tpl.inf/', dir_serv_site.'module/'.$vv['now_inf_cfg']['cfg.level'].'/tpl.inf/' );

if( !isset($_SESSION['now_user']['id']{0}) ){
    $vv['in_body_end_js']['https://www.google.com/recaptcha/api.js'] = 1;
    $vv['in_body_end_js']['//ulogin.ru/js/ulogin.js'] = 1;
}

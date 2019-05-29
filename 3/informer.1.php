<?php
   
if( file_exists($dir_tpl_site.'enter.reg.form.htm') ){
    $vv['_inf']['lk'][3]['forms'] = $dir_tpl_site.'enter.reg.form.htm';
}else{
    $vv['_inf']['lk'][3]['forms'] = dirname(__FILE__).'/tpl.inf/enter.reg.form.htm';
}

if( !isset($_SESSION['now_user']['id']{0}) ){
    $vv['in_body_end_js']['https://www.google.com/recaptcha/api.js'] = 1;
    $vv['in_body_end_js']['//ulogin.ru/js/ulogin.js'] = 1;
}

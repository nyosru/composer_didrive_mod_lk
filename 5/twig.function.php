<?php



/**
определение функций для TWIG
 */
//creatSecret

$function = new Twig_SimpleFunction('lk_enter_form', function ( ) {
    
    
    
    $e = file_get_contents( dirname(__FILE__).'/tpl.inf/enter.reg.form.htm' );
    
    return $e;
});
$twig->addFunction($function);


//echo '<br/>'.__FILE__.' '.__LINE__;
<?php

//sleep(10);

if( isset($_REQUEST['s']{2})
    && 
        (
        $_REQUEST['s'] == md5( date('ymd',$_SERVER['REQUEST_TIME']).'_'.$_SERVER['HTTP_HOST'])
        || $_REQUEST['s'] == md5( date('ymd',$_SERVER['REQUEST_TIME']-3600).'_'.$_SERVER['HTTP_HOST'])
        || $_REQUEST['s'] == md5( date('ymd',$_SERVER['REQUEST_TIME']-7200).'_'.$_SERVER['HTTP_HOST'])
        )
    ){}else{
    die( json_encode( array( 'cod' => 'error', 'text' => 'Что то, пошло не так. (ошибка #'.__LINE__.')' ) ) );
    }

    if( isset($_REQUEST['action']) 
        && 
        ( 
        $_REQUEST['action'] == 'hide' 
        || $_REQUEST['action'] == 'show' 
        )
    )
            
    {

        if( isset( $_REQUEST['cat']{1} ) 
            && isset( $_REQUEST['id']{1}) 
        ){}else{
        die( json_encode( array( 'end' => 'error', 'text' => 'Что то пошло не так (ошибка #'.__LINE__.')' ) ) );
        }

    date_default_timezone_set("Asia/Yekaterinburg"); 
    require($_SERVER['DOCUMENT_ROOT'].'/0.site/0.start.php');

    $db->sql_query('UPDATE
            `shop_item` 
        SET 
            `status` = \''.( $_REQUEST['action'] == 'hide' ? 'hide' : 'ok' ).'\'
        WHERE 
            `folder` = \''.htmlspecialchars($now['folder']).'\'
            AND `cat` = \''.htmlspecialchars($_REQUEST['cat']).'\' 
            AND `name_eng` = \''.htmlspecialchars($_REQUEST['id']).'\'
        LIMIT 1 ;');

    die( 
        json_encode( array( 'end' => 'ok', 'text' => 'Товар '
            .( $_REQUEST['action'] == 'hide' ? 'скрыт' : 'показывается' ) ) 
            ) 
        );
    }
    elseif( isset($_REQUEST['action']) 
        && $_REQUEST['action'] == 'del' 
        )
    {

        if( isset( $_REQUEST['cat']{1} ) 
            && isset( $_REQUEST['id']{1}) 
        ){}else{
        die( json_encode( array( 'end' => 'error', 'text' => 'Что то пошло не так (ошибка #'.__LINE__.')' ) ) );
        }

    date_default_timezone_set("Asia/Yekaterinburg"); 
    require($_SERVER['DOCUMENT_ROOT'].'/0.site/0.start.php');

    $db->sql_query('UPDATE
            `shop_item` 
        SET 
            `status` = \'del\'
        WHERE
            `folder` = \''.htmlspecialchars($now['folder']).'\'
            AND `cat` = \''.htmlspecialchars($_REQUEST['cat']).'\' 
            AND `name_eng` = \''.htmlspecialchars($_REQUEST['id']).'\'
        LIMIT 1;');

    die( 
        json_encode( array( 'end' => 'ok', 'text' => 'Товар будет удалён' ) ) 
        );
    }

die( json_encode( array( 'end' => 'error', 'text' => 'Что то пошло не так (ошибка #'.__LINE__.')' ) ) );


/*

<form method="post" action="#" id="ajaxform" >
        <div>
            <div class="row">
                <div class="6u 12u(mobile)">
                <input type="text" name="fio" id="name" placeholder="Имя Отчество" rel="Имя Отчество"  />
                </div>
                <div class="6u 12u(mobile)">
                <input type="text" name="tel" id="gsm" placeholder="Телефон" rel="Телефон" />
                </div>
            </div>

            {*
            <div class="row">
                <div class="12u">
                <input type="text" name="subject" id="subject" placeholder="Subject" />
                </div>
            </div>
            *}

            <div class="row">
                <div class="12u">
                <textarea name="message" id="message" placeholder="Заявка, сообщение" rel="Заявка, сообщение" ></textarea>
                </div>
            </div>

            <div class="row 200%">
                <div class="12u">
                <ul class="actions">
                <li><input type="submit" value="Отправить" /></li>
                {*
                <li><input type="reset" value="Clear Form" class="alt" /></li>
                *}
                </ul>
                </div>
            </div>

        </div>
<input type="hidden" name="run_modul" value="090.order.creat.site" />
</form>

<div class="modal-body" id="form1res" style="display:none" >
<p>Сообщение отправлено.</p>
</div>
*/

/*

    $("#ajaxform").submit( function(){ // пeрeхвaтывaeм всe при сoбытии oтпрaвки

        var form = $(this); // зaпишeм фoрму, чтoбы пoтoм нe былo прoблeм с this
        var error = false; // прeдвaритeльнo oшибoк нeт

        form.find('input, textarea').each( function(){ // прoбeжим пo кaждoму пoлю в фoрмe
            if( $(this).val() == '' )
            { // eсли нaхoдим пустoe
                alert('Зaпoлнитe пoлe "'+$(this).attr('rel')+'" !'); // гoвoрим зaпoлняй!
                error = true; // oшибкa
            }
        });

        if (!error) { // eсли oшибки нeт

            var data = form.serialize(); // пoдгoтaвливaeм дaнныe
            $.ajax({ // инициaлизируeм ajax зaпрoс

               type: 'POST', // oтпрaвляeм в POST фoрмaтe, мoжнo GET
               url: '/0.site/exe/backword/5/ajax.php', // путь дo oбрaбoтчикa, у нaс oн лeжит в тoй жe пaпкe
               dataType: 'json', // oтвeт ждeм в json фoрмaтe
               data: data, // дaнныe для oтпрaвки

            beforeSend: function(data) { // сoбытиe дo oтпрaвки
                form.find('input[type="submit"]').attr('disabled', 'disabled'); // нaпримeр, oтключим кнoпку, чтoбы нe жaли пo 100 рaз
                },
           success: function(data){ // сoбытиe пoслe удaчнoгo oбрaщeния к сeрвeру и пoлучeния oтвeтa
                    if( data['error'] )
                    { // eсли oбрaбoтчик вeрнул oшибку
                    alert( data['error'] ); // пoкaжeм eё тeкст
                    }
                    else
                    { // eсли всe прoшлo oк
                    // alert('Письмo oтврaвлeнo! Чeкaйтe пoчту! =)'); // пишeм чтo всe oк
                    $( form ).hide();
                    $( '#form1btn' ).hide();
                    $('#form1res').show('slow');
                    }
                },

                {/literal}{*
                error: function (xhr, ajaxOptions, thrownError) { // в случae нeудaчнoгo зaвeршeния зaпрoсa к сeрвeру
                     alert( xhr.status ); // пoкaжeм oтвeт сeрвeрa
                     alert( thrownError ); // и тeкст oшибки
                     },
                *}{literal}

                complete: function( data ){ // сoбытиe пoслe любoгo исхoдa
                     form.find('input[type="submit"]').prop('disabled', false); // в любoм случae включим кнoпку oбрaтнo
                     }

                 });
             }

         return false; // вырубaeм стaндaртную oтпрaвку фoрмы
         });
     });

*/

date_default_timezone_set("Asia/Yekaterinburg"); 

require($_SERVER['DOCUMENT_ROOT'].'/0.site/0.start.php');

$vv['mnu'] = $nyos -> creat_menu( $now['folder'] );

//echo '<pre>'; print_r($nyos->menu); echo '</pre>';
//echo '<pre>'; print_r($_REQUEST); echo '</pre>';
// name="run_modul" value="090.order.creat.site"

    if( isset( $_REQUEST['run_modul'] ) && isset(Nyos\nyos::$menu[$_REQUEST['run_modul']]) )
    {
    $m =
    $er = '';
    $c = Nyos\nyos::$menu[$_REQUEST['run_modul']];
        
        for( $i=1; $i<=20; $i++ )
        {

            if( isset($c['form']['pole'.$i]{0}) )
            {
            $m .= ( isset($m{2}) ? '<br/>' : ''  )
                .( isset($c['form']['pole'.$i.'name']{0}) ? $c['form']['pole'.$i.'name'] : $c['form']['pole'.$i] ).': '.htmlspecialchars($_REQUEST[$c['form']['pole'.$i]]);
            }
            
            if( isset($c['form']['pole'.$i]{0}) 
                && isset($c['form']['pole'.$i.'ob']{0})
                && !isset($_REQUEST[$c['form']['pole'.$i]]{0}) 
                )
            {
            $er .= ( isset($er{0}) ? '<br/>' : '' )
                .'Заполните поле "'.( isset($c['form']['pole'.$i.'name']{0}) ? $c['form']['pole'.$i.'name'] : $c['form']['pole'.$i] ).'" ';
            }

        }

        if( !isset($er{2}) )
        {
        require( DirAll.'class'.DS.'mail.php' );
            
        //$status = '';
        $emailer -> ns_new( ( isset($c['mailot']{1}) ? $c['mailot'] : 'support@uralweb.info' )
            , ( isset($c['mailto']{1}) ? $c['mailto'] : 'support@uralweb.info' ) );
        $emailer -> ns_send( 'сайт '.domain.' > '.( isset( $c['mail_subject']{1}) ? $c['mail_subject'] : 'новое сообщение' ), 
                '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"></head><body>'
                . '<h2>Новое письмо с сайта</h2>'
                . '<p>'.$m.'</p>'
                . '<hr>'
                . '<small>Система сайтов <a href="http://uralweb.info" >Uralweb</a><br/>Письмо отправлено: '.date( 'd.m.Y h:i', $_SERVER['REQUEST_TIME']+3600*5 )
                . '</body></html>' );
        //echo $status;
            
            
        die(json_encode( array( 'good' => 'ok' ) ) );    
        }
        else
        {
        die(json_encode( array( 'error' => $er ) ) );    
        }
    }
    else
    {
    die(json_encode( array( 'error' => 'Что то пошло не так (ошибка №'.__LINE__.')' ) ) );    
    }


exit;






//session_start();
require ( $_SERVER['DOCUMENT_ROOT'].'/index.session_start.php' );

//echo '<pre>'; print_r($_REQUEST); echo '</pre>'; echo '<hr>';
//echo '<pre>'; print_r($_SESSION); echo '</pre>'; echo '<hr>';

    if( isset($_REQUEST['s']{1})
        && isset($_REQUEST['s2']{1}) 
        && ( 
            md5(date('h',$_SERVER['REQUEST_TIME']).'-'.$_REQUEST['s2'])
            || md5(date('h',$_SERVER['REQUEST_TIME']-3600).'-'.$_REQUEST['s2'])
            || md5(date('h',$_SERVER['REQUEST_TIME']-7200).'-'.$_REQUEST['s2'])
            )
    )
    {

    $msg = '';

        if( !isset($_REQUEST['fio']{2}) ){ $msg .= ( isset($msg{1}) ? '<br/>' : '' ).' Не указано как Вас зовут'; }
        if( isset($_REQUEST['gsm']) && !isset($_REQUEST['gsm']{5}) ){ $msg .= ( isset($msg{1}) ? '<br/>' : '' ).' Не указан телефон'; }
        if( isset($_REQUEST['email']) && !isset($_REQUEST['email']{4}) ){ $msg .= ( isset($msg{1}) ? '<br/>' : '' ).' Не указан E-mail'; }

        if( !isset($msg{1}) )
        {

        //define('IN_NYOS_PROJECT',true);
        require( $_SERVER['DOCUMENT_ROOT'].'/index.cfg.start.php' );

        //echo DirSite;
        $cfg = unserialize( file_get_contents( DirSite.'/module/'.strtolower(str_replace('www.','',$_SERVER['HTTP_HOST']).'_cash.cfg.php' ) ) );

        //echo '<pre>'; print_r( $cfg ); echo '</pre>';    
          
            if( isset( $cfg[$_REQUEST['level']] ) )
            {
            $ccfg = $cfg[$_REQUEST['level']];
            }

        // если локаль то смс-ки не обрабатываем
            if( strpos($_SERVER['DOCUMENT_ROOT'], 'W:') === false &&
                ( 
                    ( isset($ccfg['smstoken']{1}) ) ||
                    ( isset($ccfg['sms_login']{1}) && isset($ccfg['sms_pass']{1}) ) 
                )
                && isset( $ccfg['gsm']{3} )
            )
            {
            $ttxt = '';
            
                foreach( $_REQUEST as $k => $v )
                {
                    if( isset( $ccfg[$k.'_sendsms'] ) )
                    $ttxt .= ( isset($ttxt{2}) ? "
" : '' ).$v;
                }

                if( isset( $ttxt{3} ) )
                {

                $xml = '<?xml version="1.0" encoding="utf-8" ?>
                    <request>
                        <message type="sms">
                            <sender>'.( isset($ccfg['smssender']{1}) ? $ccfg['smssender'] : 'msg-site' ).'</sender>
                            <text>'.$ttxt.'</text>
                            <abonent phone="'.$ccfg['gsm'].'" />
                        </message>';

                    if( isset($ccfg['smstoken']{1}) )
                    {
                    $xml .= '<security>
                            <token value="'.$ccfg['smstoken'].'" />
                        </security>';
                    }
                    elseif( isset($ccfg['sms_login']{1}) && isset($ccfg['sms_pass']{1}) ) 
                    {
                    $xml .= '<security>
                            <login value="'.$ccfg['sms_login'].'" />
                            <password value="'.$ccfg['sms_pass'].'" />
                        </security>';
                    }

                $xml .= '</request>';
                $urltopost = 'http://xml.sms16.ru/xml/';

                /**
                * Initialize handle and set options
                */
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_CRLF, true );
                curl_setopt( $ch, CURLOPT_POST, true );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
                curl_setopt( $ch, CURLOPT_URL, $urltopost );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );

                /**
                * Execute the request
                */
                $result = curl_exec($ch);

                /**
                * Check for errors
                */
                    if ( curl_errno($ch) )
                    {
                    $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
                    }
                    else
                    {
                    $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if( $returnCode == 404 )
                        {
                        $result = 'ERROR -> 404 Not Found';
                        }

                    }

                /**
                * Close the handle
                */
                curl_close($ch);

                }
            }
            
            if( isset( $_REQUEST['fio'] ) )
            {
    //        $InVarB = array();
    //        $InVarB['host'] = $_SERVER['HTTP_HOST'];
    //        $InVarB['ip'] = $_SERVER['REMOTE_ADDR'];
    //        $InVarB['date'] = date('d m Y',time());
    //        $InVarB['time'] = date('H:i:s',time());

            //$ctpl -> ins_page('bw.mail.body', 'input var', $_t_tfo1);

            $sender2 = 'spawn@uralweb.info';
            //$status = '';

            $ttxt = '';

                foreach( $_REQUEST as $k => $v )
                {
                    if( isset( $ccfg[$k.'_sendmail'] ) )
                    $ttxt .= '<tr><td>'.( isset( $ccfg[$k.'_name']{0} ) ? $ccfg[$k.'_name'] : $k ).'</td><td>'.$v.'</td></tr>';
                }

            $d = array(
                'Дата' => date( 'd.m.Y', $_SERVER['REQUEST_TIME'] )
                ,'Время' => date( 'H:i', $_SERVER['REQUEST_TIME'] )
                );
                
                foreach( $d as $k => $v )
                {
                $ttxt .= '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
                }

            require_once( $_SERVER['DOCUMENT_ROOT'].'/0.all/class/mail.php' );

            $emailer -> ns_new( $sender2, $ccfg['email'] );
            $emailer -> ns_send( 'сайт: Новая заявка', '<html><body><h2>Новая заявка '.$_SERVER['HTTP_HOST'].'</h2>
                <table cellpadding="10" >'.$ttxt.'</table>
                </body></html>' );
            //echo $status;
            //echo '<pre>'.htmlspecialchars($ClassTemplate->tpl_files['bw.mail.body']).'</pre>';
            }

            if( isset($_REQUEST['gsm']) )
            {
            $txte = ' позвоним';
            }
            elseif( isset($_REQUEST['email']) )
            { 
            $txte = ' пришлём e-mail письмо';
            }

        die(json_encode(array( 'text' => 'Данные отправлены. В&nbsp;ближайшее время '.$txte ) ));
        }
        else
        {
        die(json_encode(array('cod' => 'error', 'text' => $msg ) ));
        }    
    
    die(json_encode(array('cod' => 'error', 'text' => 'Произошла непредвиденная ситуация, обновите страницу и отправьте заново пожалуйста' ) ));
            
    }
    elseif( isset($_REQUEST['cash']{3}) && 
        ( 
            $_REQUEST['cash'] == $_SESSION['cash22'] || 
            $_REQUEST['cash'] == $_SESSION['cash22_old'] 
        ) 
    )
    {
    // обработка формы - старт

    $polya = $_REQUEST;
    
    $error_form = false;
    
        if( !isset($_REQUEST['fio']{2}) ){ $error_form = true; $msg .= ( isset($msg{1}) ? '<br/>' : '' ).' Не указано как Вас зовут'; }
        if( !isset($_REQUEST['tel']{5}) ){ $error_form = true; $msg .= ( isset($msg{1}) ? '<br/>' : '' ).' Не указан телефон'; }

    // отправка мыла старт
    
        if( $error_form === false)
        {
            
        // sms отправка
            if( 1 == 1 && strpos( $_SERVER['HTTP_HOST'],'uralweb.info') !== FALSE )
            {

            $xml = '<?xml version="1.0" encoding="utf-8" ?>
                <request>
                    <message type="sms">
                        <sender>msg-site</sender>
                        <text>'.$_REQUEST['fio'].'/'.$_REQUEST['tel'].'/'.htmlspecialchars($_REQUEST['opis']).'</text>
                        <abonent phone="79222622289"/>
                    </message>
                    <security>
                        <login value="nyos2" />
                        <password value="123nyos123" />
                    </security>
                </request>';
            $urltopost = 'http://xml.sms16.ru/xml/';

            /**
            * Initialize handle and set options
            */
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_CRLF, true );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
            curl_setopt( $ch, CURLOPT_URL, $urltopost );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );

            /**
            * Execute the request
            */
            $result = curl_exec($ch);

            /**
            * Check for errors
            */
                if ( curl_errno($ch) )
                {
                $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
                }
                else
                {
                $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if( $returnCode == 404 )
                    {
                    $result = 'ERROR -> 404 Not Found';
                    }

                }

            /**
            * Close the handle
            */
            curl_close($ch);

            /**
            * Output the results
            */
            //echo $result; 
            }

        $InVarB = array();
        $InVarB['host'] = $_SERVER['HTTP_HOST'];
        $InVarB['ip'] = $_SERVER['REMOTE_ADDR'];
        $InVarB['date'] = date('d m Y',time());
        $InVarB['time'] = date('H:i:s',time());

        //$ctpl -> ins_page('bw.mail.body', 'input var', $_t_tfo1);

        $sender2 = 'spawn@uralweb.info';
        //$status = '';
        
        require_once( $_SERVER['DOCUMENT_ROOT'].'/0.all/class/mail.php' );

        $emailer -> ns_new( $sender2, 'nyos@me.com,support@uralweb.info' );
        $emailer -> ns_send( 'uralweb_info > Новая заявка (создание сайта)', '<html><body><h2>Новая заявка (создание сайта)</h2>
            <p>ФИО: '.htmlspecialchars($_REQUEST['fio']).'</p>
            <p>Тел: '.htmlspecialchars($_REQUEST['tel']).'</p>
            <p>Меседж: '.nl2br(htmlspecialchars($_REQUEST['opis'])).'</p>
            </body></html>' );
                //echo $status;
                //echo '<pre>'.htmlspecialchars($ClassTemplate->tpl_files['bw.mail.body']).'</pre>';
        
        die( json_encode(array('cod' => 0, 'text' => 'Заявка принята, Спасибо.' ) ) );

        //$ctpl -> ins_page('bw.body', 'insert', 'bw.ok');
        }
    }
    else{
    die(json_encode(array('cod' => 1, 'text' => 'Не верно указаны цифры' ) ));
    }
    
die(json_encode(array('cod' => 1, 'text' => 'Повторите отправку формы пожалуйста' ) ));


    
    
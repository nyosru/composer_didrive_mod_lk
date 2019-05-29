<?php

// папка шаблонов из модулей (всегда есть)
$dir_mod = dir_serv_mod_ver_tpl;

// папка шаблонов привязанных к сайту (может не быть)
$dir_mod_site = dir_serv_site_mod_tpl;

//f\pa($vv['now_level']); die();
$vv['mod_name'] = isset($vv['now_level']['name_in_tpl']{0}) ? $vv['now_level']['name_in_tpl'] : $vv['now_level']['name'];
$vv['warn_form'] = '';
$vv['hide_form'] = null;

//\f\pa($_POST);
//die();
// показ общей инфы о пользователе
if (isset($_REQUEST['option']{1}) && $_REQUEST['option'] == 'show' &&
        isset($_REQUEST['ext1']{0}) && is_numeric($_REQUEST['ext1'])) {

    if (file_exists($dir_mod_site . 'show_user.htm')) {
        $vv['tpl_0body'] = $dir_mod_site . 'show_user.htm';
    } else {
        $vv['tpl_0body'] = $dir_mod . 'show_user.htm';
    }
}

// подтверждение email
elseif (isset($_GET['confirm']{1}) && isset($_GET['s']{1}) && $_GET['s'] == md5('11' . $_GET['confirm'])) {

    $vv['tpl_0body'] = $dir_mod . 'blank.htm';

    $ee = Nyos\mod\lk::confirmMail($db, $vv['folder'], $_GET['confirm']);

    if (isset($ee) && is_numeric($ee)) {
        $vv['warn'] = 'E-mail <u>' . $_GET['confirm'] . '</u> подтверждён';
    } else {
        $vv['warn'] = 'E-mail <u>' . $_GET['confirm'] . '</u> не подтверждён, обратитесь к администратору';
    }
}

// НЕ показ общей инфы о пользователе
else {

// если вход осуществлён
    if (isset($_SESSION['now_user']['id']) && is_numeric($_SESSION['now_user']['id'])) {

        /**
         * загрузка и установка нового аватара
         */
        if (isset($_POST['new_ava']) && $_POST['new_ava'] == 'ok') {

            $res = Nyos\mod\lk::setNewAvatar($db, $vv['folder'], $_SESSION['now_user']['id'], $_FILES['new_avatar']);

            if ($res === true) {
                $vv['warn'] = 'Новый аватар загружен';
            } else {
                $vv['warn'] = 'При загрузке аватара произошла ошибка, повторите загрузку, так же проверте чтобы изображение было в формате jpg и размер более 1 Кб';
            }
        }
//
        elseif (isset($_POST['save_edit']) && $_POST['save_edit'] == 'ok') {

            Nyos\mod\lk::editAccount($db, $_SESSION['now_user']['id'], $_POST);
// Nyos\nyos::creatSecret($text)

            $data = Nyos\mod\lk::getInfoAccount($db, $_SESSION['now_user']['id']);

            if ($data !== false) {
// $_SESSION['now_user']['data'] = $data;
                $_SESSION['now_user'] = $data;
            }
        }
//
        elseif (isset($_REQUEST['save_new_pass']) && $_REQUEST['save_new_pass'] == 'ok') {

            $check_old_pass = true;

//            if (isset($_SESSION['now_user']['data']['pass'] {0}) ||
//                    isset($_SESSION['now_user']['data']['pass5'] {0})) {
            if (isset($_SESSION['now_user']['pass'] {0}) ||
                    isset($_SESSION['now_user']['pass5'] {0})) {

                $check_old_pass = false;

//                if (isset($_SESSION['now_user']['data']['pass'] {0}) &&
//                        $_POST['old'] == $_SESSION['now_user']['data']['pass']) {
                if (isset($_SESSION['now_user']['pass'] {0}) &&
                        $_POST['old'] == $_SESSION['now_user']['pass']) {
                    $check_old_pass = true;
//                } elseif (isset($_SESSION['now_user']['data']['pass5'] {0}) &&
//                        md5($_POST['old']) == $_SESSION['now_user']['data']['pass5']) {
                } elseif (isset($_SESSION['now_user']['pass5'] {0}) &&
                        md5($_POST['old']) == $_SESSION['now_user']['pass5']) {
                    $check_old_pass = true;
                }
            }

            if ($check_old_pass === false) {

                $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Указан не верный текущий пароль';
            } else {

                $save_new = true;

                if (isset($_POST['new_pass1']{3}) &&
                        isset($_POST['new_pass2']{3})) {
                    
                } else {
                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Минимальная длинна пароля 4 символа';
                    $save_new = false;
                }

                if ($save_new === true &&
                        $_POST['new_pass1'] == $_POST['new_pass2']) {
                    
                } else {
                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Пароль и подтверждение пароля не сходятся';
                    $save_new = false;
                }

                if ($save_new === true) {

                    $db->sql_query('UPDATE  `gm_user` SET  `pass` = NULL , `pass5` = MD5(  \'' . addslashes($_POST['new_pass1']) . '\' ) WHERE  `id` = \'' . addslashes($_SESSION['now_user']['id']) . '\' LIMIT 1 ;');
// $_SESSION['now_user']['data']['pass5'] = md5($_POST['new_pass1']);
                    $_SESSION['now_user']['pass5'] = md5($_POST['new_pass1']);

                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Установлен новый пароль';
                }
            }

// Nyos\mod\lk::saveUserOption($db, $_SESSION['now_user']['id'], $_POST);
        }
//
        elseif (isset($_REQUEST['save_mail_option']) && $_REQUEST['save_mail_option'] == 'go') {

            $vv['warn'] = 'Изменения сохранены';
            Nyos\mod\lk::saveUserOption($db, $_SESSION['now_user']['id'], $_POST);
        }

        /**
         * выход из аккаунта
         */
        if (( isset($vv['now_level']['type2']) && $vv['now_level']['type2'] == 'exit' ) || (isset($_REQUEST['option']) && $_REQUEST['option'] == 'exit')) {

            $_SESSION['now_user'] = null;

            if (isset($vv['now_level']['head_level']{1})) {
                header('Location:http://' . $_SERVER['HTTP_HOST'] . '/' . $vv['now_level']['head_level'] . '/');
            } else {
                header('Location:http://' . $_SERVER['HTTP_HOST'] . '/');
            }
            exit;
        }
    }

// если вход не выполнен
    else {

        $vv['show_recovery_pass'] = null;

        /**
         * восстановление пароля
         */
        if (isset($_GET['recovery']{5}) && isset($_GET['user'])) {

            if (\Nyos\nyos::checkSecret($_GET['recovery'], $_GET['user'] . $vv['level'])) {

                $vv['show_recovery_pass'] = true;

                if (isset($_POST['repass']) && $_POST['repass'] == 'ok') {

                    if (isset($_POST['renew_pass1']{3}) && isset($_POST['renew_pass2']{3}) && $_POST['renew_pass1'] == $_POST['renew_pass2']) {
                        $vv['warn_form'] = 'Новый пароль установлен';
                        $vv['hide_form'] = true;

                        Nyos\mod\lk::setNewPassword($db, $vv['folder'], $_GET['user'], $_POST['renew_pass1']);
                    } else {
                        $vv['warn_form'] = 'Длинна пароля и подтверждения пароли меньше 6-ти символов или они не совпадают';
                    }
                }
            } else {
                $vv['warn'] = 'Произошла неописуемая ситуация #' . __LINE__ . '<br/><br/>Повторите пожалуйста';
            }
        }

        /**
         * отправили запрос на восстановление пароля
         */ elseif (isset($_POST['repass']) && $_POST['repass'] == 'ok') {

            if (isset($_POST['remail']{3})) {

                if (isset($vv['now_level']['recapcha']['secret']{5})) {
                    $vv['recapcha'] = \Nyos\mod\lk::verifyRecapcha($vv['now_level']['recapcha']['secret'], $_POST['g-recaptcha-response']);
                }

                if (
                        !isset($vv['now_level']['recapcha']['secret']{5}) ||
                        (
                        isset($vv['now_level']['recapcha']['secret']{5}) && isset($vv['recapcha']['status']) && $vv['recapcha']['status'] == 'ok'
                        )
                ) {


                    $data = \Nyos\mod\lk::passwordRecoveryCheck($db, $vv['folder'], $_POST['remail']);

                    if ($data !== false) {

                        require_once ($_SERVER['DOCUMENT_ROOT'] . '/0.all/class/mail.2.php');

                        Nyos\mod\mailpost::$ns['from'] = 'support@uralweb.info';
                        Nyos\mod\mailpost::$ns['to'] = $data['mail'];

                        Nyos\mod\mailpost::ns_send('Восстановление пароля', '<html><body>'
                                . '<h3>Восстановление пароля</h3>'
                                . '<p>Для установки нового пароля, перейдите по ссылке > <a '
                                . 'href="http://' . $_SERVER['HTTP_HOST'] . '/index.php?level=' . $vv['level']
                                . '&user=' . $data['id']
                                . '&recovery=' . \Nyos\nyos::creatSecret($data['id'] . $vv['level'])
                                . '" >Установить новый пароль</a></p>'
                                . '</body></html>');

                        $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Инструкция по восстановлению пароля, отправлена на E-mail';
                    } else {
                        $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'E-mail указан неверно';
                    }
                }
                //
                else {

                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Похоже вы робот, повторите отправку формы';
                }
            } else {

                $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Логин или пароль указаны неверно';
            }
        }

        /**
         * вход по соц сервису
         */ elseif (isset($_POST['token']{10})) {

            $_SESSION['now_user'] = \Nyos\Mod\Lk::enterSoc($db, $vv['folder'], $_POST['token']);
            //\f\pa($_SESSION['now_user']);
            
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/include/Nyos/nyos_msg.php')) {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Nyos/nyos_msg.php';
                $e = '';
                foreach ($_SESSION['now_user'] as $k => $v) {
                    if( isset($v{0}) )
                    $e .= $k . ': ' . $v . PHP_EOL;
                }
                \Nyos\NyosMsg::sendTelegramm('Вход на сайт ' . $_SERVER['HTTP_HOST'] . PHP_EOL . PHP_EOL . $e);
            }

            $sql = $db->prepare('SELECT * FROM `gm_user_option` WHERE `user` = :user ;');
            $sql->execute(array(':user' => $_SESSION['now_user']['id']));

            while ($r = $sql->fetch()) {

                if (!isset($_SESSION['now_user']['dop']))
                    $_SESSION['now_user']['dop'] = array();

                if (isset($r['project']{0})) {
                    $_SESSION['now_user']['dop'][$r['project']][$r['option']] = $r['value'];
                } else {
                    $_SESSION['now_user']['dop'][$r['option']] = $r['value'];
                }
            }

            if (isset($_REQUEST['goto']{1})) {
                header('Location: http://' . $_SERVER['HTTP_HOST'] . $_REQUEST['goto']);
                exit;
            }
            //
            else if (isset($vv['now_level']['goto_after_enter']{0})) {

                die('<html><head><meta http-equiv="refresh" content="0;http://' . $_SERVER['HTTP_HOST'] . $vv['now_level']['goto_after_enter'] . '"></head>' .
                        '<body><br/><br/><br/><center>секунду пожалуйста</body></html>');

                header('Location: http://' . $_SERVER['HTTP_HOST'] . $vv['now_level']['goto_after_enter']);
                exit;
            }
            //
            else if (isset($_COOKIE['show_page']{5})) {
                header('Location: ' . $_COOKIE['show_page']);
                exit;
            }
        }



// вход по логину и паролю
        else {

            if (isset($_POST['type']) && ( $_POST['type'] == 'enter_form' || $_POST['type'] == 'reg_form' )) {

                if (
                        isset($vv['now_level']['recapcha']['secret']{5}) && isset($_POST['g-recaptcha-response'])
                ) {

//                    $query = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $vv['now_level']['recapcha']['secret']
//                            . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
//
//                    $vv['recapcha'] = json_decode(file_get_contents($query), true);
                    // \f\pa($vv['recapcha']);
                    //echo '<br/>'.__LINE__;
//                if( isset( $vv['recapcha']['status'] ) && $vv['recapcha']['status'] === true ){
//                    echo '<br/>'.__LINE__;
//                }else{
//                    echo '<br/>'.__LINE__;
//                }
//                
//                die();

                    $vv['recapcha'] = \Nyos\mod\lk::verifyRecapcha($vv['now_level']['recapcha']['secret'], $_POST['g-recaptcha-response']);
                }

//echo '<br/>' . __LINE__;
                //\f\pa($vv['recapcha']);

                if (
                        !isset($vv['now_level']['recapcha']['secret']{5}) ||
                        (
                        isset($vv['now_level']['recapcha']['secret']{5}) && isset($vv['recapcha']['status']) && $vv['recapcha']['status'] == 'ok'
                        )
                ) {


// Продолжаем работать с данными для авторизации из POST массива
// рега по логину и паролю
                    if (isset($_POST['type']) && $_POST['type'] == 'reg_form') {

                        if (isset($vv['now_level']['recapcha']['secret']{5})) {
                            $vv['recapcha'] = \Nyos\mod\lk::verifyRecapcha($vv['now_level']['recapcha']['secret'], $_POST['g-recaptcha-response']);
                        }

                        if (
                                !isset($vv['now_level']['recapcha']['secret']{5}) ||
                                (
                                isset($vv['now_level']['recapcha']['secret']{5}) && isset($vv['recapcha']['status']) && $vv['recapcha']['status'] == 'ok'
                                )
                        ) {

                            if (!isset($_POST['pass']{3})) {
                                $vv['warn'] .= 'Укажите пароль<br/>';
                            } elseif (!isset($_POST['pass2']{3})) {
                                $vv['warn'] .= 'Укажите подтверждение пароля<br/>';
                            } elseif ($_POST['pass'] != $_POST['pass2']) {
                                $vv['warn'] .= 'Пароль и подтверждение не сходятся<br/>';
                            }

                            if (isset($vv['now_level']['no_need_name_for_reg']) && $vv['now_level']['no_need_name_for_reg'] == 'yes') {

                                if (!isset($_POST['name']{1})) {
                                    $vv['warn'] .= 'Укажите своё имя<br/>';
                                }

                                if (!isset($_POST['family']{1})) {
                                    $vv['warn'] .= 'Укажите Фамилию<br/>';
                                }
                            }

                            // есть ошибки
                            if (isset($vv['warn']{1})) {
                                echo $vv['warn'];

// echo '<br/>' . __LINE__;
                            }
                            // нет ошибок
                            else {

                                $indb = [];
// $indb['soc_web'] = $user['network'];

                                if (isset($_POST['name']{0}))
                                    $indb['name'] = $_POST['name'];

                                if (isset($_POST['family']{0}))
                                    $indb['family'] = $_POST['family'];

                                $indb['mail'] = trim($_POST['email']);

// $indb['about'] = $user['about'];

                                $indb['pass'] = $_POST['pass'];

                                if (isset($_POST['country']{0}))
                                    $indb['country'] = $_POST['country'];

                                $indb['folder'] = $vv['folder'];
                                $indb['dt'] = 'NOW';

                                $sql11 = $db->sql_query('SELECT * FROM gm_user '
                                        . ' WHERE '
                                        . ' `folder` = \'' . $vv['folder'] . '\' '
                                        . ' AND `mail` = \'' . addslashes($indb['mail']) . '\' '
                                        . ' LIMIT 1 ; ');

// echo $status;

                                if ($db->sql_numrows($sql11) == 1) {

                                    $vv['warn'] .= ( isset($warn{1}) ? '<br/>' : '') . 'Данный e-mail уже используется, укажите другой';
                                }
// 
                                else {

// echo '<br/>'.__LINE__;
// $status = '';

                                    $id_new = f\db\db2_insert($db, 'gm_user', $indb, false, 'last_id');

// echo $status;
// f\pa($id_new);

                                    if (isset($id_new) && is_numeric($id_new)) {

                                        if ($_POST['type'] == 'reg_form') {

                                            $vv['warn'] .= '<br/>Регистрация проведена успешно';

                                            if (isset($vv['now_level']['reg']) && $vv['now_level']['reg']['confirm_mail'] == 'da') {

//                                                require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/class/idna_convert.class.php' );  //  Подключаем  класс
//                                                $IDN = new idna_convert();
//                                                $http = $IDN->decode($_SERVER['HTTP_HOST']);

                                                if (!isset($Punycode))
                                                    $Punycode = new \TrueBV\Punycode();
//var_dump($Punycode->encode('renangonçalves.com')); // xn--renangonalves-pgb.com
                                                $http = $Punycode->decode($_SERVER['HTTP_HOST']); // народнаяэкономика.рф                                                     

                                                $vars = array(
                                                    'uri' => 'http://' . $_SERVER['HTTP_HOST']
                                                    , 'logo_img' => 'http://мыправы.рф/9.site/kl1706mu_pravu/download/img/logo.png'
                                                    , 'logo_name' => $vv['now_level']['mail_company_name']
                                                    , 'head' => 'Подтверждение E-mail'
                                                    , 'text' => '<p>Добро пожаловать на общероссийскую платформу<br/>для ваших петиций и инициатив "Мы правы" !</p>'
                                                    . '<p>Для завершения регистрации подтвердите адрес электронной почты,<br/>пройдя по ссылке: '
                                                    . '<a href="http://' . $_SERVER['HTTP_HOST'] . '/index.php?level=' . $vv['now_level']['cfg.level'] . '&confirm=' . $indb['mail'] . '&s=' . md5('11' . $indb['mail']) . '">'
                                                    . 'http://' . $http . '/index.php?level=' . $vv['now_level']['cfg.level'] . '&confirm=' . $indb['mail'] . '&s=' . md5('11' . $indb['mail']) . '</a></p>'
                                                );

                                                require_once($_SERVER['DOCUMENT_ROOT'] . '/0.site/exe/backword/class.php');

                                                $from = array(
                                                    'name' => 'Мы правы'
                                                    , 'email' => 'pravu@uralweb.info'
                                                );

                                                $to = array(
                                                    'name' => $indb['family'] . ' ' . $indb['name']
                                                    , 'email' => $indb['mail']
                                                );

// шаблон для письма (лежат в папке /template-mail/* )
// Nyos\backword::sendMailSendpulse( $SPApiClient, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
                                                Nyos\backword::sendMailSendpulse(
                                                        $vv['now_level']['sendpulse_id'], $vv['now_level']['sendpulse_secret'], $from, $to, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
                                            }
//
                                            else {

                                                /*
                                                  if( isset( $vv['now_level']['goto_after_reg'] ) ){

                                                  $indb['id'] = $id_new;
                                                  // $_SESSION['now_user'] = $indb;
                                                  // $_SESSION['now_user']['data']['id'] = $id_new;

                                                  \Nyos\mod\lk::enter( $db, $vv['folder'], $indb['login'], $indb['pass'], false );

                                                  sleep(1);

                                                  \f\redirect( '//'.$_SERVER['HTTP_HOST'].$vv['now_level']['goto_after_reg'] );
                                                  }
                                                 */

                                                try {

                                                    Nyos\mod\lk::enter2($db, $vv['folder'], $indb['mail'], $indb['pass']);

                                                    if (isset($vv['now_level']['goto_after_enter']{0})) {
                                                        //die( '#'.__LINE__ );
                                                        header('Location: ' . $vv['now_level']['goto_after_enter']);
                                                        exit;
                                                    }
                                                    //
                                                    elseif (isset($_COOKIE['show_page']{5})) {
                                                        //die( '#'.__LINE__ );
                                                        header('Location: ' . $_COOKIE['show_page']);
                                                        exit;
                                                    }
                                                }
                                                // вход не удался
                                                catch (Exception $e) {
                                                    //$vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . $e->getMessage() . ' <abbr title="#' . __LINE__ . '.' . $e->getLine() . '" >.</abbr>';
                                                }

                                                $vv['warn'] .= '<br/>Входите в личный кабинет указав e-mail и пароль<br/><br/>';
                                            }
                                        }
                                        //
                                        else {
                                            $vv['warn'] .= '<br/>Осуществлена регистрация с помощью соц. сервиса';
                                        }
                                    }
                                }
                            }
                        }
                        //
                        else {
                            $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Похоже вы робот, повторите отправку формы';
                        }
                    }

// вход по логину и паролю
                    elseif (isset($_POST['type']) && $_POST['type'] == 'enter_form') {

                        try {

                            Nyos\mod\lk::enter2($db, $vv['folder'], $_POST['mail_enter'], $_POST['pass_enter']);

                            if (isset($vv['now_level']['goto_after_enter']{0})) {
                                header('Location: ' . $vv['now_level']['goto_after_enter']);
                                exit;
                            } elseif (isset($_COOKIE['show_page']{5})) {
                                header('Location: ' . $_COOKIE['show_page']);
                                exit;
                            }
                        }
                        // вход не удался
                        catch (Exception $e) {
                            $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . $e->getMessage() . ' <abbr title="#' . __LINE__ . '.' . $e->getLine() . '" >.</abbr>';
                        }
                    }
                }
                //
                else {
                    $vv['warn'] .= 'Извините, но похоже вы робот. Просим вас повторить вход<br/>';
                }
            }
        }
    }

    $vv['tpl_0body'] = \f\like_tpl('body', $dir_mod, $dir_mod_site);

    /*
      if (file_exists($dir_mod_site . 'body.htm')) {
      $vv['tpl_0body'] = $dir_mod_site . 'body.htm';
      } else {
      $vv['tpl_0body'] = $dir_mod . 'body.htm';
      }
     */

//echo '#<br/>#'.__LINE__;
// если вход выполнен
    if (isset($_SESSION['now_user']['id']{0})) {

// \f\pa($vv['now_level']);

        /**
         * запись нескольких адресов на аккаунт
         */
// module_adress = yes
        if (isset($vv['now_level']['module_adress'])) {

            require_once dirname(__FILE__) . '/v.3.smarty.address.php';
        }
        // если не работа с адресами
        else {

            $vv['tpl_body'] = \f\like_tpl('body_lk', $dir_mod, $dir_mod_site);
        }
    }

// если вход не выполнен
    else {

// echo '<Br/>'.__LINE__.' + '.$dir_mod;
// echo '<Br/>'.__LINE__.' + '.$dir_mod_site;

        $vv['tpl_body'] = \f\like_tpl('body_noenter', $dir_mod, $dir_mod_site);

// echo '<Br/>'.__LINE__.' + '.$vv['tpl_body'];

        /*
          if (file_exists($dir_mod_site . 'body_noenter.htm')) {
          $vv['tpl_body'] = $dir_mod_site . 'body_noenter.htm';
          } else {
          $vv['tpl_body'] = $dir_mod . 'body_noenter.htm';
          }
         */
    }
}

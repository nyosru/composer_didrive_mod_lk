<?php

//f\pa($vv['now_level']);
//    $indb = array(
//        'name' => 'Иван Петров'
//        ,'mail' => '3@uralweb.info'
//        );
//echo '<hr><hr>';

require_once dirname(__FILE__) . DS . 'class.php';

// папка шаблонов из модулей (всегда есть)
$dir_mod = $_SERVER['DOCUMENT_ROOT'] . '/0.site/exe/' . $vv['now_level']['type'] . '/' . $vv['now_level']['version'] . '/tpl_smarty/';
// папка шаблонов привязанных к сайту (может не быть)
$dir_mod_site = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $vv['folder'] . '/module/' . $vv['now_level']['cfg.level'] . '/tpl_smarty/';

//f\pa($vv['now_level']); die();

$vv['mod_name'] = isset($vv['now_level']['name_in_tpl']{0}) ? $vv['now_level']['name_in_tpl'] : $vv['now_level']['name'];
$vv['warn_form'] = '';
$vv['hide_form'] = null;

if( isset( $_GET['send_confirm'] ) && isset( $_GET['s'] ) && Nyos\nyos::checkSecret( $_GET['s'], $_GET['send_confirm']) ){
// send_confirm='.$login.'&s='.nyos2::creatSecret($login).'" style="color:blue;" >отправить email подтверждения ещё раз</a> )'

    // $r = Nyos\mod\lk::confirmMail($db, $vv['folder'], $_GET['send_confirm'] );
    $indb = Nyos\mod\lk::getUser_mail($db, $vv['folder'], $_GET['send_confirm'] );
    //f\pa($indb);

    if( $indb !== false && ( !isset( $indb['mail_confirm'] ) || isset( $indb['mail_confirm'] ) && $indb['mail_confirm'] != 'yes' ) ){
    // f\pa($r);

        require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/class/idna_convert.class.php' );  //  Подключаем  класс
        $IDN = new idna_convert();
        $http = $IDN->decode($_SERVER['HTTP_HOST']);
        // die( $http );

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

        //$status = '';

        $from = array(
            'name' => 'Мы правы'
            //, 'email' => 'mail@mypravy.ru'
            , 'email' => 'pravu@uralweb.info'
        );

        $to = array(
            'name' => $indb['family'] . ' ' . $indb['name']
            , 'email' => $indb['mail']
        );

        // шаблон для письма (лежат в папке /template-mail/* )
        // Nyos\backword::sendMailSendpulse( $SPApiClient, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
        /*
          Nyos\backword::sendMailSendpulse(
          $vv['now_level']['sendpulse_id'], $vv['now_level']['sendpulse_secret'],
          $from, $to,
          $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
         */
        $vars['sp_id'] = isset($vv['now_level']['sendpulse_id']{1}) ? $vv['now_level']['sendpulse_id'] : null;
        $vars['sp_secret'] = isset($vv['now_level']['sendpulse_secret']{1}) ? $vv['now_level']['sendpulse_secret'] : null;

        //global $status;
        //$status = '';
        Nyos\backword::sendMailSuper($from, $to, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации (повторный email)', $vars);
        //echo $status;

    $vv['warn'] = 'E-mail подтверждения отправлен';
    }else{
    $vv['warn'] = 'E-mail подтверждён';
    }

}



// показ общей инфы о пользователе
if (isset($_REQUEST['option']{1}) && $_REQUEST['option'] == 'show' &&
        isset($_REQUEST['ext1']{0}) && is_numeric($_REQUEST['ext1'])) {

    if (file_exists($dir_mod_site . 'show_user.htm')) {
        $vv['tpl_0body'] = $dir_mod_site . 'show_user.htm';
    } else {
        $vv['tpl_0body'] = $dir_mod . 'show_user.htm';
    }
}


// пришли по ссылке подтверждения мыльника
elseif (isset($_GET['confirm']{1}) && isset($_GET['s']{1}) && $_GET['s'] == md5('11' . $_GET['confirm'])) {

    $vv['tpl_0body'] = $dir_mod . 'blank.htm';
    $vv['warn'] = 'E-mail <u>' . $_GET['confirm'] . '</u> подтверждён';

    //$status = '';
    $db->sql_query('UPDATE  `gm_user`
        SET `mail_confirm`= \'yes\'
        WHERE
            `folder` = \'' . addslashes($vv['folder']) . '\'
            AND `mail` =  \'' . addslashes($_GET['confirm']) . '\'
        LIMIT 1 ;');
    //echo $status;
}

// НЕ показ общей инфы о пользователе
else {


//$_POST['token'] = '9ec205f92de63e33bc71091b21cf72b3';
// если вход осуществлён
    if (isset($_SESSION['now_user']['id']) && is_numeric($_SESSION['now_user']['id'])) {

        //[name] => Сергей
        //[soname] => Бакланов
        //[adres] => ул. М.Горького д.41 кв.18
        //[phone] => +79222622289
        //[about] =>
        //[save_edit] => ok
        //)

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
        } elseif (isset($_POST['save_edit']) && $_POST['save_edit'] == 'ok') {

            Nyos\mod\lk::editAccount($db, $_SESSION['now_user']['id'], $_POST);
            // Nyos\nyos::creatSecret($text)

            $data = Nyos\mod\lk::getInfoAccount($db, $_SESSION['now_user']['id']);

            if ($data !== false) {
                $_SESSION['now_user']['data'] = $data;
            }
        } elseif (isset($_REQUEST['save_new_pass']) && $_REQUEST['save_new_pass'] == 'ok') {

            $check_old_pass = true;

            if (isset($_SESSION['now_user']['data']['pass'] {0}) ||
                    isset($_SESSION['now_user']['data']['pass5'] {0})) {

                $check_old_pass = false;

                if (isset($_SESSION['now_user']['data']['pass'] {0}) &&
                        $_POST['old'] == $_SESSION['now_user']['data']['pass']) {
                    $check_old_pass = true;
                } elseif (isset($_SESSION['now_user']['data']['pass5'] {0}) &&
                        md5($_POST['old']) == $_SESSION['now_user']['data']['pass5']) {
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
                    $_SESSION['now_user']['data']['pass5'] = md5($_POST['new_pass1']);

                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Установлен новый пароль';
                }
            }


            // Nyos\mod\lk::saveUserOption($db, $_SESSION['now_user']['id'], $_POST);
        } elseif (isset($_REQUEST['save_mail_option']) && $_REQUEST['save_mail_option'] == 'go') {

            $vv['warn'] = 'Изменения сохранены';
            Nyos\mod\lk::saveUserOption($db, $_SESSION['now_user']['id'], $_POST);
        }

        if (isset($_REQUEST['option']) && $_REQUEST['option'] == 'exit') {
            $_SESSION['now_user'] = null;
            header('Location:http://' . $_SERVER['HTTP_HOST'] . '/');
            exit;
        }
    }

// если вход не выполнен
    else {

        // f\pa($_POST);
        // exit();

        $vv['show_recovery_pass'] = null;

        //echo '<br/>' . __FILE__ . ' [' . __LINE__ . ']';
        // восстановление пароля
        if (isset($_GET['recovery']{5}) && isset($_GET['user'])) {

            // echo '<br/>' . __FILE__ . ' [' . __LINE__ . ']';

            if (Nyos\mod\lk::recoveryPassCheck($db, $vv['folder'], $_GET['user'], $_GET['recovery']) === true) {

                //echo '<br/>' . __FILE__ . ' [' . __LINE__ . ']';
                $vv['show_recovery_pass'] = true;

                //[renew_pass1] => 111111111
                //[renew_pass2] => 222222222
                //[repass] => ok
                if (isset($_POST['repass']) && $_POST['repass'] == 'ok') {

                    if (isset($_POST['renew_pass1']{3}) && isset($_POST['renew_pass2']{3}) && $_POST['renew_pass1'] == $_POST['renew_pass2']) {
                        $vv['warn_form'] = 'Новый пароль установлен';
                        $vv['hide_form'] = true;

                        Nyos\mod\lk::setNewPassword($db, $vv['folder'], $_GET['user'], $_POST['renew_pass1']);
                    } else {
                        $vv['warn_form'] = 'Длинна пароля и подтверждения пароли меньше 6-ти символов или они не совпадают';
                    }
                }
            }
        }

        // отправили запрос на восстановление пароля
        elseif (isset($_POST['repass']) && $_POST['repass'] == 'ok') {

            if (isset($_POST['remail']{3})) {

                $data = Nyos\mod\lk::passwordRecoveryCheck($db, $vv['folder'], $_POST['remail']);

                if ($data !== false) {

                    require_once ($_SERVER['DOCUMENT_ROOT'] . '/0.all/class/mail.2.php');

                    Nyos\mod\mailpost::$ns['from'] = 'support@uralweb.info';
                    Nyos\mod\mailpost::$ns['to'] = $data['mail'];

                    Nyos\mod\mailpost::ns_send('Восстановление пароля', '<html><body>'
                            . '<h3>Восстановление пароля</h3>'
                            . '<p>Для установки нового пароля, перейдите по ссылке > <a href="http://' . $_SERVER['HTTP_HOST'] . '/index.php?level=' . $vv['level'] . '&user=' . $data['id'] . '&recovery=' . $data['recovery'] . '" >Установить новый пароль</a></p>'
                            . '</body></html>');

                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Инструкция по восстановлению пароля, отправлена на E-mail';
                } else {
                    $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'E-mail указан неверно';
                }
            } else {

                $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Логин или пароль указаны неверно';
            }
        }

        // вход по соц сервису
        elseif (isset($_POST['token']{10})) {

            //echo '<br/>'.__LINE__;

            $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
            $user = json_decode($s, true);
            // die(f\pa($user));
//        if( isset($ee['response'][0]['photo']{1}) ){
//        copy($ee['response'][0]['photo'],);
//        }



            $sql1 = $db->sql_query('SELECT * FROM gm_user WHERE `folder` = \'' . $vv['folder'] . '\' AND `soc_web` = \'' . addslashes($user['network']) . '\' AND `soc_web_id` = \'' . addslashes($user['uid']) . '\' LIMIT 1 ; ');

            if ($db->sql_numrows($sql1) == 1) {

                //echo '<br/>'.__LINE__;
                //$vv['warn'] .= 'Осуществлён вход с помощью соц. сервиса';

                $_SESSION['now_user']['data'] = $db->sql_fr($sql1);
                $_SESSION['now_user']['id'] = $_SESSION['now_user']['data']['id'];

                if (isset($_COOKIE['show_page']{5})) {
                    header('Location: ' . $_COOKIE['show_page']);
                    exit;
                }
            } else {

                // echo '<br/>'.__LINE__;
                // f\pa($user);
                //$user['network'] - соц. сеть, через которую авторизовался пользователь
                //$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
                //$user['first_name'] - имя пользователя
                //$user['last_name'] - фамилия пользователя

                $indb = $user;
                $indb['soc_web'] = $user['network'];
                $indb['name'] = $user['first_name'];
                $indb['family'] = $user['last_name'];
                $indb['soc_web_link'] = $user['profile'];
                $indb['soc_web_id'] = $user['uid'];
                $indb['folder'] = $vv['folder'];

                $dir_for_ava = $_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $vv['folder'] . '/download/avatars';

                if (!is_dir($dir_for_ava))
                    mkdir($dir_for_ava, 0755);

                require_once $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/file.2.php';

                if (isset($user['uid']{3}) && isset($user['network']) && $user['network'] == 'facebook') {

                    // http://graph.facebook.com/user_id/picture?type=large
                    $indb['avatar2'] = $user['uid'];

                    //copy( 'http://graph.facebook.com/'.$user['uid'].'/picture?type=large' , $dir_for_ava . '/' . $indb['avatar']);
                    $avat = file_get_contents('http://graph.facebook.com/' . $user['uid'] . '/picture?type=large');
                    file_put_contents($dir_for_ava . '/' . $indb['avatar2'], $avat);

                    $new_ext = f\readTypeImage($dir_for_ava . '/' . $indb['avatar2']);

                    if ($new_ext !== false) {
                        rename($dir_for_ava . '/' . $indb['avatar2'], $dir_for_ava . '/' . $indb['avatar2'] . '.' . $new_ext);
                        $indb['avatar'] .= $indb['avatar2'] . '.' . $new_ext;
                    }

                    if (file_exists($dir_for_ava . '/' . $indb['avatar'])) {
                        // $indb['avatar'] = $indb['avatar2'];
                    }
                } elseif (isset($user['uid']{3}) && isset($user['network']) && $user['network'] == 'vkontakte') {

                    $ee = json_decode(file_get_contents('http://api.vk.com/method/users.get?user_ids=' . $user['uid'] . '&fields=photo_200'), true);

                    // f\pa($ee);
                    if (isset($ee['response'][0]['photo_200']{5})) {

                        $indb['avatar2'] = $user['uid'] . '.' . f\get_file_ext($ee['response'][0]['photo_200']);

                        //copy($ee['response'][0]['photo_200'], $dir_for_ava . '/' . $indb['avatar']);
                        $avat = file_get_contents($ee['response'][0]['photo_200']);
                        file_put_contents($dir_for_ava . '/' . $indb['avatar'], $avat);

                        if (file_exists($dir_for_ava . '/' . $indb['avatar2']))
                            $indb['avatar'] = $indb['avatar2'];
                    }
                    // echo '<img src="' . $ee['response'][0]['photo_200'] . '" xwidth="50" alt="" border="0" />';
                    // f\pa($user);
                    // die();
                }

                // echo __FILE__.'['.__LINE__.']<br/>';
                // $status = '';
                $indb['dt'] = 'NOW';




                $sql11 = $db->sql_query('SELECT * FROM gm_user '
                        . ' WHERE '
                        . ' `folder` = \'' . $vv['folder'] . '\' '
                        . ' AND `mail` = \'' . addslashes($indb['mail']) . '\' '
                        . ' LIMIT 1 ; ');

                if ($db->sql_numrows($sql11) == 1) {
                    $vv['warn'] .= 'Данный e-mail уже используется, укажите другой';
                } else {


                    $id_new = f\db\db2_insert($db, 'gm_user', $indb, 'no', 'last_id');
                    // echo $status;
                    // die(f\pa($id_new));
                    //f\pa($_POST);

                    if (isset($id_new{0}) && is_numeric($id_new)) {

                        if ($_POST['type'] == 'reg_form') {
                            $vv['warn'] .= 'Регистрация проведена успешно';

                            if ($vv['now_level']['activaciya_mail'] == 'da') {
                                $vv['warn'] .= '<br/>Необходимо подтвердить регистрацию, ссылка отправлена на email';
                            }
                        } else {
                            $vv['warn'] .= 'Осуществлена регистрация с помощью соц. сервиса.';
                        }

                        $_SESSION['now_user']['data'] = $indb;
                        $_SESSION['now_user']['id'] = $id_new;

                        if (isset($_COOKIE['show_page']{5})) {
                            header('Location: ' . $_COOKIE['show_page']);
                            exit;
                        }
                    }
                }
            }
        }

        // вход по логину и паролю
        else {
            if (isset($_POST['g-recaptcha-response'])) {

                $url_to_google_api = "https://www.google.com/recaptcha/api/siteverify";
                $secret_key = '6LcXBCgUAAAAAG2ssPIcW_vKo326EETC6y7ZqDrn';
                $query = $url_to_google_api . '?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
                $data = json_decode(file_get_contents($query));

                if ($data->success) {

                    // f\pa($_POST);
                    // exit;
                    // Продолжаем работать с данными для авторизации из POST массива
                    // рега по логину и паролю
                    if (isset($_POST['type']) && $_POST['type'] == 'reg_form') {

                        // f\pa($_POST);
                        // echo __FILE__ . '[' . __LINE__ . ']';

                        if (!isset($_POST['pass']{3})) {
                            $vv['warn'] .= 'Укажите пароль<br/>';
                        } elseif (!isset($_POST['pass2']{3})) {
                            $vv['warn'] .= 'Укажите подтверждение пароля<br/>';
                        } elseif ($_POST['pass'] != $_POST['pass2']) {
                            $vv['warn'] .= 'Пароль и подтверждение не сходятся<br/>';
                        }

                        if (!isset($_POST['name']{1}))
                            $vv['warn'] .= 'Укажите своё имя<br/>';

                        if (!isset($_POST['family']{1}))
                            $vv['warn'] .= 'Укажите пароль<br/>';

                        if (!isset($vv['warn']{1})) {

                            $indb = array();
                            // $indb['soc_web'] = $user['network'];
                            $indb['name'] = $_POST['name'];
                            $indb['family'] = $_POST['family'];
                            $indb['mail'] = trim($_POST['email']);
                            // $indb['about'] = $user['about'];
                            $indb['pass'] = $_POST['pass'];

                            // $indb['soc_web_link'] = $user['profile'];
                            // $indb['soc_web_id'] = $user['uid'];
                            $indb['folder'] = $vv['folder'];
                            $indb['dt'] = 'NOW';

                            $sql11 = $db->sql_query('SELECT * FROM gm_user '
                                    . ' WHERE '
                                    . ' `folder` = \'' . $vv['folder'] . '\' '
                                    . ' AND `mail` = \'' . addslashes($indb['mail']) . '\' '
                                    . ' LIMIT 1 ; ');

                            if ($db->sql_numrows($sql11) == 1) {
                                $vv['warn'] .= '<br/>Данный e-mail уже используется, укажите другой';
                            } else {

                                // $status = '';

                                $id_new = f\db\db2_insert($db, 'gm_user', $indb, 'no', 'last_id');

                                // echo $status;
                                // f\pa($id_new);



                                if (isset($id_new{0}) && is_numeric($id_new)) {

                                    if ($_POST['type'] == 'reg_form') {
                                        $vv['warn'] .= ( isset($vv['warn']{3}) ? '<br/>' : '' ) . '<center>Регистрация проведена успешно';

                                        // f\pa($vv['now_level']);
                                        //echo '<br/>'.__FILE__.'['.__LINE__.']';

                                        if ($vv['now_level']['activaciya_mail'] == 'da') {
                                            $vv['warn'] .= '<br/>Необходимо подтвердить регистрацию, ссылка отправлена на email';
                                        }


                                        if ($vv['now_level']['reg']['confirm_mail'] == 'da' || $vv['now_level']['activaciya_mail'] == 'da'
                                        ) {

                                            //echo '<br/>'.__FILE__.'['.__LINE__.']';

                                            require_once( $_SERVER['DOCUMENT_ROOT'] . '/0.all/class/idna_convert.class.php' );  //  Подключаем  класс
                                            $IDN = new idna_convert();
                                            $http = $IDN->decode($_SERVER['HTTP_HOST']);
                                            // die( $http );

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

                                            //$status = '';

                                            $from = array(
                                                'name' => 'Мы правы'
                                                //, 'email' => 'mail@mypravy.ru'
                                                , 'email' => 'pravu@uralweb.info'
                                            );

                                            $to = array(
                                                'name' => $indb['family'] . ' ' . $indb['name']
                                                , 'email' => $indb['mail']
                                            );

                                            // шаблон для письма (лежат в папке /template-mail/* )
                                            // Nyos\backword::sendMailSendpulse( $SPApiClient, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
                                            /*
                                              Nyos\backword::sendMailSendpulse(
                                              $vv['now_level']['sendpulse_id'], $vv['now_level']['sendpulse_secret'],
                                              $from, $to,
                                              $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
                                             */
                                            $vars['sp_id'] = isset($vv['now_level']['sendpulse_id']{1}) ? $vv['now_level']['sendpulse_id'] : null;
                                            $vars['sp_secret'] = isset($vv['now_level']['sendpulse_secret']{1}) ? $vv['now_level']['sendpulse_secret'] : null;

                                            Nyos\backword::sendMailSuper($from, $to, $vv['now_level']['reg']['mail_tpl'], $vv['now_level']['mail_company_name'] . ': Подтверждение регистрации', $vars);
                                            // echo $status;
                                            //echo '<br/>'.__FILE__.'['.__LINE__.']';
                                        }

                                        // echo '<br/>'.__FILE__.'['.__LINE__.']';
                                        // die();
// если обязательно нужна активация майла
                                        if ($vv['now_level']['activaciya_mail'] == 'da') {
                                            //$_SESSION['start_msg'] = 'Необходимо подтвердить регистрацию, ссылка отправлена на email';
                                        }
// если НЕ обязательно нужна активация майла
                                        else {

                                            $_SESSION['now_user']['data'] = $indb;
                                            $_SESSION['now_user']['id'] = $id_new;
                                        }
                                    } else {
                                        $vv['warn'] .= '<br/>Осуществлена регистрация с помощью соц. сервиса';
                                        $_SESSION['now_user']['data'] = $indb;
                                        $_SESSION['now_user']['id'] = $id_new;
                                    }


                                    if (isset($_COOKIE['show_page']{5})) {
                                        $_SESSION['start_msg'] = 'Регистрация проведена успешно.';
                                        header('Location: ' . $_COOKIE['show_page']);
                                        exit;
                                    }
                                }
                            }
                        }
                    }

                    // вход по логину и паролю
                    elseif (isset($_POST['type']) && $_POST['type'] == 'enter_form') {

                        $enter = Nyos\mod\lk::enter($db, $vv['folder'], $_POST['mail_enter'], $_POST['pass_enter'], ( $vv['now_level']['activaciya_mail'] == 'da' ) ? true : false);

                        //f\pa($enter);
                        // вход не удался
                        if ($enter === false) {
                            $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . 'Логин или пароль указаны неверно';
                        }

                        // вход не удался, есть описание
                        elseif (isset($enter['status']) && $enter['status'] == 'error' && isset($enter['html']{1})) {
                            $vv['warn'] .= ( isset($vv['warn']{1}) ? '<br/>' : '' ) . $enter['html'];
                        }

                        // вход удался
                        else {
                            if (isset($_COOKIE['show_page']{5})) {
                                header('Location: ' . $_COOKIE['show_page']);
                                exit;
                            }
                        }
                    }
                } else {
                    $vv['warn'] .= 'Извините, но похоже вы робот. Просим вас повторить вход<br/>';
                }
            } else {
                // $vv['warn'] .= 'Вы не прошли валидацию reCaptcha';
            }
        }
    }


    if (file_exists($dir_mod_site . 'body.htm')) {
        $vv['tpl_0body'] = $dir_mod_site . 'body.htm';
    } else {
        $vv['tpl_0body'] = $dir_mod . 'body.htm';
    }

// если вход выполнен
    if (isset($_SESSION['now_user']['id']{0})) {


        if (file_exists($dir_mod_site . 'body_noenter.htm')) {
            $vv['tpl_body'] = $dir_mod_site . 'body_lk.htm';
        } else {
            $vv['tpl_body'] = $dir_mod . 'body_lk.htm';
        }
    }
// если вход не выполнен
    else {

        if (file_exists($dir_mod_site . 'body_noenter.htm')) {
            $vv['tpl_body'] = $dir_mod_site . 'body_noenter.htm';
        } else {
            $vv['tpl_body'] = $dir_mod . 'body_noenter.htm';
        }
    }
}


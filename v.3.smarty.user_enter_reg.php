<?php

if (!defined('IN_NYOS_PROJECT'))
    die('Сработала защита <b>функций MySQL</b> от злостных розовых хакеров.' .
            '<br>Приготовтесь к DOS атаке (6 поколения на ip-' . $_SERVER["REMOTE_ADDR"] . ') в течении 30 минут... .');


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

// [option] => didrive
if (isset($_REQUEST['option']) && $_REQUEST['option'] == 'didrive') {
    $indb['folder'] = $vv['folder'];
}

$dir_for_ava = $_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $vv['folder'] . '/download/avatars';

if (!is_dir($dir_for_ava))
    mkdir($dir_for_ava, 0755);



require_once $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/file.2.php';






//
if (isset($user['uid']{3}) && isset($user['network']) && $user['network'] == 'facebook') {

    // http://graph.facebook.com/user_id/picture?type=large
    $indb['avatar2'] = $user['uid'];

    //copy( 'http://graph.facebook.com/'.$user['uid'].'/picture?type=large' , $dir_for_ava . '/' . $indb['avatar']);
    $avat = file_get_contents('http://graph.facebook.com/' . $user['uid'] . '/picture?type=large');
    file_put_contents($dir_for_ava . '/' . $indb['avatar2'], $avat);

    $new_ext = f\readTypeImage($dir_for_ava . '/' . $indb['avatar2']);

    if ($new_ext !== false) {
        rename($dir_for_ava . '/' . $indb['avatar2'], $dir_for_ava . '/' . $indb['avatar2'] . '.' . $new_ext);
        $indb['avatar'] = $indb['avatar2'] . '.' . $new_ext;
    }

    if (file_exists($dir_for_ava . '/' . $indb['avatar'])) {
        // $indb['avatar'] = $indb['avatar2'];
    }
}
//
elseif (isset($user['uid']{3}) && isset($user['network']) && $user['network'] == 'vkontakte') {

    $ee = json_decode(file_get_contents('http://api.vk.com/method/users.get?user_ids=' . $user['uid'] . '&fields=photo_200'), true);

    // f\pa($ee);
    if (isset($ee['response'][0]['photo_200']{5})) {

        $indb['avatar2'] = $user['uid'] . '.' . f\get_file_ext($ee['response'][0]['photo_200']);

        if (!is_dir($dir_for_ava . '/'))
            mkdir($dir_for_ava, 0755);

        //copy($ee['response'][0]['photo_200'], $dir_for_ava . '/' . $indb['avatar']);
        $avat = file_get_contents($ee['response'][0]['photo_200']);
        file_put_contents($dir_for_ava . '/' . $indb['avatar2'], $avat);

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

if (isset($indb['mail']{1})) {
    $sql11 = $db->sql_query('SELECT * FROM gm_user '
            . ' WHERE '
            . ' `folder` = \'' . $vv['folder'] . '\' '
            . ' AND `mail` = \'' . addslashes($indb['mail']) . '\' '
            . ' LIMIT 1 ; ');
}

if (isset($indb['mail']{1}) && $db->sql_numrows($sql11) == 1) {

    $vv['warn'] .= 'Данный e-mail уже используется, укажите другой';
} else {

    $_SESSION['now_user'] = array();

    $id_new = f\db\db2_insert($db, 'gm_user', $indb, 'no', 'last_id');
    $indb['id'] = $id_new;

    // echo $status;
    // die(f\pa($id_new));
    //f\pa($_POST);

    if (isset($id_new{0}) && is_numeric($id_new)) {

        if (isset($_POST['type']) && $_POST['type'] == 'reg_form') {
            $vv['warn'] .= 'Регистрация проведена успешно';
        } else {
            $vv['warn'] .= 'Осуществлена регистрация с помощью соц. сервиса';
        }

        $_SESSION['now_user'] = $indb;
        // $_SESSION['now_user']['data'] = $indb;
        // $_SESSION['now_user']['id'] = $id_new;
/*
        if (isset($_REQUEST['goto']{1})) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_REQUEST['goto']);
            exit;
        }
        //
        elseif (isset($vv['now_level']['goto_after_enter']{0})) {
            header('Location: ' . $vv['now_level']['goto_after_enter']);
            exit;
        }
        //
        else if (isset($_COOKIE['show_page']{5})) {
            header('Location: ' . $_COOKIE['show_page']);
            exit;
        }
  */
        
    }
}
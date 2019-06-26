<?php

//f\pa($vv['now_mod']);
// \f\pa($_POST);
// echo '<br/>'.__FILE__.' '.__LINE__;
// \f\pa($_SESSION,2);

/**
 * входа не было ещё
 */
if (!isset($_SESSION['now_user']['id'])) {

    /**
     * вход по соц сервису
     */
    if (isset($_POST['token']{10}) && !isset($_SESSION['now_user']['id'])) {

        //echo '<br/>' . __FILE__ . ' ' . __LINE__;

        $_SESSION['now_user'] = \Nyos\Mod\Lk::enterSoc($db, $vv['folder'], $_POST['token']);
        //\f\pa($_SESSION['now_user']);

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


        /**
         * если есть модуль отправки сообщений в телегу, то шлём
         */
        if (class_exists('\\nyos\\Msg')) {

            $e = '';
            foreach ($_SESSION['now_user'] as $k => $v) {
                if (isset($v{0}))
                    $e .= $k . ': ' . $v . PHP_EOL;
            }

            \nyos\Msg::sendTelegramm('Вход на сайт' . PHP_EOL . PHP_EOL . $e, null, 1);

            if (isset($vv['info_send_telegram']['enter_on_site'])) {
                foreach ($vv['info_send_telegram']['enter_on_site'] as $k => $v) {
                    \nyos\Msg::sendTelegramm('Вход на сайт' . PHP_EOL . PHP_EOL . $e, $v);
                    //\Nyos\NyosMsg::sendTelegramm('Вход в управление ' . PHP_EOL . PHP_EOL . $e, $k );
                }
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
        //
        else {

            die('<html><head><meta http-equiv="refresh" content="0;http://' . $_SERVER['HTTP_HOST'] . '/' . $vv['now_level']['cfg.level'] . '/"></head>' .
                    '<body><br/><br/><br/><center>секунду пожалуйста</body></html>');

            header('Location: http://', $_SERVER['HTTP_HOST'], '/', $vv['now_level']['cfg.level'], '/');
            exit;
        }
    }
}




/**
 * вход был
 */
if (isset($_SESSION['now_user']['id'])) {

    /**
     * выход из аккаунта
     */
    if (
            ( isset($vv['now_level']['type2']) && $vv['now_level']['type2'] == 'exit' ) ||
            (isset($_REQUEST['option']) && $_REQUEST['option'] == 'exit')
    ) {

        $_SESSION['now_user'] = null;

        if (isset($vv['now_level']['head_level']{1})) {
            header('Location:http://' . $_SERVER['HTTP_HOST'] . '/' . $vv['now_level']['head_level'] . '/');
        } else {
            header('Location:http://' . $_SERVER['HTTP_HOST'] . '/');
        }
        exit;
    }
}



/*
if (isset($vv['now_mod']['no_cats']{1})) {
    $vv['tpl_0body'] = \f\like_tpl('sh-no.cat', $vv['dir_module_tpl'], $vv['dir_site_tpl']);
} else {
    $vv['tpl_0body'] = \f\like_tpl('sh', $vv['dir_module_tpl'], $vv['dir_site_tpl']);
}
 * 
 */
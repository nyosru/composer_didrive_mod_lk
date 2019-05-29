<?php

// \f\pa($_POST);

if (isset($_GET['user']) && is_numeric($_GET['user']) && isset($_GET['access']) && $_GET['access'] == 'success') {

    //    $_SESSION['status1'] = true;
    //    $status = '';
    $db->sql_query('UPDATE gm_user SET `admin_status` = \'access\' WHERE `id` = \'' . $_GET['user'] . '\' AND folder = \'' . $vv['folder'] . '\' ;');
    //echo $status;

    $e = $_GET;
    $e['user'] = '';
    $e['access'] = '';
    $e['rand'] = rand();
    \f\redirect('/', 'i.didrive.php', $e);
} elseif (isset($_GET['user']) && is_numeric($_GET['user']) && isset($_GET['access']) && $_GET['access'] == 'block') {

    $db->sql_query('UPDATE gm_user SET `admin_status` = \'block\' WHERE `id` = \'' . $_GET['user'] . '\' AND folder = \'' . $vv['folder'] . '\' ;');

    $e = $_GET;
    $e['user'] = '';
    $e['access'] = '';
    $e['rand'] = rand();
    \f\redirect('/', 'i.didrive.php', $e);
}

//\f\pa($now_mod);
//\f\pa($now_level);
//\f\pa($vv['now_mod_raw']);
//\f\pa($vv['now_mod']);

require_once dir_serv_mod . 'class.php';


// если раздел верификации
if (isset($vv['now_mod']['type2']) && $vv['now_mod']['type2'] == 'creat_moder') {

    //\f\pa($_POST);
    // удаляем запись модера
    if (isset($_GET['delete']) && is_numeric($_GET['delete']) && isset($_GET['s']) && $_GET['s'] == md5('x' . $_GET['delete'])) {

        $db->sql_query('UPDATE `gm_user` 
            SET 
                `status` = \'delete\'
            WHERE 
                `id` = \'' . $_GET['delete'] . '\' AND `folder` = \'' . $vv['folder'] . '_di\' ; '
        );
    }
    // создаём модератора
    elseif (isset($_POST['creat_moder']) && $_POST['creat_moder'] == 'y') {

//         $_SESSION['status1'] = true;
//         $status = '';
//        $db->sql_query('DELETE FROM gm_user WHERE 
//            ( `login` = \''.$_POST['email'].'\' OR `mail` = \''.$_POST['email'].'\' )
//            AND `folder` = \''.$vv['folder'].'_di\' ;');
        $db->sql_query('UPDATE `gm_user` 
            SET 
            `status` = \'delete\'
            WHERE 
            ( `login` = \'' . $_POST['email'] . '\' OR `mail` = \'' . $_POST['email'] . '\' )
            AND `folder` = \'' . $vv['folder'] . '_di\' ; '
        );
        //echo $status;

        $new_id = \f\db\db2_insert($db, 'gm_user', array(
            'access' => 'moder',
            'login' => $_POST['email'],
            'pass5' => md5($_POST['pass']),
            'folder' => $vv['folder'] . '_di',
            'mail' => $_POST['email'],
            'name' => $_POST['name'],
            'dt' => 'NOW'
                ), true, 'last_id');

//            $indb = [];
//            foreach ($_POST['access'] as $k => $v) {
//                $indb[] = array('module' => $k);
//            }


        $indb = [];
        foreach ($vv['modules'] as $k => $v) {
            $indb[] = array('module' => $k);
        }


        if (isset($_POST['access'])) {
            
        }

        \f\db\sql_insert_mnogo2($db, 'gm_user_access', $indb, array(
            'folder' => $vv['folder'],
            'access' => 'moder',
            'user' => $new_id,
            'var1' => 'y',
            'd' => 'NOW',
            't' => 'NOW'
                ), true);


        \f\pa($indb, 2, null, '$indb ' . __LINE__);

        $indb = [];
        foreach ($vv['modules'] as $k => $v) {
            $indb[] = array('module' => $k);
        }
        //\f\pa($vv['modules']);

        \f\db\sql_insert_mnogo2($db, 'gm_user_di_mod', $indb, array(
            'folder' => $vv['folder'],
            //'status' => 'yes',
            'user_id' => $new_id,
            'mode' => 'didrive'
                ), true);

        \f\pa($indb, 2, null, '$indb ' . __LINE__);





        /*
          //$status = '';
          $res = \f\db\getSql($db, 'SELECT

          u.id user
          ,m.module
          ,m.status
          ,m.mode

          FROM gm_user_di_mod m

          INNER JOIN gm_user u ON u.folder = \'' . addslashes($folder) . '_di\' AND u.id = m.user_id

          WHERE
          m.folder = \'' . addslashes($folder) . '\'
         */
    }

// \f\pa($vv['modules']);

    $vv['access_list'] = ['notification', 'verification', 'payments'];
    $vv['list_moder'] = \Nyos\mod\lk::getUsers($db, $vv['folder'] . '_di');
    $vv['tpl_body'] = didr_tpl . 'body.creat_moder.htm';
    
} elseif (isset($vv['now_mod']['type2']) && $vv['now_mod']['type2'] == 'verific') {

//[comment] => 6666666 77777777777
//[return] => Вернуть на доработку
//[act] => return
//[id] => 193
//[s] => cb9c9820a92e67b458a3d43654a4ef36
    // возвращаем на доработку анкету верификации
    if (isset($_POST['act']) && $_POST['act'] == 'return') {

        if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['s']) && $_POST['s'] == md5('s' . $_POST['id'])) {

            $db->sql_query('UPDATE gm_user SET admin_status = \'return\' WHERE id = \'' . $_POST['id'] . '\' ;');
            $db->sql_query('DELETE FROM gm_user_option WHERE user = \'' . $_POST['id'] . '\' AND `option` = \'etap_last\' ;');

            require_once $_SERVER['DOCUMENT_ROOT'] . '/0.site/exe/shop2/class.php';
            \Nyos\mod\shop2::addNotification($db, 'verification', nl2br($_POST['comment']), $_POST['id']);

            \f\redirect('/', 'i.didrive.php', array('level' => $_GET['level'], 'rand' => rand()));
            exit;
        }
        //
        else {

            $vv['warn'] = 'Что то пошло не так #' . __LINE__;
        }
    }

    $vv['tpl_body'] = dir_serv_mod_didr_tpl . 'body.verific.htm';
} else {

    $vv['tpl_body'] = dir_serv_mod_didr_tpl . 'body.htm';
}







if (1 == 2) {



// f\pa($vv['now_mod']);
// f\pa($vv);
// добавление новости
    if (isset($_POST['save']) && $_POST['save'] == 'Сохранить') {

        $in = $_POST;

        if (isset($in['id']))
            unset($in['id']);

        if (isset($in['publick']))
            unset($in['publick']);

        if (isset($in['status']))
            unset($in['status']);

        if (isset($in['import']))
            unset($in['import']);

        if (isset($in['check']))
            unset($in['check']);

        if (isset($_FILES['img_mini'])) {
            if (isset($_FILES['img_mini']['size']) && $_FILES['img_mini']['size'] > 100 && $_FILES['img_mini']['error'] == 0) {

                $img_dir = $_SERVER['DOCUMENT_ROOT'] . $vv['sd'] . 'news' . DS;

                if (!is_dir($img_dir))
                    mkdir($img_dir, 0755);

                $in['img_mini'] = f\newfile($img_dir, f\translit($vv['level'], 'uri2') . '..' . substr(f\translit($_FILES['img_mini']['name'], 'uri2'), 0, 50) . '.' . f\get_file_ext($_FILES['img_mini']['name']));
                copy($_FILES['img_mini']['tmp_name'], $img_dir . $in['img_mini']);
            }
        }

        if (isset($_FILES['img'])) {
            if (isset($_FILES['img']['size']) && $_FILES['img']['size'] > 100 && $_FILES['img']['error'] == 0) {

                $img_dir = $_SERVER['DOCUMENT_ROOT'] . $vv['sd'] . 'news' . DS;

                if (!is_dir($img_dir))
                    mkdir($img_dir, 0755);

                $in['img'] = f\newfile($img_dir, f\translit($vv['level'], 'uri2') . '..' . substr(f\translit($_FILES['img']['name'], 'uri2'), 0, 50) . '.' . f\get_file_ext($_FILES['img']['name']));
                copy($_FILES['img']['tmp_name'], $img_dir . $in['img']);
            }
        }

        $in['folder'] = $now['folder'];
        $in['modul'] = $vv['level'];
        $in['date'] = $in['time'] = 'NOW';

        $vv['warn'] .= '<div class="warn" >Записал, показал (<a href="/' . $vv['level'] . '/" target="_blank" >страница на сайте</a>)</div>';
        db2_insert($db, 'mod_news_text', $in, 'da');

        Nyos\news::clearCash($now['folder']);
    }

// удаление новости
    elseif (isset($_REQUEST['delnews']) && is_numeric($_REQUEST['delnews'])) {
        // db_edit( 'mod_news_text', 'id', $_REQUEST['delnews'], array( 'status' => 'del.us' ) );
        $db->sql_query('UPDATE `mod_news_text` 
        SET 
            `status` = \'del.us\'
            ,`lastedit_date` = NOW( )
            ,`lastedit_time` = NOW( )
        WHERE 
            `folder` = \'' . $now['folder'] . '\'
            AND `modul` = \'' . $vv['level'] . '\'
            AND `id` = \'' . $_REQUEST['delnews'] . '\'
        LIMIT 1 ;');
        $vv['warn'] .= '<div class="warn" >новость удалена (<a href="/' . $vv['level'] . '/" target="_blank" >страница на сайте</a>)</div>';
        Nyos\news::clearCash($now['folder']);
    }

// сохранение изменений новости
    elseif (isset($_REQUEST['editnews']) && is_numeric($_REQUEST['editnews'])) {

        //$status = '';
        $MS3e = $db->sql_query('SELECT *
        FROM
            `mod_news_text`
        WHERE
            `folder` = \'' . $now['folder'] . '\' 
            AND 
                `modul`=\'' . $vv['level'] . '\'
            AND 
                `id`=\'' . $_REQUEST['editnews'] . '\'
        LIMIT 1 ;');
        //echo $status;

        if ($db->sql_numrows($MS3e) > 0) {
            $ro3e = $db->sql_fetchrow($MS3e);

            //echo '<pre>'; print_r($ro3e); echo '</pre>';

            $vv['editnews'] = $ro3e;
        }
    }
// сохранение изменений новости
    elseif ($_POST['save_edit'] == 'Сохранить' && isset($_POST['id']) && is_numeric($_POST['id'])
    ) {


        //$status = '';
        $MS3e = $db->sql_query('SELECT id
        FROM
            `mod_news_text`
        WHERE
            `folder` = \'' . $now['folder'] . '\' 
            AND 
                `modul`=\'' . $vv['level'] . '\'
            AND 
                `id`=\'' . $_POST['id'] . '\'
        LIMIT 1 ;');
        //echo $status;

        if ($db->sql_numrows($MS3e) > 0) {
            $vv['warn'] .= '<div class="warn" >Сохранил изменения, показываю новую новость (<a href="/' . $vv['level'] . '/" target="_blank" >страница на сайте</a>)</div>';

            $v = $_POST;

            if (isset($v['id']))
                unset($v['id']);

            if (isset($v['folder']))
                unset($v['folder']);
            if (isset($v['modul']))
                unset($v['modul']);
            if (isset($v['time publick']))
                unset($v['time publick']);
            if (isset($v['status']))
                unset($v['status']);
            if (isset($v['import']))
                unset($v['import']);
            if (isset($v['check']))
                unset($v['check']);

            $v['lastedit_date'] = $v['lastedit_time'] = 'NOW';

            // $status = '';
            db_edit('mod_news_text', 'id', $_POST['id'], $v, 1);
            // echo $status;
        }
    }

    $dirmod = $_SERVER['DOCUMENT_ROOT'] . DS . '9.site' . DS . $now['folder'] . DS . 'module' . DS . $vv['level'] . DS . 'tpl' . DS;
    /*
      if( isset( $_POST['editor'] ) )
      {
      $vv['warn'] .= '<div class="warn" >Данные записаны (<a href="/'.$vv['level'].'/" target="_blank" >страница на сайте</a>)</div>';
      file_put_contents( $dirmod.'page.txt.data.htm', $_POST['editor'] );
      }
     */

    $vv['sys']['ckeditor'] = 112;
    $vv['sys']['ckeditor_in'][] = array('one' => 'editor1', 'dop' => " { toolbar: [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Maximize', 'ShowBlocks', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
	{ name: 'links', items: [ 'Link', 'Unlink' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] }
    ]
    }");
    $vv['sys']['ckeditor_in'][] = array('one' => 'editor2', 'dop' => " { toolbar: [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Maximize', 'ShowBlocks', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
	{ name: 'links', items: [ 'Link', 'Unlink' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] }
    ]
    }");
    $vv['sys']['ckeditor_in'][] = 'editor01';
    $vv['sys']['ckeditor_in'][] = 'editor02';

// $vv['html'] = ( file_exists( $dirmod.'page.txt.data.htm' ) ) ? file_get_contents( $dirmod.'page.txt.data.htm' ) : 'файл данных не обнаружен, записывайте новый' ;
// $status = '';
    $MS3 = $db->sql_query('SELECT *
    FROM `mod_news_text`
    WHERE
        `folder` = \'' . $now['folder'] . '\' 
        AND `modul`=\'' . $vv['level'] . '\'
        AND 
            ( 
            `status` = \'look\'
            OR `status` = \'mod\'
            )
    ORDER BY 
        `date` DESC,
        `time` DESC;');
// echo $status;

    if ($db->sql_numrows($MS3) > 0) {
        while ($ro3 = $db->sql_fetchrow($MS3)) {
            $vv['newss'][] = $ro3;
        }
    }
}
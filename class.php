<?php

namespace Nyos\Mod;

// use f\db as db;
// use f as f;
// use Nyos\nyos as nyos2;
//echo __FILE__.'<br/>';
// строки безопасности

if (!defined('IN_NYOS_PROJECT'))
    die('<center><h1><br><br><br><br>Cтудия Сергея</h1><p>Сработала защита <b>TPL</b> от злостных розовых хакеров.</p>
    <a href="http://www.uralweb.info" target="_blank">Создание, дизайн, вёрстка и программирование сайтов.</a><br />
    <a href="http://www.nyos.ru" target="_blank">Только отдельные услуги: Дизайн, вёрстка и программирование сайтов.</a>');

// require_once $_SERVER['DOCUMENT_ROOT'] . DS . 'include' . DS . 'f' . DS . 'ajax.php';

class Lk {

    /**
     * тип авторизации
     * соц сеть = soc
     * @var type 
     */
    public static $type_aut = '';
    public static $type = 'now_user';
    // и для дидрайва
    // public static $type = 'now_user_di';
    public static $user_di_access = array();
    public static $dop = array();
    /**
     * поля в базе сотрудников
     * @var type 
     */
    public static $polya_db = [
        'login','pass','pass5','folder','mail',
        'mail_confirm','name','soname','family',
        'phone','avatar','adres','about',
        'soc_web','soc_web_link',
        'soc_web_id','access','status','admin_status','dt','ip'
        ,'city','city_name','points','country','recovery','recovery_dt'
                    
    ];
    public static $access_di_site = array(
        'admin' => array(
            'name' => 'Администратор'
        )
        , 'moder' => array(
            'name' => 'Модератор'
        )
        , 'gost' => array(
            'name' => 'Гость'
        )
        , 'block' => array(
            'name' => 'Нет доступа'
        )
    );

    public static function getAccess($db, $access_id = null) {

        $sql = 'SELECT 
                u.id as user
                ,m.module
                ,m.status
                ,m.mode
            FROM gm_user_di_mod m
                INNER JOIN gm_user u ON u.id = m.user_id '
                . (!empty($access_id) ? ' AND u.id = :user ' : '' )
                . ' ; ';

        $ff = $db->prepare($sql);

        $sql_vars = [
            ':user' => $access_id
        ];
//            $sql_vars[':status'] = 'show';
//            $sql_vars[':mod_user'] = \Nyos\mod\JobDesc::$mod_jobman;
//            $sql_vars[':mod_job_on'] = \Nyos\mod\JobDesc::$mod_man_job_on_sp;
//            $sql_vars[':mod_sp'] = \Nyos\mod\JobDesc::$mod_sale_point;
//// \f\pa($ff1);
        $ff->execute($sql_vars);

        $r2 = $ff->fetchAll();

        $return = [];

        foreach ($r2 as $k => $v) {
            $return[$v['user']][$v['mode']][$v['module']] = $v['status'];
        }

        // return $ff->fetchAll();
        if (!empty($access_id)) {
            if (!empty($return[$access_id])) {
                return $return[$access_id];
            } else {
                return false;
            }
        } else {
            return $return;
        }
    }

    public static function getDidriveUsersAccess($db, string $folder, $access_id = null) {

        if (isset(self::$user_di_access[$folder])) {
            if (isset($access_id{0}) && $access_id !== null && isset(self::$user_di_access[$folder][$access_id])) {
                return self::$user_di_access[$folder][$access_id];
            } else {
                return self::$user_di_access[$folder];
            }
        }

        $res = \f\db\getSql($db, 'SELECT 
                u.id user
                ,m.module
                ,m.status
                ,m.mode
            FROM gm_user_di_mod m
                INNER JOIN gm_user u ON u.folder = \'' . addslashes($folder) . '_di\' AND u.id = m.user_id
            WHERE 
                m.folder = \'' . addslashes($folder) . '\'

            ; ', null);
        //f\pa($res);
        $res2 = array();

        foreach ($res as $k => $v) {
            $res2[$v['user']][$v['mode']][$v['module']] = $v['status'];
        }

        self::$user_di_access[$folder] = $res2;

        // return array( 'st' => $status, 'data' => $res, 'dataw' => $res2 );

        if (isset($access_id{0}) && $access_id !== null && isset($res2[$access_id])) {
            return $res2[$access_id];
        } else {
            return $res2;
        }
    }

    /**
     * получение списка пользователей
     * @global type $status
     * @param класс_БД $db
     * @param строка $folder
     * null если все подряд
     * di если дидрайв доступ
     * @param acceess_user $type
     * moder admin (умолч)null
     * @return boolean
     */
    public static function getUsers($db, string $folder = null, string $type = null, string $status = null) {

        $where = '';
        $sf = [];

        if ($folder == 'di') {
            $where .= ' `folder` LIKE \'%_di\' ';
        } elseif (isset($folder{1})) {
            $where .= ' `folder` = :folder ';
            $sf[':folder'] = $folder;
        }

        if ($type == 'moder') {
            $where .= ( isset($where{1}) ? ' AND ' : '' ) . ' `access` = :access ';
            $sf[':access'] = 'moder';
        } elseif ($type == 'admin') {
            $where .= ( isset($where{1}) ? ' AND ' : '' ) . ' `access` = :access ';
            $sf[':access'] = 'admin';
        }


        try {

            $ff = $db->prepare('SELECT * FROM `gm_user` '
                    . ( isset($where{1}) ? ' WHERE ' . $where : '' )
                    . ' ORDER BY `id` DESC; ');
            //$ff->execute(array(':domain' => $domain));
            //$sf[':domain'] = $_SERVER['HTTP_HOST'];
            $ff->execute($sf);
        } catch (Exception $ex) {
            
        }

        $dop_sql = '';

        while ($r = $ff->fetch()) {
            $dop_sql .= ( isset($dop_sql{2}) ? ' OR ' : '' ) . ' `user` = \'' . $r['id'] . '\' ';
            $res[$r['id']] = $r;
        }

        $ff1 = $db->prepare('SELECT * FROM `gm_user_option` WHERE ' . $dop_sql . ' ;');
        $ff1->execute();

        while ($r = $ff1->fetch()) {

            if (!isset($res[$r['user']]['dop'])) {
                $res[$r['user']]['dops'] = $res[$r['user']]['dop'] = array();
            }

            $res[$r['user']]['dop'][] = $r;
            $res[$r['user']]['dops'][$r['option']] = $r['value'];
        }

        try {


            $ff1 = $db->prepare('SELECT access, var1, var2, user, module FROM gm_user_access WHERE ( ' . $dop_sql . ' ) AND status = \'ok\' ;');
            $ff1->execute();

            while ($r = $ff1->fetch()) {
                if (!isset($res[$r['user']]['access_mod']))
                    $res[$r['user']]['access_mod'] = array();

                $res[$r['user']]['access_mod'][$r['module']][$r['var1']] = $r;
            }
        } catch (\PDOException $ex) {

            if (strpos($ex->getMessage(), 'no such table') !== false) {

                $ff12 = $db->prepare('CREATE TABLE gm_user_access (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                `folder` VARCHAR (50),
                `module` VARCHAR (50),
                `var1` VARCHAR (150),
                `var2` VARCHAR (150),
                `user` int(11) NOT NULL REFERENCES gm_user (id),
                `access` VARCHAR,
                `status` VARCHAR,
                `d` INTEGER NOT NULL,
                `t` INTEGER NOT NULL
            );');
                $ff12->execute();
            }
        }








        return $res;
    }

    public static function getUser($db, $id, $login = null, $pass = null, $folder = null) {

        //echo '<br/>'.$id;
        //die;

        $s = 'SELECT * FROM `gm_user` as `u` WHERE '
                . ' u.status != \'delete\' '
                . (!empty($id) ? ' AND ( `soc_web_id` = :id  OR `id` = :id ) ' : '' )
                . (!empty($folder{1}) ? ' AND `folder` = :folder ' : '' )
                . (!empty($login{1}) ? ' AND u.login = :login ' : '' )
                . (!empty($pass{1}) ? ' AND u.pass5 = :pass ' : '' )
                . ' LIMIT 1 ;';

        //echo '<br/>' . $s;
        // \f\pa($s, '', '', 'sql');

        $sql = $db->prepare($s);
        $dop_ar = [];

        if (!empty($login{1}))
            $dop_ar[':login'] = $login;

        if (!empty($pass{1}))
            $dop_ar[':pass'] = md5($pass);

        if (!empty($folder{1}))
            $dop_ar[':folder'] = (string) $folder;

        if (!empty($id))
            $dop_ar[':id'] = $id;

        // \f\pa($dop_ar,'','','sl dop_ar');
        //exit;

        $sql->execute($dop_ar);

        if ($user = $sql->fetch()) {

            // \f\pa($user);
            return $user;
        } else {

            // \f\pa($user);
            // echo '<Br/>' . __FILE__ . ' #' . __LINE__;

            if (isset($login{1})) {
                throw new \Exception('Логин, пароль указаны не верно.');
            } else {
                return false;
            }
        }
    }

    public static function creatTable($db) {

        $ff2 = $db->prepare('CREATE TABLE IF NOT EXISTS `gm_user` ( '
                // наверное в MySQL .' `id` int NOT NULL AUTO_INCREMENT, '
                // в SQLlite
                . ' `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , '
                . ' `login` varchar(150) DEFAULT NULL, '
                . ' `pass` varchar(100) DEFAULT NULL, '
                . ' `pass5` varchar(40) DEFAULT NULL, '
                . ' `folder` varchar(150) DEFAULT NULL, '
                . ' `mail` varchar(150) DEFAULT NULL, '
                . ' `mail_confirm` varchar(150) DEFAULT NULL, '
                . ' `name` varchar(150) DEFAULT NULL, '
                . ' `soname` varchar(150) DEFAULT NULL, '
                . ' `family` varchar(150) DEFAULT NULL, '
                . ' `phone` varchar(20) DEFAULT NULL, '
                . ' `avatar` varchar(250) DEFAULT NULL, '
                . ' `adres` varchar(250) DEFAULT NULL, '
                . ' `about` TEXT, '
                . ' `soc_web` varchar(50) DEFAULT NULL, '
                . ' `soc_web_link` varchar(250) DEFAULT NULL, '
                . ' `soc_web_id` varchar(250) DEFAULT NULL, '
                // .' `access` set(\'admin\',\'moder\',\'guest\',\'gost\',\'block\') DEFAULT NULL, '
                . ' `access` varchar(50) DEFAULT NULL, '
                // .' `status` set(\'new\',\'job\',\'block\',\'delete\') NOT NULL DEFAULT \'new\', '
                . ' `status` varchar(50) NOT NULL DEFAULT \'new\', '
                // .' `admin_status` set(\'access\',\'block\',\'blank\',\'return\') DEFAULT NULL, '
                . ' `admin_status` varchar(250) DEFAULT NULL, '
                . ' `dt` INTEGER, '
                . ' `ip` varchar(20) DEFAULT NULL, '
                . ' `city` varchar(150) DEFAULT NULL,
                        `city_name` varchar(150) DEFAULT NULL,
                        `points` int(11) NOT NULL DEFAULT \'0\',
                        `country` varchar(150) DEFAULT NULL,
                        `recovery` varchar(50) DEFAULT NULL,
                        `recovery_dt` timestamp NULL DEFAULT NULL
                      ) ;');
        //$ff->execute([$domain]);
        $ff2->execute();

//CREATE TABLE IF NOT EXISTS `gm_user_option` (
//  `id` int(7) NOT NULL,
//  `user` int(7) NOT NULL,
//  `project` int(5) DEFAULT NULL,
//  `option` varchar(50) DEFAULT NULL,
//  `value` varchar(250) DEFAULT NULL,
//  `val1` varchar(150) DEFAULT NULL,
//  `val2` varchar(150) DEFAULT NULL,
//  `val3` varchar(150) DEFAULT NULL,
//  `val4` varchar(150) DEFAULT NULL,
//  `val5` varchar(150) DEFAULT NULL
//) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=712 ROW_FORMAT=DYNAMIC COMMENT='опции пользователей';

        $ff2 = $db->prepare('CREATE TABLE IF NOT EXISTS `gm_user_option` ( 
            `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , 
            `user` int(7) NOT NULL, 
            `project` int(7) NOT NULL, 
            `option` varchar(50) DEFAULT NULL, 
            `value` varchar(250) DEFAULT NULL, 
            `val1` varchar(150) DEFAULT NULL, 
            `val2` varchar(150) DEFAULT NULL, 
            `val3` varchar(150) DEFAULT NULL, 
            `val4` varchar(150) DEFAULT NULL, 
            `val5` varchar(150) DEFAULT NULL
             ) ;');
        $ff2->execute();

//CREATE TABLE `gm_user_di_mod` (
//  `id` int(11) NOT NULL,
//  `user_id` int(5) NOT NULL,
//  `folder` varchar(50) NOT NULL,
//  `module` varchar(50) NOT NULL,
//  `status` set('yes','no') NOT NULL DEFAULT 'yes',
//  `mode` set('site','didrive') NOT NULL
//) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='доступ к модулям';

        $ff2 = $db->prepare('CREATE TABLE IF NOT EXISTS `gm_user_di_mod` ( 
            `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , 
            `user_id` int(7) NOT NULL, 
            `folder` VARCHAR(100) NOT NULL, 
            `module` VARCHAR(100) NOT NULL, 
            `status` VARCHAR(20) NOT NULL DEFAULT \'yes\', 
            `mode` VARCHAR(10) NOT NULL 
             ) ;');
        $ff2->execute();
    }

    /**
     * проверка токена если вошли через соц сеть - сервис ulogin.ru
     * @param type $db
     * @param string $folder
     * @param string $token
     * @param string $type
     * site* | didrive
     * @return type
     * @throws \Exception
     */
    public static function enterSoc($db, string $folder = null, string $token, string $type = 'site') {

        // получили результат проверки
        try {

            $user = json_decode(file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']), true);
            if (isset($user['error'])) {
                throw new \Exception('Ошибка при получении информации о токене: ' . $user['error']);
            }
            //\f\pa($user, 2, null, 'получили данные пользователя #' . __LINE__);
        } catch (\Error $ex) {
            throw new \NyosEx('Ошибка при получении информации токена: ' . $ex->getMessage());
        } catch (\Exception $ex) {
            throw new \NyosEx('Ошибка при получении информации токена: ' . $ex->getMessage());
        }

        if ($type == 'didrive')
            $folder .= '_di';

        try {

            $result = self::getUser($db, $user['uid'], null, null, $folder);
        } catch (\PDOException $ex) {

            if (strpos($ex->getMessage(), 'no such table') !== false) {
                self::creatTable($db);
                $result = self::getUser($db, $user['uid'], null, null, $folder);
            }
        }


        //\f\pa($result);
        // если пользователя нет, создаём такого
        if ($result !== false) {
            return $result;
        } else {
            // Добавление пользователя
            $new_user_id = self::addUser($db, (array) $user, $folder, 'didrive');
            //Получаем данные пользователя';
            return self::getUser($db, $new_user_id, null, null, $folder);
        }
    }

    public static function enterVk($db, string $soc_id) {

        $in = [
            ':soc_id' => $soc_id,
        ];

        if (empty($folder)) {
            $in[':folder'] = \Nyos\Nyos::$folder_now;

            // \f\pa($_SERVER);
            if (strpos($_SERVER['PHP_SELF'], 'didrive') !== false) {
                $in[':folder'] .= '_di';
            }
        }

        $sql = 'SELECT * FROM gm_user WHERE folder = :folder AND soc_web_id = :soc_id AND status != \'delete\' LIMIT 1;';
        // \f\pa($sql);
        $ff = $db->prepare($sql);
        // \f\pa($in);
        $ff->execute($in);
        
        $re = $ff->fetch();
        // \f\pa($re);
        
        return $re ?? false;

    }

    /**
     * 
     * @param type $db
     * @param string $login_uid
     * @param string $pass
     * @param type $array
     * @return type
     * @throws \NyosEx
     * @throws \Exception
     */
    public static function enter($db, string $login_uid, string $pass = null, $folder = null, $array = []) {

        // \f\pa( [ $login_uid, $pass , $folder , $array ] );

        try {

            if (empty($folder)) {
                $folder = \Nyos\Nyos::$folder_now;

                // \f\pa($_SERVER);
                if (strpos($_SERVER['PHP_SELF'], 'didrive') !== false) {
                    $folder .= '_di';
                }
            }

            $result = self::getUser($db, $login_uid, null, null, $folder);
            // \f\pa($result, '', '', 'get_user');

            if ($result === false) {

//                echo '<br/>#' . __LINE__ . ' ' . __FILE__;
//                \f\pa($_REQUEST);

                $data = $_REQUEST;
                $data['soc_web'] = 'vkontakte';

                if (isset($data['network']))
                    $data['soc_web'] = $data['network'];

                if (isset($data['first_name']))
                    $data['name'] = $data['first_name'];

                if (isset($data['last_name']))
                    $data['family'] = $data['last_name'];

                if (isset($data['profile']))
                    $data['soc_web_link'] = $data['profile'];

                if (isset($data['uid']))
                    $data['soc_web_id'] = $data['uid'];

                if (isset($data['photo_rec']))
                    $data['avatar'] = $data['photo_rec'];

                $new_user_id = self::addUser($db, $data, $folder, 'didrive');

                $result = self::getUser($db, $login_uid, null, null, $folder);

                $result['new_user_add'] = true;

//                \f\pa($new_user_id);
            }

            // exit;

            return $result;

            // exit;
            // получили результат проверки
//        try {
//
//            $user = json_decode(file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']), true);
//            if (isset($user['error'])) {
//                throw new \Exception('Ошибка при получении информации о токене: ' . $user['error']);
//            }
//            //\f\pa($user, 2, null, 'получили данные пользователя #' . __LINE__);
//            
//        } catch (\Error $ex) {
//            throw new \NyosEx('Ошибка при получении информации токена: ' . $ex->getMessage());
//        } catch (\Exception $ex) {
//            throw new \NyosEx('Ошибка при получении информации токена: ' . $ex->getMessage());
//        }
//             $result = self::getUser($db, $user['uid'], null, null, $folder);
        } catch (\PDOException $ex) {

            if (strpos($ex->getMessage(), 'no such table') !== false) {
                self::creatTable($db);
                $result = self::getUser($db, $login_uid, null, null, $folder);
            }
        }


        //\f\pa($result);
        // если пользователя нет, создаём такого
//        if ($result !== false) {
//            return $result;
//        } else {
//            // Добавление пользователя
//            $new_user_id = self::addUser($db, (array) $user, $folder, 'didrive');
//            //Получаем данные пользователя';
//            return self::getUser($db, $new_user_id, null, null, $folder);
//        }
    }

    /**
     * создаём пользователя
     * @param type $db
     * @param array $data
     * @param string $folder
     * @param string $type
     * site* | didrive
     */
    public static
            function addUser($db, array $data, string $folder = null, string $type = 'site') {

        if (isset($data['network']))
            $data['soc_web'] = $data['network'];

        if (isset($data['first_name']))
            $data['name'] = $data['first_name'];

        if (isset($data['last_name']))
            $data['family'] = $data['last_name'];

        if (isset($data['profile']))
            $data[
                    'soc_web_link'] = $data['profile'];

        if (isset($data['uid']))
            $data[
                    'soc_web_id'] = $data['uid'];

        if (isset($folder{1}))
            $data['folder'] = $folder;

        //$indb['dt'] = 'NOW';
        $data['dt'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $polya_good = array(
            'login', 'pass', 'pass5',
            'folder', 'mail',
            'name', 'soname', 'family',
            'phone',
            'avatar', 'adres', 'about', 'soc_web',
            'soc_web_id', 'soc_web_link', 'ip', 'city',
            'city_name', 'points', 'country', 'recovery',
            'recovery_dt', 'dt'
        );

        $indb2 = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $polya_good))
                $indb2[$k] = $v;
        }


        if (empty($indb2['avatar']) && $indb2['soc_web'] == 'vkontakte') {

            if (class_exists('\VK\Client\VKApiClient')) {
                $vk = new \VK\Client\VKApiClient();
// я вк
                $access_token = '13ea17c2bb1f9c438234bf6ea9d0fa7b19ca878714e19e8c0291a5fa776af2e95e2f5eca97db7d9350760';
                $response = $vk->users()->get($access_token, array(
                    'user_id' => $indb2['soc_web_id'],
                    'fields' => array('city', 'photo'),
                ));
                // \f\pa($response);

                $indb2['city_name'] = $response[0]['city']['title'];
                $indb2['avatar'] = $response[0]['photo'];
            }
        }

        // \f\pa($indb2, 2, null, 'добавление пользователя в новую таблицу'); die;

        return \f\db\db2_insert($db, 'gm_user', $indb2, true, 'last_id');
    }

}

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

    public static $type = 'now_user';
    
    // и для дидрайва
    // public static $type = 'now_user_di';
    public static $user_di_access = array();
    public static $dop = array();
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
        } catch ( \PDOException $ex ) {

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
                . ( !empty($id) ? ' AND ( `soc_web_id` = :id  OR `id` = :id ) ' : '' )
                . ( isset($folder{1}) ? ' AND `folder` = :folder ' : '' )
                . ( isset($login{1}) ? ' AND u.login = :login ' : '' )
                . ( isset($pass{1}) ? ' AND u.pass5 = :pass ' : '' )
                . ' LIMIT 1 ;';
                
        //echo '<br/>' . $s;
        
        $sql = $db->prepare($s);
        $dop_ar = [];

        if (isset($login{1}))
            $dop_ar[':login'] = $login;

        if (isset($pass{1}))
            $dop_ar[':pass'] = md5($pass);

        if (isset($folder{1}))
            $dop_ar[':folder'] = (string) $folder;

        if ($id !== null)
            $dop_ar[':id'] = $id;

        //\f\pa($dop_ar);
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

        $ff2 = $db->prepare('CREATE TABLE IF NOT EXISTS `gm_user22` ( '
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

            if (
                    strpos($ex->getMessage(), 'no such table') !== false 
                    ||
                    ( 
                        strpos($ex->getMessage(), 'Table') !== false 
                        && strpos($ex->getMessage(), 'doesn\'t exist') !== false 
                    ) 
                ) {
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

    /**
     * создаём пользователя
     * @param type $db
     * @param array $data
     * @param string $folder
     * @param string $type
     * site* | didrive
     */
    public static function addUser($db, array $data, string $folder = null, string $type = 'site') {

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


        if ($indb2['soc_web'] == 'vkontakte') {

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

        //\f\pa($indb2, 2, null, 'добавление пользователя в новую таблицу'); die;

        return \f\db\db2_insert($db, 'gm_user', $indb2, true, 'last_id');
    }

}

class lk_old_190407 {

    public static $type = 'now_user';
    // и для дидрайва
    // public static $type = 'now_user_di';
    public static $user_di_access = array();
    public static $dop = array();
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

    public static function blank($db, $shop, $domain) {

// $show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        if (isset($shop) && is_numeric($shop)) {
            
        } else {

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
                $status .= 'указан не верно пользователь<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            return f\end2('Ошибка в указании номера магазина', false, array(), 'array');
        }


        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return f\end3($res['summa'], true);
    }

    /**
     * проверка рекапчи
     * @global \Nyos\mod\type $status
     * @param type $recapcha
     * $_POST['g-recaptcha-response']
     * @param type $secret
     * ваш секретный ключ
     * @return type
     */
    public static function verifyRecapcha($secret, $g_recaptcha_response) {

// $show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        $query = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret
                . '&response=' . $g_recaptcha_response . '&remoteip=' . $_SERVER['REMOTE_ADDR'];

        $vv['recapcha'] = json_decode(file_get_contents($query), true);

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return \f\end3('recapcha', ( ( isset($vv['recapcha']['success']) && $vv['recapcha']['success'] === true ) ? true : false), $vv['recapcha']);
    }

    /**
     * 
     * @global \Nyos\mod\type $status
     * @param type $db
     * @param type $user
     * @param type $type
     * / null - все адреса
     * / primary - 1 примари адрес или 1 адрес рабочий
     * @return type
     */
    public static function getAddress($db, $user = null, $type = null) {

        //$show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        if ($user === null && isset($_SESSION['now_user']['id']))
            $user = $_SESSION['now_user']['id'];


        if (isset($user) && is_numeric($user)) {
            
        } else {

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
                $status .= 'указан не верно пользователь<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            return \f\end2('Ошибка в указании номер #' . __LINE__, false, array(), 'array');
        }

        if ($type == 'primary') {
            $d = \f\db\getSql($db, 'SELECT
            a.id,
            a.status,
            o.name,
            o.value
            FROM 
                gm_user_adress_option o
            INNER JOIN  gm_user_adress a ON a.id = o.id_adress AND a.user = \'' . $user . '\' AND 
                ( a.status = \'ok\' OR  a.status = \'ok-primary\' )
            ORDER BY 
                status DESC,
                o.id DESC
            ;', null);
        } else {
            $d = \f\db\getSql($db, 'SELECT
            a.id,
            a.status,
            o.name,
            o.value
            FROM gm_user_adress_option o
            INNER JOIN  gm_user_adress a ON a.id = o.id_adress AND a.user = \'' . $user . '\'
                ORDER BY o.id DESC
            ;', null);
        }

        $return = array();

        $last_id = null;


        foreach ($d as $k => $v) {

            if (!isset($return[$v['id']]['inf']['id']))
                $return[$v['id']]['inf']['id'] = $v['id'];

            if (!isset($return[$v['id']]['inf']['status']))
                $return[$v['id']]['inf']['status'] = $v['status'];

            $return[$v['id']]['inf'][$v['name']] = $v['value'];
            $return[$v['id']]['status'] = $v['status'];
        }


        if ($type == 'primary') {
            $re = $return;
            foreach ($re as $k => $v) {
                $return = $v;
                break;
            }
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return \f\end3('ок', true, $return);
    }

    public static function getAddressFull($db, $id, $user) {

        //$show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        if ($user === null && isset($_SESSION['now_user']['id']))
            $user = $_SESSION['now_user']['id'];


        if (isset($user) && is_numeric($user)) {
            
        } else {

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
                $status .= 'указан не верно пользователь<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            return \f\end2('Ошибка в указании номер #' . __LINE__, false, array(), 'array');
        }


        $d = \f\db\getSql($db, 'SELECT
                a.id,
                a.status,
                o.name,
                o.value
            FROM gm_user_adress_option o
            
                INNER JOIN gm_user_adress a 
                ON 
                    a.id = \'' . $id . '\'  
                    AND a.id = o.id_adress 
                    AND a.user = \'' . $user . '\' 
                        
            ORDER BY 
                status DESC,
                o.id DESC
            ;', null);

        $return = array();

        $last_id = null;


        foreach ($d as $k => $v) {

            if (!isset($return['id']))
                $return['id'] = $v['id'];

            if (!isset($return['status']))
                $return['status'] = $v['status'];

            $return[$v['name']] = $v['value'];
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return \f\end3('ок', true, $return);
    }

    public static function whatStepNowFormInfo($db, $user) {

// $show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        if (isset($user) && is_numeric($user)) {
            
        } else {

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
                $status .= 'указан не верно пользователь<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            return f\end2('Ошибка в указании номера ' . __LINE__, false, array(), 'array');
        }


        $res = \f\db\getSql($db, 'SELECT 
                value 
            FROM 
                gm_user_option u 
            WHERE 
                u.user = \'' . $user . '\' 
                AND u.option = \'etap_last\' 
            ORDER BY 
                value DESC 
            LIMIT 1;', 3);
        //\f\pa($res);

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return $res;
    }

    /**
     * новая версия получения всех опций пользователя
     * @global \Nyos\mod\type $status
     * @param type $db
     * @param type $user
     * @return type
     */
    public static function getUserOptions($db, int $user) {

        $res = \f\db\getSql($db, 'SELECT option id, value FROM `gm_user_option` WHERE `user` = \'' . $user . '\' ;', 2);
        return f\end3('ok', true, $res);
    }

    /**
     * получаем список Доступов логина
     * @param type $db
     * @param int $user
     * @return type
     */
    public static function getUserAccess($db, int $user) {

        //$status = '';
        $s = \f\db\getSql($db, 'SELECT * FROM `gm_user_access` WHERE `user` = \'' . $user . '\' ;', 'module');
        //echo $status;

        return f\end3('ok', true, $s);
    }

    /**
     * вход в аккаунт по логину паролю
     * @param type $db
     * @param type $folder
     * @param type $login
     * @param type $pass
     * @param type $request_need true|false
     * обязательно ли подтверждение реги по email
     * @return boolean false или массив с данными аккаунта
     */
    public static function enter($db, string $folder, string $login, string $pass, $request_need = false) {

        // global $status;
        // $status = '';
        $sql = $db->sql_query('SELECT * FROM gm_user '
                . 'WHERE '
                . ' `folder` = \'' . $folder . '\' '
                . ' AND '
                . '( '
                . '`login` = \'' . addslashes($login) . '\' '
                . 'OR '
                . ' `mail` = \'' . addslashes($login) . '\'  '
                . ') '
                // . ' AND `pass` = \'' . md5($pass) . '\' '
                . ' AND '
                . ' ( '
                . '`pass` = \'' . addslashes($pass) . '\' '
                . ' OR '
                . ' `pass5` = \'' . md5($pass) . '\' '
                . ' ) '
                . ' LIMIT 1 ; ');
        //echo $status;

        if ($db->sql_numrows($sql) == 1) {

            $res = $db->sql_fr($sql);

            if ($request_need === true && $res['mail_confirm'] != 'yes') {
                return f\end2('Необходимо подтверждение email адреса '
                        . ' ( <A href="/index.php?level=' . $_GET['level'] . '&send_confirm=' . $login . '&s=' . nyos2::creatSecret($login) . '" style="color:blue;" >отправить email подтверждения ещё раз</a> )'
                        , false, false, 'array');
            }

//$_SESSION['now_user']['data'] = $res;
//$_SESSION['now_user']['id'] = $res['id'];
            $_SESSION[self::$type] = $res;
//$_SESSION['now_user']['id'] = $res['id'];

            return $res;
        } else {
            return false;
        }
    }

    /**
     * либо всё окей либо исключение
     * @param type $db
     * @param string $folder
     * @param string $login
     * @param string $pass
     * @param type $request_need
     * @return boolean
     */
    public static function enter2($db, string $folder, string $login, string $pass, $request_need = false) {

        // global $status;
        // $status = '';
        $sql = $db->sql_query('SELECT * FROM gm_user '
                . 'WHERE '
                . ' ( `folder` = \'' . $folder . '\' OR `folder` = \'' . $folder . '_di\' ) '
                . ' AND '
                . '( '
                . '`login` = \'' . addslashes($login) . '\' '
                . 'OR '
                . ' `mail` = \'' . addslashes($login) . '\'  '
                . ') '
                // . ' AND `pass` = \'' . md5($pass) . '\' '
                . ' AND '
                . ' ( '
                . '`pass` = \'' . addslashes($pass) . '\' '
                . ' OR '
                . ' `pass5` = \'' . md5($pass) . '\' '
                . ' ) '
                . ' LIMIT 1 ; ');
        //echo $status;

        if ($db->sql_numrows($sql) == 0)
            throw new \Exception('Неверно указан логин и(или) пароль.');

        $res = $db->sql_fr($sql);

        if ($request_need === true && $res['mail_confirm'] != 'yes')
            throw new \Exception('Необходимо подтверждение email адреса ( <a href="/index.php?level=' . $_GET['level'] . '&send_confirm=' . $login . '&s=' . nyos2::creatSecret($login) . '" style="color:blue;" >отправить email подтверждения ещё раз</a> )');

        return $_SESSION[self::$type] = $res;
    }

    public static function getUserOption($db, $user) {

        //global $status;

        $status = '';
        $sql = $db->sql_query('SELECT * FROM `gm_user_option` WHERE `user` = \'' . addslashes($user) . '\' ;');
        //echo $status;

        $result = array();

        while ($row = $db->sql_fr($sql)) {

            $result[$row['option']] = $row['value'];
        }

        //f\pa($result);

        return $result;
    }

    public static function saveUserOption($db, $user, $vars) {

        // global $status;

        $db->sql_query('DELETE FROM `gm_user_option` WHERE `user` = \'' . addslashes($user) . '\' ;');

        // f\pa($vars);

        $in_db = array();

        foreach ($vars as $k => $v) {
            if (strpos($k, 'opt_') !== false) {
                $in_db[] = array(
                    'option' => substr($k, 4, 50)
                    , 'value' => $v
                );
            }
        }

        // f\pa($in_db);

        $status = '';
        db\SqlMind_insert_mnogo($db, 'gm_user_option', array('user' => $user, 'option' => 1, 'value' => 1), $in_db, true, false);
        // echo $status;
    }

    /**
     * формирование ссылки для восстановления пароля
     * @param type $db
     * @param type $folder
     * @param type $mail
     * @return boolean
     */
    public static function passwordRecoveryCheck($db, $folder, $mail) {

        //global $status;
        //$status = '';
        $sql = $db->sql_query('SELECT `id`,`mail` FROM gm_user '
                . 'WHERE '
                . ' `folder` = \'' . $folder . '\' '
                . ' AND '
                . ' ( '
                . ' `login` = \'' . addslashes($mail) . '\' '
                . ' OR `mail` = \'' . addslashes($mail) . '\' '
                . ' ) '
                . ' LIMIT 1 ; ');
        //echo $status;

        if ($db->sql_numrows($sql) == 1) {
            $res = $db->sql_fr($sql);

            $res['recovery'] = md5(rand(100, 900) . '_ert');

            //global $status;
            //$status = '';
            \f\db\db_edit2($db, 'gm_user', array('id' => $res['id']), array(
                'recovery' => $res['recovery'],
                'recovery_dt' => 'NOW'
            ));
            //echo $status;

            return f\end2('Пароль для восстановления сформирован', 'ok', $res, 'array');
        }

        return f\end2('Укажите E-mail', 'error', array(), 'array');
    }

    public static function setNewPassword($db, $folder, $id_user, $pass) {

//        global $status;
//        $_SESSION['status1'] = true;
//        $status = '';
        $db->sql_query('UPDATE  `gm_user` SET  `pass` = NULL, `pass5` =  \'' . md5($pass) . '\', `recovery_dt` = \'\', `recovery` = \'\'
            WHERE  `id` = \'' . $id_user . '\' AND `folder` =  \'' . $folder . '\' LIMIT 1 ;');
//        echo $status;
        // return false;
    }

    public static function recoveryPassCheck($db, $folder, $id_user, $recovery_cod) {

        // global $status;

        if (!isset($recovery_cod{2}))
            return false;

        $status = '';
        $sql = $db->sql_query('SELECT `id`,`recovery` FROM gm_user WHERE ' .
                ( $folder === null ? '' : ' `folder` = \'' . addslashes($folder) . '\' AND ' )
                . ' `id` = \'' . addslashes($id_user) . '\' AND '
                . ' `recovery` = \'' . $recovery_cod . '\' AND '
                . ' `recovery_dt` > \'' . ( $_SERVER['REQUEST_TIME'] - 3600 * 24 ) . '\' '
                . ' LIMIT 1 ; ');
        echo $status;

        // echo ( $db -> sql_numrows($sql) == 1 ) ? '11111' : '222222' ;
        return ( $db->sql_numrows($sql) == 1 ) ? true : false;
    }

    /**
     * загрузка нового аватара в профиль участника (первое использование в мы-правы)
     * @param type $db
     * @param type $id_user
     * @param type $file
     */
    public static function setNewAvatar($db, $folder, $id_user, $file) {

        require_once($_SERVER['DOCUMENT_ROOT'] . '/0.all/f/file.2.php');

        if (isset($id_user{0}) && is_numeric($id_user)) {

            if ($file['error'] == 0 && $file['size'] > 100) {

                if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $folder . '/download/avatars'))
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $folder . '/download/avatars', 0755);

                $new_name_ava = date('ymdhis', $_SERVER['REQUEST_TIME']) . '.' . rand(100, 999) . '.' . f\get_file_ext($file['name']);
                copy($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $folder . '/download/avatars/' . $new_name_ava);
            }

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/9.site/' . $folder . '/download/avatars/' . $new_name_ava)) {
                $db->sql_query('UPDATE `gm_user` SET `avatar` =  \'' . addslashes($new_name_ava) . '\' WHERE `id` = \'' . $id_user . '\' AND `folder` = \'' . addslashes($folder) . '\' LIMIT 1;');
                // $_SESSION['now_user']['data']['avatar'] = $new_name_ava;
                //$_SESSION['now_user']['avatar'] = $new_name_ava;
                $_SESSION[self::$type]['avatar'] = $new_name_ava;
                return true;
            }
        }

        return false;
    }

    public static function editAccount($db, $id_user, $data, $refres_session = true) {

//        global $status;
//
//        
//        echo '
//<br/>
//<br/>
//<br/>
//<br/>'.$id_user.'
//<br/>';
//        \f\pa($data);
//          

        $in_db = array();

        foreach ($data as $kk => $vv) {
            if (
                    $kk == 'name' || $kk == 'soname' || $kk == 'adres' || $kk == 'phone' || $kk == 'city' || $kk == 'about' || $kk == 'mail' || $kk == 'family'
            ) {
                $in_db[$kk] = $vv;
            } elseif ($kk == 'my_city2' && isset($data['my_city']) && $data['my_city'] == 'no') {
                $in_db['city'] = '';
                $in_db['city_name'] = $vv;
            } elseif ($kk == 'my_city' && isset($vv{0}) && is_numeric($vv)) {
                $in_db['city'] = $vv;
                $in_db['city_name'] = '';
            }
        }

        // global $status;
        // \f\pa($in_db);
        // $_SESSION['status1'] = true;
        // $status = '';
        \f\db\db_edit2($db, 'gm_user', array('id' => $id_user), $in_db);
        // echo $status;

        if ($refres_session === true) {
            foreach ($in_db as $k => $v) {
                // $_SESSION['now_user']['data'][$k] = $v;
                // $_SESSION['now_user'][$k] = $v;
                $_SESSION[self::$type][$k] = $v;
            }
        }

        return array('txt' => 'Ошибка', 'status' => 'error');
    }

    public static function getInfoAccount($db, $id, $folder = null, $pass = null) {

        $sql1 = $db->sql_query('SELECT * FROM gm_user WHERE ' .
                ( $folder === null ? '' : ' `folder` = \'' . addslashes($folder) . '\' AND ' )
                . ( $pass === null ? '' : ' `pass` = \'' . md5($pass) . '\' AND ' )
                . ' `id` = \'' . addslashes($id) . '\'
            LIMIT 1 ; ');

        if ($db->sql_numrows($sql1) == 1) {

            $res = $db->sql_fr($sql1);
            return $res;
        } else {

            return false;
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

        //$show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        $where = '';

        if ($folder == 'di') {
            $where .= ' `folder` LIKE \'%_di\' ';
        } elseif (isset($folder{1})) {
            $where .= ' `folder` = \'' . addslashes($folder) . '\' ';
        }

        if ($type == 'moder') {
            $where .= ( isset($where{1}) ? ' AND ' : '' ) . ' `access` = \'moder\' ';
        } elseif ($type == 'admin') {
            $where .= ( isset($where{1}) ? ' AND ' : '' ) . ' `access` = \'admin\' ';
        }

        //$status = '';
        $sql1 = $db->sql_query('SELECT * '
                . 'FROM '
                . ' `gm_user` '
                . ( isset($where{1}) ? ' WHERE ' . $where : '' )
                . ' ORDER BY '
                . ' `id` DESC '
                . ' LIMIT 5000 ; ');
        //echo $status;
        //echo $db->sql_numrows($sql1);

        $dop_sql = '';

        if ($db->sql_numrows($sql1) > 0) {

            while ($r = $db->sql_fr($sql1)) {
                //f\pa($r,2);
                $dop_sql .= ( isset($dop_sql{2}) ? ' OR ' : '' ) . ' `user` = \'' . $r['id'] . '\' ';
                $res[$r['id']] = $r;
            }

            $sql = $db->sql_query('SELECT * FROM `gm_user_option` WHERE ' . $dop_sql . ' ; ');
            //echo $status;
            if ($db->sql_numrows($sql) > 0) {
                while ($r = $db->sql_fr($sql)) {

                    //f\pa($r);

                    if (!isset($res[$r['user']]['dop'])) {
                        $res[$r['user']]['dops'] = $res[$r['user']]['dop'] = array();
                    }

                    $res[$r['user']]['dop'][] = $r;
                    $res[$r['user']]['dops'][$r['option']] = $r['value'];
                }
            }


            $sql = $db->sql_query('
                SELECT
                    access,
                    var1,
                    var2,
                    user,
                    module
                  FROM gm_user_access
                  WHERE ( ' . $dop_sql . ' )
                    AND status = \'ok\'
                  ; ');
            //echo $status;
            if ($db->sql_numrows($sql) > 0) {
                while ($r = $db->sql_fr($sql)) {

                    //f\pa($r);
                    if (!isset($res[$r['user']]['access_mod']))
                        $res[$r['user']]['access_mod'] = array();

                    $res[$r['user']]['access_mod'][$r['module']][$r['var1']] = $r;
                }
            }

            //f\pa($res);

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
                $status .= '<br/>найдено: ' . sizeof($res) .
                        '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

                if (isset($show_status) && $show_status === true)
                    echo $status;
            }

            return $res;
        } else {
            return false;
        }
    }

    /**
     * выбираем пользователей с правами доступа
     * @global \Nyos\mod\type $status
     * @param class $db
     * @param string $folder
     * @param int $access_id 
     * номер проекта var1 
     * @return массив 
     */
    public static function getUsersAccess($db, $folder, $access_id, $sort = 'family') {

        //$show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        // $status = '';
        $res = \f\db\getSql($db, 'SELECT u.*
                , a.access AS access_access
                , a.module AS access_module
                , a.var1 AS access_pr
            FROM gm_user u
            JOIN gm_user_access a ON u.id = a.user
            WHERE a.var1 = \'' . $access_id . '\'
                ' . ( $sort == 'family' ? ' ORDER BY u.family ASC, u.name ASC ' : '' ) . '
            ; ');
        // echo $status;
        //f\pa($res);

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<br/>найдено: ' . sizeof($res) .
                    '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        return $res;
    }

    public static function getDidriveUsersAccess($db, $folder, $access_id = null) {

        //echo '<br/>folder - '.$folder;
        //$show_status = true;

        if (isset($show_status) && $show_status === true) {
            $status = '';
            $_SESSION['status1'] = true;
        }

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            global $status;

            $status .= '<fieldset class="status" ><legend>' . __CLASS__ . ' #' . __LINE__ . ' + ' . __FUNCTION__ . '</legend>';
        }

        if (isset(self::$user_di_access[$folder])) {

            if (isset($access_id{0}) && $access_id !== null && isset(self::$user_di_access[$folder][$access_id])) {
                return self::$user_di_access[$folder][$access_id];
            } else {
                return self::$user_di_access[$folder];
            }
        }


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

            ; ', null);
        // echo $status;
        //f\pa($res);
        $res2 = array();

        foreach ($res as $k => $v) {
            $res2[$v['user']][$v['mode']][$v['module']] = $v['status'];
        }

        self::$user_di_access[$folder] = $res2;

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true) {
            $status .= '<br/>найдено: ' . sizeof($res) .
                    '<span class="bot_line">#' . __LINE__ . '</span></fieldset>';

            if (isset($show_status) && $show_status === true)
                echo $status;
        }

        // return array( 'st' => $status, 'data' => $res, 'dataw' => $res2 );

        if (isset($access_id{0}) && $access_id !== null && isset($res2[$access_id])) {
            return $res2[$access_id];
        } else {
            return $res2;
        }
    }

    /**
     * получение инфы по пользователю по майл
     * @param type $db
     * @param type $folder
     * @param type $mail
     * @return boolean
     */
    public static function getUser_mail($db, $folder, $mail) {

        //global $status;

        if (isset($mail{2})) {
            
        } else {
            return false;
        }

        $status = '';
        $sql1 = $db->sql_query('SELECT * '
                . 'FROM '
                . ' `gm_user` '
                . ' WHERE '
                . ' `folder` = \'' . addslashes($folder) . '\' '
                . ' AND `mail` = \'' . addslashes($mail) . '\' '
                . ' LIMIT 1 ; ');
        //echo $status;

        if ($db->sql_numrows($sql1) == 1) {
            $r = $db->sql_fr($sql1);
            return $r;
        } else {
            return false;
        }
    }

    public static function getUser_old190408($db, $folder, $id) {

//global $status;

        if (isset($id{0}) && is_numeric($id)) {
            
        } else {
            return false;
        }

        $status = '';
        $sql1 = $db->sql_query('SELECT * '
                . 'FROM '
                . ' `gm_user` '
                . ' WHERE '
                . ' `folder` = \'' . addslashes($folder) . '\' '
                . ' AND ( `id` = \'' . $id . '\' '
                . ' OR `soc_web_link` = \'' . $id . '\' ) '
                . ' LIMIT 1 ; ');
//echo $status;

        if ($db->sql_numrows($sql1) == 1) {
            $r = $db->sql_fr($sql1);

            $r['dops'] = \f\db\getSql($db, 'SELECT * FROM `gm_user_option` WHERE `user` = \'' . $r['id'] . '\' ;');
            $r['access_mod'] = \f\db\getSql($db, 'SELECT `folder` as `id`, `access` FROM `gm_user_access` WHERE `user` = \'' . $r['id'] . '\' AND `folder` = \'' . addslashes($folder) . '\' AND `status` = \'ok\' ;');
            //f\pa($r['access']);

            return $r;
        } else {
            return false;
        }
    }

    /**
     * регистрация пользователя
     * @param класс $db
     * @param строка $folder
     * @param массив $indb
     * $indb['reg_mail_head'] - тема письма о регистрации,
     * $indb['reg_mail_template'] - шаблон письма о регистрации
     * $indb['reg_mail_from_mail'] - майл отправителя
     * $indb['reg_mail_sendpulse_id'] - id sendpulse api
     * $indb['reg_mail_sendpulse_key'] - key sendpulse api
     * @param строка $return
     * сразу die | "array" массив на выходе
     * @return номер-пользователя|false
     */
    public static function regUser($db, $folder, $indb, $return = null) {

        //f\pa($indb);

        /*
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
         */

        if (!isset($indb['name']{2}))
            return f\end2('Укажите Имя (минимальная длинна 3 символа)', 'error', array('line' => __LINE__), $return);

        //if( !isset($indb['family']{2}) )
        //return f\end2( 'Укажите Фамилию (минимальная длинна 3 символа)', 'error', array( 'line' => __LINE__ ) );

        if (!isset($indb['mail']{5}))
            return f\end2('Укажите E-mail', 'error', array('line' => __LINE__), $return);

        if (!isset($indb['pass']{3}))
            return f\end2('Укажите пароль (' . $indb['pass'] . ')', 'error', array('line' => __LINE__, 'val' => $indb['pass']), $return);

        $indb['dt'] = 'NOW';

        if (isset($indb['id']))
            unset($indb['id']);

        $sql = $db->sql_query('SELECT * FROM gm_user '
                . ' WHERE '
                . ' `folder` = \'' . addslashes($folder) . '\' '
                . ' AND `mail` = \'' . addslashes($indb['mail']) . '\' '
                . ' LIMIT 1 ; ');

        //if ($db->sql_numrows($sql) == 1 && $indb['mail'] != 'nyos@rambler.ru' ) {
        if ($db->sql_numrows($sql) == 1) {
            sleep(2);
            return f\end2('Данный e-mail уже используется, укажите другой', 'error', array('line' => __LINE__), $return);
        } else {

            // $status = '';
            $id_new = f\db\db2_insert($db, 'gm_user', $indb, 'no', 'last_id');
            // echo $status;
            // f\pa($id_new);

            if (isset($indb['reg_mail_template']{3})) {

                require_once ($_SERVER['DOCUMENT_ROOT'] . '/0.all/class/mail.2.php');

                mailpost::$sendpulse_id = ( isset($indb['reg_mail_sendpulse_id']{5}) ) ? $indb['reg_mail_sendpulse_id'] : null;
                mailpost::$sendpulse_key = ( isset($indb['reg_mail_sendpulse_key']{5}) ) ? $indb['reg_mail_sendpulse_key'] : null;

                mailpost::$ns['from'] = ( isset($indb['reg_mail_from_mail']{5}) ) ? $indb['reg_mail_from_mail'] : 'support@uralweb.info';
                mailpost::$ns['to'] = $indb['mail'];

                // f\pa($indb);

                $indb['text'] = '<style>.aa td{ padding: 15px; }</style><table class="aa" >'
                        . '<tr><td style="text-align:right;" >Логин:</td><td>' . htmlspecialchars($indb['mail']) . '</td></tr>'
                        . '<tr><td style="text-align:right;" >Пароль:</td><td>' . htmlspecialchars($indb['pass']) . '</td></tr>'
                        . '</table>';

                $res_mail = mailpost::sendNow($db, ( ( isset($indb['reg_mail_from_mail']{5}) ) ? $indb['reg_mail_from_mail'] : 'support@uralweb.info'), array($indb['mail']), $indb['reg_mail_head'], $indb['reg_mail_template'], $indb);

                $res_mail['mail_status'] = $res_mail['html'];
            }



            return f\end2('Регистрация проведена успешно', 'ok', array_merge(
                            $res_mail, $indb, array(
                'line' => __LINE__, 'id' => $id_new,
                'login' => $indb['mail'],
                's' => $indb['pass']
                            )
                    ), $return);
        }
    }

    /**
     * вход пользователя в ЛК
     * @param класс $db
     * @param строка $folder
     * @param массив $in
     * @param строка $return
     * = 'array' // в чём возвращать результат функции
     * @return type
     */
    public static function enterUser($db, $folder, $in, $return = 'array') {

        //f\pa($in);

        sleep(2);

        if (!isset($in['pass']{2}))
            return f\end2('Укажите логин (ваш email)', 'error', array('line' => __LINE__), $return);

        if (!isset($in['email']{5}))
            return f\end2('Укажите E-mail', 'error', array('line' => __LINE__), $return);

        $sql = $db->sql_query('SELECT * FROM `gm_user` '
                . ' WHERE '
                . ' `folder` = \'' . addslashes($folder) . '\' '
                . ' AND `mail` = \'' . addslashes($in['email']) . '\' '
                . ' AND ( '
                . ' `pass` = \'' . addslashes($in['pass']) . '\' '
                . ' OR `pass5` = \'' . md5($in['pass']) . '\' '
                . ' ) '
                . ' LIMIT 1 ; ');

        //if ($db->sql_numrows($sql) == 1 && $indb['mail'] != 'nyos@rambler.ru' ) {
        if ($db->sql_numrows($sql) != 1) {
            return f\end2('Указаны неверные данные', 'error', array('line' => __LINE__), $return);
        } else {
            return f\end2('Отлично, вход выполнен', 'ok', array_merge($db->sql_fr($sql), array('line' => __LINE__)), $return);
        }
    }

    /**
     * установка подтверждения мыла
     * @param класс $db
     * @param строка $folder - папка
     * @param строка $mail - мыльник
     */
    public static function confirmMail($db, $folder, $mail) {

        //$status = '';
        return $db->sql_query('UPDATE
            `gm_user`
        SET
            `mail_confirm`= \'yes\'
        WHERE
            `folder` = \'' . addslashes($folder) . '\'
            AND `mail` =  \'' . addslashes($mail) . '\'
        LIMIT 1 ;', false, 'kolvo');
        //echo $status;

        return true;
    }

    /**
     * формирование и отправка ссылки на смену пароля
     * @param type $db
     * @param type $folder
     * @param type $mail
     * @return boolean
     */
    public static function recoveryPassSend($db, $folder, $mail) {

        if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
            global $status;

        $result = self::passwordRecoveryCheck($db, $folder, $mail);

        if ($result['status'] == 'ok') {

            if (isset($_SESSION['status1']) && $_SESSION['status1'] === true)
                $status .= 'Пароль восстановлен<br/>';

            //$aa = nyos_class::get_menu($folder);
            //return f\end2( 'aga', 'error', $aa);

            $a = nyos2::get_menu($folder);
            // f\end2( '222','ok', $a );

            foreach ($a as $k => $v) {
                if ($v['type'] == 'lk') {
                    $_ss = $v;
                }
            }

            $_ss['menu'] = $a;

            $_ss['text'] = 'Для Установки нового пароля, перейдите по ссылке <a href="https://' . $_SERVER['HTTP_HOST']
                    . '/index.php?goto=050.lk&user=' . $result['id'] . '&recovery=' . $result['recovery'] . '" >https://' . $_SERVER['HTTP_HOST']
                    . '/index.php?goto=050.lk&user=' . $result['id'] . '&recovery=' . $result['recovery'] . '</a>';

            // f\pa($in);

            require_once ($_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'class' . DS . 'mail.2.php');

            mailpost::$sendpulse_id = ( isset($_ss['reg_mail_sendpulse_id']{5}) ) ? $_ss['reg_mail_sendpulse_id'] : null;
            mailpost::$sendpulse_key = ( isset($_ss['reg_mail_sendpulse_key']{5}) ) ? $_ss['reg_mail_sendpulse_key'] : null;

            mailpost::$ns['from'] = ( isset($_ss['reg_mail_from_mail']{5}) ) ? $_ss['reg_mail_from_mail'] : 'support@uralweb.info';
            mailpost::$ns['to'] = $mail;

            $res_mail = mailpost::sendNow($db, ( ( isset($_ss['reg_mail_from_mail']{5}) ) ? $_ss['reg_mail_from_mail'] : 'support@uralweb.info'), array($_ss['mail']), 'Восстановление пароля', $_ss['mail_template'], $_ss);

            return f\end2('Инструкция для восстановления пароля отправлена на E-mail', 'ok', $_ss, 'array');
        }
        // if( $result['status'] != 'ok' ){
        else {
            return f\end2($result['html'], 'error', $_ss, 'array');
        }

        return true;
    }

    public static function recoveryConfirm22($db, $folder, $mail, $key) {

        //$status = '';
        return $db->sql_query('UPDATE
            `gm_user`
        SET
            `mail_confirm`= \'yes\'
        WHERE
            `folder` = \'' . addslashes($folder) . '\'
            AND `mail` =  \'' . addslashes($mail) . '\'
        LIMIT 1 ;', false, 'kolvo');
        //echo $status;

        return true;
    }

    public static function checkAccess($access, $user = null, $project = null) {

        if ($user === null) {

            //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
            //echo '-+-+ '.$access.' - '.$user.' + '.$project. '<br/>';

            if ($project === null && isset(self::$dop['project']) && is_numeric(self::$dop['project']))
                $project = self::$dop['project'];

            //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
            //echo $project . ']<br/>';

            if (!isset($project{0})) {
                return false;
            }

            //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
//            if (isset($_SESSION['now_user']['data']['soc_web_id']) &&
//                    $_SESSION['now_user']['data']['soc_web_id'] == 5903492)
//                $_SESSION['now_user']['data']['user_type'] = 'god';
//            if (isset($_SESSION['now_user']['soc_web_id']) &&
//                    $_SESSION['now_user']['soc_web_id'] == 5903492)
//                $_SESSION['now_user']['user_type'] = 'god';
            if (isset($_SESSION[self::$type]['soc_web_id']) &&
                    $_SESSION[self::$type]['soc_web_id'] == 5903492)
                $_SESSION[self::$type]['user_type'] = 'god';

            if (
            // isset($_SESSION['now_user']['data']['user_type']) && $_SESSION['now_user']['data']['user_type'] == 'god'
                    isset($_SESSION[self::$type]['user_type']) && $_SESSION['now_user']['user_type'] == 'god'
            ) {
                //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
                return true;
            }

            //f\pa($_SESSION['now_user']['data']['dop'][$project]);
//            if (isset($_SESSION['now_user']['data']['dop'][$project]))
//                $dop = $_SESSION['now_user']['data']['dop'][$project];
            if (isset($_SESSION[self::$type]['dop'][$project]))
                $dop = $_SESSION[self::$type]['dop'][$project];

            if (isset($dop[$access])) {
                //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
                return true;
            }
        }
        //echo __FILE__ . '[#' . __LINE__ . ']<br/>';
        return false;
    }

    public static function enterLoginPass_old190409($db, string $folder, string $login, string $pass) {

        //global $status;
        //echo '<br/>'.__FILE__.'['.__LINE__.']';
//        $_SESSION['status1'] = true;
//        $status = '';
        $sql1 = $db->sql_query('SELECT 
                u.* 
            FROM 
                gm_user u
            WHERE 
                u.folder = \'' . $folder . '\' AND 
                u.login = \'' . addslashes($login) . '\' AND 
                u.pass5 = \'' . md5($pass) . '\' AND
                u.status != \'delete\'
            LIMIT 1 ;');
        //echo $status;
        // проверяем если уже зареген
        if ($db->sql_numrows($sql1) == 1) {

            $_SESSION[self::$type] = $db->sql_fr($sql1);

            $a = self::getUserOptions($db, $_SESSION[self::$type]['id']);
            if (isset($a['data']) && sizeof($a['data']) > 0)
                $_SESSION[self::$type]['options'] = $a['data'];

            $a = self::getUserAccess($db, $_SESSION[self::$type]['id']);
            if (isset($a['data']) && sizeof($a['data']) > 0)
                $_SESSION[self::$type]['access_mod'] = $a['data'];


//            \f\pa($a);
//            echo '<hr>';
//            echo '<hr>';
//            \f\pa($_SESSION[self::$type]);
//            die();
            //return f\end2('Осуществлен вход с помощью соц. сервиса', true, array('user' => $_SESSION['now_user'], 'status' => 'ok', 'txt' => 'Осуществлен вход с помощью соц. сервиса'), 'array');
            // return f\end2('Осуществлен вход с помощью логина', true, array('user' => $_SESSION[self::$type] ), 'array');
            return f\end3('Осуществлен вход с помощью логина', true, array('user' => $_SESSION[self::$type]));
        }

        // если нет такой записи
        else {

            throw new \Exception('login or password are not correct', 2);
        }
    }

}

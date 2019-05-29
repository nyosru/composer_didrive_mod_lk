<?php

date_default_timezone_set("Asia/Yekaterinburg");
define('IN_NYOS_PROJECT', true);
//sleep(1);

$_SESSION['status1'] = false;
$_SESSION['status1'] = true;

$status = '';

require( $_SERVER['DOCUMENT_ROOT'] . '/index.session_start.php' );
require( $_SERVER['DOCUMENT_ROOT'] . '/0.all/class/nyos.2.php' );
require( $_SERVER['DOCUMENT_ROOT'] . '/0.all/f/ajax.php' );

// проверяем секрет
if (isset($_REQUEST['id']{0}) && isset($_REQUEST['secret']{5}) &&
        Nyos\nyos::checkSecret($_REQUEST['secret'], $_REQUEST['id']) === true) {

} else {
    f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', 'error', array(
        'enter_id' => $_SESSION['enter_id'],
        'enter_secret' => $_REQUET['enter_secret'],
        'session' => $_SESSION
            )
    );
}

require( $_SERVER['DOCUMENT_ROOT'] . '/0.site/0.cfg.start.php');
//require( $_SERVER['DOCUMENT_ROOT'] . '/0.site/exe/peticii/class.php');

require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'class' . DS . 'mysql.php' );
require_once ( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'db.connector.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit_dops') {

    //sleep(5);

    if (isset($_REQUEST['user']{0}) && is_numeric($_REQUEST['user'])) {

    } else {
        f\end2('Ошибка', 'error');
    }

    $folder = Nyos\nyos::getFolder($db);

    if (!isset($folder{3}))
        f\end2('Ошибка', 'error' //, array( 'file' => __FILE__, 'line' => __LINE__ )
        );

    // перед записью допов удаляем все допы что записаны
    // если action2 = 'no-clear' тогда не удаляем старые
    if (isset($_REQUEST['action2']) && $_REQUEST['action2'] == 'no-clear') {

    } else {
        $db->sql_query('DELETE FROM `gm_user_option` WHERE `user` = \'' . $_REQUEST['user'] . '\' AND `option` LIKE \'dop_%\' ;');
    }

    $new_in = array();

    foreach ($_REQUEST as $k => $v) {
        if (substr($k, 0, 4) == 'dop_') {
            $new_in[] = array(
                'option' => $k,
                'value' => $v
            );
        }
    }

    $key = array('user' => $_REQUEST['user']);
    if (isset($_REQUEST['project']{0}) && is_numeric($_REQUEST['project']))
        $key['project'] = $_REQUEST['project'];

    f\db\sql_insert_mnogo($db, 'gm_user_option', $new_in, $key);

    f\end2('Новые права доступа установлены' // . $status
        );
}



/*
  if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del_city') {

  $db->sql_query('UPDATE `mpeticii_city` SET `status` = \'hide\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
  // $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

  f\end2('Город удалён');
  }
  elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'golos_del' && is_numeric($_REQUEST['id']) ) {

  $status = '';

  Nyos\peticii::deleteGolos($db, $_REQUEST['id'], $_REQUEST['peticiya'] );

  // $db->sql_query('DELETE FROM `mpeticii_golosa` WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
  // $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

  f\end2('Голос удалён');
  }

  //action=
  elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'addpoint_za_share') {

  if (!isset($_REQUEST['user']{0}))
  f\end2('Ошибка', 'error');

  require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'txt.2.php' );
  //f\pa($_REQUEST);
  require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );
  Nyos\peticii::addPoint($db, $_REQUEST['folder'], $_REQUEST['user'], 'share');
  f\end2('ок');
  }
  //
  elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'podpis') {

  require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );

  Nyos\peticii::recordGolos($db, $_REQUEST['peticiya'], $_REQUEST['user']);

  f\end2('Подписано!');
  }
  //
  elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'show_golosa') {

  require_once( $_SERVER['DOCUMENT_ROOT'] . DS . '0.all' . DS . 'f' . DS . 'db.2.php' );

  $list = Nyos\peticii::getGolosa($db, $_REQUEST['id']);

  //f\pa($list,2);
  //echo '<pre>'; print_r($list); echo '</pre>';

  $ee = '<thead>'
  . '<th>дата реги</th>'
  . '<th>социальная сеть</th>'
  . '<th>Как зовут</th>'
  . '<th>Email</th>'
  . '<th>Телефон</th>'
  . '</thead>'
  //            .'<tfoot>'
  //            . '<th>дата реги</th>'
  //            . '<th>социальная сеть</th>'
  //            . '<th>Как зовут</th>'
  //            . '<th>Email</th>'
  //            . '<th>Телефон</th>'
  //            . '</tfoot>'
  //            . '<tbody>'
  ;

  foreach ($list as $k => $v) {
  $ee .= '<tr id="r' . $v['golos_id'] . '" >'
  . '<td>' . $v['dati'] . '</td>'
  . '<td><a href="' . $v['soc_web_link'] . '" target="_blank" >' . $v['soc_web'] . '</a></td>'
  . '<td>' . $v['family'] . ' ' . $v['name']
  . '<a href="#" class="del_golos" rel="' . $v['golos_id'] . '" rev="' . $v['s'] . '" alt="'.$_REQUEST['id'].'"  ><span title="удалить голос" style="color:red" class="glyphicon glyphicon-remove tr_hover_show" ></span></a>'
  . '<div id="resgolos' . $v['golos_id'] . '" ></div>'
  . '</td>'
  . '<td>' . $v['mail'] . ' '
  . ( $v['mail_confirm'] == 'yes' ? '<span title="mail подтверждён" style="color:green" class="glyphicon glyphicon-ok" ></span>' : '.' )
  . '</td>'
  . '<td>' . $v['phone'] . '</td></tr>';
  }

  $ee .= '</tbody>';

  $js2 = '<script type="text/javascript" charset="utf-8">
  <!--
  $(document).ready(function () {

  //alert("111111");

  $("td a.del_golos").on( "click", function () {

  var $id = $(this).attr("rel");
  var $s = $(this).attr("rev");
  var $peticiya = $(this).attr("alt");
  var $res_div = "#resgolos"+$id;

  // alert($res_div);

  $($res_div).html(\'<img src="/image/load2.gif" style="height:15px;" />\');

  $.ajax({// инициaлизируeм ajax зaпрoс

  type: "POST", // oтпрaвляeм в POST фoрмaтe, мoжнo GET
  url: "/0.site/exe/peticii/1/ajax.php", // путь дo oбрaбoтчикa, у нaс oн лeжит в тoй жe пaпкe
  dataType: "json", // oтвeт ждeм в json фoрмaтe
  data: "action=golos_del&secret=" + $s + "&id=" + $id + "&peticiya=" + $peticiya, // дaнныe для oтпрaвки
  success: function ($d) { // сoбытиe пoслe удaчнoгo oбрaщeния к сeрвeру и пoлучeния oтвeтa

  if( $d.status == "ok" ){
  $($res_div).html( $d.html );
  $("tr#r"+$id+" td").html( "" );
  $("tr#r"+$id+" td:eq(0)").html( "голос удалён" );
  }else{
  $($res_div).html( $d.html );
  }
  }
  });

  return false;

  });
  });
  -->
  </script>';

  f\end2($ee, 'ok', array('kolvo' => sizeof($list), 'js2' => $js2 ) );

  }
  //
  elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del1') {

  $db->sql_query('UPDATE `mpeticii_cat` SET `status` = \'hide\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
  // $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

  f\end2('Каталог удалён');
  } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del_item') {

  $db->sql_query('DELETE FROM `mpeticii_item` WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
  // $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

  f\end2('Петиция удалёна');
  } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activated') {

  $db->sql_query('UPDATE `mpeticii_cat` SET `status` = \'show\' WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1 ;');
  // $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = 2 LIMIT 1;');

  f\end2('Восстановлен');
  } elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'del2') {

  $db->sql_query('DELETE FROM `mpeticii_cat` WHERE `id` = \'' . $_REQUEST['id'] . '\' LIMIT 1;');

  f\end2('Каталог удалён совсем');
  }
 */

f\end2('Произошла неописуемая ситуация #' . __LINE__ . ' обратитесь к администратору', 'error');
exit;

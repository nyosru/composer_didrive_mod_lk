<?php

// require dirname(__FILE__) . DS . '..'.DS.'class.php';





if (1 == 2) {

//echo $item.'='.$_GET['level'].'<br/><pre>'; print_r($ActModCfg); echo '</pre>';
// $item - текущий модуль


    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/0.zip'))
        mkdir($_SERVER['DOCUMENT_ROOT'] . '/0.zip', 0755);

    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/0.zip/cash.inf'))
        mkdir($_SERVER['DOCUMENT_ROOT'] . '/0.zip/cash.inf', 0755);

//if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/0.zip/cash.inf/' . $domen_info['folder']))
//    mkdir($_SERVER['DOCUMENT_ROOT'] . '/0.zip/cash.inf/' . $domen_info['folder'], 0755);
// файл кеша информера
    $temp_file = $_SERVER['DOCUMENT_ROOT'] . '/0.zip/cash.inf/' . folder . '__news__7.1__' . f\translit($item . '__' . $_GET['level'], 'uri2') . '.arr';
// перменная для информера
    $temp_var = 'informer_' . f\translit($item, 'uri2');


    if (file_exists($temp_file) && ( filemtime($temp_file) > $_SERVER['REQUEST_TIME'] - 3600 * 3 )) {
        // echo '<br/>'.__FILE__.'['.__LINE__.']';
        // include($_FCash);
        $vv[$temp_var] = unserialize(file_get_contents($temp_file));
    } else {
        // echo '<br/>'.__FILE__.'['.__LINE__.']';
        //$status = '';
        // $status = '';
        $MRes = $db->sql_query("SELECT 
            *
        FROM
            `mod_news_text`
        WHERE
            folder = '" . folder . "' AND
            modul = '" . addslashes($item) . "' AND
            status = 'look'
        ORDER BY 
            date DESC, 
            id DESC 
        ;");
        // echo $status;
        // $_tNews = array();
        $vv[$temp_var] = array();
        // while( $MRw = $db->sql_fetchrow_assoc($MRes) )
        while ($news = $db->sql_fr($MRes)) {
            // f\pa($news);

            if (!isset($news['cat2']{0}))
                $news['cat2'] = $MRw['cat1'];

            $news['eng_name'] = urlencode(str_replace('-', '', $news['head_eng']));
            $news['level'] = $item;

            // $_tNews[] = $MRw;
            $vv[$temp_var][] = $news;
        }

        file_put_contents($temp_file, serialize($vv[$temp_var]));

        /*
          $ctpl -> tpl_in_list('inf71.body', 'inf list', 'inf71.item', $_tNews);
          $ctpl -> ins_page('body', $_FVar, 'inf71.body');

          $fp = fopen($_FCash, 'w');
          fwrite($fp, '<?php
          $ClassTemplate -> ins_loads_vars(\'body\',
          array( \''.$_FVar.'\' => stripslashes(\''.addslashes(str_replace("'","\'",$ctpl->tpl_files['inf71.body'])).'\')) );
          ');
         */
        //echo $status;
    }
}
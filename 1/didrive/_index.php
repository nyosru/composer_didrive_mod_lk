<?php


    if( isset( $_POST['add'] ) )
    {

        if( !function_exists('f_csv') )
        require( $_SERVER['DOCUMENT_ROOT'].DS.'0.all'.DS.'f'.DS.'txt.php');

    $vv['warn'] .= 'sdfsdf';

    $ert = iconv( 'windows-1251','utf-8',file_get_contents( $_FILES['f']['tmp_name'] ));
    $e = f_csv($ert);

    //[0] => Вид +
    //[1] => Улица +
    //[2] => № дома +
    //[3] => Район +
    //[4] => Сдача
    //[5] => Застройщик
    //[6] => Жилой комплекс
    //[7] => Этаж
    //[8] => Этажность
    //[9] => Материал стен
    //[10] => Общая площадь
    //[11] => Площадь кухни
    //[12] => Отделка
    //[13] => Секция
    //[14] => № квартиры
    //[15] => Стоимость в тыс.рублях
    //[16] => Скидка от подрядчика
    //[17] =>     

    $h = 0;
    $a = array();
    
        foreach( $e as $k => $v )
        {
            if( $h == 0 )
            {
            $h = 1;
            }
            else
            {
                
            //Поле 	Тип 	Сравнение 	Атрибуты 	Ноль 	По умолчанию 	Дополнительно 	Действие
            //id 	int(7) 			Нет 		auto_increment 	Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            //agent 	int(3) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + d 	date 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + t 	time 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + type
            // + otdelka 	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + pl_k 	double(5,1) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + pl_all 	double(5,1) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + stens 	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + etag 	int(11) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + etagnost 	int(11) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + house 	varchar(10) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + street 	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + raion 	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + city 	varchar(150) 	utf8_general_ci 		Нет 	Тюмень 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            // + gilkompleks 	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
            //status                

                if( !isset( $v[1]{1} ) )
                continue;
                
            $aa = array(
                'd' => 'NOW'
                ,'t' => 'NOW'
                ,'type' => $v[0]
                ,'street' => $v[1]
                ,'raion' => $v[3]
                ,'gilkompleks' => $v[6]
                ,'otdelka' => $v[12]
                ,'pl_k' => $v[11]
                ,'pl_all' => $v[10]
                ,'stens' => $v[9]
                ,'etag' => $v[7]
                ,'etagnost' => $v[8]
                ,'house' => $v[2]
                ,'sdacha' => $v[4]
                ,'zastroi' => $v[5]
                ,'secciya' => $v[13]
                ,'price' => $v[15]
                ,'skidka' => $v[16]
                );

            $a[] = $aa;
            }
        }
        
    // echo '<pre>'; print_r($a); echo '</pre>';
    // echo '<pre>'; print_r( $e ); echo '</pre>';

    // $status = '';
    $db->sql_query('DELETE FROM `nedvig` WHERE `agent` = \''.$_SESSION['dilogin'].'\';');
    SqlMind_insert_mnogo( $db, 'nedvig', array(
        'agent' => $_SESSION['dilogin'], // 	int(3) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'd' => 'NOW', // date 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	't' => 'NOW', // time 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'type' => 1, // varchar(5) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'otdelka' => 1, // varchar(150) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'pl_k' => 1, //  	double(5,1) 			Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'pl_all'  => 1, //  	double(5,1) 			Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'stens' => 1, //  	varchar(150) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'etag' => 1, //  	int(11) 			Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'etagnost' => 1, //  	int(11) 			Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'house' => 1, //  	varchar(10) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'street' => 1, //  	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'raion' => 1, //  	varchar(150) 	utf8_general_ci 		Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'city' => 1, //  	varchar(150) 	utf8_general_ci 		Да 	tmn 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'gilkompleks' => 1, //  	varchar(150) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'sdacha' => 1, //  	varchar(20) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'zastroi' => 1, //  	varchar(50) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'secciya' => 1, //  	varchar(50) 	utf8_general_ci 		Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'price' => 1, //  	int(5) 			Нет 			Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'skidka' => 1, //  	int(5) 			Да 	NULL 		Browse distinct values 	Изменить 	Уничтожить 	Первичный 	Уникальное 	Индекс 	ПолнТекст
	'status' => 1 // 
        ), $a, 'da' );
    // echo $status;

    $vv['warn'] = 'готово';
    
    }

    
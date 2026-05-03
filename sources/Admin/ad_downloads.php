<?php

/*
+--------------------------------------------------------------------------
|   Download manager
|   ========================================
|   by Parmeet Singh (Improved By Ryan Ong and Brandon Farber)
|
|	Extended by bfarber for use with Download System
|     (c) 2001,2002,2003 IBForums
|	{c} 2003 Bfarber
|   	http://www.phpwiz.net
|	http://bfarber.com
|   ========================================
|   Web: http://www.phpwiz.net
|   Email: parmeet@emirates.net.ae
|   IBFORUMS: Licence Info: phpib-licence@ibforums.com
+---------------------------------------------------------------------------
|   ========================================
|   Web: http://bfarber.net
|   Email: bfarber@bfarber.com
+---------------------------------------------------------------------------
|
|
|   > Admin Forum function
|   > Module written by Parmeet
|   > Extended by bfarber for use with Download System
|   > Date started: 23th July 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


$idx = new ad_downloads();


class ad_downloads {

	var $base_url;

	function __construct() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		switch($IN['code'])
		{
		    case 'showaddcat':
				$this->show_cat('add');
				break;

			case 'showdelcat':
				$this->show_cat('del');
				break;

			case 'showeditcat':
				$this->show_cat('edit');
				break;

			case 'showeditcat1':
				$this->show_cat('edit1');
				break;

			case 'doaddcat':
				$this->do_cat('add');
				break;

			case 'dodelcat':
				$this->do_cat('del');
				break;

			case 'doeditcat':
				$this->do_cat('edit');
				break;

			case 'switch':
				$this->switch_download( );
				break;

			case 'settings':
				$this->show_edit_vars( );
				break;

			case 'do_switch':
				$this->save_config( array( 'd_section_close'));
				break;
				
            	case 'editvars':
                		$this->edit_vars( );
                		break;
                
			case 'reorder':
				$this->reorder_form();
				break;

			case 'doreorder':
				$this->do_reorder();
				break;

            default:
				$this->main_screen( );
				break;
		}
		
	}

	
	//-------------------------------------------------------------
	//
	// Save config. Does the hard work, so you don't have to.
	//
	//--------------------------------------------------------------
	
	function save_config( $new )
	{
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP, $_POST;
		
		$master = array();
		
		if ( is_array($new) )
		{
			if ( count($new) > 0 )
			{
				foreach( $new as $field )
				{
				
					// Handle special..
					
					if ($field == 'img_ext' or $field == 'avatar_ext')
					{
						$_POST[ $field ] = preg_replace( "/[\.\s]/", "" , $_POST[ $field ] );
						$_POST[ $field ] = preg_replace( "/,/"     , '|', $_POST[ $field ] );
					}
					else if ($field == 'coppa_address')
					{
						$_POST[ $field ] = nl2br( $_POST[ $field ] );
					}
					
					$_POST[ $field ] = preg_replace( "/'/", "&#39;", stripslashes($_POST[ $field ]) );
				
					$master[ $field ] = stripslashes($_POST[ $field ]);
				}
				
				$ADMIN->rebuild_config($master);
			}
		}
		
		$ADMIN->save_log("Обновление настроек файлового архива, Back Up создан");
		
		$ADMIN->done_screen("Конфигурация файлового архива обновлена", "Главная страница файлового архива", "act=downloads" );
		
		
		
	}

 
 	function show_cat( $type = "add" ) {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		if( $type == "add" ) {
			$a_array   = array( );
			$a_array[] = array( 1 , "Да" );
			$a_array[] = array( 0 , "Нет" );

			$s_array   = array( );
			$s_array[] = array( 1 , "Да" );
			$s_array[] = array( 0 , "Нет" );

			$sn_array   = array( );
			$sn_array[] = array( 1 , "Да" );
			$sn_array[] = array( 0 , "Нет" );

			$as_array   = array( );
			$as_array[] = array( 1 , "Да" );
			$as_array[] = array( 0 , "Нет" );

			$DB->query( "SELECT * from ibf_files_cats WHERE sub=0" );
			$e_array   = array( );
			$e_array[] = array( 0 , "Не добавлять" );
			while( $row = $DB->fetch_row( ) ) {
				$e_array[] = array( $row['cid'] , $row['cname'] );

			}

		$DB->query( "SELECT * FROM ibf_forums" );
		$tt_array   = array( );
		$tt_array[] = array( 0 , "<b>Не использовать</b>" );
		while( $row = $DB->fetch_row( ) ) {
			$tt_array[] = array( $row['id'] , "--{$row['name']}" );

		}


			$ADMIN->page_title = "Создание категории файлового архива!";

			$ADMIN->page_detail = "На этой странице Вы можете создать категорию для Вашего файлового архива.";

			//+-------------------------------

			$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'doaddcat' ),
													  2 => array( 'act'   , 'downloads'     ),
									     	)      );

			$ADMIN->html .= $SKIN->start_table( "Создание категории" );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Название</b>" ,
													  $SKIN->form_input("cname","","text"),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Описание (не обязательно)</b>" ,
													  $SKIN->form_input("cdesc","","text"),
											     )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории на главной странице категории?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_dropdown("dis_screen_cat",$s_array,"text"),
											     )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории, на странице скачивания файла?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_dropdown("dis_screen",$s_array,"text"),
											     )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить предварительную проверку файлов модератором перед допуском к скачиванию?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_dropdown("authorize",$as_array,"text"),
											     )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Форум, в котором будет автоматически создаваться тема поддержки для этого файла?</b><br />--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции какую-то опцию, отличную от \"Подкатегория\", то эта установка будет проигнорирована." ,
												  $SKIN->form_dropdown("fordaforum",$tt_array,"text"),
											    )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Показывать в этой категории заметку Администратора?</b>" ,
												  $SKIN->form_dropdown("show_notes",$sn_array,"text"),
											    )		);


			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Введите текст заметки для этой категории:</b>" ,
													  $SKIN->form_textarea("cnotes",""),
											     )		);



			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Сделать эту категорию доступной?</b>" ,
													  $SKIN->form_dropdown("copen",$a_array,"text"),
											     )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Выберите категорию для добавления этой категории в неё, в виде подкатегории.</b><br>Если Вы не хотите назначать эту категорию подкатегорией, выберите 'Не добавлять'." ,
													  $SKIN->form_dropdown("sub",$e_array,"text"),
									 	    )      );

			$ADMIN->html .= $SKIN->end_form("Создать категорию");

			$ADMIN->html .= $SKIN->end_table( );

			$ADMIN->output( );
			}
		elseif( $type == "del" ) {

			$ADMIN->page_title = "Удаление категории!";

			$ADMIN->page_detail = "Здесь Вы можете удалять Ваши категории.";


			$DB->query( "SELECT * from ibf_files_cats" );
			$d_array   = array( );
			$w_array   = array( );

			while( $row = $DB->fetch_row( ) ) {
				$d_array[] = array( $row['cid'] , $row['cname'] );
				$w_array[] = array( $row['cid'] , $row['cname'] );
			}
			//+-------------------------------

			$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'dodelcat' ),
													  2 => array( 'act'   , 'downloads'     ),
									     	)      );

			$ADMIN->html .= $SKIN->start_table( "Удаление категории" );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Выберите категорию для удаления</b>" ,
													  $SKIN->form_dropdown("del",$d_array,"text"),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>В какую категорию переместить файлы из этой категории?</b>" ,
													  $SKIN->form_dropdown("trans",$w_array,"text"),
											     )      );

			$ADMIN->html .= $SKIN->end_form("Удалить категорию");

			$ADMIN->html .= $SKIN->end_table( );

			$ADMIN->output( );
		}
		elseif( $type == "edit" ) {

			$ADMIN->page_title = "Редактирование категории файлового архива!";

			$ADMIN->page_detail = "Здесь Вы можете редактировать категории Вашего файлового архива.";


			$DB->query( "SELECT * from ibf_files_cats" );
			$e_array   = array( );


			while( $row = $DB->fetch_row( ) ) {
				$e_array[] = array( $row['cid'] , $row['cname'] );
			}
			//+-------------------------------

			$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'showeditcat1' ),
													  2 => array( 'act'   , 'downloads'     ),
									     	)      );

			$ADMIN->html .= $SKIN->start_table( "Редактирование категории" );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Выберите категорию для редактирования</b>" ,
													  $SKIN->form_dropdown("cid",$e_array,"text"),
									 	    )      );

			$ADMIN->html .= $SKIN->end_form("Редактировать");

			$ADMIN->html .= $SKIN->end_table( );

			$ADMIN->output( );

		}
		elseif( $type == "edit1" ) {

			if( $IN['cid'] == "" ) {
				$ADMIN->error("Вы не выбрали категорию...");
			}

			$DB->query( "SELECT * from ibf_files_cats WHERE sub=0" );
			$e_array   = array( );
			$e_array[] = array( 0 , "Не добавлять" );
			while( $row = $DB->fetch_row( ) ) {
				$e_array[] = array( $row['cid'] , $row['cname']);

			}

		$DB->query( "SELECT * FROM ibf_forums" );
		$tt_array   = array( );
		$tt_array[] = array( 0 , "<b>Не создавать</b>" );
		while( $row = $DB->fetch_row( ) ) {
			$tt_array[] = array( $row['id'] , "--{$row['name']}" );

		}


			$ADMIN->page_title = "Редактирование категории файлового архива!";

			$ADMIN->page_detail = "Здесь Вы можете редактировать категории Вашего файлового архива.";


			$DB->query( "SELECT * FROM ibf_files_cats WHERE cid = " . $IN['cid'] );

			$row = $DB->fetch_row( );



			$auth = $row['authorize'] == 1 ? 1 : 0;
			$dis = $row['dis_screen'] == 1 ? 1 : 0;
			$notes = $row['show_notes'] == 1 ? 1 : 0;

			$open = $row['open'] == 1 ? 1 : 0;
			//+-------------------------------

			$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'doeditcat' ),
													  2 => array( 'act'   , 'downloads' ),
									     	)      );
			$a = array( );
			$a[] = array('cid', $IN['cid']);


			$ADMIN->html .= $SKIN->form_hidden($a);

			$ADMIN->html .= $SKIN->start_table( "Редактирование категории" );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Название</b>" ,
													  $SKIN->form_input("cname",$row['cname'],"text"),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Описание (не обязательно)</b>" ,
													  $SKIN->form_input("cdesc",$row['cdesc'],"text"),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории на главной странице категории?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_yes_no("dis_screen_cat", $dis),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории, на странице скачивания файла?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_yes_no("dis_screen", $dis),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить предварительную проверку файлов модератором перед допуском к скачиванию?</b><br>--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции опцию Да или Нет, то эта установка будет проигнорирована." ,
													  $SKIN->form_yes_no("authorize", $auth),
									 	    )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Форум, в котором будет автоматически создаваться тема поддержки для этого файла?</b><br />--Примечание: Если Вы в настройках архива, в глобальных установках выбрали для этой секции какую-то опцию, отличную от \"Подкатегория\", то эта установка будет проигнорирована." ,
												  $SKIN->form_dropdown("fordaforum",$tt_array, $row['fordaforum']),
											    )		);

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Показывать в этой категории заметку Администратора?" ,
												  $SKIN->form_yes_no("show_notes", $notes),
											    )		);


			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Введите текст заметки для этой категории:</b>" ,
													  $SKIN->form_textarea("cnotes",$row['cnotes']),
											     )		);



			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Выберите категорию для добавления этой категории в неё, в виде подкатегории.</b><br>Если Вы не хотите назначать эту категорию подкатегорией, выберите 'Не добавлять'." ,
													  $SKIN->form_dropdown("sub",$e_array,$row['sub']),
									 	    )      );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Сделать эту категорию доступной?</b>" ,
													  $SKIN->form_yes_no("copen", $open),
									 	    )      );

			$ADMIN->html .= $SKIN->end_form("Сохранить изменения");

			$ADMIN->html .= $SKIN->end_table( );

			$ADMIN->output( );

		}
	}
	//+---------------------------------------------------------------------------------
	//
	// RE-ORDER CATEGORY
	//
	//+---------------------------------------------------------------------------------
	function reorder_form() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		$ADMIN->page_title = "Пересортировка категорий";
		$ADMIN->page_detail  = "Для пересортировки категорий, выберите номер позиции, в выпадающем меню, рядом с каждой категорией и нажмите кнопку Сохранить изменения.";

		$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'doreorder'),
									2 => array( 'act'   , 'downloads'     ),
											) );


		$SKIN->td_header[] = array( "Позиция"       , "10%" );
		$SKIN->td_header[] = array( "Название категории"   , "90%" );

		$ADMIN->html .= $SKIN->start_table( "Ваши категории" );

		$cats   = array();

		$DB->query("SELECT * from ibf_files_cats WHERE cid > 0 ORDER BY position ASC");
		while ($r = $DB->fetch_row())
		{
			$cats[] = $r;
		}


		// Build up the drop down box

		$form_array = array();

		for ($i = 1 ; $i <= count($cats) ; $i++ )
		{
			$form_array[] = array( $i , $i );
		}


		$last_cat_id = -1;

		foreach ($cats as $c)
		{

			$ADMIN->html .= $SKIN->add_td_row( array(  $SKIN->form_dropdown( 'POS_'.$c['cid'], $form_array, $c['position'] ),
													   $c['cname'],
											 ), 'catrow'     );
			$last_cat_id = $c['cid'];




				if ($r['category'] == $last_cat_id)
				{
					$ADMIN->html .= $SKIN->add_td_row( array(
															   '&nbsp;',
															   "<b>".$r['cname']."</b><br>".$r['cdesc'],
															   $r['posts'],
															   $r['topics'],
													 )      );
				}
				}

		$ADMIN->html .= $SKIN->end_form("Сохранить изменения");

		$ADMIN->html .= $SKIN->end_table();

		$ADMIN->output();

	}

	//+---------------------------------------------------------------------------------

	function do_reorder() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		$cat_query = $DB->query("SELECT cid from ibf_files_cats");

		while ( $r = $DB->fetch_row($cat_query) )
		{
			$order_query = $DB->query("UPDATE ibf_files_cats SET position='".$IN[ 'POS_' . $r['cid'] ]."' WHERE cid='".$r['cid']."'");
		}

		$ADMIN->done_screen("Категории пересортированы", "Администрирование файлового архива", "act=downloads" );
		
		
	}
	function do_cat( $type = "add" ) {

		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		if( $type == "add" ) {
			if( $IN['cname'] == "" ) {
				$ADMIN->error("Вы не заполнили поле - Название...");
			}

			$DB->query( "SELECT MAX(cid) as mid FROM ibf_files_cats" );
			$row = $DB->fetch_row( );

			$last_id = $row['mid'] + 1;
			if( $IN['sub'] == 0 || $IN['sub'] == "" ) {
				$DB->query( "SELECT cname FROM ibf_files_cats WHERE cname = '" . $IN['cname'] . "' AND sub = 0" );
			}
			else {
				$DB->query( "SELECT cname FROM ibf_files_cats WHERE cname = '" . $IN['cname'] . "' AND sub = '" . $IN['sub'] . "'" );
			}

			if( $DB->get_num_rows( ) > 0 ) {
				$ADMIN->error("Такое название существует...");
			}

			$DB->query( "SELECT cid FROM ibf_files_cats WHERE cid = '" . $IN['sub'] . "'" );

			if( $DB->get_num_rows( ) > 0 ) {
				$ADMIN->error("ID выбранной категории не существует или эта категория уже представлена в виде подкатегории...");
			}

			$DB->query( "INSERT INTO ibf_files_cats ( cid , sub , cname , cdesc , copen, dis_screen, dis_screen_cat, authorize, fordaforum, show_notes, cnotes ) VALUES( '$last_id' , '" . $IN['sub'] . "' , '" . $IN['cname'] . "' , '" . $IN['cdesc'] . "' , '" . $IN['copen'] . "', '" . $IN['dis_screen'] . "', '" . $IN['dis_screen_cat'] . "', '" . $IN['authorize'] ."', '" . $IN['fordaforum'] ."', '" . $IN['show_notes'] ."', '" . $IN['cnotes'] ."'  )" );

			$ADMIN->done_screen("Категория создана", "Администрирование файлового архива", "act=downloads" );
		}
		if( $type == "edit" ) {
			if( $IN['cid'] == "" ) {
				$ADMIN->error("Вы не выбрали категорию...");
			}
			elseif( $IN['cname'] == "" ) {
				$ADMIN->error("Вы не указали название категории...");
			}

			$DB->query( "UPDATE ibf_files_cats SET cname = '" . $IN['cname'] . "' , sub = '" . $IN['sub'] . "' , cdesc = '" . $IN['cdesc'] . "' , copen = '" . $IN['copen'] . "', dis_screen = '" . $IN['dis_screen'] . "', dis_screen_cat = '" . $IN['dis_screen_cat'] . "', authorize = '" .$IN['authorize'] . "', fordaforum = '" . $IN['fordaforum'] . "', show_notes = '" . $IN['show_notes'] . "', cnotes = '" . $IN['cnotes'] . "'  WHERE cid = " . $IN['cid'] );

			$ADMIN->done_screen("Категория отредактирована", "Администрирование файлового архива", "act=downloads" );
		}
		if( $type == "del" ) {
			if( $IN['del'] == "" ) {
				$ADMIN->error("Вы не выбрали категорию...");
			}
			if( $IN['trans'] == "" ) {
				$ADMIN->error("Категория, в которую Вы хотите переместить файлы, не определена");
			}
			$DB->query( "UPDATE ibf_files SET cat = '" . $IN['trans'] . "' WHERE cat = '" . $IN['del'] . "'" );
			$DB->query( "DELETE FROM ibf_files_cats WHERE cid = '" . $IN['del'] . "'" );

			$ADMIN->done_screen("Категория удалена и файлы перемещены!", "Администрирование файлового архива", "act=downloads" );
		}
	}

	function switch_download( ) {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
        if (!$INFO['d_section_close']) {
            $status = "включён";
        } else {
            $status = "выключен";
        }
        $ADMIN->page_title   = "Настройки файлового архива (Вкл/Выкл архива)";
        $ADMIN->page_detail  = "Сейчас архив ".$status;
		$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'do_switch' ),
												  2 => array( 'act'   , 'downloads'     ),
									     )      );

		$ADMIN->html .= $SKIN->start_table( "Настройки" );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Выключить файловый архив?</b>" ,
												  $SKIN->form_yes_no("d_section_close", $INFO['d_section_close'] )
									     )      );
		$ADMIN->html .= $SKIN->end_form("Сохранить изменения");

		$ADMIN->html .= $SKIN->end_table( );

		$ADMIN->output( );
	}

	function show_edit_vars(  ) {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		$o_array   = array( );
		$o_array[] = array( 1 , "Да" );
		$o_array[] = array( 0 , "Нет" );

		$ADMIN->page_title = "Редактирование настроек файлового архива";

		$ADMIN->page_detail = "Здесь Вы можете редактировать настройки Вашего файлового архива";

		//+-------------------------------

		$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'editvars' ),
												  2 => array( 'act'   , 'downloads'     ),
									     )      );

		$ADMIN->html .= $SKIN->start_table( "Редактирование настроек" );
		$max_size=ini_get('upload_max_filesize');


		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Полный путь к директории для загрузки файлов</b><br>Не забудьте указать в конце слэш" ,
												  $SKIN->form_input("d_download_dir",$INFO['d_download_dir'],"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Полный путь к директории для загрузки скриншотов</b><br>Не забудьте указать в конце слэш" ,
												  $SKIN->form_input("d_screen_dir",$INFO['d_screen_dir'],"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Полный URL директории для загрузки файлов</b><br>Не забудьте указать в конце слэш</b>" ,
												  $SKIN->form_input("d_download_url",$INFO['d_download_url'],"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b><b>Полный URL директории для загрузки скриншотов</b><br>Не забудьте указать в конце слэш" ,
												  $SKIN->form_input("d_screen_url",$INFO['d_screen_url'],"text"),
											    )		);


        $ADMIN->html .= $SKIN->add_td_row( array( "<b>Загружать файлы на Ваш сервер?</b>" ,
												  $SKIN->form_yes_no("d_upload",$INFO['d_upload'] ),
											    )		);
											    
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Разрешить использование сторонних ссылок на файлы?<br>(Скачивание файлов с других серверов... Не личьте ссылки, без разрешения хозяев)</b>" ,
												  $SKIN->form_yes_no("d_linking", $INFO['d_linking'] )
									            )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Максимальный размер загружаемого в архив файла?</b><br />В Вашем файле php.ini, переменная upload_max_filesize установлена на ".$max_size."<br />В Кб(килобайтах)" ,
												  $SKIN->form_input("d_max_dwnld_size",$INFO['d_max_dwnld_size'],"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Разрешить загрузку скриншотов?</b>" ,
												  $SKIN->form_yes_no("d_screenshot_allowed", $INFO['d_screenshot_allowed'] )
									            )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Установить загрузку скриншотов обязательным требованием?</b>" ,
												  $SKIN->form_yes_no("d_screenshot_required", $INFO['d_screenshot_required'] )
											    )		);


		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Максимальный размер загружаемого в архив файла скриншота?</b><br>В Кб(килобайтах)" ,
												  $SKIN->form_input("d_screen_max_dwnld_size",$INFO['d_screen_max_dwnld_size'],"text"),
											    )		);

		// Reconvert array into a text string
		$dext = "";
        foreach( $INFO['d_allowable_ext'] as $value){
            $dext .= $value."|";
        }
        $dext = substr($dext ,0 ,-1);	
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Допустимые расширения для загружаемых файлов</b><br>Необходимо разделять через символ '|', пример \".txt|.zip\"" ,
												  $SKIN->form_input("d_allowable_ext",$dext,"text"),
											    )		);
		// Reconvert array into a text string
		$sext = "";
        foreach( $INFO['d_screenshot_ext'] as $value){
            $sext .= $value."|";
        }
        $sext = substr($sext ,0 ,-1);	
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Допустимые расширения для загружаемых скриншотов</b><br>Необходимо разделять через символ '|', пример \".gif|.jpeg\"" ,
												  $SKIN->form_input("d_screenshot_ext",$sext,"text"),
											    )		);

		// Reconvert array into a text string

	if( $INFO['d_files_perpage'] ==""){
		$pages = "10|20|30|40|50";
	} else {
		$pages = "";
        foreach( $INFO['d_files_perpage'] as $value){
            $pages .= $value."|";
        }
        $pages = substr($pages ,0 ,-1);	
	}
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Опции выбора пользователями отображения кол-ва файлов за страницу, в выпадающем меню</b><br />Необходимо разделять через символ '|', пример \"10|20|30\"" ,
												  $SKIN->form_input("d_files_perpage",$pages,"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Кол-во отображаемых за страницу файлов, по умолчанию</b>" ,
												  $SKIN->form_input("d_perpage",$INFO['d_perpage'],"text"),
											    )		);


        $ADMIN->html .= $SKIN->add_td_row( array( "<b>Активизировать защиту файлов?(Это существенно увеличивает защиту файла, однако работает не на каждом сервере)</b>" ,
												  $SKIN->form_yes_no("d_force",$INFO['d_force'] ),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Предел скорости для скачивания файлов (Максимальная скорость скачиваемых файлов в Kb/s. Для этого, необходимо включить защиту файлов. Для отключения предела, введите 0)</b>" ,
												  $SKIN->form_input("d_speed",$INFO['d_speed'],"text"),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить автоматическое уменьшение (сжатие) скриншотов?</b><br /> " ,
										 $SKIN->form_checkbox( 'd_show_thumb', $INFO['d_show_thumb'] )."Показывать уменьшённые скриншоты? <br /> Размер ".$SKIN->form_simple_input( 'd_thumb_w', $INFO['d_thumb_w'] )." x ".$SKIN->form_simple_input( 'd_thumb_h', $INFO['d_thumb_h'] )
								 )      );					 
																								

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить автоматическую проверку ссылок на файлы и если какие-то ссылки являются битыми, отображать информацию об этом на странице скачивания файла?</b><br />(ПРИМЕЧАНИЕ: Если Ваш хостер не позволяет использовать fopen ресурс вне Вашей корневой директории, это не будет работать)" ,
												  $SKIN->form_yes_no("d_link_check", $INFO['d_link_check'] )
									            )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Разрешить пользователям оставлять комментарии к файлам?</b><br />(ПРИМЕЧАНИЕ: Не рекомендуется использовать это, если Вы включили автоматическое создание тем на форуме при загрузке файлов. Используйте что-нибудь одно.)" ,
												  $SKIN->form_yes_no("d_use_comments", $INFO['d_use_comments'] )
									            )      );

											    
        $ADMIN->html .= $SKIN->add_td_row( array( "<b>Разрешить добавление \"[название категории]\" перед заголовком автоматически создаваемой темы?</b><br />Это действует только в том случае, если Вы включили автоматическое создание тем, при загрузке файлов." ,
												  $SKIN->form_yes_no("d_cat_add",$INFO['d_cat_add'] ),
											    )		);

        $ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить автоматическое принятие файлов, которые загрузил Администратор?</b>" ,
												  $SKIN->form_yes_no("d_admin_auto",$INFO['d_admin_auto'] ),
											    )		);
                                                									
        $ADMIN->html .= $SKIN->add_td_row( array( "<b>Показывать в темах кол-во загруженных пользователем файлов?</b>" ,
												  $SKIN->form_yes_no("d_topic",$INFO['d_topic'] ),
											    )		);
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Показывать в темах кол-во скачанных пользователем файлов?</b>" ,
												  $SKIN->form_yes_no("d_downloads",$INFO['d_downloads'] ),
												)       );

			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить отображение главной заметки Администратора?</b><br />Примечание: Вы можете отключить эту функцию, но всё ещё использовать заметки Администратора для каждой категории, либо можете включить и использовать и то и другое." ,
												  $SKIN->form_yes_no("d_show_global_notes", $INFO['d_show_global_notes'] ),
											    )		);

		$return_nl = str_replace("<br />", "\n", $INFO['d_global_notes']);
			$ADMIN->html .= $SKIN->add_td_row( array( "<b>Введите текст главной заметки:</b>" ,
													  $SKIN->form_textarea("d_global_notes",$return_nl),
											     )		);


		//-----------------------------------------------------------------------------------------------------------						 
		
		$ADMIN->html .= $SKIN->end_table( );
		$ADMIN->html .= $SKIN->start_table("Глобальные установки или установки для подкатегорий");
		
		//-----------------------------------------------------------------------------------------------------------


		$ds_array   = array( );
		$ds_array[] = array( 0 , "Нет" );
		$ds_array[] = array( 1 , "Да" );
		$ds_array[] = array( 2 , "Подкатегория" );

		$as_array   = array( );
		$as_array[] = array( 0 , "Нет" );
		$as_array[] = array( 1 , "Да" );
		$as_array[] = array( 2 , "Подкатегория" );


		$DB->query( "SELECT * FROM ibf_forums" );
		$e_array   = array( );
		$e_array[] = array( 0 , "<b>Не создавать</b>" );
		$e_array[] = array( 'percat' , "<b>Подкатегория</b>" );
		while( $row = $DB->fetch_row( ) ) {
			$e_array[] = array( $row['id'] , "--{$row['name']}" );

		}



		$ADMIN->html .= $SKIN->add_td_basic( 'Эта секция, - для установки единых настроек для всех категорий.<br>&nbsp;&nbsp;Если Вы хотите сконфигурировать нижеуказанные настройки для каждой категории в отдельности, а эти настройки задействовать только для подкатегорий, выбирайте в нужных секциях настроек, опцию \'Подкатегория\'', 'left', 'catrow2' );


 		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить предварительную проверку файлов модератором, перед допуском к скачиванию?" ,
												  $SKIN->form_dropdown("d_authorize",$as_array,$INFO['d_authorize']),
									     )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории на главной странице категорий?</b>" ,
												  $SKIN->form_dropdown("d_dis_screen_cat",$ds_array,$INFO['d_dis_screen_cat']),
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать автоматически скриншоты в этой категории, на странице скачивания файла?</b>" ,
												  $SKIN->form_dropdown("d_dis_screen",$ds_array,$INFO['d_dis_screen']),
											    )		);


		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Форум, в котором будет автоматически создаваться тема поддержки для этого файла.</b><br>Если Вы хотите отключить автоматическое создание тем, выберите пункт Не создавать, а при необходимости конфигурирования данной функции для каждой категории в отдельности, выберите \"Подкатегория\"" ,
												  $SKIN->form_dropdown("d_create_topic",$e_array,$INFO['d_create_topic']),
											    )		);
		$ADMIN->html .= $SKIN->end_table( );


		$ADMIN->html .= $SKIN->end_form("Сохранить настройки");

		$ADMIN->output( );
	}

	function edit_vars( ) {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP, $_POST;

		if( $IN['d_max_dwnld_size'] == "" ) {
			$ADMIN->error("Вы не указали максимальный размер загружаемых файлов...");
		}
		elseif( $IN['d_download_dir'] == "" ) {
			$ADMIN->error("Вы не указали путь к директории загружаемых файлов...");
		}
		elseif( $IN['d_download_url'] == "" ) {
			$ADMIN->error("Вы не указали URL директории загружаемых файлов...");
		}
		elseif( $IN['d_allowable_ext'] == "" ) {
			$ADMIN->error("Вы не указали допустимые расширения файлов...");
		}
		elseif( $IN['d_files_perpage'] == "" ) {
			$ADMIN->error("Вы не определили опции выбора кол-ва отображаемых за страницу файлов для выпадающего меню...");
		}

		$fix_notes = nl2br($_POST['d_global_notes']);
		$content = "<?php\n";
		$allow1 = preg_replace( "/&#124;/", "|", stripslashes($_POST['d_allowable_ext']) );
		$allow1 = explode('|',$allow1);
		$allowable = "";
        foreach( $allow1 as $value){
            $allowable .= "'".strtolower($value)."', ";
        }
        $allowable = substr($allowable ,0 ,-2);
		$content .= "\$INFO['d_allowable_ext']         = array($allowable);\n";
		$screen = preg_replace( "/&#124;/", "|", stripslashes($_POST['d_screenshot_ext']) );
		$screen = explode('|',$screen);
		$screenshot = "";
        foreach( $screen as $value){
            $screenshot .= "'".$value."', ";
        }
        $screenshot = substr($screenshot ,0 ,-2);
		$content .= "\$INFO['d_screenshot_ext']        	= array({$screenshot});\n";

		$filesperpage = preg_replace( "/&#124;/", "|", stripslashes($_POST['d_files_perpage']) );
		$filesperpage = explode('|',$filesperpage);
		$theoptions = "";
        foreach( $filesperpage as $value){
            $theoptions .= "'".$value."', ";
        }
        $theoptions = substr($theoptions ,0 ,-2);
		$content .= "\$INFO['d_files_perpage']        	= array({$theoptions});\n";
		$content .= "\$INFO['d_max_dwnld_size']        	= {$IN['d_max_dwnld_size']};\n";
		$content .= "\$INFO['d_screen_max_dwnld_size'] 	= {$IN['d_screen_max_dwnld_size']};\n";
		$content .= "\$INFO['d_download_dir']          	= '{$IN['d_download_dir']}';\n";
		$content .= "\$INFO['d_download_url']          	= '{$IN['d_download_url']}';\n";
		$content .= "\$INFO['d_screen_dir']            	= '{$IN['d_screen_dir']}';\n";
		$content .= "\$INFO['d_screen_url']            	= '{$IN['d_screen_url']}';\n";
		$content .= "\$INFO['d_screenshot_allowed']     = '{$IN['d_screenshot_allowed']}';\n";
		$content .= "\$INFO['d_screenshot_required']    = '{$IN['d_screenshot_required']}';\n";
		$content .= "\$INFO['d_authorize']		      = {$IN['d_authorize']};\n";
		$content .= "\$INFO['d_speed']               	= '{$IN['d_speed']}';\n";
		$content .= "\$INFO['d_force']               	= {$IN['d_force']};\n";
		$content .= "\$INFO['d_linking']               	= {$IN['d_linking']};\n";
		$content .= "\$INFO['d_link_check']               	= '{$IN['d_link_check']}';\n";
		$content .= "\$INFO['d_use_comments']		= {$IN['d_use_comments']};\n";
		$content .= "\$INFO['d_upload']               	= {$IN['d_upload']};\n";
		$content .= "\$INFO['d_perpage']		      = '{$IN['d_perpage']}';\n";
		$content .= "\$INFO['d_create_topic']		= '{$IN['d_create_topic']}';\n";
		$content .= "\$INFO['d_topic']                 	= {$IN['d_topic']};\n";
		$content .= "\$INFO['d_downloads']			= {$IN['d_downloads']};\n";
		$content .= "\$INFO['d_dis_screen_cat']		= {$IN['d_dis_screen_cat']};\n";
		$content .= "\$INFO['d_dis_screen']			= {$IN['d_dis_screen']};\n";
		$content .= "\$INFO['d_cat_add']			= {$IN['d_cat_add']};\n";
		$content .= "\$INFO['d_admin_auto']			= {$IN['d_admin_auto']};\n";
		$content .= "\$INFO['d_show_thumb']              = '{$IN['d_show_thumb']}';\n";
		$content .= "\$INFO['d_thumb_w']               	= '{$IN['d_thumb_w']}';\n";
		$content .= "\$INFO['d_thumb_h']               	= '{$IN['d_thumb_h']}';\n";
		$content .= "\$INFO['d_show_global_notes']      = '{$IN['d_show_global_notes']}';\n";
		$content .= "\$INFO['d_global_notes']           = '{$fix_notes}';\n";

		$content .= "?".">\n";

		$open = fopen( $root_path."downloads_config.php" , "w" );
		fwrite( $open , $content );
		fclose( $open );

		$ADMIN->done_screen("Настройки успешно сохранены", "Администрирование файлового архива", "act=downloads" );
	}
	

	function main_screen( ) {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		$DB->query( "SELECT cid FROM ibf_files_cats" );
		$cats 	 = $DB->get_num_rows( );

		$DB->query( "SELECT id FROM ibf_files" );
		$scripts = $DB->get_num_rows( );

		$DB->query( "SELECT DISTINCT author FROM ibf_files" );

		$authors = "";

		while( $row = $DB->fetch_row( ) ) {
			if( $authors == "" ) {
				$authors .= $row['author'];
			}
			else {
				$authors .= " , " . $row['author'];
			}
		}

		$DB->query( "SELECT SUM( downloads ) as down FROM ibf_files" );
		$downs	 = $DB->fetch_row( );

		$DB->query( "SELECT sname FROM ibf_files ORDER BY downloads DESC LIMIT 0,1" );
		$maxd	 = $DB->fetch_row( );

		$DB->query( "SELECT sname FROM ibf_files ORDER BY views DESC LIMIT 0,1" );
		$maxv	 = $DB->fetch_row( );

		$ADMIN->page_title = "Добро пожаловать в панель администрирования файлового архива";

		$ADMIN->page_detail = "Это, главная страница администрирования файлового архива.";

		//+-------------------------------
		$SKIN->td_header[] = array( "&nbsp;", "50%" );
		$SKIN->td_header[] = array( "&nbsp;", "50%" );
		$ADMIN->html .= $SKIN->start_table( "Страница администрирования файлового архива" );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Кол-во категорий в архиве</b>" , $cats)      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Кол-во файлов в архиве</b>" , $scripts)		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Кол-во скачиваний файлов</b>" , $downs['down'])		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Индивидуальные авторы</b>" ,$authors
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Самый популярный файл по кол-ву скачиваний</b>" ,
												  $maxd['sname']
											    )		);

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Самый популярный файл по кол-ву просмотров</b>" ,
												  $maxv['sname']
											    )		);

		$ADMIN->html .= $SKIN->end_table();

		$ADMIN->output();

	}
}


?>

<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v1.2
|   ========================================
|   by Matthew Mecham
|	Modified to work with Download System
|	by bfarber
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Custom download field functions
|   > Module written by Matt Mecham
|   > Extended by bfarber (http://bfarber.com | bfarber@bfarber.com)
|   > Date started: 24th June 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/




$idx = new ad_dfields();


class ad_dfields {

	var $base_url;

	function __construct() {
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		//---------------------------------------
		// Kill globals - globals bad, Homer good.
		//---------------------------------------
		
		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );
		
		foreach ( $tmp_in as $k => $v )
		{
			unset($$k);
		}
		
		//---------------------------------------

		switch($IN['code'])
		{
			case 'add':
				$this->main_form('add');
				break;
				
			case 'doadd':
				$this->main_save('add');
				break;
				
			case 'edit':
				$this->main_form('edit');
				break;
				
			case 'doedit':
				$this->main_save('edit');
				break;
				
			case 'delete':
				$this->delete_form();
				break;
				
			case 'dodelete':
				$this->do_delete();
				break;
						
			default:
				$this->main_screen();
				break;
		}
		
	}
	
	
	
	//+---------------------------------------------------------------------------------
	//
	// Delete a group
	//
	//+---------------------------------------------------------------------------------
	
	function delete_form()
	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		if ($IN['id'] == "")
		{
			$ADMIN->error("Невозможно определить ID группы. Попробуйте снова.");
		}
		
		$ADMIN->page_title = "Удаление дополнительного поля";
		
		$ADMIN->page_detail = "Проверьте повторно то, что Вы собираетесь удалять действительно ненужное поле, т.к. <b>все данные этого поля будут утеряны!</b>.";
		
		
		//+-------------------------------
		
		$DB->query("SELECT ftitle, fid FROM ibf_files_custfields WHERE fid='".$IN['id']."'");
		
		if ( ! $field = $DB->fetch_row() )
		{
			$ADMIN->error("Невозможно ввыбрать ряд в базе данных");
		}
		
		$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , 'dodelete'  ),
												  2 => array( 'act'   , 'dfield'     ),
												  3 => array( 'id'    , $IN['id']   ),
									     )      );
									     
		
		
		//+-------------------------------
		
		$SKIN->td_header[] = array( "&nbsp;"  , "40%" );
		$SKIN->td_header[] = array( "&nbsp;"  , "60%" );
		
		//+-------------------------------
		
		$ADMIN->html .= $SKIN->start_table( "Подтверждение удаления" );
		
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Удаляемое дополнительное поле</b>" ,
												  "<b>".$field['ftitle']."</b>",
									     )      );
									     
		$ADMIN->html .= $SKIN->end_form("Удалить это поле");
										 
		$ADMIN->html .= $SKIN->end_table();
		
		$ADMIN->output();
			
			
	}
	
	function do_delete()
	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		if ($IN['id'] == "")
		{
			$ADMIN->error("Невозможно определить ID поля. Попробуйте снова.");
		}
		
		
		// Check to make sure that the relevant groups exist.
		
		$DB->query("SELECT ftitle, fid FROM ibf_files_custfields WHERE fid='".$IN['id']."'");
		
		if ( ! $row = $DB->fetch_row() )
		{
			$ADMIN->error("Невозможно определить ID удаляемого поля.");
		}
		
		$DB->query("ALTER TABLE ibf_files_custentered DROP field_{$row['fid']}");
		
		$DB->query("DELETE FROM ibf_files_custfields WHERE fid='".$IN['id']."'");
		
		$ADMIN->done_screen("Дополнительное поле архива удалено", "Дополнительные поля архива", "act=dfield" );
		
	}
	
	
	//+---------------------------------------------------------------------------------
	//
	// Save changes to DB
	//
	//+---------------------------------------------------------------------------------
	
	function main_save($type='edit')
	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP, $_POST;
		
		if ($IN['ftitle'] == "")
		{
			$ADMIN->error("Необходимо ввести название поля.");
		}
		
		if ($type == 'edit')
		{
			if ($IN['id'] == "")
			{
				$ADMIN->error("Невозможно определить id поля");
			}
			
		}
		
		$content = "";
		
		if ($_POST['fcontent'] != "")
		{
			$content = str_replace( "\n", '|', str_replace( "\n\n", "\n", trim($_POST['fcontent']) ) );
		}
		
		$db_string = array( 'ftitle'    => $IN['ftitle'],
						    'fcontent'  => stripslashes($content),
						    'ftype'     => $IN['ftype'],
						    'freq'      => $IN['freq'],
						    'fmaxinput' => $IN['fmaxinput'],
						    'fshow'     => $IN['fshow'],
						    'ftopic'    => $IN['ftopic'],
						  );
		
						  
		if ($type == 'edit')
		{
			$rstring = $DB->compile_db_update_string( $db_string );
			
			$DB->query("UPDATE ibf_files_custfields SET $rstring WHERE fid='".$IN['id']."'");
			
			$ADMIN->done_screen("Дополнительное поле архива отредактировано", "Дополнительные поля архива", "act=dfield" );
			
		}
		else
		{
			$rstring = $DB->compile_db_insert_string( $db_string );
			
			$DB->query("INSERT INTO ibf_files_custfields (" .$rstring['FIELD_NAMES']. ") VALUES (". $rstring['FIELD_VALUES'] .")");
			
			$new_id = $DB->get_insert_id();
			
			$DB->query("ALTER TABLE ibf_files_custentered ADD field_$new_id text default ''");
			
			$DB->query("OPTIMIZE TABLE ibf_files_custfields");
			
			$ADMIN->done_screen("Дополнительное поле создано", "Дополнительные поля архива", "act=dfield" );
		}
	}
	
	
	//+---------------------------------------------------------------------------------
	//
	// Add / edit group
	//
	//+---------------------------------------------------------------------------------
	
	function main_form($type='edit')
	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		if ($type == 'edit')
		{
			if ($IN['id'] == "")
			{
				$ADMIN->error("Невозможно выбрать id группы из базы данных. Попробуйте снова.");
			}
			
			$form_code = 'doedit';
			$button    = 'Сохранить изменения';
				
		}
		else
		{
			$form_code = 'doadd';
			$button    = 'Создать поле';
		}
		
		if ($IN['id'] != "")
		{
			$DB->query("SELECT * FROM ibf_files_custfields WHERE fid='".$IN['id']."'");
			$fields = $DB->fetch_row();
		}
		else
		{
			$fields = array();
		}
		
		if ($type == 'edit')
		{
			$ADMIN->page_title = "Редактирование дополнительного поля архива ".$fields['ftitle'];
		}
		else
		{
			$ADMIN->page_title = 'Создание нового поля архива';
			$fields['ftitle'] = '';
		}
		
		$ADMIN->page_detail = "Дважды проверьте введённые данные, перед подтверждением запроса.";
		
		
		
		
		$ADMIN->html .= $SKIN->start_form( array( 1 => array( 'code'  , $form_code  ),
												  2 => array( 'act'   , 'dfield'     ),
												  3 => array( 'id'    , $IN['id']   ),
									     )  );
									     
		$fields['fcontent'] = str_replace( '|', "\n", $fields['fcontent'] );
		
		//+-------------------------------
		
		$SKIN->td_header[] = array( "&nbsp;"  , "40%" );
		$SKIN->td_header[] = array( "&nbsp;"  , "60%" );
		
		//+-------------------------------
		
		$ADMIN->html .= $SKIN->start_table( "Настройки поля" );
		
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Название поля</b><br>Максимум: 200 символов" ,
												  $SKIN->form_input("ftitle", $fields['ftitle'] )
									     )      );
									     
	
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Тип поля</b>" ,
												  $SKIN->form_dropdown("ftype",
												  					   array(
												  					   			0 => array( 'text' , 'Текстовое поле' ),
												  					   			1 => array( 'drop' , 'Выпадающее меню' ),
												  					   			2 => array( 'area' , 'Текстовая область' ),
												  					   		),
												  					   $fields['ftype'] )
									     )      );
									     
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Максимальное кол-во вводимых символов (для текстового поля или области)</b>" ,
												  $SKIN->form_input("fmaxinput", $fields['fmaxinput'] )
									     )      );
									     
								     
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Содержимое (для выпадающего меню)</b><br>Одна установка на строку<br>Пример для поля 'Разрешение':<br>64=640x480<br>80=800x640<br>u=Не определено<br>Будет отображено так:<br><select name='pants'><option value='64'>640x480</option><option value='80'>800x640</option><option value='u'>Не определено</option></select><br>64, 80, или u загружено в базу данных. При отображении поля на странице формы загрузки/редактирования файла используется значение из пары (64=640x480, отображается как '640x480')" ,
												  $SKIN->form_textarea("fcontent", $fields['fcontent'] )
									     )      );
		
									     
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Сделать это поле обязательным для заполнения??</b>" ,
												  $SKIN->form_yes_no("freq", $fields['freq'] )
									     )      );
									     
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Отображать это поле и на странице редактирования файла?</b>" ,
												  $SKIN->form_yes_no("fshow", $fields['fshow'] )
									     )      );						     							     
		
		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Если Вы выбрали автоматическое создание тем при загрузке файлов, включать информацию этого поля в текст создаваемой темы?</b>" ,
												  $SKIN->form_yes_no("ftopic", $fields['ftopic'] )
									     )      );						     							     
		
									     
		$ADMIN->html .= $SKIN->end_form($button);
										 
		$ADMIN->html .= $SKIN->end_table();
		
		$ADMIN->output();
			
			
	}

	//+---------------------------------------------------------------------------------
	//
	// Show "Management Screen
	//
	//+---------------------------------------------------------------------------------
	
	function main_screen()
	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		$ADMIN->page_title = "Дополнительные поля архива";
		
		$ADMIN->page_detail = "Дополнительные поля архива, служат для добавления различных обязательных и не обязательных к заполнению полей, при загрузке, редактировании файлов. Вы можете также выбрать, добавлять информацию с дополнительного поля в автоматически создаваемую тему или нет.";
		
		$SKIN->td_header[] = array( "Название поля"    , "20%" );
		$SKIN->td_header[] = array( "Тип"           , "10%" );
		$SKIN->td_header[] = array( "Название переменной" , "20%" );
		$SKIN->td_header[] = array( "Обязательное"       , "10%" );
		$SKIN->td_header[] = array( "Видимое"         , "10%" );
		$SKIN->td_header[] = array( "ОТОБР. В ТЕМЕ"       , "10%" );
		$SKIN->td_header[] = array( "Редактировать"           , "10%" );
		$SKIN->td_header[] = array( "Удалить"         , "10%" );
		
		//+-------------------------------
		
		$ADMIN->html .= $SKIN->start_table( "Управление дополнительными полями архива" );
		
		$real_types = array( 'drop' => 'Выпадающее меню',
							 'area' => 'Текстовая область',
							 'text' => 'Текстовое поле',
						   );
		
		$DB->query("SELECT * FROM ibf_files_custfields");
		
		if ( $DB->get_num_rows() )
		{
			while ( $r = $DB->fetch_row() )
			{
			
				$hide   = '&nbsp;';
				$req    = '&nbsp;';
				$regi   = '&nbsp;';
				
				"<center><a href='{$ADMIN->base_url}&act=group&code=delete&id=".$r['g_id']."'>Удалить</a></center>";
				
				//-----------------------------------
				if ($r['fshow'] == 1)
				{
					$hide = '<center><span style="color:red">Да</span></center>';
				}
				//-----------------------------------
				if ($r['freq'] == 1)
				{
					$req = '<center><span style="color:red">Да</span></center>';
				}
				
				if ($r['ftopic'] == 1)
				{
					$regi = '<center><span style="color:red">Да</span></center>';
				}
				
				
				$ADMIN->html .= $SKIN->add_td_row( array( "<b>{$r['ftitle']}</b>" ,
														  "<center>{$real_types[$r['ftype']]}</center>",
														  "<center>field_".$r['fid']."</center>",
														  $req,
														  $hide,
														  $regi,
														  "<center><a href='{$ADMIN->base_url}&act=dfield&code=edit&id=".$r['fid']."'>Редактировать</a></center>",
														  "<center><a href='{$ADMIN->base_url}&act=dfield&code=delete&id=".$r['fid']."'>Удалить</a></center>",
											 )      );
											 
			}
		}
		else
		{
			$ADMIN->html .= $SKIN->add_td_basic("Нет полей", "center", "pformstrip");
		}
		
		$ADMIN->html .= $SKIN->add_td_basic("<a href='{$ADMIN->base_url}&act=dfield&code=add' class='fauxbutton'>СОЗДАТЬ НОВОЕ ПОЛЕ</a></center>", "center", "pformstrip");

		$ADMIN->html .= $SKIN->end_table();
		
		
		$ADMIN->output();
		
		
	}
	
		
}


?>
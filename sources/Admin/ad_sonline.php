<?php

/*
+--------------------------------------------------------------------------
|  Image/Text Online/Offline  Mod v3.3 by Shadow Fox (c) 2003
+---------------------------------------------------------------------------
|
|   > Image/Text Online/Offline  Mod v3.3
|   > Module written by Shadow Fox
|   > Date started: May 26th 2003
|   > Module Version Number: 1.0
+--------------------------------------------------------------------------
*/

$idx = new ad_sonline();


class ad_sonline {

	var $base_url;

		function __construct() {

		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		$tmp_in = array_merge( $_GET, $_POST, $_COOKIE );



		foreach ( $tmp_in as $k => $v )

		{

			unset($$k);

		};

		switch($IN['code'])
		{
                        case 'sonline':

				$this->sonline();

				break;

			case 'dosonline':

				$this->save_config( array ( 'on_status_color', 'off_status_color','on_status_image', 'off_status_image','status_type','status_set', 'status_prefix', ) );

				break;
			
			default:
				$this->sonline();
				break;
		}
		
	}	
	
	function sonline() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		$ADMIN->page_title = "Настройка статуса Online/Offline";
		$ADMIN->page_detail  = "Эта модификация, для отображения Online/Offline статуса пользователей, под аватаром пользователей в сообщениях форума
                                        <br><br>
                                        If you have any problems with this mod please see the support topic <a href='http://forums.ibplanet.com/index.php?&act=ST&f=50&t=7863'>here</a>
                                        <br><br>
                                        Автор модификации <a href='http://forums.ibplanet.com/index.php?act=Profile&CODE=03&MID=5'>Shadow Fox</a> - Модификация написана для <a href='http://ibplanet.com'>IBPlanet.com</a>";
		
                $ADMIN->html .= $SKIN->start_form( array( 					1 => array( 'code'  , 'dosonline'),
												2 => array( 'act'   , 'sonline'     ),
											) );
  
                $SKIN->td_header[] = array( ""       , "60%" );
                $SKIN->td_header[] = array( ""       , "40%" );


                $ADMIN->html .= $SKIN->start_table( "Настройка статуса Online/Offline" );

                $ADMIN->html .= $SKIN->add_td_basic( 'Основные настройки', 'left', 'pformstrip' );



                $ADMIN->html .= $SKIN->add_td_row( array( "<b>Включить отображение статуса Online/Offline?</b>" ,

										  $SKIN->form_yes_no( "status_set", $INFO['status_set'] )

								 )      );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Вид отображения статуса</b><br>(Можно использовать отображение в виде изображения или в виде текста)" ,



		$SKIN->form_dropdown( 'status_type', array( 0 => array( 'text', 'Текст' ), 1 => array( 'image' , 'Изображение' ), ), $INFO['status_type']  )

								 )      );

               $ADMIN->html .= $SKIN->add_td_row( array( "<b>Приставка для статусов</b><br>(Например, 'Статус:' <br>Можно не заполнять)" ,

										  $SKIN->form_input( "status_prefix", $INFO['status_prefix'] )

								 )      );
         
                $ADMIN->html .= $SKIN->add_td_basic( 'Настройки текста', 'left', 'pformstrip' );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Цвет для Online статуса</b><br>(По умолчанию используется чёрный цвет. Можете использовать hex коды.)" ,

										  $SKIN->form_input( "on_status_color", $INFO['on_status_color'] )

								 )      );

                $ADMIN->html .= $SKIN->add_td_row( array( "<b>Цвет для Offline статуса</b><br>(По умолчанию используется чёрный цвет. Можете использовать hex коды.)" ,

										  $SKIN->form_input( "off_status_color", $INFO['off_status_color'] )

								 )      );
         
               $ADMIN->html .= $SKIN->add_td_basic( 'Настройки изображения', 'left', 'pformstrip' );

		$ADMIN->html .= $SKIN->add_td_row( array( "<b>Изображение для Online статуса</b><br>(Просто введите название файла, например: Online.gif)" ,

										  $SKIN->form_input( "on_status_image", $INFO['on_status_image'] )

								 )      );

                $ADMIN->html .= $SKIN->add_td_row( array( "<b>Изображение для Offline статуса</b><br>(Просто введите название файла, например: Offline.gif)" ,

										  $SKIN->form_input( "off_status_image", $INFO['off_status_image'] )

								 )      );
  
                $this->common_footer();
                
		$ADMIN->output();
		
	}
 
function save_config( $new )

	{
		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP, $_POST;

		$master = array();

		if ( is_array($new) )

		{
			if ( count($new) > 0 )

			{
				foreach( $new as $field )

				{

				$_POST[ $field ] = preg_replace( "/'/", "&#39;", stripslashes($_POST[ $field ]) );

					$master[ $field ] = stripslashes($_POST[ $field ]);
				}

				$ADMIN->rebuild_config($master);
			}
		}

		$ADMIN->save_log("Обновление настроек форума, Back Up создан");


		$ADMIN->done_screen("Настройки форума обновлены", "Главная страница Админцентра", "act=index" );
}

	function common_footer( $button="Сохранить изменения" )

	{

		global $IN, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		$ADMIN->html .= $SKIN->end_form($button);

		$ADMIN->html .= $SKIN->end_table();

		$ADMIN->output();

	}
}

?>

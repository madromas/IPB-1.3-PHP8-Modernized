<?php

/*
+--------------------------------------------------------------------------
|  Group Name Indicator v1.5 by Shadow Fox (c) 2003 
+---------------------------------------------------------------------------
|
|   > Admin Member Groups Display Controls
|   > Module written by Shadow Fox
|   > Date started: 19thst March 2003
|
|	> Module Version Number: 1.5
+--------------------------------------------------------------------------
*/



$idx = new ad_shadow();


class ad_shadow {

	var $base_url;

	function __construct() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;

		switch($IN['code'])
		{
			case 'reorder':
				$this->SHADOW_form();
				break;
			case 'doreorder':
				$this->do_SHADOW();
				break;
			
			default:
				$this->SHADOW_form();
				break;
		}
		
	}	
	
	function SHADOW_form() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		$ADMIN->page_title = "Настройка отображения групп пользователей на главной странице форума";
		$ADMIN->page_detail  = "Для пересортировки порядка отображения групп пользователей на главной странице форума, просто выберите номер позиции, в выпадающем меню, рядом с каждой группой и нажмите кнопку Сохранить изменения<br><br>(Примечание: Группы Гости, Ожидающие и Banned, по умолчанию не отображаются.)";
		
		$ADMIN->html .= $SKIN->start_form( array( 					1 => array( 'code'  , 'doreorder'),
												2 => array( 'act'   , 'shadow'     ),
											) );
		
		
		$SKIN->td_header[] = array( "&nbsp;"       , "10%" );
		$SKIN->td_header[] = array( "Пользовательская группа"   , "75%" );
		$SKIN->td_header[] = array( "ID группы"        , "15%" );
		
		$ADMIN->html .= $SKIN->start_table( "Порядок отображения пользовательских групп" );



		
		$shadow   = array();	
		
		$DB->query("SELECT * from ibf_groups WHERE (g_id !=5 AND g_id !=1 AND g_id !=2) ORDER BY g_display ASC");
		while ($r = $DB->fetch_row())
		{
			$shadow[] = $r;
		}		
		
		// Build up the drop down box
		
		$form_array = array();
		
		for ($i = 1 ; $i <= count($shadow) ; $i++ )
		{
			$form_array[] = array( $i , $i );
		}
		
		
		$last_fox_id = -1;

		$href = "<a href='{$INFO['board_url']}/index.{$INFO['php_ext']}?act=Members&max_results=30&filter=";
            $link = "&sort_order=asc&sort_key=name&st=0' target='_blank' title='Список участников'>";
		$close = "</a>";
		
		foreach ($shadow as $X)
		{
			
			$ADMIN->html .= $SKIN->add_td_row( array(  $SKIN->form_dropdown( 'POS_'.$X['g_id'], $form_array, $X['g_display'] ),
													   $href.$X['g_id'].$link.$X['prefix'].$X['g_title'].$X['suffix'].$close,
													   $X['g_id'],
											 ), 'subforum'     );
			$last_fox_id = $X['id'];
			
			
				}
		
		$ADMIN->html .= $SKIN->end_form("Сохранить изменения");
		
		$ADMIN->html .= $SKIN->end_table();
		
		$ADMIN->output();
		
	}
	
	//+---------------------------------------------------------------------------------
	
	function do_SHADOW() {
		global $IN, $root_path, $INFO, $DB, $SKIN, $ADMIN, $std, $MEMBER, $GROUP;
		
		$fox_query = $DB->query("SELECT g_id from ibf_groups WHERE (g_id !=5 AND g_id !=1 AND g_id !=2)");
		
		while ( $r = $DB->fetch_row($fox_query) )
		{
			$order_query = $DB->query("UPDATE ibf_groups SET g_display='".$IN[ 'POS_' . $r['g_id'] ]."' WHERE g_id='".$r['g_id']."'");
		}
		
		$ADMIN->save_log("Пересортировка порядка отображения пользовательских групп");
		
		$ADMIN->done_screen("Порядок отображения групп изменён", "Настройка отображения цветов групп", "act=shadow" );
		
	}

	
}


?>

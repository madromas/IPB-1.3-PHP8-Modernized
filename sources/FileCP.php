<?php

/*
+--------------------------------------------------------------------------
|   File Control Panel v1.0
|   ========================================
|   by bfarber
|   (c) 2003 bfarber.com
|   http://bfarber.com
|   ========================================
|   Email: bfarber@bfarber.com
+---------------------------------------------------------------------------
*/


$idx = new FileCP;

class FileCP {

    var $output     = "";
    var $page_title = "";
    var $nav        = array();
    var $html       = "";
    var $member     = array();
    var $parser;
    

    function __construct() {
    	global $ibforums, $DB, $std, $print;

		require "./sources/lib/post_parser.php";
        	$this->parser = new post_parser();

		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_filecp', $ibforums->lang_id);
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_downloads', $ibforums->lang_id);
    		$this->html = $std->load_template('skin_filecp');
 	    	$this->base_url = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?";
    	
		$this->member  = $ibforums->member;
		
		if (empty($this->member['id'])) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}
		
    	
    	//--------------------------------------------
    	// What to do?
    	//--------------------------------------------
    	
    	
    	switch($ibforums->input['do']) {
		case 'files':
			$this->show_files();
			break;
		case 'edit':
			$this->show_edit();
			break;
		case 'stats':
			$this->show_stats();
			break;
		case 'downs':
			$this->show_downs();
			break;
		case 'favs':
			$this->show_favs();
			break;
		case 'remove':
			$this->remove_file();
			break;
		case 'manage':
			$this->manage();
			break;
		case 'tcreate':
			$this->create_missing_topics();
			break;
    			
    		default:
    			$this->show_splash();
    			break;
    	}
    	
		$this->output .= $this->html->bottom( );
    		$print->add_output("$this->output");
        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, 'NAV' => $this->nav ) );
    		
 	}


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//
//  File Control Panel
//  by bfarber
//	
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


	function show_splash()
		{
 		global $ibforums, $DB, $std, $print;

		$here="";
		$wrapper = $this->set_wrapper($here);

		if($this->member['g_d_add_files']){
			$out['add'] = "&middot;<a href='".$this->base_url."act=Downloads&amp;do=add'>{$ibforums->lang['add_link']}</a><br /><br />";
		} else {
			$out['add'] = "";
		}
		if($this->member['id']){
			$out['favs'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=favs'>{$ibforums->lang['favs_link']}</a><br /><br />";
		} else {
			$out['favs'] = "";
		}
		if($this->member['g_d_edit_files']){
			$out['edit'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=files'>{$ibforums->lang['edit_link']}</a><br /><br />";
		} else {
			$out['edit'] = "";
		}
		if($this->member['g_d_manage_files']){
			$out['manage'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage'>{$ibforums->lang['manage_link']}</a><br /><br />";
		} else {
			$out['manage'] = "";
		}

		$this->output .= $this->html->file_cp_splash($out);

		$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>");

            $this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title'];
	}


	function show_files()
		{
 		global $ibforums, $DB, $std, $print;

		$here="";
		$wrapper = $this->set_wrapper($here);

		$this->output .= $this->html->file_cp_top();

		$DB->query( "SELECT * FROM ibf_files WHERE mid = '". $this->member['id'] ."' ORDER BY date DESC" );

		if( $DB->get_num_rows() == 0 ){
			$this->output .= $this->html->no_downs_submitted();
		}

		while($row = $DB->fetch_row( )){
	
			if( $row['rating'] == 0 ) {
				$row['rating'] = $ibforums->lang['bf_not_rated'];
			}
		      $row['date'] = date("j.m.Y - H:i",$row['date']);
			if ($row['updated'] >0){
		      	$row['updated'] = date("j.m.Y - H:i",$row['updated']);
			} else {
				$row['updated'] = $ibforums->lang['never_updated'];
			}

			$this->output .= $this->html->file_cp_info( array( "id"   => $row['id'],
											      "name"  => $row['sname'],
												"date"  => $row['date'],
												"views" => $row['views'],
												"downloads"  => $row['downloads'],
												"updated"  => $row['updated']) );
		}

		$this->output .= $this->html->file_cp_end();


		$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
					  "<a href='".$this->base_url."act=FileCP&amp;do=files'>".$ibforums->lang['nav_filecp_files']."</a>" );

            $this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_files_title'];
	}


	function remove_file() {
		global $print , $ibforums , $DB , $std;

		if($ibforums->input['type'] == "sub"){

		if( $ibforums->input['id'] == "" ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_id') );
		}

		$DB->query( "DELETE FROM ibf_files WHERE id = '" . $ibforums->input['id'] . "'" );
		$DB->query( "DELETE FROM ibf_files_favorites WHERE fid = '" . $ibforums->input['id'] . "'" );
		$DB->query( "DELETE FROM ibf_files_comments WHERE file_id = '" . $ibforums->input['id'] . "'" );
		$DB->query( "DELETE FROM ibf_files_custentered WHERE file_id = '" . $ibforums->input['id'] . "'" );
		$DB->query( "DELETE FROM ibf_files_votes WHERE did = '" . $ibforums->input['id'] . "'" );
		$DB->query( "UPDATE ibf_members SET files=files-1 WHERE id = '" . $this->member['id'] . "'" );

		$print->redirect_screen("{$ibforums->lang['delete_redirect']}", "act=FileCP&amp;do=files" );
		exit();

		} 
		elseif($ibforums->input['type'] == "fav"){

		if( $ibforums->input['id'] == "" ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_id') );
		}

		$DB->query( "DELETE FROM ibf_files_favorites WHERE id = '" . $ibforums->input['id'] . "'" );

				$print->redirect_screen("{$ibforums->lang['fav_redirect1']}", "act=FileCP&amp;do=favs" );
				exit();
		}
	}


	function show_edit( ) {
		global $print, $ibforums, $std, $DB;

		if( $ibforums->input['id'] == "" ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_id') );
		}

		$here="";
		$wrapper = $this->set_wrapper($here);

		// Are the members of this group allowed to edit files?

		if( $this->member['g_d_edit_files'] == 1 ) {
			if( $this->member['g_d_eofs'] == 0 ) {
				$DB->query( "SELECT f.*, c.cname, c.cid FROM ibf_files f
						 LEFT JOIN ibf_files_cats c ON (f.cat=c.cid) WHERE f.mid = '".$this->member['id']."' AND f.id = '" . $ibforums->input['id'] . "' AND f.open = 1 " );
			} elseif( $this->member['g_d_eofs'] == 1 )  {
				$DB->query( "SELECT f.*, c.cname, c.cid FROM ibf_files f
						 LEFT JOIN ibf_files_cats c ON (f.cat=c.cid) WHERE f.id = " . $ibforums->input['id'] );
			}

			$result = $DB->fetch_row( );

			if( $DB->get_num_rows( ) == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_dwnld_mem') );
			} else {

				$cats = "";

				$cats = "<option value ='{$result['cid']}' selected='selected'>{$result['cname']}</option>";

				$DB->query( "SELECT cid,cname,sub FROM ibf_files_cats WHERE copen = 1 AND cid !='{$result['cid']}' ORDER BY cid ASC" );
				while($row = $DB->fetch_row( )){
					if($row['sub'] == 0){
							$cats .= "<option value='{$row['cid']}'>{$row['cname']}</option>";
					} else {
							$cats .= "<option value='{$row['cid']}'>&nbsp;&nbsp; - {$row['cname']}</option>";
					}
				}

				$result['sdesc'] = $this->parser->unconvert( $result['sdesc'], $this->member['g_d_ibcode_files'], $this->member['g_d_html_files']);
				$result['sdesc'] = str_replace("[dohtml]", "", $result['sdesc']);
				$result['sdesc'] = str_replace("[/dohtml]", "", $result['sdesc']);

		$required_output = "";
		$optional_output = "";
		$field_data     = array();

		$DB->query("SELECT * from ibf_files_custentered WHERE file_id='".$result['id']."'");
		
		while ( $content = $DB->fetch_row() ){
			foreach($content as $k => $v)	{
				if ( preg_match( "/^field_(\d+)$/", $k, $match) ){
					$field_data[ $match[1] ] = $v;
				}
			}
		}

		
		$DB->query("SELECT * from ibf_files_custfields WHERE fshow=1");
		
		while( $cust = $DB->fetch_row() ){
			$form_element = "";
			
			if ( $cust['freq'] == 1 ){
				$ftype = 'required_output';
				$reqq = "*";
			} else {
				$ftype = 'optional_output';
				$reqq = "";
			}
			
			if ( $cust['ftype'] == 'drop' ){
				$carray = explode( '|', trim($cust['fcontent']) );
				
				$d_content = "";
				
				foreach( $carray as $entry ){
					$value = explode( '=', $entry );
					
					$ov = trim($value[0]);
					$td = trim($value[1]);
					
					if ($ov !="" and $td !=""){
						$d_content .= ($field_data[$cust['fid']] == $ov) ? "<option value='$ov' selected='selected'>$td</option>\n"
																		: "<option value='$ov'>$td</option>\n";
					}
				}
				
				if ($d_content != ""){
					$form_element = $this->html->field_dropdown( 'field_'.$cust['fid'], $d_content );
				}
			} else if ( $cust['ftype'] == 'area' ) {
				$form_element = $this->html->field_textarea( 'field_'.$cust['fid'], $field_data[$cust['fid']] );
			} else {
				$form_element = $this->html->field_textinput( 'field_'.$cust['fid'], $field_data[$cust['fid']] );
			}
			
			${$ftype} .= $this->html->field_entry( $cust['ftitle'], $form_element, $reqq );
		}



				$ext = "";
        			foreach( $ibforums->vars['d_allowable_ext'] as $value){
         	   			$ext .= $value."|";
       	 		}
        			$ext = substr($ext ,0 ,-1);

				$sext = "";
        			foreach( $ibforums->vars['d_screenshot_ext'] as $value){
         	   			$sext .= $value."|";
       	 		}
        			$sext = substr($sext ,0 ,-1);

				if($ibforums->vars['d_screenshot_allowed'] == 1){
					if($ibforums->vars['d_screenshot_required'] == 1){
						$ssreq = "*";
					} else {
						$ssreq = "";
					}
					$link .= $this->html->screen_upload($sext,$ssreq);
				}


				if($ibforums->vars['d_upload']){
                			$link .= $this->html->upload_file($ext);
            		}
            		if($ibforums->vars['d_linking']){
					if($result['link']){
						$default = $result['link'];
					} else { 
						$default = "";
					}
                			$link .= $this->html->link($default,$ext);
            		}

				$this->output .= $this->html->show_edit_downloads($cats,$result,$link,$required_output,$optional_output);

				$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
							  "<a href='".$this->base_url."act=FileCP&amp;do=edit&amp;id={$ibforums->input['id']}'>".$ibforums->lang['nav_filecp_edit']."</a>" );
            		$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_edit_title'];
			}
		} else {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}
	}


	function show_downs()
		{
 		global $ibforums, $DB, $std, $print;

		$here="";
		$wrapper = $this->set_wrapper($here);

		$this->output .= $this->html->file_downs_top();

		$DB->query( "SELECT * FROM ibf_files_downloads WHERE m_id = '". $this->member['id'] ."' ORDER BY downloaded DESC" );
		if( $DB->get_num_rows( ) == 0 ){
			$this->output .= $this->html->no_downs_down();
		}

		while($row = $DB->fetch_row( )){
	      	$row['downloaded'] = date("j.m.Y - H:i",$row['downloaded']);

			$this->output .= $this->html->file_down_info( array( "name"  => $row['file_name'],
												"date"  => $row['downloaded'],
												"id"	=> $row['file_id'],
												"mid"  => $row['m_id']) );
		}

		$this->output .= $this->html->file_cp_end();

		$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
					  "<a href='".$this->base_url."act=FileCP&amp;do=downs'>".$ibforums->lang['nav_filecp_downs']."</a>"  );

     		$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_downs_title'];
	}

	function manage()
		{
 		global $ibforums, $DB, $std, $print;

		if(!$this->member['g_d_manage_files']){
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_perm') );
		} 

		if(!$ibforums->input['type']){

			$here="";
			$wrapper = $this->set_wrapper($here);



			if($this->member['g_d_check_links']){
				$out['links'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=linkcheck'>{$ibforums->lang['filecp_checklinks']}</a><br /><br />";
			} else {
				$out['links'] = "";
			}
			if($this->member['g_d_approve_down']){
				$DB->query("SELECT COUNT(id) as blah FROM ibf_files WHERE open=0");
				$accept = $DB->fetch_row();
				$out['approve'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=approve'>{$ibforums->lang['filecp_approvedown']}</a> ({$accept['blah']})<br /><br />";
			} else {
				$out['approve'] = "";
			}
			if($this->member['g_d_eofs']){
				$out['edit'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=edit'>{$ibforums->lang['filecp_editfiles']}</a><br /><br />";
			} else {
				$out['edit'] = "";
			}
			if($this->member['g_d_optimize_db']){
				$out['optimize'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=optimize'>{$ibforums->lang['filecp_optimize']}</a><br /><br />";
			} else {
				$out['optimize'] = "";
			}
			if($this->member['g_d_check_topics']){
				$out['tcheck'] = "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=tcheck'>{$ibforums->lang['filecp_tcheck']}</a><br /><br />";
				$out['tcheck'] .= "&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=tcheck1'>{$ibforums->lang['filecp_tcheck1']}</a><br /><br />";
			} else {
				$out['tcheck'] = "";
			}

			$DB->query("SELECT COUNT(id) as blah FROM ibf_files WHERE open=0");
			$accept = $DB->fetch_row();
			$out['waiting'] = $accept['blah'];

			$this->output .= $this->html->file_manage($out);
			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title'];
		}
		elseif($ibforums->input['type'] == "optimize"){

			$DB->query("OPTIMIZE TABLE ibf_files");
			$DB->query("OPTIMIZE TABLE ibf_files_cats");
			$DB->query("OPTIMIZE TABLE ibf_files_comments");
			$DB->query("OPTIMIZE TABLE ibf_files_favorites");
			$DB->query("OPTIMIZE TABLE ibf_files_votes");
			$DB->query("OPTIMIZE TABLE ibf_files_downloads");
			$DB->query("OPTIMIZE TABLE ibf_files_custfields");
			$DB->query("OPTIMIZE TABLE ibf_files_custentered");

			$print->redirect_screen("{$ibforums->lang['optimize_redirect']}", "act=FileCP&amp;do=manage" );
			exit();
		}
		elseif($ibforums->input['type'] == "approve"){

			if(!$ibforums->input['id']){

			$here="";
			$wrapper = $this->set_wrapper($here);

            	$DB->query( "SELECT id,sname FROM ibf_files WHERE open = 0" );

			if( $DB->get_num_rows( ) == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_accept') );
			}
			$this->output .= $this->html->show_accept_downloads_top( );

			while( $row = $DB->fetch_row( ) ) {
				$this->output .= $this->html->show_accept_downloads($row);
			}

			$this->output .= $this->html->show_manage_bottom( );

			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=approve'>".$ibforums->lang['nav_filecp_approve']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_approve_title'];
			} else {
				$DB->query("SELECT f.*,c.fordaforum,c.cname,c.authorize,e.* FROM ibf_files f
						LEFT JOIN ibf_files_cats c ON (f.cat=c.cid)
						LEFT JOIN ibf_files_custentered e ON (e.file_id=f.id) WHERE f.id='{$ibforums->input['id']}'");
				$grab_mem = $DB->fetch_row();

				if ($ibforums->vars['d_create_topic'] == 'percat') {
					$topic_forum = $grab_mem['fordaforum'];
				} elseif ($ibforums->vars['d_create_topic'] == "0") {
					$topic_forum = "";
				} else {
					$topic_forum = $ibforums->vars['d_create_topic'];
				}
				if($ibforums->vars['d_cat_add'] == 1) {
	    				$bftitle = "[{$grab_mem['cname']}] {$grab_mem['sname']}";
				} elseif ($ibforums->vars['d_cat_add'] == 0) {
					$bftitle = "{$grab_mem['sname']}";
				}
            		if( $ibforums->vars['d_create_topic'] != 0 && ($ibforums->vars['d_authorize'] == 1 || ($ibforums->vars['d_authorize'] == 2 && $grab_mem['authorize'] == 1)) ) {

		$custom_fields = array();
		
		$DB->query("SELECT * from ibf_files_custfields WHERE fshow=1");
		
		while ( $cust = $DB->fetch_row() ){
			if($cust['ftopic'] == 1){
				if ($grab_mem['field_'.$cust['fid']] != ""){
					if ( $cust['ftype'] == 'drop' ){
						$carray = explode( '|', trim($cust['fcontent']) );
				
						foreach( $carray as $entry ){
							$value = explode( '=', $entry );
					
							$ov = trim($value[0]);
							$td = trim($value[1]);
					
							if ($ibforums->input['field_'.$cust['fid']] == $ov){
								$d_content = $td;
							}
						}
					} else {
						$d_content = $ibforums->input['field_'.$cust['fid']];
					}

				$outto_topic .= "[B]{$cust['ftitle']}[/B] :: {$d_content}\n";;
				} else {
					$outto_topic .= "";
				}
			}				
		}


				$do_post = array('title'	=> $bftitle,
							'extra_crap'	=> $outto_topic,
							'right_forum'	=> $grab_mem['fordaforum'],
							'author'		=> $grab_mem['author'],
							'author_id'		=> $grab_mem['mid'] ? $grab_mem['mid'] : 0,
							'fid'			=> $topic_forum,
							'sname'		=> $grab_mem['sname'],
							'edit'		=> $ibforums->lang['t_update_topic'],
							'starter_id'      => $grab_mem['mid'],
					 		'last_poster_id'   => $grab_mem['mid'],
					 		'last_poster_name' => $grab_mem['author'],
							'cname'		=> $grab_mem['cname'],
							'sdesc'		=> $grab_mem['sdesc'],
							'username'		=> $grab_mem['author'],
							'sid'			=> $grab_mem['id'],
							'author_mode'	=> $grab_mem['mid'],
							'ipadd'		=> "0.0.0.0",
							'enabletra'		=> $ibforums->input['enabletra'],
						);

					$postit = $this->post_topic($do_post);

				} else {
					$postit = 0;
				}


				$DB->query("UPDATE ibf_files SET open=1, topic='{$postit}' WHERE id='{$ibforums->input['id']}'");
				$DB->query("UPDATE ibf_members SET files=files+1 WHERE id='{$grab_mem['mid']}'");

				$query = $DB->query("SELECT COUNT(id) FROM ibf_files WHERE open=0");
				$waiting = $DB->fetch_row($query);
				if($waiting['blah'] > 0){
					$print->redirect_screen("{$ibforums->lang['approvedfile_redirect']}", "act=FileCP&amp;do=manage&amp;type=approve" );
					exit();
				} else {
					$print->redirect_screen("{$ibforums->lang['approvedfile_redirect']}", "act=FileCP&amp;do=manage" );
					exit();
				}
			}				
		}
		elseif($ibforums->input['type'] == "delete"){
			if( $ibforums->input['id'] == "" ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_id') );
			}

			$DB->query("SELECT * FROM ibf_files WHERE id='{$ibforums->input['id']}'");
			$cache = $DB->fetch_row;
			if( $cache['current'] ) {
				if (!@unlink( $ibforums->vars['d_download_dir'].$cache['url']) && @file_exists($ibforums->vars['d_download_dir'].$cache['url'])){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'n_unlink') );
				}
			}
			if( $cache['screenshot'] ) {
				if (!@unlink( $ibforums->vars['d_screen_dir'].$cache['screenshot']) && @file_exists($ibforums->vars['d_screen_dir'].$cache['screenshot'])){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'n_unlink') );
				}
			}
			$DB->query("DELETE FROM ibf_files WHERE id='{$ibforums->input['id']}'");
			$DB->query( "DELETE FROM ibf_files_favorites WHERE fid = '" . $ibforums->input['id'] . "'" );
			$DB->query( "DELETE FROM ibf_files_comments WHERE file_id = '" . $ibforums->input['id'] . "'" );
			$DB->query( "DELETE FROM ibf_files_custentered WHERE file_id = '" . $ibforums->input['id'] . "'" );
			$DB->query( "DELETE FROM ibf_files_votes WHERE did = '" . $ibforums->input['id'] . "'" );
			$DB->query( "UPDATE ibf_members SET files=files-1 WHERE id = '" . $cache['mid'] . "'" );
			$query = $DB->query("SELECT COUNT(id) FROM ibf_files WHERE open=0");
			$waiting = $DB->fetch_row($query);
			if($waiting['blah'] > 0){
				$print->redirect_screen("{$ibforums->lang['deletefile_redirect']}", "act=FileCP&amp;do=manage&amp;type=approve" );
				exit();
			} else {
				$print->redirect_screen("{$ibforums->lang['deletefile_redirect']}", "act=FileCP&amp;do=manage" );
				exit();
			}
		}
		elseif($ibforums->input['type'] == "edit"){

			$here="";
			$wrapper = $this->set_wrapper($here);

			$this->output .= $this->html->show_mod_search( );

			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=approve'>".$ibforums->lang['nav_filecp_editsearch']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_editsearch_title'];
		}
		elseif($ibforums->input['type'] == "listedit"){
			if( !($ibforums->input['id'] == "" XOR $ibforums->input['name'] == "" ) ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_idname') );
			}

			if( $ibforums->input['id'] == "" ) {
				$id = NULL;
			} else {
				$id = $ibforums->input['id'];
			}

			if( $ibforums->input['name'] == "" ) {
				$name = NULL;
			} else {
				$name = "%".$ibforums->input['name']."%";
			}

			$here="";
			$wrapper = $this->set_wrapper($here);

			$this->output .= $this->html->show_listedit_top( );
            	$DB->query( "SELECT id,sname FROM ibf_files WHERE id = '{$id}' OR sname LIKE '{$name}'" );

			if( $DB->get_num_rows( ) == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'd_no_search_result') );
			}
			while( $row = $DB->fetch_row( ) ) {
				$this->output .= $this->html->show_listedit_row($row);
			}

			$this->output .= $this->html->show_manage_bottom();
			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=approve'>".$ibforums->lang['nav_filecp_editsearch']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_editsearch_title'];
		}
		elseif($ibforums->input['type'] == "linkcheck"){

			$here="";
			$wrapper = $this->set_wrapper($here);

			$this->output .= $this->html->show_linkcheck_top( );
            	$DB->query( "SELECT * FROM ibf_files WHERE link !=''" );
			while($row = $DB->fetch_row()){
				$fp = $this->file_get_contents($row['link']);
				if (!$fp){
					$this->output .= $this->html->link_valid_row($row);
				}
			}
			$this->output .= $this->html->show_manage_bottom();
			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=approve'>".$ibforums->lang['nav_filecp_linkcheck']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_linkcheck_title'];

		}
		elseif($ibforums->input['type'] == "tcheck"){

			$here="";
			$wrapper = $this->set_wrapper($here);

			$this->output .= $this->html->show_tcheck_top( );

			$DB->query( "SELECT s.id,s.sname,s.topic,t.* FROM ibf_files s
				 LEFT JOIN ibf_topics t ON (s.topic=t.tid)
				 WHERE s.topic !='' AND s.topic !='0'" );
			while($valid = $DB->fetch_row()){
				if(!$valid['tid']){
					$this->output .= $this->html->topic_valid_row($valid);
				}
			}
		

        		$this->output .= $this->html->topic_valid_bottom( );
			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=tcheck'>".$ibforums->lang['nav_filecp_tcheck']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_tcheck_title'];

		}
		elseif($ibforums->input['type'] == "tcheck1"){

			$here="";
			$wrapper = $this->set_wrapper($here);

			$this->output .= $this->html->show_tcheck_top( );

			$DB->query( "SELECT s.id,s.sname,s.topic,t.* FROM ibf_files s
				 LEFT JOIN ibf_topics t ON (s.topic=t.tid)
				 WHERE s.topic ='' OR s.topic ='0'" );
			while($valid = $DB->fetch_row()){
				$this->output .= $this->html->topic_valid_row($valid);
			}
		

        		$this->output .= $this->html->topic_valid_bottom( );
			$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage'>".$ibforums->lang['nav_filecp_manage']."</a>",
						  "<a href='".$this->base_url."act=FileCP&amp;do=manage&amp;type=tcheck1'>".$ibforums->lang['nav_filecp_tcheck1']."</a>"  );

     			$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_manage_title']." -> ".$ibforums->lang['bf_tcheck1_title'];

		}
	}

	function file_get_contents($f) {
    		ob_start();
    		$retval = @readfile($f);
    		if (false !== $retval) { // no readfile error
        		$retval = ob_get_contents();
    		}
    		ob_end_clean();
    		return $retval;
	}

	function show_favs()
		{
 		global $ibforums, $DB, $std, $print;

		$here="";
		$wrapper = $this->set_wrapper($here);

		$this->output .= $this->html->file_favs_top();

		$DB->query( "SELECT * FROM ibf_files_favorites WHERE mid = '". $this->member['id'] ."' ORDER BY date DESC" );

		if( $DB->get_num_rows( ) == 0 ){
			$this->output .= $this->html->no_downs_fav();
		}

		while( $row = $DB->fetch_row( ) ) {
		   	$row['date'] = date("j.m.Y - H:i",$row['date']);

			$this->output .= $this->html->file_fav_info( array( "id"  => $row['id'],
												"sname"  => $row['fname'],
												"date"  => $row['date'],
												"sid"	=> $row['fid'],
												"mid"  => $row['mid']) );
		}

		$this->output .= $this->html->file_cp_end();

		$this->nav = array( "<a href='".$this->base_url."&act=FileCP'>".$ibforums->lang['nav_filecp']."</a>",
					  "<a href='".$this->base_url."act=FileCP&amp;do=favs'>".$ibforums->lang['nav_filecp_favs']."</a>" );

     		$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title']." -> ".$ibforums->lang['bf_favs_title'];
	}

		
	function show_stats()
		{
 		global $ibforums, $DB, $std, $print;

		$here="";
		$wrapper = $this->set_wrapper($here);


				// Get the info we need

				$database = $wrapper['files'];

				$DB->query( "SELECT COUNT(id) as bf1 FROM ibf_files WHERE mid = '". $this->member['id'] ."'" );
				$bf1 = $DB->fetch_row();
				$member = $bf1['bf1'];

				$totaldown = $wrapper['down'];

				$DB->query( "SELECT COUNT(*) as bf3 FROM ibf_files_downloads WHERE m_id = '". $this->member['id'] ."'" );
				$bf3 = $DB->fetch_row();
				$thisdown = $bf3['bf3'];


				$this->output .= $this->html->file_stats($database, $member, $totaldown, $thisdown);

// Top File Downloads
	$pop = $DB->query('SELECT sname, id, downloads, open FROM ibf_files WHERE open=1 ORDER BY downloads DESC LIMIT 10');
	while ($pop1top = $DB->fetch_row($pop)) {
		$data['downloads_down'] .= "<tr><td width='90%' class='row4'><a href='".$this->base_url."download={$pop1top['id']}'>{$pop1top['sname']}</a></td><td width='10%' class='row4'>{$pop1top['downloads']}</td></tr>";
	}

// Top File Views
	$pop = $DB->query('SELECT sname, id, views, open FROM ibf_files WHERE open=1 ORDER BY views DESC LIMIT 10');
	while ($pop2top = $DB->fetch_row($pop)) {
		$data['downloads_views'] .= "<tr><td width='90%' class='row4'><a href='".$this->base_url."download={$pop2top['id']}'>{$pop2top['sname']}</a></td><td width='10%' class='row4'>{$pop2top['views']}</td></tr>";
	}

// Top Submitters
	$pop = $DB->query('SELECT id, name, files FROM ibf_members ORDER BY files DESC LIMIT 10');
	while ($pop3top = $DB->fetch_row($pop)) {
		$data['authors'] .= "<tr><td width='90%' class='row4'><a href='".$this->base_url."showuser={$pop3top['id']}'>{$pop3top['name']}</a></td><td width='10%' class='row4'>{$pop3top['files']}</td></tr>";
	}

// Top Downloaders
	$pop = $DB->query('SELECT id, name, downloads FROM ibf_members ORDER BY downloads DESC LIMIT 10');
	while ($pop4top = $DB->fetch_row($pop)) {
		$data['downloaders'] .= "<tr><td width='90%' class='row4'><a href='".$this->base_url."showuser={$pop4top['id']}'>{$pop4top['name']}</a></td><td width='10%' class='row4'>{$pop4top['downloads']}</td></tr>";
	}

		$this->output .= $this->html->file_statistics($data);

		$this->nav = array( "<a href='".$this->base_url."act=FileCP'>".$ibforums->lang['nav_filecp']."</a>" );

		$this->page_title = $ibforums->vars['board_name']." -> ".$ibforums->lang['bf_page_title'];
	}

   //-----------------------------------------------------
   // Wrapper
   //-----------------------------------------------------
   
   function set_wrapper($input){
   		global $ibforums, $DB;
		
		$randompic = $this->randompic();
		if($randompic != FALSE){
			$ranpic_output = $this->html->random_pic($randompic);
		}
		
		//Permission Time...
		if($this->member['g_d_add_files']){
			$out['add'] = "&nbsp;&middot;<a href='".$this->base_url."act=Downloads&amp;do=add'>{$ibforums->lang['add_link']}</a><br />";
		} else {
			$out['add'] = "";
		}
		if($this->member['id']){
			$out['favs'] = "&nbsp;&middot;<a href='".$this->base_url."act=FileCP&amp;do=favs'>{$ibforums->lang['favs_link']}</a><br />";
		} else {
			$out['favs'] = "";
		}
		if($this->member['g_d_edit_files']){
			$out['edit'] = "&nbsp;&middot;<a href='".$this->base_url."act=FileCP&amp;do=files'>{$ibforums->lang['edit_link']}</a><br />";
		} else {
			$out['edit'] = "";
		}
		if($this->member['g_d_manage_files']){
			$out['manage'] = "&nbsp;&middot;<a href='".$this->base_url."act=FileCP&amp;do=manage'>{$ibforums->lang['manage_link']}</a><br />";
		} else {
			$out['manage'] = "";
		}


		//Stats...
		$DB->query( "SELECT COUNT(file_id) as down FROM ibf_files_downloads" );
		$row    = $DB->fetch_row( );
		$DB->query( "SELECT COUNT(id) as files FROM ibf_files WHERE open='1'" );
		$row1   = $DB->fetch_row( );
		$DB->query( "SELECT COUNT(cid) as cats FROM ibf_files_cats WHERE copen='1'" );
		$row2   = $DB->fetch_row( );
		$out['down'] = $row['down'];
		$out['files'] = $row1['files'];
		$out['cats'] = $row2['cats'];
		
		if($ibforums->vars['d_show_global_notes'] == 1){
			$out['all_notes'] = $ibforums->vars['d_global_notes'];
		}
		if($input['show_notes'] == 1){
			$out['all_notes'] .= "<br /><br />".$input['cnotes']."<br />";
		}		
		
		if($input['show_notes'] == 1 || $ibforums->vars['d_show_global_notes'] == 1){
			$out['notes'] = $this->html->show_global_notes($out['all_notes']);
		}

		$this->output .= $this->html->wrapper($out,$ranpic_output);   
   		return $out;
   }
   
   //-----------------------------------------------------

   //-----------------------------------------------------
   // Random Pic 
   //-----------------------------------------------------

   	function randompic()
   	{
	   	function is_remote($file_name) {
   			return (preg_match('#^https?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $file_name)) ? 1 : 0;
	 	}

       	global $DB, $ibforums, $std;
	 	$query = $DB->query("SELECT COUNT(screenshot) as total_images FROM ibf_files WHERE open='1' AND screenshot!=''");
	 	if ($DB->get_num_rows($query) <= 0)
	 	{
 	 		return false;
	 	}	
	 	$row = $DB->fetch_row($query);
	 	$total = $row['total_images'];
	 	mt_srand((double)microtime() * 1000000);
	 	$number = ($total > 1) ? mt_rand(0, $total - 1) : 0;
	 	$query1 = $DB->query("SELECT id, screenshot, mid, cat, sname, open, author FROM ibf_files  WHERE open='1' AND screenshot!='' LIMIT {$number}, 1");
	 	if ($DB->get_num_rows($query1) <= 0)
	 	{
 			return false;
	 	}
	 	$row = $DB->fetch_row($query1);
	 	$row['screenshot'] = (is_remote($row['screenshot'])) ? $row['screenshot'] : $ibforums->vars['d_screen_url'].$row['screenshot']; 
       	return $row;
   	}
   //-----------------------------------------------------


   //-----------------------------------------------------
   // Topic Poster
   //-----------------------------------------------------

	function post_topic ($data)
	{
		global $ibforums, $DB;

					$topic = array(
					 	'title'            => $data['title'],
					 	'description'      => $data['edit'],
					 	'state'            => "open",
					 	'posts'            => 0,
						'starter_id'       => $data['starter_id'],
					 	'starter_name'     => $data['author'],
					 	'start_date'       => time(),
					 	'last_poster_id'   => $data['last_poster_id'],
					 	'last_poster_name' => $data['last_poster_name'],
					 	'last_post'        => time(),
					 	'icon_id'          => 0,
					 	'author_mode'      => $data['author_mode'] ? 1 : 0,
					 	'poll_state'       => 0,
					 	'last_vote'        => 0,
					 	'views'            => 0,
				 	 	'forum_id'         => $data['fid'],
					 	'approved'         => 1,
					 	'pinned'           => 0,
					);


				$db_string = $DB->compile_db_insert_string( $topic );
				$DB->query("INSERT INTO ibf_topics (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
				$posty['topic_id']  = $DB->get_insert_id();

				//------------- POST CLASS COPIED ---------------//
				$post1 = "[B]{$ibforums->lang['t_filename']}[/B] :: {$data['sname']}\n";
				$post1 .= "[B]{$ibforums->lang['t_author']}[/B] :: {$data['author']}\n";
				$post1 .= "[B]{$ibforums->lang['t_cat']}[/B] :: {$data['cname']}\n";
				$post1 .= $data['extra_crap'];
				$post1 .= "[B]{$ibforums->lang['t_desc']}[/B] \n";
				$post2  ="\n\n";
				$post2 .= "[URL=".$this->base_url ."download=".$data['sid']."]{$ibforums->lang['t_viewfile']}[/URL]\n";
				$desc = $this->parser->convert( array( TEXT    => $post1,
													   SMILIES => 1,
													   CODE    => 1,
													   HTML    => 0
												)  );
				$desc .= $this->parser->convert( array( TEXT    => $data['sdesc'],
													   SMILIES => 1,
													   CODE    => $this->member['g_d_ibcode_files'],
													   HTML    => $this->member['g_d_html_files']
												)  );
				$desc .= $this->parser->convert( array( TEXT    => $post2,
													   SMILIES => 1,
													   CODE    => 1,
													   HTML    => 0
												)  );
				$post = array(
						'author_id'   => $data['author_id'],
						'use_sig'     => 1,
						'use_emo'     => 1,
						'ip_address'  => $data['ipadd'],
						'post_date'   => time(),
						'icon_id'     => 0,
						'post'        => $desc,
						'author_name' => $data['author'],
						'forum_id'    => $data['fid'],
						'topic_id'    => $posty['topic_id'],
						'queued'      => 0,
						'attach_id'   => "",
						'attach_hits' => "",
						'attach_type' => "",
					 );
				//------------- END POST CLASS COPIED ---------------//



				$db_string = $DB->compile_db_insert_string( $post );

				$DB->query("INSERT INTO ibf_posts (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
				$post['pid'] = $DB->get_insert_id();
				// start oska podpiska na temu poddergki
				if ($data['enabletra'] ) {
					$db_string = $DB->compile_db_insert_string( array ('member_id'   => $this->member['id'],
																		'topic_id'    => $posty['topic_id'],
																		'start_date'  => time(),
																)       );
					$DB->query("INSERT INTO ibf_tracker (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
				}
				// end oska podpiska na temu poddergki

				$forum['last_title']       = $data['title'];
				$forum['last_id']          = $posty['topic_id'];
				$forum['last_post']        = time();
				$forum['last_poster_name'] = $data['author'];
				$forum['last_poster_id']   = $data['last_poster_id'];
				$forum['topics']++;

				// Update the database

				$DB->query("UPDATE ibf_forums    SET last_title='"      .$forum['last_title']       ."', ".
											"last_id='"         .$forum['last_id']          ."', ".
											"last_post='"       .$forum['last_post']        ."', ".
											"last_poster_name='".$forum['last_poster_name'] ."', ".
											"last_poster_id='"  .$forum['last_poster_id']   ."', ".
											"topics=topics + 1 ".
											"WHERE id='" .$data['fid']."'");


				$DB->query("UPDATE ibf_stats SET TOTAL_TOPICS=TOTAL_TOPICS+1");

				if ($this->member['id'])
				{

				$pcount = "posts=posts+ 1, ";
				$ibforums->member['last_post'] = time();

				$DB->query("UPDATE ibf_members SET posts=posts+1, last_post='".time()."' WHERE id='"  .$this->member['id']."'");
				}


		return $posty['topic_id'];

	}

   //-----------------------------------------------------


   //-----------------------------------------------------
   // Topic Poster
   //-----------------------------------------------------

	function create_missing_topics( ) {
		global $print , $ibforums , $DB , $std;

		if( $ibforums->member['g_d_check_topics'] == 0 ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}

 		foreach ($ibforums->input as $key => $value)
 		{
 			if ( preg_match( "/^file_(\d+)$/", $key, $match ) )
 			{
 				if ($ibforums->input[$match[0]])
 				{
 					$id = $match[1];
 				}
				$create_it = $DB->query("SELECT s.*, c.cname, c.fordaforum FROM ibf_files s, ibf_files_cats c WHERE s.id='{$id}' AND c.cid=s.cat");
				while($row = $DB->fetch_row($create_it)){
	
					if($ibforums->vars['d_cat_add'] == 1) {
	    					$bftitle = "[{$row['cname']}] {$row['sname']}";
					} elseif ($ibforums->vars['d_cat_add'] == 0) {
						$bftitle = "{$row['sname']}";
					}

					if ($ibforums->vars['d_create_topic'] == 'percat') {
						$topic_forum = $row['fordaforum'];
					} elseif ($ibforums->vars['d_create_topic'] == "0") {
						$topic_forum = "";
					} else {
						$topic_forum = $ibforums->vars['d_create_topic'];
					}


					$do_post = array('title'	=> $bftitle,
							'right_forum'	=> $row['authorize'],
							'author'		=> $row['author'],
							'author_id'		=> $row['mid'] ? $row['mid'] : 0,
							'fid'			=> $topic_forum,
							'sname'		=> $row['sname'],
							'edit'		=> $ibforums->lang['t_update_topic'],
							'starter_id'      => $row['mid'],
					 		'last_poster_id'   => $row['mid'],
				 			'last_poster_name' => $row['author'],
							'cname'		=> $row['cname'],
							'sdesc'		=> $row['sdesc'],
							'username'		=> $row['author'],
							'sid'			=> $row['id'],
							'author_mode'	=> $row['mid'],
							'ipadd'		=> "0.0.0.0",
							'enabletra'		=> $ibforums->input['enabletra'],
					);
					$postit = $this->post_topic($do_post);
					$DB->query("UPDATE ibf_files SET topic='{$postit}' WHERE id='{$id}'");
				}
 			}
 		}


		$print->redirect_screen("{$ibforums->lang['recreatet_redirect']}", "act=FileCP&amp;do=manage" );
		exit();

	}

   //-----------------------------------------------------



}

?>

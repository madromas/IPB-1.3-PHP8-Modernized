<?php
/*
*-------------------------------------------------*
|
|	Download Section v1.0
|
|	by bfarber 
|	(c) 2003 Brandon Farber
|	http://bfarber.com
|	bfarber@bfarber.com
|
|	Based on code written by Parmeet Singh for use
|	with IPB 1.0.X.  
|
+-------------------------------------------------*
|
|	This module will give you a download section
|	on your site, skin and language independent
|	of this backbone file, configurable in the
|	Admin Control Panel.
|
|	Permission for all code not-written by bfarber
|	has been expressly granted for use with this
|	Download Mod.
*-------------------------------------------------*
*/

$idx = new Downloads;

class Downloads
{
var $html   = "";
var $output = "";
var $nav    = "";
var $base_url;
var $member = "";
var $downloads = "";

	function __construct()
	{

		global $ibforums, $root_path, $std, $DB, $print;

	 	if($ibforums->vars['d_section_close'] == '1' && $ibforums->member['g_d_allow_dl_offline'] == '0') {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'd_section_close') );
	 	}
		require ROOT_PATH."/sources/lib/post_parser.php";
        $this->parser = new post_parser();

	    $this->html = $std->load_template('skin_downloads');
        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_downloads', $ibforums->lang_id );
		$this->base_url = "{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?";
		$this->member = $ibforums->member;


		switch( $ibforums->input['do'] ) {
			case "view":
				$this->show( );
               		break;
			case "add":
				$this->add( );
				break;
			case "do_add":
				$this->do_add( );
				break;
			case "edit":
				$this->edit( );
				break;
			case "search":
				$this->search( );
				break;
			case "manage":
				$this->manage( );
				break;
			case "rating":
				$this->do_rating( );
				break;	
			case "post":
				$this->post_comment( );
				break;
			case "download":
				$this->download( );
				break;
			case "favorites":
				$this->do_favorite( );
				break;

			default:
				$this->show( );
				break;
			}

			$this->output .= $this->html->bottom( );
	    		$print->add_output("$this->output");
	        	$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 0, 'NAV' => $this->nav ) );
		}


   //-----------------------------------------------------
   // Display Functions
   //-----------------------------------------------------

	function show(){

		global $ibforums, $DB, $std, $print;
		
		if(!$ibforums->input['type']) $ibforums->input['type'] = "cats";

		if($ibforums->input['type'] == "cats"){
		
			$here = "";
			$needwrapper = $this->set_wrapper($here);

			$catarray[0] = "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>";
			$cattitle = $ibforums->lang['title'];
			$this->output .= $this->html->cats_header($cattitle);

			$query = $DB->query( "SELECT * FROM ibf_files_cats WHERE copen = 1 && sub = 0 ORDER BY position ASC" );
			if( $DB->get_num_rows($query) == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_cats_exists') );
			} else {
				while($row = $DB->fetch_row($query)){
						$grab_subs = $DB->query( "SELECT * FROM ibf_files_cats WHERE sub = '".$row['cid']."'");
						if( $DB->get_num_rows($grab_subs) > 0){
							$this->output .= $this->html->cats_row_with_subs($row);
							while($subs = $DB->fetch_row($grab_subs)){
								$last_file = $DB->query("SELECT id,sname,author,date FROM ibf_files WHERE cat = ".$subs['cid']." AND open= 1 ORDER by date DESC");
								$last = $DB->fetch_row($last_file);
								if($DB->get_num_rows($last_file) > 0){
									$last['files'] = $DB->get_num_rows($last_file);
	      							$last['date'] = date("j.m.Y - H:i",$last['date']);
									$last['sname'] = "<a href='index.php?download={$last['id']}'>{$last['sname']}</a>";
								} else {
									$last['files'] = "0";
									$last['date'] = $ibforums->lang['no_cat_info'];
									$last['author'] = $ibforums->lang['no_cat_info'];
									$last['sname'] = $ibforums->lang['no_cat_info'];
								}
								$this->output .= $this->html->cats_sub_row($subs,$last);
							}
							$this->output .= $this->html->cats_row_with_subs_close();
						} elseif( $DB->get_num_rows($grab_subs) == 0) {
							$last_file = $DB->query("SELECT id,sname,author,date FROM ibf_files WHERE cat = ".$row['cid']." AND open = 1 ORDER by date DESC");
							$last = $DB->fetch_row($last_file);
								if($DB->get_num_rows($last_file) == 0){
									$last['files'] = "0";								
									$last['date'] = $ibforums->lang['no_cat_info'];
									$last['author'] = $ibforums->lang['no_cat_info'];
									$last['sname'] = $ibforums->lang['no_cat_info'];
								} else {
									$last['files'] = $DB->get_num_rows($last_file);
	      							$last['date'] = date("j.m.Y - H:i",$last['date']);
									$last['sname'] = "<a href='index.php?download={$last['id']}'>{$last['sname']}</a>";
								}
							$this->output .= $this->html->cats_row_without_subs($row,$last);
						}
				}
			}

			$this->output .= $this->html->cats_bottom();
			$this->nav = $catarray;
			$this->page_title = $ibforums->vars['board_name'] ." -> ". $cattitle;
		}  	
		elseif($ibforums->input['type'] == "cat") {

	 		if( $ibforums->input['cat'] == "" || !$ibforums->input['cat'] ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_cat') );
	 		}
			
			if( ($ibforums->vars['d_perpage'] == "") || ($ibforums->vars['d_perpage'] == "0")) $ibforums->vars['d_perpage'] = "10";
			if($ibforums->input['page'] > "1" && ($ibforums->input['num'] != $ibforums->input['cur_num'])) $ibforums->input['page'] = "1";
	 		if( $ibforums->input['num'] == "" ) $ibforums->input['num'] = $ibforums->vars['d_perpage'];
	 		$perpage = $ibforums->input['num'];
	 		if($ibforums->input['page'] == "" ) $ibforums->input['page'] = "1";
	 		$page = $ibforums->input['page'];
	 		if ($ibforums->input['group'] == "") $ibforums->input['group'] = "date";
			if ($ibforums->input['order'] == "") $ibforums->input['order'] = "DESC";
			$theoptions = "<option value='{$perpage}' selected='selected'>{$perpage}</option>";
			foreach($ibforums->vars['d_files_perpage'] as $filesperpage){
				if($filesperpage != $perpage){
					$theoptions .= "<option value='{$filesperpage}'>{$filesperpage}</option>";
				}
			}
		
			if($ibforums->input['cat'] == "all"){
               	$sub = "";
				$subn = "";
				$subc = "";
			} else {
               	$sub = " AND s.cat = ".$ibforums->input['cat'];
				$subn = " AND cat = ".$ibforums->input['cat'];
				$subc = " AND cid= ".$ibforums->input['cat'];
			}
					
			$pullnav = $DB->query( "SELECT * FROM ibf_files_cats WHERE copen = 1 ".$subc );
			$subcat = $DB->fetch_row($pullnav);
	 		if($DB->get_num_rows($pullnav)==0) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cat_not_found') );
	 		}
			
			$needwrapper = $this->set_wrapper($subcat);			

			$catarray[0] = "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>";
			if($ibforums->input['cat'] == 'all'){
				$cattitle = " -> {$ibforums->lang['view_all_cats']}";
				$catarray[1] = $ibforums->lang['view_all_cats'];
			} else {
			$cattitle = " -> {$subcat['cname']}";
			if($subcat['sub'] != 0){
				$DB->query("SELECT cname, cid FROM ibf_files_cats WHERE copen= 1 and cid = '{$subcat['sub']}'");
				$add_nav = $DB->fetch_row();
				$catarray[1] = "<a href='".$this->base_url."dlcategory={$add_nav['cid']}'>{$add_nav['cname']}</a>";
				$cattitle = " -> {$add_nav['cname']} {$cattitle}";
			}
			$catarray[2] = "<a href='".$this->base_url."dlcategory={$subcat['cid']}'>{$subcat['cname']}</a>";
			}
			$cattitle = "{$ibforums->lang['title']} {$cattitle}";

			$cats = "";
			$cats = "<option value='{$subcat['cid']}' selected='selected'>{$subcat['cname']}</option>";
			$catquery = $DB->query( "SELECT cid,cname,sub FROM ibf_files_cats WHERE copen = 1 AND cid != '".$subcat['cid']."' ORDER BY cid ASC" );
		    	while($row1 = $DB->fetch_row($catquery)){
			    if($row1['sub'] == 0) {
				    $cats .= "<option value='{$row1['cid']}'>{$row1['cname']}</option>";
			    } else {
				    $cats .= "<option value='{$row1['cid']}'>&nbsp;&nbsp; - {$row1['cname']}</option>";
			    }
		  	}

			$this->output .= $this->html->cat_header($cattitle,$cats,$theoptions,$perpage);

			if($ibforums->input['cat'] != 'all'){
			$pull_subcats = $DB->query("SELECT * FROM ibf_files_cats WHERE sub = '".$ibforums->input['cat']."'");
			if($DB->get_num_rows($pull_subcats) > 0){	
				while($unlimited = $DB->fetch_row($pull_subcats)){
 					$last_file = $DB->query("SELECT id,sname,author FROM ibf_files WHERE cat = ".$unlimited['cid']." AND open = 1 ORDER by date DESC");
					$last = $DB->fetch_row($last_file);
								if($DB->get_num_rows($last_file) > 0){
									$last['files'] = $DB->get_num_rows($last_file);
	      							$last['date'] = date("j.m.Y - H:i",$last['date']);
									$last['sname'] = "<a href='index.php?download={$last['id']}'>{$last['sname']}</a>";
								} else {
									$last['files'] = "0";								
									$last['date'] = $ibforums->lang['no_cat_info'];
									$last['author'] = $ibforums->lang['no_cat_info'];
									$last['sname'] = $ibforums->lang['no_cat_info'];
								}					
					$this->output .= $this->html->cats_row_without_subs($unlimited,$last);
				}
			} else {
				$check = 1;
			}		
			}
				
 	      	$grab_num_files = $DB->query("SELECT COUNT(id) as count FROM ibf_files WHERE open = 1". $subn);
            	$scriptc = $DB->fetch_row($grab_num_files);
           		$number = $scriptc['count'] / $perpage;
           		$start1 = $page-1;
           		$start = $start1 * $perpage;
			if( $scriptc > 0){	
			if(($ibforums->vars['d_dis_screen_cat'] == "0" || ($ibforums->vars['d_dis_screen_cat'] == "2" && $subcat['dis_screen_cat'] == "0" )) && $scriptc['count'] > 0){
				$this->output .= $this->html->cat_noss_header( );
			}	
			}			

			if(!isset($ibforums->input['order']) && !isset($ibforums->input['group'])){
				$findscript = $DB->query("SELECT s.*,c.dis_screen_cat FROM ibf_files s
									LEFT JOIN ibf_files_cats c ON (s.cat=c.cid) WHERE s.open = 1 ".$sub." ORDER BY s.date DESC LIMIT $start,$perpage");
				if( $DB->get_num_rows($findscript) == 0 && $check == 1) {
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_files_cat') );
				}
 			} else {
				$findscript = $DB->query( "SELECT s.*, c.dis_screen_cat, c.cname FROM ibf_files s
									LEFT JOIN ibf_files_cats c ON (s.cat=c.cid) WHERE s.open = 1 ".$sub." ORDER BY s.".$ibforums->input['group']." ".$ibforums->input['order']." LIMIT $start,$perpage");
				if( $DB->get_num_rows($findscript) == 0 && $check == 1) {
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_files_cat') );
				}
 			}
			if( $DB->get_num_rows($findscript) > 0){
			while($row = $DB->fetch_row($findscript)) {
			
                $row['date'] = date("j.m.Y - H:i",$row['date']);
			  	if ($row['updated']){
					$row['updated'] = date("j.m.Y - H:i",$row['updated']);
			  	} else {
					$row['updated'] = $ibforums->lang['never_updated'];
			  	}
				if($row['author'] == $row['poster']) {
					$row['author'] = "<a href='{$ibforums->base_url}showuser={$row['mid']}'>{$row['author']}</a>";
				} else {
					$row['author'] = $row['author'];
				}
				if($row['rating'] == 0){
					$row['rating'] = $ibforums->lang['not_rated'];
				} else {
					$row['rating'] = $this->return_rate($row);
				}
			if($ibforums->input['cat'] != 'all'){
				$row['cat_extra'] = "";		
				if($ibforums->vars['d_dis_screen_cat'] == "1" || ($ibforums->vars['d_dis_screen_cat'] == "2" && $row['dis_screen_cat'] == "1")){
					$row['screen_extra'] = "1";
					$row['screenshot'] = $this->screenshot($row);
					$this->output .= $this->html->files($row);
				} elseif ($ibforums->vars['d_dis_screen_cat'] == "0" || ($ibforums->vars['d_dis_screen_cat'] == "2" && $row['dis_screen_cat'] == "0")) {
					$row['screenshot'] = "";
					$this->output .= $this->html->files1($row);
				}
			} else {
				$row['cat_extra'] = "<br />(".$row['cname'].")";			
				$row['screenshot'] = "";
				$this->output .= $this->html->files1($row);
			}
			}
			if($ibforums->vars['d_dis_screen_cat'] == "0" || ($ibforums->vars['d_dis_screen_cat'] == "2" && $subcat['dis_screen_cat'] == "0" )){
				$this->output .= $this->html->cat_noss_footer( );
			}	

			
			$b_pages = array();
			$b_num = 5;

			if ( ($scriptc % $perpage == 0 ))
			{
				$b_pages['pages'] = $number;
			}
			else
			{
				$b_pages['pages'] = ceil( $number);
			}
			$b_pages['total_page']   = $b_pages['pages'];
			$b_pages['current_page'] = $page > 0 ? ($page / $perpage) + 1 : 1;
			if ($b_pages['pages'] > 1)
			{
				$b_pages['first_page'] = "{$ibforums->lang['word_pages']}&nbsp;";
				for( $i = 0; $i <= $b_pages['pages'] - 1; ++$i )
				{
					$RealNo = $i+1;
					$PageNo = $i+1;
					if ($RealNo == $page)
					{
						$b_pages['page_span'] .= "&nbsp;<b>[{$PageNo}]</b>";
					}
					else
					{
						if ($PageNo < ($page - $b_num))
						{
							$b_pages['st_dots'] = "&nbsp;<a href='".$this->base_url."dlcategory={$ibforums->input['cat']}&amp;page=1&amp;num={$ibforums->input['num']}&amp;cur_num={$perpage}'>&laquo; {$ibforums->lang['first_page']}</a>&nbsp;...";
							continue;
						}
						if ($PageNo > ($page + $b_num))
						{
							$b_pages['end_dots'] = "...&nbsp;<a href='".$this->base_url."dlcategory={$ibforums->input['cat']}&amp;page=".$b_pages['pages']."&amp;num={$ibforums->input['num']}&amp;cur_num={$perpage}'>{$ibforums->lang['last_page']} &raquo;</a>";
							break;
						}
						$b_pages['page_span'] .= "&nbsp;<a href='".$this->base_url."dlcategory={$ibforums->input['cat']}&amp;page={$RealNo}&amp;num={$ibforums->input['num']}&amp;cur_num={$perpage}'>{$PageNo}</a>";
					}
				}
				$numbers    = $b_pages['first_page'].$b_pages['st_dots'].$b_pages['page_span'].'&nbsp;'.$b_pages['end_dots'];
			} else {
				$numbers    = "[1]";
			}			
			} else {
				$numbers = "";
			}


			$this->output .= $this->html->cat_bottom($numbers);
			$this->nav = $catarray;
			$this->page_title = $ibforums->vars['board_name'] ." -> ". $cattitle;

		}
		elseif($ibforums->input['type'] == "file") {
			if(!$ibforums->input['id'] || $ibforums->input['id'] == "" ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_file') );
			}
			if($this->member['g_d_approve_down'] == "1"){
				$open = "";
			} else {
				$open = " AND f.open = '1'";
			}

	      	$DB->query("SELECT f.*, c.*, v.mid as vid, v.did, fav.id as favid, fav.fid, fav.mid as favmid,m.mgroup FROM ibf_files f
					LEFT JOIN ibf_files_cats c ON (f.cat=c.cid)
					LEFT JOIN ibf_files_votes v ON (f.id=v.did AND v.mid={$this->member['id']})
					LEFT JOIN ibf_files_favorites fav ON (f.id=fav.fid AND fav.mid={$this->member['id']})
					LEFT JOIN ibf_members m ON (f.mid=m.id) WHERE f.id =".$ibforums->input['id'].$open);
			if( $DB->get_num_rows() == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_file') );
			}
			$row = $DB->fetch_row();
			
			$needwrapper = $this->set_wrapper($row);			

			$catarray[0] = "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>";
			$cattitle = " -> ".$row['cname'];
			if($row['sub'] != 0){
				$DB->query("SELECT cname, cid FROM ibf_files_cats WHERE copen = 1 and cid ='{$row['sub']}'");
				$add_nav = $DB->fetch_row();
				$catarray[1] = "<a href='".$this->base_url."dlcategory={$add_nav['cid']}'>{$add_nav['cname']}</a>";
				$cattitle = " -> {$add_nav['cname']} {$cattitle}";
			}
			$catarray[2] = "<a href='".$this->base_url."dlcategory={$row['cid']}'>{$row['cname']}</a>";
			$catarray[3] = "<a href='".$this->base_url."download={$row['id']}'>{$row['sname']}</a>";
			$cattitle = "{$ibforums->lang['title']} {$cattitle} -> {$row['sname']}";

			$row['screen_extra'] = "0";
			$row['screenshot'] = $this->screenshot($row);

			if ($row['author'] == $row['poster']) {
				$row['author'] = "<a href='{$ibforums->base_url}showuser={$row['mid']}'>{$row['author']}</a>";
			} else {
				$row['author'] = $row['author'];
			}

 			if( $row['topic'] == "" || $row['topic'] == "0" ) {
 				$row['topic'] = "<i>{$ibforums->lang['t_notavail']}</i>";
 			} else {
 				$row['topic'] = "<a href='{$ibforums->base_url}showtopic={$row['topic']}'>{$ibforums->lang['t_clickhere']}</a>";
 			}

			$grab_comments = $DB->query("SELECT * FROM ibf_files_comments WHERE file_id = {$row['id']} ORDER BY date DESC");
			if($DB->get_num_rows($grab_comments) > 0){
				while($com = $DB->fetch_row($grab_comments)){
					if(stristr($com['comment'],'[dohtml]')){
						$com['comment'] = $this->parser->post_db_parse($com['comment'],"1");
					}
					$com['date'] = date("j.m.Y - H:i",$com['date']);
					$the_comments .= $this->html->comment_row($com);
				}
			} else {
				$the_comments = $ibforums->lang['no_comments_yet'];
			}

			if($ibforums->vars['d_use_comments'] == 1){
				$row['dis_comm_now'] = $this->html->comment_is_on($the_comments, $row);
			}

    		$custom_out  = "";
		$field_data     = array();
		
		$DB->query("SELECT * from ibf_files_custentered WHERE file_id='".$row['id']."'");
		
		while ( $content = $DB->fetch_row() ){
			foreach($content as $k => $v)	{
				if ( preg_match( "/^field_(\d+)$/", $k, $match) ){
					$field_data[ $match[1] ] = $v;
				}
			}
		}
		
		$DB->query("SELECT * from ibf_files_custfields WHERE fshow=1");
		
		while( $cust = $DB->fetch_row() ){
			
			if ( $cust['ftype'] == 'drop' ){
				$carray = explode( '|', trim($cust['fcontent']) );
				
				foreach( $carray as $entry ){
					$value = explode( '=', $entry );
					
					$ov = trim($value[0]);
					$td = trim($value[1]);
					
					if ($field_data[ $cust['fid'] ] == $ov){
						$field_data[ $cust['fid'] ] = $td;
					}
					if ($field_data[ $cust['fid'] ] == ""){
						$field_data[ $cust['fid'] ] = $ibforums->lang['no_d_info'];
					}
				}
			} else {
				$field_data[ $cust['fid'] ] = ($field_data[ $cust['fid'] ] == "") ? $ibforums->lang['no_d_info'] : nl2br($field_data[ $cust['fid'] ]);
			}
			
    			$row['custom_out'] .= $this->html->custom_field($cust['ftitle'], $field_data[ $cust['fid'] ] );
		}
			$row['custom_out'] .= "    <tr><td colspan='2' width='100%'>&nbsp;</td></tr>";


			if($row['url'] != ""){
				$file = $ibforums->vars['d_download_dir'] . $row['url'];
				if( @filesize( $file ) == 0 ) {
					$row['filesize'] = $ibforums->lang['size_unavailable'];
				} elseif(@filesize($file) < 1024){
					$row['filesize'] = @round(@filesize($file),4)." {$ibforums->lang['bytes']}";
				} else {
					$row['filesize'] = @round((@filesize($file)/1024),2)." {$ibforums->lang['kb']}";
				}
			}
			if($row['link'] != ""){
				$fp = $this->file_get_contents( $row['link']);
				if (!$fp){
					if($ibforums->vars['d_link_check'] == 1){
						$linked = TRUE;
						$row['linksize'] = $ibforums->lang['size_unavailable'];
					} else {
						$row['linksize'] = $ibforums->lang['size_unavailable'];
					}
				} 
				elseif(@strlen($fp) < 1024){
					$row['linksize'] = "{$ibforums->lang['link_approx']}".@round(@strlen($fp),4)." {$ibforums->lang['bytes']}";
				} else {
					$row['linksize'] = "{$ibforums->lang['link_approx']}".@round((@strlen($fp)/1024),2)." {$ibforums->lang['kb']}";
				}
			}
			
			if($row['rating'] == 0){
				$row['rating'] = $ibforums->lang['not_rated'];
			} else {
				$row['rating'] = $this->return_rate($row);
			}

			if($row['mid'] = $this->member['id']){
				$row['edit_op'] = "<br /><a href='".$this->base_url."act=FileCP&amp;do=edit&amp;id={$row['id']}'>{$ibforums->lang['edit_own_dlpage']}</a>";
			} else {
				$row['edit_op'] = "";
			}
			
			if($row['favid']){
				$row['favid'] = "<a href='".$this->base_url."act=Downloads&amp;do=favorites&amp;type=remove&amp;favid={$row['favid']}&amp;id={$row['id']}'>{$ibforums->lang['remove_favorite']}</a>";
			} else {
				$row['favid'] = "<a href='".$this->base_url."act=Downloads&amp;do=favorites&amp;type=add&amp;name={$row['sname']}&amp;id={$row['id']}'>{$ibforums->lang['add_favorite']}</a>";
			}


			if(!$row['vid']) {
				$row['rate_now'] = $this->html->rating($row);
			} else {
				$row['rate_now'] = "";
			}
	    	  	$row['date'] = date("j.m.Y - H:i",$row['date']);
			if($row['updated']){
				$row['updated'] = date("j.m.Y - H:i",$row['updated']);
			} else {
				$row['updated'] = $ibforums->lang['never_updated'];
			}
			if(stristr($row['sdesc'],'[dohtml]')){
				$row['sdesc'] = $this->parser->post_db_parse($row['sdesc'],"1");
			}

			if($ibforums->vars['d_upload'] == 1 && ($row['url'] != "")) {
			      $row['dl_link'] = "<a href='".$this->base_url."act=Downloads&amp;do=download&amp;id={$row['id']}'>{$ibforums->lang['download_now']}</a>";
					$ext = strrchr($row['url'],'.');
 					$row['ext'] = strtolower($ext);				  
			}

        		if($ibforums->vars['d_linking'] == 1 && ($row['link'] !="")) {
					if ($row['url']){
        	 	   		$row['dl_link'] .=  "<br />";
					}	
					$ext = strrchr($row['link'],'.');
 					$row['ext'] = strtolower($ext);					
				if ($linked){
					$row['dl_link'] .= $ibforums->lang['link_is_down'];
				} else {
		 	   		$row['dl_link'] .= "<a href='".$this->base_url."act=Downloads&amp;do=download&amp;id={$row['id']}&amp;l=1'>{$ibforums->lang['download_direct']}</a>";
				}
        		}

			if($ibforums->vars['d_dis_screen'] == "0" || ($ibforums->vars['d_dis_screen'] == "2" && $row['dis_screen'] == "0")) {
				$info['width1'] = "10%";
				$info['width2'] = "40%";
				if($row['linksize']){
					$info['lang'] = $ibforums->lang['file_link_size'];
					$info['size'] = $row['linksize'];
					$row['linksize'] = $this->html->size_row($info);
				} else {
					$row['linksize'] = "";
				}
				if($row['filesize']){
					$info['lang'] = $ibforums->lang['file_size'];
					$info['size'] = $row['filesize'];
					$row['filesize'] = $this->html->size_row($info);
				} else {
					$row['filesize'] = "";
				}	
				if($ibforums->vars['d_create_topic'] != "0"){
					$info['topic'] = $row['topic'];
					$row['topicrow'] = $this->html->topic_row($info);
				} else {
					$row['topicrow'] = "";
				}		
			if ($ibforums->member['g_is_supmod'] != 1 && $ibforums->member['g_d_manage_files'] != 1) {
				$row['ipaddress'] = "";
			} else {
				$info['ip_address'] = $row['mgroup'] == $ibforums->vars['admin_group']
						  ? "[ ---------- ]"
						  : "[ <a href='{$ibforums->base_url}act=modcp&amp;CODE=ip&amp;incoming={$row['ipaddress']}' target='_blank'>{$row['ipaddress']}</a> ]";
				$row['ipaddress'] = $this->html->ip_view($info);

			}
				$this->output .= $this->html->file_view1($row,$cattitle,$the_comments);
			} elseif ($ibforums->vars['d_dis_screen'] == "1" || ($ibforums->vars['d_dis_screen'] == "2" && $row['dis_screen'] == "1")) {
				$info['width1'] = "30%";
				$info['width2'] = "70%";
				if($row['linksize']){
					$info['lang'] = $ibforums->lang['file_link_size'];
					$info['size'] = $row['linksize'];
					$row['linksize'] = $this->html->size_row($info);
				} else {
					$row['linksize'] = "";
				}
				if($row['filesize']){
					$info['lang'] = $ibforums->lang['file_size'];
					$info['size'] = $row['filesize'];
					$row['filesize'] = $this->html->size_row($info);
				} else {
					$row['filesize'] = "";
				}	
				if($ibforums->vars['d_create_topic'] !="0"){
					$info['topic'] = $row['topic'];
					$row['topicrow'] = $this->html->topic_row($info);
				} else {
					$row['topicrow'] = "";
				}					
				
			if ($ibforums->member['g_is_supmod'] != 1 && $ibforums->member['g_d_manage_files'] != 1) {
				$row['ipaddress'] = "";
			} else {
				$info['ip_address'] = $row['mgroup'] == $ibforums->vars['admin_group']
						  ? "[ ---------- ]"
						  : "[ <a href='{$ibforums->base_url}act=modcp&amp;CODE=ip&amp;incoming={$row['ipaddress']}' target='_blank'>{$row['ipaddress']}</a> ]";
				$row['ipaddress'] = $this->html->ip_view($info);

			}
				$this->output .= $this->html->file_view($row,$cattitle,$the_comments);
			}
			$DB->query( "UPDATE ibf_files SET views = views+1 WHERE id = {$ibforums->input['id']}");

			$this->nav = $catarray;
        	$this->page_title = $ibforums->vars['board_name'] ." -> ". $cattitle;
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


   //-----------------------------------------------------

   //-----------------------------------------------------
   // Add File Functions
   //-----------------------------------------------------

	function add( )
	{

		global $print, $ibforums, $std, $DB;
		
		$here = "";
		$needwrapper = $this->set_wrapper($here);				

		if( $this->member['g_d_add_files'] == 1 ) {
			$cats = "";
			$catquery = $DB->query("SELECT cid,cname,sub FROM ibf_files_cats WHERE copen = 1 AND sub= 0 ORDER BY cid ASC" );
			while($row = $DB->fetch_row($catquery)){
				$cats .= "<option value='{$row['cid']}'>{$row['cname']}</option>";
				$catquery1 = $DB->query("SELECT cid,cname FROM ibf_files_cats WHERE sub = '".$row['cid']."'");
				if($DB->get_num_rows($catquery1) > 0 ){
					while($row1 = $DB->fetch_row($catquery1)){
						$cats .= "<option value='{$row1['cid']}'>&nbsp;&nbsp; - {$row1['cname']}</option>";
					}

				}
			}

		$required_output = "";
		$optional_output = "";
		$field_data     = array();
		
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
						$d_content .= "<option value='$ov'>$td</option>\n";
					}
				}
				
				if ($d_content != ""){
					$form_element = $this->html->field_dropdown( 'field_'.$cust['fid'], $d_content );
				}
			} else if ( $cust['ftype'] == 'area' ) {
				$form_element = $this->html->field_textarea( 'field_'.$cust['fid'], $ibforums->input['field_'.$cust['fid']] );
			} else {
				$form_element = $this->html->field_textinput( 'field_'.$cust['fid'], $ibforums->input['field_'.$cust['fid']] );
			}
			
			${$ftype} .= $this->html->field_entry( $cust['ftitle'], $form_element, $reqq );
		}



			$ext = "";
        	foreach( $ibforums->vars['d_allowable_ext'] as $value){
         	   	$ext .= $value."|";
       	 	}
        	$ext = substr($ext ,0 ,-1);
		$teext = wordwrap($ext, 20, "<br />&nbsp;&nbsp;",1);


			$sext = "";
        	foreach( $ibforums->vars['d_screenshot_ext'] as $value){
         		$sext .= $value."|";
       	 	}
        	$sext = substr($sext ,0 ,-1);
		$tesext = wordwrap($sext, 20, "<br />&nbsp;&nbsp;",1);

		if($ibforums->vars['d_screenshot_allowed'] == 1){
			if($ibforums->vars['d_screenshot_required'] == 1){
				$ssreq = "*";
			} else {
				$ssreq = "";
			}
			$link .= $this->html->screen_upload($tesext,$ssreq);
		}

            if($ibforums->vars['d_upload']){
                $link .= $this->html->upload_file($teext);
            }

            if($ibforums->vars['d_linking']){
			    $default = "";
                $link .= $this->html->link($default,$teext);
            }

			$this->output .= $this->html->show_add_form($cats,$link,$required_output,$optional_output);

			$this->nav = array( "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>",
							"<a href='".$this->base_url."act=Downloads&amp;do=add'>{$ibforums->lang['nav_add']}</a>" );
            $this->page_title = $ibforums->vars['board_name'] . " -> {$ibforums->lang['title']} -> {$ibforums->lang['title_add']}";
		} else {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_perm') );
		}
	}

	function do_add( )
	{

		global $ibforums, $std, $print, $_FILES, $DB;

		if( $this->member['g_d_add_files'] == 1 ) {
			if( $ibforums->input['sname'] == "" ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_name_empty') );
			}

			if($ibforums->input['author'] == "" && $this->member['mgroup'] != $ibforums->vars['guest_group']){
				$author = $this->member['name'];
			} elseif($ibforums->input['author'] == "" && $this->member['mgroup'] == $ibforums->vars['guest_group']){
				$author = $ibforums->lang['comment_guest'];
			} else {
				$author = $ibforums->input['author'];
			}

			if( $ibforums->input['desc'] == "" ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_desc_empty') );
			}

			if($ibforums->vars['d_screenshot_required'] == 1 && ($_FILES['screen']['name'] == "") && (!$_FILES['screen']['name'] || $_FILES['screen']['name'] == "none")) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_screen_empty') );
			}

			if($ibforums->input['link']=="" && ($_FILES['file']['name'] == "" || !$_FILES['file']['name'] || ($_FILES['file']['name'] == "none"))){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_file_empty') );
			}

			if( $ibforums->input['cat'] == "" ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_cat_empty') );
			}

			if ($ibforums->member['g_d_html_files']) {
				$ibforums->input['desc'] = "[dohtml]".$ibforums->input['desc']."[/dohtml]";
			}

		$custom_fields = array();
		
		$DB->query("SELECT * from ibf_files_custfields WHERE fshow=1");
		
		while ( $cust = $DB->fetch_row() ){
			if ($cust['freq'] == '1'){
				if ($ibforums->input['field_'.$cust['fid']] == ""){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
				}
			}
			
			if ($cust['fmaxinput'] > 0){
				if (strlen($ibforums->input['field_'.$cust['fid']]) > $cust['fmaxinput']){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cf_to_long', 'EXTRA' => $cust['ftitle'] ) );
				}
			}

			if($cust['ftopic'] == 1){
				if ($ibforums->input['field_'.$cust['fid']] != ""){
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
			
			$custom_fields[ 'field_'.$cust['fid'] ] = str_replace( '<br>', "\n", $ibforums->input[ 'field_'.$cust['fid'] ] );
		}


			$DB->query( "SELECT cname,authorize,fordaforum FROM ibf_files_cats WHERE cid = ".$ibforums->input['cat']." AND copen = 1");
			$result = $DB->fetch_row();
			if( $DB->get_num_rows( ) == 0 ) {
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cat_not_found') );
			}

			$DB->query( "SELECT MAX(id) as fid FROM ibf_files" );
			$lastq = $DB->fetch_row( );
			$last  = $lastq['fid'];
			$next_insert = $last + 1;

			//-------------------------------------------------
			// Partly taken from IBFORUMS code
			// And some code written by Parmeet for Download Mod
			//-------------------------------------------------
			// Set up some variables to stop carpals developing
			//-------------------------------------------------

			if($ibforums->vars['d_screenshot_allowed'] == 1){
				if(($_FILES['screen']['name'] != "") && ($_FILES['screen']['name'] || $_FILES['screen']['name'] != "none")){

					$SCREEN_NAME = $_FILES['screen']['name'];
					$FILE_SIZE = $_FILES['screen']['size'] / 1024;
					$FILE_TYPE = $_FILES['screen']['type'];

					if($FILE_SIZE > $ibforums->vars['d_screen_max_dwnld_size']){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_upload_too_big'));
					}

					//-------------------------------------------------
					// Make the uploaded file safe
					//-------------------------------------------------

					$SCREEN_NAME = preg_replace( "/[^\w\.]/", "_", $SCREEN_NAME );
    			    $ext = strrchr($SCREEN_NAME,'.');
 					$ext = strtolower($ext);
				    $screen_file = "[".$next_insert."]".$SCREEN_NAME;
	    		    if(!in_array($ext ,$ibforums->vars['d_screenshot_ext'])) {
	    				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	     		    }

					if (!@move_uploaded_file($_FILES['screen']['tmp_name'], $ibforums->vars['d_screen_dir'].$screen_file)){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_upload_error') );
					}
					@chmod($ibforums->vars['d_screen_dir'].$screen_file, 0777);
				}
			}elseif (($ibforums->vars['d_screenshot_allowed'] != 1) && ($_FILES['screen']['name'] != "") && ($_FILES['screen']['name'] || $_FILES['screen']['name'] != "none")){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_not_allowed') );
			}

			if($ibforums->vars['d_upload'] == 1 && !($_FILES['file']['name'] == "" || !$_FILES['file']['name'] || ($_FILES['file']['name'] == "none"))){
				
					$FILE_NAME = $_FILES['file']['name'];
					$FILE_SIZE = $_FILES['file']['size'] / 1024;
					$FILE_TYPE = $_FILES['file']['type'];


					if(($ibforums->vars['d_upload'] == 1) && ($ibforums->input['link']=="") && ($_FILES['file']['name'] == "" || !$_FILES['file']['name'] || ($_FILES['file']['name'] == "none"))){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_file_empty') );
					}

					if($FILE_SIZE > $ibforums->vars['d_max_dwnld_size']){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_upload_big') );
					}

            		//-------------------------------------------------
	    			// Make the uploaded file safe
		    		//-------------------------------------------------

    				$FILE_NAME = preg_replace( "/[^\w\.]/", "_", $FILE_NAME );

    			    $ext = strrchr($FILE_NAME,'.');
 					$ext = strtolower($ext);
					$FILE_NAME = "[".$next_insert."]".$FILE_NAME;
	    			if(!in_array($ext ,$ibforums->vars['d_allowable_ext'])) {
	    				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	    			}

            		if(!@move_uploaded_file($_FILES['file']['tmp_name'], $ibforums->vars['d_download_dir'].$FILE_NAME)){
	    				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_upload_error') );
	    			}
					@chmod($ibforums->vars['d_download_dir'].$FILE_NAME, 0777);
					$current=1;
					$verifyfile = $FILE_NAME;
			}

            		if($ibforums->vars['d_authorize'] == 0 || ($ibforums->vars['d_admin_auto'] == 1 && $this->member['mgroup'] == $ibforums->vars['admin_group'])){
                			$open = 1;
            		}elseif($ibforums->vars['d_authorize'] == 1 && ($ibforums->vars['d_admin_auto'] == 0 ||($ibforums->vars['d_admin_auto'] == 1 && $this->member['mgroup'] != $ibforums->vars['admin_group']))){
                			$open = 0;
            		}elseif ($ibforums->vars['d_authorize'] == 2) {
						if ($result['authorize'] == 0 || ($ibforums->vars['d_admin_auto'] == 1 && $ibforums->member['mgroup'] == $ibforums->vars['admin_group'])){
							$open = 1;
						}elseif($result['authorize'] == 1 && ($ibforums->vars['d_admin_auto'] == 0 ||($ibforums->vars['d_admin_auto'] == 1 && $this->member['mgroup'] != $ibforums->vars['admin_group']))){
							$open = 0;
						}
					}


            		if($ibforums->vars['d_linking'] && $ibforums->input['link']!=""){
						$ext = strrchr($ibforums->input['link'],'.');
 						$ext = strtolower($ext);

	    				if(!(in_array($ext, $ibforums->vars['d_allowable_ext']))){
	    					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	    				}
						$link = $ibforums->input['link'];
            		}else{
						$link="";
					}


				// Topic Stuff

				if($ibforums->vars['d_cat_add'] == 1) {
					$bftitle = "[{$result['cname']}] {$ibforums->input['sname']}";
				} elseif ($ibforums->vars['d_cat_add'] == 0) {
					$bftitle = "{$ibforums->input['sname']}";
				}

				if ($ibforums->vars['d_create_topic'] == 'percat') {
					$topic_forum = $result['fordaforum'];
				} elseif ($ibforums->vars['d_create_topic'] == "0") {
					$topic_forum = "";
				} else {
					$topic_forum = $ibforums->vars['d_create_topic'];
				}


				if( $topic_forum != "" && ($ibforums->vars['d_authorize'] == 0 || ($ibforums->vars['d_authorize'] == 2 && $result['authorize'] == 0) || ($ibforums->vars['d_admin_auto'] == 1 && $this->member['g_id'] == $ibforums->vars['admin_group'])) ) {

				$do_post = array('title'	=> $bftitle,
							'extra_crap'	=> $outto_topic,
							'right_forum'	=> $result['fordaforum'],
							'author'		=> $author,
							'author_id'		=> $this->member['id'] ? $this->member['id'] : 0,
							'fid'			=> $topic_forum,
							'sname'		=> $ibforums->input['sname'],
							'edit'		=> "",
							'starter_id'      => $this->member['id'],
					 		'last_poster_id'   => $this->member['id'],
					 		'last_poster_name' => $this->member['id'] ?  $this->member['name'] : $ibforums->input['author'],
							'cname'		=> $result['cname'],
							'sdesc'		=> $ibforums->input['desc'],
							'username'	=> $ibforums->input['author'],
							'sid'		=> $next_insert,
							'author_mode'	=> $this->member['id'],
							'ipadd'		=> $ibforums->input['IP_ADDRESS'],
							'enabletra'	=> $ibforums->input['enabletra'],
						);

				$postit = $this->post_topic($do_post);

				} else {
					$postit = 0;
				}


		    		$the_file = array(
						'id'          => $next_insert,
						'cat'         => $ibforums->input['cat'],
						'sname'       => $ibforums->input['sname'],
						// ib code oska fixed
						'sdesc'        => $this->parser->convert( array( TEXT   => $ibforums->input['desc'],
														 SMILIES => 1,
														 CODE    => $this->member['g_d_ibcode_files'],
														 HTML    => $this->member['g_d_html_files']
													) ),
						// ib code oska fixed
						'author'      => $author,
						'open'        => $open,
						'poster'      => $this->member['name'],
						'mid'      => $this->member['id'],
						'date'        => time(),
						'topic'	  => $postit,
						'screenshot'      => $screen_file,
						'url'         => $FILE_NAME,
						'link'        => $link,
						'current'	  => $current,
						'ipaddress'	  => $ibforums->input['IP_ADDRESS'],
					 );

		    	$db_string = $DB->compile_db_insert_string($the_file);
				$DB->query("INSERT INTO ibf_files (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
			unset($db_string);

		
			$DB->query("DELETE FROM ibf_files_custentered WHERE file_id=".$next_insert);
		
			$custom_fields['file_id'] = $next_insert;
			
			$db_string = $DB->compile_db_insert_string($custom_fields);
			
			$DB->query("INSERT INTO ibf_files_custentered (".$db_string['FIELD_NAMES'].") VALUES(".$db_string['FIELD_VALUES'].")");
		
			unset($db_string);


				if( $ibforums->vars['d_authorize'] == 0 || ($ibforums->vars['d_authorize'] == 2 && $result['authorize'] == 0) ||($ibforums->vars['d_admin_auto'] == 1 && $this->member['mgroup'] == $ibforums->vars['admin_group'])) {
          			$DB->query("UPDATE ibf_members SET files=files + 1 WHERE id='".$this->member['id']."'");
					$print->redirect_screen("{$ibforums->lang['redirect_submission']}", "download=".$next_insert);
					exit();

				} elseif (($ibforums->vars['d_authorize'] == 1 || ($ibforums->vars['d_authorize'] == 2 && $result['authorize'] == 1))&& ($ibforums->vars['d_admin_auto'] == 0 ||($ibforums->vars['d_admin_auto'] == 1 && $this->member['mgroup'] != $ibforums->vars['admin_group']))){
					$print->redirect_screen("{$ibforums->lang['redirect_approval']}", "act=Downloads" );
					exit();
				}
		}else{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}
	}


   //-----------------------------------------------------


   //-----------------------------------------------------
   // Edit File Functions
   //-----------------------------------------------------

	function edit( ) {

		global $ibforums, $std, $print, $_FILES, $DB;

		if( $ibforums->input['id'] == "" ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_id') );
		}

		if( $this->member['g_d_edit_files'] == 1 ) {
			if( $this->member['g_d_eofs'] == 1 ) {
				$DB->query("SELECT f.*,c.cid,c.cname,c.fordaforum FROM ibf_files f, ibf_files_cats c WHERE f.id = ".$ibforums->input['id']." AND f.cat=c.cid");
			} else {
				$DB->query("SELECT f.*,c.cid,c.cname,c.fordaforum FROM ibf_files f, ibf_files_cats c WHERE f.mid = '".$this->member['id']."' AND f.id = '".$ibforums->input['id']."' AND f.open = 1 AND f.cat=c.cid");
			}
			if($DB->get_num_rows( ) == 0){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_dwnld_mem') );
			} else {

				$result = $DB->fetch_row( );

				if( $ibforums->input['sname'] == "" ) {
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_name_empty') );
				}
				if( $ibforums->input['desc'] == "" ) {
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_desc_empty') );
				}
				if( $ibforums->input['cat'] == "" ) {
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_cat_empty') );
                		}
				if ($this->member['g_d_html_files']) {
					$ibforums->input['desc'] = "[dohtml]".$ibforums->input['desc']."[/dohtml]";
				}
				if( $ibforums->input['author'] == "" && $this->member['mgroup'] != $ibforums->vars['guest_group'] ) {
					$author = $result['author'];
				} else {
					$author = $ibforums->input['author'];
				}

		$custom_fields = array();
		
		$DB->query("SELECT * from ibf_files_custfields WHERE fshow=1");
		
		while ( $cust = $DB->fetch_row() ){
			if ($cust['freq'] == '1'){
				if ($ibforums->input['field_'.$cust['fid']] == ""){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'complete_form' ) );
				}
			}
			
			if ($cust['fmaxinput'] > 0){
				if (strlen($ibforums->input['field_'.$cust['fid']]) > $cust['fmaxinput']){
					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'cf_to_long', 'EXTRA' => $cust['ftitle'] ) );
				}
			}

			if($cust['ftopic'] == 1){
				if ($ibforums->input['field_'.$cust['fid']] != ""){
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

			$custom_fields[ 'field_'.$cust['fid'] ] = str_replace( '<br>', "\n", $ibforums->input[ 'field_'.$cust['fid'] ] );
		}


			if($ibforums->vars['d_screenshot_allowed'] == 1){
						if( $_FILES['screen']['name'] == "" or !$_FILES['screen']['name'] or ($_FILES['screen']['name'] == "none")  ) {
							$screen_file = $result['screenshot'];
						} else{

					$FILE_NAME = $_FILES['screen']['name'];
					$FILE_SIZE = $_FILES['screen']['size'] / 1024;
					$FILE_TYPE = $_FILES['screen']['type'];

					if($FILE_SIZE > $ibforums->vars['d_screen_max_dwnld_size']){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_upload_too_big'));
					}

					//-------------------------------------------------
					// Make the uploaded file safe
					//-------------------------------------------------

					$FILE_NAME = preg_replace( "/[^\w\.]/", "_", $FILE_NAME );
    			    		$ext = strrchr($FILE_NAME,'.');
 					$ext = strtolower($ext);
				    	$screen_file = "[".$result['id']."]".$FILE_NAME;
	    		    		if(!in_array($ext ,$ibforums->vars['d_screenshot_ext'])) {
	    			    		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	     		    		}

					if (!@move_uploaded_file($_FILES['screen']['tmp_name'], $ibforums->vars['d_screen_dir'].$screen_file)){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_upload_error') );
					}
					@chmod($ibforums->vars['d_screen_dir'].$screen_file, 0777);
				} 
			}elseif (($ibforums->vars['d_screenshot_allowed'] == 0) && ($_FILES['screen']['name'] != "") && ($_FILES['screen']['name'] || $_FILES['screen']['name'] != "none")){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'screen_not_allowed') );
			}

					if($ibforums->input['link'] == "" &&( $_FILES['file']['name'] == "" or !$_FILES['file']['name'] or ($_FILES['file']['name'] == "none"))  ) {
                        		$FILE_NAME = $result['url'];
						$link = $result['link'];
						$current = $result['current'];
					}

			if($ibforums->vars['d_upload'] == 1 && !($_FILES['file']['name'] == "" || !$_FILES['file']['name'] || ($_FILES['file']['name'] == "none"))){
						$current = 1;
				
					$FILE_NAME = $_FILES['file']['name'];
					$FILE_SIZE = $_FILES['file']['size'] / 1024;
					$FILE_TYPE = $_FILES['file']['type'];


					if(($ibforums->vars['d_upload'] == 1) && ($ibforums->input['link']=="") && ($_FILES['file']['name'] == "" || !$_FILES['file']['name'] || ($_FILES['file']['name'] == "none"))){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'upload_file_empty') );
					}

					if($FILE_SIZE > $ibforums->vars['d_max_dwnld_size']){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_upload_big') );
					}

            			//-------------------------------------------------
	    				// Make the uploaded file safe
		    			//-------------------------------------------------

    					$FILE_NAME = preg_replace( "/[^\w\.]/", "_", $FILE_NAME );

    			    	$ext = strrchr($FILE_NAME,'.');
 						$ext = strtolower($ext);
						$FILE_NAME = "[".$result['id']."]".$FILE_NAME;
	    				if(!in_array($ext ,$ibforums->vars['d_allowable_ext'])) {
	    					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	    				}
						if( $result['current'] ) {
							if( ! @unlink( $ibforums->vars['d_download_dir'] .$result['url'] ) ) {
								$std->Error( array( 'LEVEL' => 1, 'MSG' => 'n_unlink') );
							}
						}


            			if(!@move_uploaded_file($_FILES['file']['tmp_name'], $ibforums->vars['d_download_dir'].$FILE_NAME)){
	    					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'file_upload_error') );
	    				}
						@chmod($ibforums->vars['d_download_dir'].$FILE_NAME, 0777);
						$current=1;
				} 

            		if($ibforums->vars['d_linking'] && $ibforums->input['link']!=""){
					$ext = strrchr($ibforums->input['link'],'.');
 					$ext = strtolower($ext);

	    				if(!(in_array($ext, $ibforums->vars['d_allowable_ext']))){
	    					$std->Error( array( 'LEVEL' => 1, 'MSG' => 'ext_error') );
	    				}
					$link = $ibforums->input['link'];
            		}else{
					$link="";
				}
				}


			$descfile = $this->parser->convert( array( TEXT    => $ibforums->input['desc'],
									   SMILIES => 1,
									   CODE    => $ibforums->member['g_d_ibcode_files'],
									   HTML    => $ibforums->member['g_d_html_files']
									)  );
			$DB->query( "UPDATE ibf_files SET sname = '".$ibforums->input['sname']."', cat = '".$ibforums->input['cat']."', author = '".$author."', updated = '".time()."', sdesc = '".addslashes($descfile)."', screenshot = '".$screen_file."', url = '".$FILE_NAME."', link = '".$link."', current = '".$current."' WHERE id = '".$ibforums->input['id']."'");
			$DB->query("DELETE FROM ibf_files_custentered WHERE file_id=".$ibforums->input['id']);
		
			$custom_fields['file_id'] = $ibforums->input['id'];
			
			$db_string = $DB->compile_db_insert_string($custom_fields);
			
			$DB->query("INSERT INTO ibf_files_custentered (".$db_string['FIELD_NAMES'].") VALUES(".$db_string['FIELD_VALUES'].")");
		
			unset($db_string);

				// Topic Posting
				if ($ibforums->vars['d_create_topic'] == 'percat') {
					$topic_forum = $result['fordaforum'];
				} elseif ($ibforums->vars['d_create_topic'] == "0") {
					$topic_forum = "";
				} else {
					$topic_forum = $ibforums->vars['d_create_topic'];
				}

				if(($result['topic'] != "") && ($result['topic'] != "0")) {

				$DB->query("UPDATE ibf_topics SET description='".$ibforums->lang['update_topic1']." " . date("j.m.Y - H:i") . "' WHERE tid=".$result['topic']);
				$DB->query( "SELECT pid, forum_id FROM ibf_posts WHERE topic_id = ".$result['topic'] );
				$pidgrab = $DB->fetch_row();
				//------------- POST CLASS COPIED ---------------//
				$post1 = "[B]{$ibforums->lang['t_filename']}[/B] :: {$ibforums->input['sname']}\n";
				$post1 .= "[B]{$ibforums->lang['t_author']}[/B] :: {$author}\n";
				$post1 .= "[B]{$ibforums->lang['t_cat']}[/B] :: {$result['cname']}\n";
				$post1 .= $outto_topic;
				$post1 .= "[B]{$ibforums->lang['t_desc']} :: [/B]\n";
				// start oska last update date
				$post2 = "\n[I]{$ibforums->lang['t_fileupdated']} " . date("j.m.Y - H:i") . "[/I]";
				// end oska last update date
				$post2 .= "\n\n[URL=".$this->base_url ."download=".$result['id']."]{$ibforums->lang['t_viewfile']}[/URL]\n";
				$desc = $this->parser->convert( array( TEXT    => $post1,
													   SMILIES => 1,
													   CODE    => 1,
													   HTML    => 0
												)  );
				$desc .= $descfile;

				$desc .= $this->parser->convert( array( TEXT    => $post2,
													   SMILIES => 1,
													   CODE    => 1,
													   HTML    => 0
												)  );
				$post = array(
						'append_edit' => 1,
						'author_id'   => $this->member['id'] ? $this->member['id'] : 0,
						'use_sig'     => 1,
						'use_emo'     => 1,
						'ip_address'  => $ibforums->input['IP_ADDRESS'],
						'edit_time'   => time(),
						'icon_id'     => 0,
						'post'        => $desc,
						'author_name' => $author,
						'forum_id'    => $pidgrab['forum_id'],
						'topic_id'    => $result['topic'],
						'queued'      => 0,
						'attach_id'   => "",
						'attach_hits' => "",
						'attach_type' => "",
					 );
				//------------- END POST CLASS COPIED ---------------//
				$db_string = $DB->compile_db_update_string( $post );

				$DB->query("UPDATE ibf_posts SET $db_string WHERE pid=".$pidgrab['pid']);
			}


           	if($result['open'] == 0) {
				$print->redirect_screen( "{$ibforums->lang['redirect_edit']}" , "act=Downloads" );
				exit();
			}else {
				$print->redirect_screen( "{$ibforums->lang['redirect_edit']}" , "download=".$result['id']);
				exit();
			}
		}else {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}
	}

   //-----------------------------------------------------



   //-----------------------------------------------------
   // Search Functions
   //-----------------------------------------------------

	function search( ){
		global $ibforums, $DB, $print, $std;

	if($ibforums->input['type'] == ""){

		$here = "";
		$needwrapper = $this->set_wrapper($here);						

		$cats = "";
		$DB->query( "SELECT cid,cname,sub FROM ibf_files_cats WHERE copen = 1 ORDER BY cid ASC" );
		while($row = $DB->fetch_row( )){
			if($row['sub'] == 0){
				$cats .= "<option value='{$row['cid']}'>{$row['cname']}</option>";
			} else {
				$cats .= "<option value='{$row['cid']}'>&nbsp;&nbsp; - {$row['cname']}</option>";
			}
		}
		foreach($ibforums->vars['d_files_perpage'] as $filesperpage){
			$theoptions .= "<option value='{$filesperpage}'>{$filesperpage}</option>";
		}

		$this->output .= $this->html->search_form($cats,$theoptions);

		$this->nav = array( "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>",
							"<a href='".$this->base_url."act=Downloads&amp;do=search'>{$ibforums->lang['nav_search']}</a>" );
        	$this->page_title = $ibforums->vars['board_name'] . " -> {$ibforums->lang['title']} -> {$ibforums->lang['title_search']}";
	}
	elseif($ibforums->input['type'] == "do_search"){

		if( ($ibforums->input['name'] == "") && ($ibforums->input['author'] == "") && ($ibforums->input['mid'] == "") && ($ibforums->input['desc'] == "") && ($ibforums->input['cat'] == "")){
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'd_no_search_field') );
		}
		
		$here = "";
		$needwrapper = $this->set_wrapper($here);						

		if( ($ibforums->vars['d_perpage'] == "") || ($ibforums->vars['d_perpage'] == "0")) $ibforums->vars['d_perpage'] = "10";
	 	if( $ibforums->input['num'] == "" ) $ibforums->input['num'] = $ibforums->vars['d_perpage'];
	 	$perpage = $ibforums->input['num'];
	 	if($ibforums->input['page'] == "" ) $ibforums->input['page'] = "1";
	 	$page = $ibforums->input['page'];
	 	if ($ibforums->input['group'] == "") $ibforums->input['group'] = "date";
		if ($ibforums->input['order'] == "") $ibforums->input['order'] = "DESC";


		if( $ibforums->input['name'] != "" ) {
			$name = "%".$ibforums->input['name']."%";
		} else { $name = NULL; }
		if( $ibforums->input['desc'] != "" ) {
			$desc = "%".$ibforums->input['desc']."%";
		} else { $desc = NULL; }
		if( $ibforums->input['author'] != "" ) {
			$author = "%".$ibforums->input['author']."%";
		} else { $author = NULL; }
		if( $ibforums->input['cat'] != "" ) {
			$cat = $ibforums->input['cat'];
		} else { $cat = NULL; }
		if( $ibforums->input['mid'] != "" ) {
			$mid = $ibforums->input['mid'];
		} else { $mid = NULL; }


 	      	$grab_num_files = $DB->query("SELECT COUNT(id) as count FROM ibf_files WHERE sname LIKE '{$name}' OR author LIKE '{$author}' OR sdesc LIKE '{$desc}' OR cat = '{$cat}' OR mid = '{$mid}' AND open = 1");
            	$scriptc = $DB->fetch_row($grab_num_files);
           		$number = $scriptc['count'] / $perpage;
           		$start1 = $page-1;
           		$start = $start1 * $perpage;


		if(!isset($ibforums->input['order']) && !isset($ibforums->input['group'])){
			$query = $DB->query("SELECT f.*, c.cname FROM ibf_files f
							LEFT JOIN ibf_files_cats c ON (f.cat=c.cid) WHERE f.sname LIKE '{$name}' OR f.author LIKE '{$author}' OR f.sdesc LIKE '{$desc}' OR f.cat = '{$cat}' OR f.mid = '{$mid}' AND open = 1 ORDER BY id DESC LIMIT $start,$perpage" );
		} else {
			$query = $DB->query("SELECT f.*, c.cname FROM ibf_files f
							LEFT JOIN ibf_files_cats c ON (f.cat=c.cid) WHERE f.sname LIKE '{$name}' OR f.author LIKE '{$author}' OR f.sdesc LIKE '{$desc}' OR f.cat = '{$cat}' OR f.mid = '{$mid}' AND open = 1 ORDER BY ".$ibforums->input['group']." ".$ibforums->input['order']." LIMIT $start,$perpage");
		}

		if($DB->get_num_rows( ) == 0){
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'd_no_search_result') );
		}

		$this->output .= $this->html->search_top( );

		while($row = $DB->fetch_row($query)){

			if(stristr($row['sdesc'],'[dohtml]')){
				$row['sdesc'] = $this->parser->post_db_parse($row['sdesc'],"1");
			}
			$row['sdesc']  = substr($row['sdesc'] , 0 , 20 )."...";
            	$row['date'] = date("j.m.Y - H:i",$row['date']);
			$this->output .= $this->html->search($row);
		}

			$b_pages = array();
			$b_num = 5;

			if ( ($scriptc % $perpage == 0 )){
				$b_pages['pages'] = $number;
			}else	{
				$b_pages['pages'] = ceil( $number);
			}

			$b_pages['total_page']   = $b_pages['pages'];
			$b_pages['current_page'] = $page > 0 ? ($page / $perpage) + 1 : 1;
			if ($b_pages['pages'] > 1){
				$b_pages['first_page'] = "{$ibforums->lang['word_pages']}&nbsp;";
				for( $i = 0; $i <= $b_pages['pages'] - 1; ++$i ){
					$RealNo = $i+1;
					$PageNo = $i+1;
					if ($RealNo == $page){
						$b_pages['page_span'] .= "&nbsp;<b>[{$PageNo}]</b>";
					}else	{
						if ($PageNo < ($page - $b_num)){
							$b_pages['st_dots'] = "&nbsp;<a href='".$this->base_url."act=Downloads&amp;do=search&amp;type=do_search&amp;name={$ibforums->input['name']}&amp;mid={$ibforums->input['mid']}&amp;desc={$ibforums->input['desc']}&amp;author={$ibforums->input['author']}&amp;cat={$ibforums->input['cat']}&amp;page=1'>&laquo; {$ibforums->lang['first_page']}</a>&nbsp;...";
							continue;
						}
						if ($PageNo > ($page + $b_num)){
							$b_pages['end_dots'] = "...&nbsp;<a href='".$this->base_url."act=Downloads&amp;do=search&amp;type=do_search&amp;name={$ibforums->input['name']}&amp;mid={$ibforums->input['mid']}&amp;desc={$ibforums->input['desc']}&amp;author={$ibforums->input['author']}&amp;cat={$ibforums->input['cat']}&amp;page=".$b_pages['pages']."'>{$ibforums->lang['last_page']} &raquo;</a>";
							break;
						}
						$b_pages['page_span'] .= "&nbsp;<a href='".$this->base_url."act=Downloads&amp;do=search&amp;type=do_search&amp;name={$ibforums->input['name']}&amp;mid={$ibforums->input['mid']}&amp;desc={$ibforums->input['desc']}&amp;author={$ibforums->input['author']}&amp;cat={$ibforums->input['cat']}&amp;page={$RealNo}'>{$PageNo}</a>";
					}
				}
				$numbers    = $b_pages['first_page'].$b_pages['st_dots'].$b_pages['page_span'].'&nbsp;'.$b_pages['end_dots'];
			} else {
				$numbers    = "[1]";
			}			

		$this->output .= $this->html->search_bottom($numbers);

		$this->nav = array( "<a href='".$this->base_url."act=Downloads'>{$ibforums->lang['nav']}</a>",
							"{$ibforums->lang['nav_search_results']}" );
        	$this->page_title = $ibforums->vars['board_name'] . " -> {$ibforums->lang['title']} -> {$ibforums->lang['title_search_results']}";
	}
	}

   //-----------------------------------------------------


   //-----------------------------------------------------
   // Download Functions
   //-----------------------------------------------------

	function download( ) {
		global $print , $DB , $ibforums , $std;

	// Download Code written originally by Parmeet and Sno,
	// modified by bfarber

		$id = $ibforums->input['id'];
		if($this->member['g_d_approve_down']) {
			$extra = "";
		} else {
			$extra = " AND open='1'";
		}
		$current = $DB->query( "SELECT sname,downloads,url,link FROM ibf_files WHERE id = {$id}{$extra}" );
			if($DB->get_num_rows($current) == 0){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_download') );
			}

		if( $this->member['g_d_max_dls'] > 0){
			$thetime = time()-86400;
			$limitit = $DB->query("SELECT COUNT(m_id) as id FROM ibf_files_downloads WHERE downloaded > {$thetime}");
			$limiter = $DB->fetch_row($limitit);
			if($limiter['id'] > ($this->member['g_d_max_dls'] - 1)){
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'too_many_dls') );
			}
		}

 		if( $this->member['g_do_download'] == 1 ) {
 			$count=$DB->fetch_row($current);

   		      if($ibforums->input['l'] == 1) {
	 		     	$download         = $count['link'];
            	}else{
              		$download         = $ibforums->vars['d_download_url'].$count['url'];
            	}

  			$mjcount = "downloads=downloads+1 ";
 			$memname = $this->member['name'];
 			$timebit = time();
 			$DB->query( "UPDATE ibf_files SET ".$mjcount." WHERE id = ".$id );
 			$DB->query( "UPDATE ibf_members SET ".$mjcount." WHERE id= ".$this->member['id']);
 			$DB->query( "INSERT into ibf_files_downloads ( m_id, member_name, downloaded, file_id, file_name) VALUES ( '{$this->member['id']}', '{$memname}', '{$timebit}', '{$id}', '{$count['sname']}' )" );

				if($ibforums->vars['d_force'] && $ibforums->input['l'] != '1'){
					if (!$this->send_file($count['url'])){
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'd_failure'));
					}
				}else{
					header("Location: ".$download); 
					print "<a href='".$download."'>{$ibforums->lang['click_redirect']}</a>";
				}
 				exit;
		} else {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'download_no_perm') );
		}
	}

	function send_file($name) {
		global $ibforums;

	// This code compliments of php.net

		$status = FALSE;
		$path = $ibforums->vars['d_download_dir'].$name;
 		$bffilename = substr(strrchr($name,']'),1);
		if (!is_file($path) or connection_status()!=0) return(FALSE);
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$bffilename."\"");
		header("Content-length: ".(string)(filesize($path)));
		header("Expires: ".gmdate("j.m.Y - H:i", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
 		header("Last-Modified: ".gmdate("j.m.Y - H:i")." GMT");
 		header("Cache-Control: no-cache, must-revalidate");
 		header("Pragma: no-cache");
		if($ibforums->vars['d_speed']!='0' && $ibforums->vars['d_speed'] !=''){
			$speed=round($ibforums->vars['d_speed']*1024);
			$d_speed=1;
		}else{
			$speed=filesize($path);
			$d_speed=0;
		}
 		if ($file = fopen($path, 'rb')) {
   			while(!feof($file) and (connection_status()==0)) {
   				print(fread($file, $speed));
   				flush();
				if($d_speed)sleep(1);
			}
 			$status = (connection_status()==0);
 			fclose($file);
 		}
 		return($status);
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
   // Screenshot Sizer
   //-----------------------------------------------------

	function screenshot ($data)
	{
		global $ibforums;

		if($data['screen_extra'] == '0'){
			$cat_script = $ibforums->vars['d_dis_screen'];
			$cat_screen = $data['dis_screen'];
		} else {
			$cat_script = $ibforums->vars['d_dis_screen_cat'];
			$cat_screen = $data['dis_screen_cat'];
		}

		if($ibforums->vars['d_show_thumb'] == '1') {
	
			$url = "{$ibforums->vars['d_screen_url']}{$data['screenshot']}";
			$imageInfo = @getimagesize($url); 
			$src_width = $imageInfo[0]; 
			$src_height = $imageInfo[1]; 
			$w = $ibforums->vars['d_thumb_w'];
			$h = $ibforums->vars['d_thumb_h'];

			if (($src_width < $w) AND ($src_height < $h))
				{
	            		$dest_height = $src_height; 
	            		$dest_width = $src_width; 
				}
	        	else if ($src_width > $src_height) 
	            	{ 
	            		$dest_width  = $w; 
	            		$dest_height = $this->unpercent($this->percent($dest_width, $src_width), $src_height);          
	            	} 
	        	else if ($src_height > $src_width) 
	            	{ 
	            		$dest_height = $h; 
	            		$dest_width = $this->unpercent($this->percent($dest_height, $src_height), $src_width); 
	            	} 
	        	else 
	            	{ 
	            		$dest_height = $h; 
	            		$dest_width = $w; 
	            	} 

			if($cat_script == 0) {
				if( $data['screenshot'] == "" ) {
					$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
				} else {
					$screen = "<a href='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' target='_blank'>{$ibforums->lang['click_here']}</a>";
				}
			} elseif($cat_script == 1) {
				if( $data['screenshot'] == "" ) {
					$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
				} else {
					$screen = "<img src='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' alt='{$ibforums->lang['screenshot_alt']}' align='center' width='{$dest_width}' height='{$dest_height}' />";
				}
			} elseif($cat_script == 2) {

				if($cat_screen == 1){
					if( $data['screenshot'] == "" ) {
						$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
					} else {
						$screen = "<img src='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' alt='{$ibforums->lang['screenshot_alt']}' align='center' width='{$dest_width}' height='{$dest_height}' />";
					}
				} else {
					if( $data['screenshot'] == "" ) {
						$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
					} else {
						$screen = "<a href='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' target='_blank'>{$ibforums->lang['click_here']}</a>";
					}
				}
			}
		} else {
			if($cat_script == 0) {
				if( $data['screenshot'] == "" ) {
					$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
				} else {
					$screen = "<a href='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' target='_blank'>{$ibforums->lang['click_here']}</a>";
				}
			} elseif($cat_script == 1) {
				if( $data['screenshot'] == "" ) {
					$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
				} else {
					$screen = "<img src='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' alt='{$ibforums->lang['screenshot_alt']}' align='center' />";
				}
			} elseif($cat_script == 2) {

				if($cat_screen == 1){
					if( $data['screenshot'] == "" ) {
						$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
					} else {
						$screen = "<img src='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' alt='{$ibforums->lang['screenshot_alt']}' align='center' />";
					}
				} else {
					if( $data['screenshot'] == "" ) {
						$screen = "<i>{$ibforums->lang['ss_not_available']}</i>";
					} else {
						$screen = "<a href='{$ibforums->vars['d_screen_url']}{$data['screenshot']}' target='_blank'>{$ibforums->lang['click_here']}</a>";
					}
				}
			}
		}

		return $screen;
	}
	
		function percent($p, $w) 
    		{ 
    			return (float)(100 * ($p / $w)); 
    		} 

		function unpercent($percent, $whole) 
    		{ 
    			return (float)(($percent * $whole) / 100); 
    		} 

   //-----------------------------------------------------


   //-----------------------------------------------------
   // Rating Functions
   //-----------------------------------------------------

	function do_rating( ) {
		global $ibforums , $DB, $print , $std;
		if( $this->member['id'] == "" || $this->member['id'] == "0") {
		    $std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
       	}
		if( $ibforums->input['rate'] == "" || $ibforums->input['rate'] == "-" ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_rating') );
		}

		$DB->query( "SELECT mid FROM ibf_files_votes WHERE mid = '" . $this->member['id'] . "' and did = '" . $ibforums->input['id'] . "'" );
		if( $DB->get_num_rows( ) > 0 ) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'already_rated') );
		}

		$DB->query( "SELECT votes , total FROM ibf_files WHERE id = '" . $ibforums->input['id'] . "'" );
		$row    = $DB->fetch_row( );
		$total  = $row['total'] + $ibforums->input['rate'];
		$votes  = $row['votes'] + 1;
		$rating = $total / $votes;

		$DB->query( "INSERT INTO ibf_files_votes ( mid,rating,did ) VALUES( '" .  $this->member['id'] . "', '" . $ibforums->input['rate'] ."' , '" . $ibforums->input['id'] . "')" );
		$DB->query( "UPDATE ibf_files SET votes = $votes, total = $total , rating = $rating WHERE id = '" . $ibforums->input['id'] . "'" );

		$print->redirect_screen("{$ibforums->lang['redirect_rate']}", "download={$ibforums->input['id']}" );
		exit();
 	}


	function return_rate($how_much) {
		global $ibforums , $DB, $print , $std;

		for($i = 0; $i < $how_much['rating']; $i++) {
			$this_much .= "<img src='{$ibforums->vars['img_url']}/pip.gif' alt='{$ibforums->lang['rate_img']}' />";
		}
		return $this_much;
	}
   //-----------------------------------------------------


   //-----------------------------------------------------
   // Comment Poster
   //-----------------------------------------------------

	function post_comment ( )
	{
		global $ibforums, $DB, $print, $std;

		if($this->member['id'] == 0 || !$this->member['id']){
			$this->member['name'] = $ibforums->lang['comment_guest'];
		}

		if($this->member['g_d_post_comments'] == '1'){
			if($this->member['g_d_html_files'] == 1){
				$ibforums->input['Post'] = "[dohtml]".$ibforums->input['Post']."[/dohtml]";
			}

			$comment = array(
					'file_id'		=> $ibforums->input['id'],
		 			'mem_id'             => $this->member['id'],
					'name'		=> $this->member['name'],
				 	'date'      	=> time(),
				 	'comment'         => $this->parser->convert( array( TEXT    => $ibforums->input['Post'],
										   SMILIES => 1,
										   CODE    => $this->member['g_d_ibcode_files'],
										   HTML    => $this->member['g_d_html_files']
									)  )
					);
			$db_string = $DB->compile_db_insert_string($comment);
			$DB->query("INSERT INTO ibf_files_comments (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
			$std->boink_it("index.php?download={$ibforums->input['id']}");
		} else {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission') );
		}
	}
   //-----------------------------------------------------
   
   
   //-----------------------------------------------------
   // Add/Remove Favorites
   //-----------------------------------------------------   
	function do_favorite( ) {
		global $ibforums, $DB, $print, $std;
		
		if($ibforums->input['type'] == "remove"){
			$DB->query("DELETE FROM ibf_files_favorites WHERE id='{$ibforums->input['favid']}'");
			$print->redirect_screen("{$ibforums->lang['fav_redirect1']}", "download={$ibforums->input['id']}" );
			exit();
				
		} elseif($ibforums->input['type'] == "add") {

			$DB->query( "SELECT MAX(id) as nid FROM ibf_files_favorites" );
			$lastq = $DB->fetch_row();
			$last  = $lastq['nid'];
			$last_insert = $last + 1;
			
			$favorite = array("id" => $last_insert,
						"mid" => $this->member['id'],
						"mname" => $this->member['name'],
						"fid" => $ibforums->input['id'],
						"fname" => $ibforums->input['name'],
						"date" => time());

		    $db_string = $DB->compile_db_insert_string($favorite);
			$DB->query("INSERT INTO ibf_files_favorites (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");

		$print->redirect_screen("{$ibforums->lang['fav_redirect']}", "download={$ibforums->input['id']}" );
		exit();
		}
 }   
   
   //-----------------------------------------------------   
   
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
		if ( is_array($input) && isset($input['show_notes']) && $input['show_notes'] == 1 )
{
    // Ensure $out is also an array or string as expected by the logic
    $out['all_notes'] = ($out['all_notes'] ?? "") . "<br /><br />".$input['cnotes']."<br />";
}
		
		if( (is_array($input) && isset($input['show_notes']) && $input['show_notes'] == 1) || ($ibforums->vars['d_show_global_notes'] == 1) ){
    $out['notes'] = $this->html->show_global_notes($out['all_notes'] ?? "");
}

		$this->output .= $this->html->wrapper($out,$ranpic_output);   
   		return TRUE;
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


 
}

?>





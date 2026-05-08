<?php

$idx = new Reputation;

class Reputation {

	var $output     = "";
	var $page_title = "";
	var $nav        = array();
	var $parser;
	
	var $mem        = array();
	var $rep_ranks  = array();
	var $use_ranks  = 0;
	var $rep_html   = "";
	
	var $sort_order = "";
	var $sort_key   = "";
	var $first      = 0;
	var $max_results = 0;
	
	function __construct()
	{
		global $ibforums, $DB, $std, $print;
		
		$ibforums->lang = $std->load_words($ibforums->lang, 'lang_rep', $ibforums->lang_id);
		
		if (! $ibforums->vars['rep_per_page']) $ibforums->vars['rep_per_page'] = 30;
		if (! $ibforums->vars['rep_msg_length']) $ibforums->vars['rep_msg_length'] = 0;
		
		require ROOT_PATH."sources/lib/post_parser.php";
		$this->parser = new post_parser();
		
		if ( $ibforums->input['CODE'] != 'totals' and $ibforums->input['CODE'] != 'getnew' and
			 $ibforums->input['CODE'] != 'comment' and $ibforums->input['CODE'] != 'save_comment' )
		{
			if (! $ibforums->member['id'] )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'not_registered' ) );
			}
			
			$ibforums->input['mid'] = intval($ibforums->input['mid']);
			
			if ( empty($ibforums->input['mid']) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'incorrect_use') );
			}
			
			$DB->query("SELECT id, name, mgroup, rep, rep_do, rep_do_open FROM ibf_members WHERE id = '".$ibforums->input['mid']."'");
			
			if (! $DB->get_num_rows() )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_user') );
			}
			else
			{
				$this->mem = $DB->fetch_row();
			}
		}
		
		if ($ibforums->vars['rep_use_ranks'])
		{
			$DB->query("SELECT id, title, amount FROM ibf_reput_ranks ORDER BY amount DESC");
			while ($row = $DB->fetch_row())
			{
				$this->rep_ranks[ $row['id'] ] = array(
														'TITLE'  => $row['title'],
														'AMOUNT' => $row['amount'],
													  );
			}
			
			if ( is_array( $this->rep_ranks )) $this->use_ranks = 1;
		}
		
		require "./Skin/".$ibforums->skin_id."/skin_rep.php";
		$this->rep_html = new skin_rep();
		
		switch($ibforums->input['CODE'])
		{
			case '01':
				$this->show_form('01');
				break;
				
			case '02':
				$this->show_form('02');
				break;
				
			case '03':
				$this->mem_stats($this->mem['id']);
				break;
				
			case '04':
				$this->mem_change_stats($this->mem['id']);
				break;
				
			case '11':
				$this->add_rep($this->mem['id']);
				break;
				
			case '12':
				$this->remove_rep($this->mem['id']);
				break;
				
			case 'delete':
				$this->delete($this->mem['id']);
				break;
			
			case 'comment':
				$this->edit_comment();
				break;
				
			case 'save_comment':
				$this->save_comment();
				break;
				
			case 'totals':
				$this->totals();
				break;
				
			case 'getnew':
				$this->getnew();
				break;
				
			default:
				$this->totals();
		}
		
		$print->add_output("$this->output");
		$print->do_output( array( 'TITLE' => $this->page_title, 'JS' => 1, 'NAV' => $this->nav ) );
	}
	
	
	//----------------------------------
	// Main form for reputation changes
	//----------------------------------
	
	function show_form( $code='01' )
	{
		global $ibforums, $DB, $std, $print;
		
		// Is this member allowed to change rep?
		
		if (! $ibforums->member['allow_rep'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_cantchange') );
		}
		
		// Is this group allowed to change rep?
		
		if (! $ibforums->member['g_change_rep'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_gcantchange') );
		}
		else
		{
			$group_exclude = explode(",", $ibforums->member['g_exclude_rep'] ?? "");
			
			if ( in_array( $this->mem['mgroup'], $group_exclude ))
			{
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_gcantchangeg') );
			}
		}
		
		if ($ibforums->member['posts'] < $ibforums->vars['rep_posts'] )
		{
			// Haven't got enough posts
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_noposts', 'EXTRA' => $ibforums->vars['rep_posts']) );
		}
		
		// Flood control!
		
		$DB->query("SELECT msg_date FROM ibf_reputation WHERE member_id='".$ibforums->input['mid']."' AND from_id='".$ibforums->member['id']."' ORDER BY msg_date DESC");
		
		$date = $DB->fetch_row();
		
		if ($date && isset($date['msg_date']) && (time() - $date['msg_date'] < (24 * 3600 * ($ibforums->vars['rep_time'] ?? 0))))
		{
			if (! ($ibforums->vars['rep_time_nomod'] and ($ibforums->member['is_mod'] or $ibforums->member['g_is_supmod']) ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_early', 'EXTRA' => $ibforums->vars['rep_time'] ) );
			}
		}
		
		// Something must be specified as 't'!
		
		if ( empty($ibforums->input['t']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}
		
		if ( is_numeric ($ibforums->input['t']) )
		{
			// Changing rep from within some topic... Forum ID and post ID must exist
			
			if ( empty($ibforums->input['f']) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			
			if ( empty($ibforums->input['p']) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			
			// Is rep enabled for this forum?
			$rep_exclude = explode(",", $ibforums->vars['rep_total_exclude']  ?? "");
$rep_hide    = explode(",", $ibforums->vars['rep_change_exclude'] ?? "");
			
			if ( in_array( $ibforums->input['f'], $rep_hide ) or in_array( $ibforums->input['f'], $rep_exclude ))
			{
				// Rep changes disabled within this forum
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_forum_hide') );
			}
			
			// Does this post belong to its author? :\
			$DB->query("SELECT post FROM ibf_posts WHERE author_id = '".$this->mem['id']."' AND pid = '".$ibforums->input['p'].
					   "' AND topic_id = '".$ibforums->input['t']."' AND forum_id = '".$ibforums->input['f']."'");
			
			if (! $DB->get_num_rows() )
			{
				// No, it doesn't!
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
		}
		else
		{
			// Changing rep from within one's profile or rep stats
			if ( $ibforums->input['t'] != 's' && $ibforums->input['t'] != 'p' )
			{
				// This wasn't the profile nor the stats... Error!
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
		}
		
		if ($ibforums->member['id'] != $this->mem['id'])
		{
			if ($code == '02')
			{
				$level = $this->get_rep($ibforums->input['mid']);
				
				if ( empty( $level ) ) $level = 0;
				
				if (is_numeric ($ibforums->vars['rep_remove']))
				{
					if ($level <= $ibforums->vars['rep_remove'])
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_low') );
					}
				}
			}
		}
		else
		{
			// We cannot change our own reputation!
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_self') );
		}
		
		
		$info = array();
		
		$info['memid'] = $this->mem['id'];
		
		$info['action'] = ($code == '01') ? $ibforums->lang['raise'] : $ibforums->lang['lower'];
		$info['code']   = ($code == '01') ? '11' : '12';
		
		$info['level']  = $ibforums->input['rep_level'];
		$info['f']      = $ibforums->input['f'];
		$info['t']      = $ibforums->input['t'];
		$info['p']      = $ibforums->input['p'];
		
		if ($ibforums->vars['rep_allow_anon'] && $ibforums->member['allow_anon'] && ($ibforums->member['posts'] >= $ibforums->vars['rep_anon_posts']) )
		{
			$info['anon'] = "<input type='checkbox' name='anonymno' value='yes'> {$ibforums->lang['vote_anon']}";
		}
		else
		{
			$info['anon'] = "";
		}
		
		$info['mem_name'] = $this->mem['name'];
		
		$this->output .= $this->rep_html->ShowForm( $info );
		
		$this->nav = array( $ibforums->lang['pnav'] );
		$this->page_title = $ibforums->lang['ptitle'];
	}
	
	
	//----------------------------
	// Functions for changing rep
	//----------------------------
	
	function add_why($memid)
	{
		global $std, $DB, $ibforums, $print;
		
		// Is this member allowed to change rep?
		
		if (! $ibforums->member['allow_rep'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_cantchange') );
		}
		
		// Is this group allowed to change rep?
		
		if (! $ibforums->member['g_change_rep'] )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_gcantchange') );
		}
		else
		{
			$group_exclude = explode(",", $ibforums->member['g_exclude_rep'] ?? "");
			
			if ( in_array( $this->mem['mgroup'], $group_exclude ))
			{
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_gcantchangeg') );
			}
		}
		
		if ($ibforums->member['posts'] < $ibforums->vars['rep_posts'] )
		{
			// Haven't got enough posts
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_noposts', 'EXTRA' => $ibforums->vars['rep_posts']) );
		}
		
		// Flood control!
		
		$DB->query("SELECT msg_date FROM ibf_reputation WHERE member_id='".$ibforums->input['mid']."' AND from_id='".$ibforums->member['id']."' ORDER BY msg_date DESC");
		
		$date = $DB->fetch_row();
		
		if ($date && isset($date['msg_date']) && (time() - $date['msg_date'] < (24 * 3600 * ($ibforums->vars['rep_time'] ?? 0))))
		{
			if (! ($ibforums->vars['rep_time_nomod'] and ($ibforums->member['is_mod'] or $ibforums->member['g_is_supmod']) ) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_early', 'EXTRA' => $ibforums->vars['rep_time'] ) );
			}
		}
		
		// Something must be specified as 't'!
		
		if ( empty($ibforums->input['t']) )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
		}
		
		if ( is_numeric ($ibforums->input['t']) )
		{
			// Changing rep from within some topic... Forum ID and post ID must exist
			
			if ( empty($ibforums->input['f']) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			
			if ( empty($ibforums->input['p']) )
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			
			// Is rep enabled for this forum?
			$rep_exclude = explode(",", $ibforums->vars['rep_total_exclude']  ?? "");
$rep_hide    = explode(",", $ibforums->vars['rep_change_exclude'] ?? "");
			
			if ( in_array( $ibforums->input['f'], $rep_hide ) or in_array( $ibforums->input['f'], $rep_exclude ))
			{
				// Rep changes disabled within this forum
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_forum_hide') );
			}
			
			// Does this post belong to its author? :\
			$DB->query("SELECT post FROM ibf_posts WHERE author_id = '".$this->mem['id']."' AND pid = '".$ibforums->input['p'].
					   "' AND topic_id = '".$ibforums->input['t']."' AND forum_id = '".$ibforums->input['f']."'");
			
			if (! $DB->get_num_rows() )
			{
				// No, it doesn't!
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
		}
		else
		{
			// Changing rep from within one's profile or rep stats
			if ( $ibforums->input['t'] != 's' && $ibforums->input['t'] != 'p' )
			{
				// This wasn't the profile nor the stats... Error!
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			// We begin to protect us from wanna-be huck3r$			
			else if ( $ibforums->input['t'] == 's' and !$ibforums->vars['rep_memstats'])
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			else if ( $ibforums->input['t'] == 'p' and !$ibforums->vars['rep_profile'])
			{
				$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files') );
			}
			
		}
		
		if ($ibforums->member['id'] != $this->mem['id'])
		{
			if ($ibforums->input['CODE'] == '12')
			{
				$level = $this->get_rep($ibforums->input['mid']);
				
				if ( empty( $level ) ) $level = 0;
				
				if (is_numeric ($ibforums->vars['rep_remove']))
				{
					if ($level <= $ibforums->vars['rep_remove'])
					{
						$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_low') );
					}
				}
			}
		}
		else
		{
			// We cannot change our own reputation!
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_self') );
		}
		
		if ($ibforums->input['anonymno'] == 'yes')
		{
			if (! $ibforums->vars['rep_allow_anon'] )
			{
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_noanon') );
			}
			
			if (! $ibforums->member['allow_anon'] )
			{
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_noanon') );
			}
			
			if ($ibforums->member['posts'] < $ibforums->vars['rep_anon_posts'])
			{
				$std->Error( array( 'LEVEL' => 2, 'MSG' => 'rep_noanon') );
			}
			
			$show = 0;
		}
		else
		{
			$show = 1;
		}
		
		if ($ibforums->input['CODE'] != '11')
		{
			$code = '02';
		}
		else
		{
			$code = '01';
		}
		
		if ($ibforums->vars['rep_msg_length']) $ibforums->input['message'] = substr($ibforums->input['message'], 0, $ibforums->vars['rep_msg_length']);
		
		$db_string = $std->compile_db_string( array( 
													'member_id'      => $memid,
													'msg_date'       => time(),
													'message'        => $this->parser->convert( array( 	'TEXT'    => $ibforums->input['message'],
																										'SMILIES' => $ibforums->vars['rep_enable_emo'],
																										'CODE'    => $ibforums->vars['rep_enable_ibc'],
																										'HTML'    => 0
																								)		),
													'from_id'        => $ibforums->member['id'],
													'forum_id'       => $ibforums->input['f'],
													'topic_id'       => $ibforums->input['t'],
													'post'           => $ibforums->input['p'],
													'CODE'           => $code,
													'vis'            => $show,
											)      );
		
		$DB->query("INSERT INTO ibf_reputation (" .$db_string['FIELD_NAMES']. ") VALUES (". $db_string['FIELD_VALUES'] .")");
		unset($db_string);
		
		// Update rep changes count for the member changing this rep
		$query = 'rep_do=rep_do+1';
		if ($show) $query .= ', rep_do_open=rep_do_open+1';
		
		$DB->query("UPDATE ibf_members SET $query WHERE id = {$ibforums->member['id']}");
	}
	
	function add_rep($memid=0)
	{
		global $ibforums, $DB, $std, $print;
		
		$this->add_why( $memid );
		
		// Assume that if we're here then no errors have been found. Update member's rep count
		
		$level = $this->get_rep($memid);
		
		if (empty ($level) ) $level = 0;
		
		$this->update_rep($level+1, $memid);
		
		$print->redirect_screen($ibforums->lang['add_success'], "act=rep&CODE=03&mid=".$memid."&t=".$ibforums->input['t']."&f=".$ibforums->input['f'] );
	}
	
	function remove_rep($memid=0)
	{
		global $ibforums, $DB, $std, $print;
		
		$this->add_why( $memid );
		
		// Assume that if we're here then no errors have been found. Update member's rep count
		
		$level = $this->get_rep($memid);
		
		if( empty($level) ) $level = 0;
		
		// Are we to remove posting rights?
		if (is_numeric ($ibforums->vars['rep_remove']))
		{
			$DB->query("SELECT m.id, moderator.mid as is_mod, g.g_is_supmod
							FROM ibf_members m
								LEFT JOIN ibf_groups g ON (g.g_id=m.mgroup)
								LEFT JOIN ibf_moderators moderator ON (moderator.member_id=m.id OR moderator.group_id=m.mgroup )
							WHERE m.id=$memid");
			
			$row = $DB->fetch_row();
			
			$is_mod = 0;
			
			if ($row['is_mod'] or $row['g_is_supmod']) $is_mod = 1;
			
			// Be sure not to remove posting rights from admins & mods
			if ( ( $level <= ( $ibforums->vars['rep_remove'] + 1) ) && !is_mod)
			{
				$this->update_rep($level-1, $memid);
				
				if ($ibforums->vars['rep_remove_days'])
				{
					$rp = $std->hdl_ban_line( array( 'timespan' => intval($ibforums->vars['rep_remove_days']), 'unit' => 'd'  ) );
				}
				else
				{
					$rp = 1;
				}
				
				$DB->query("UPDATE ibf_members SET restrict_post='$rp' WHERE id='$memid'");
				
				$print->redirect_screen($ibforums->lang['no_post_right'], "act=rep&CODE=03&mid=".$memid  );
			}
		}
		
		$this->update_rep($level-1, $memid);
		
		$print->redirect_screen($ibforums->lang['rem_success'], "act=rep&CODE=03&mid=".$memid."&t=".$ibforums->input['t']."&f=".$ibforums->input['f'] );
	}
	
	function mem_stats($memid)
	{
		global $ibforums, $DB, $std, $print;
		
		// Preparing pagelinks
		
		/* old depreciated way - no need for this query
		$DB->query("SELECT COUNT(msg_id) as total FROM ibf_reputation WHERE member_id = '".$memid."'");
		$max = $DB->fetch_row();
		
		$DB->free_result();
		*/
		
		$ibforums->input['st'] = intval($ibforums->input['st']);
		if (!isset($ibforums->input['st']))	$ibforums->input['st'] = 0;
		
		$links = $std->build_pagelinks( array( 	'TOTAL_POSS'  => $this->mem['rep'],
												'PER_PAGE'    => $ibforums->vars['rep_per_page'],
												'CUR_ST_VAL'  => $ibforums->input['st'],
												'L_SINGLE'     => "",
												'L_MULTI'      => $ibforums->lang['multi_pages'],
												'BASE_URL'     => $ibforums->base_url."act=rep&CODE=03&mid=".$memid,
										)	);
		
		$this->output .= $this->rep_html->Links($links, "");
		$this->output .= "<br />";
		
		// Counting + and - rep changes...
		
		/* older slower way!
		$DB->query("SELECT m.id, m.name, m.rep, COUNT(r.CODE) as ups
					FROM ibf_members m
					LEFT JOIN ibf_reputation r ON (m.id = r.member_id and r.CODE = '01')
					WHERE m.id = '$memid' GROUP BY m.id");
		*/
		
		/* old depreciated way (why do we need +1 query :blink:)
		$DB->query("SELECT id, name, rep FROM ibf_members WHERE id = '$memid'");
		$row = $DB->fetch_row();
		*/
		
		$info = array(
						'rep'  => $this->mem['rep'],
						'name' => $this->mem['name'],
						'id'   => $this->mem['id'],
					 );
		
		$DB->query("SELECT COUNT(msg_id) AS ups FROM ibf_reputation WHERE (CODE='01' AND member_id='$memid')");
		$row = $DB->fetch_row();
		
		$info['ups'] = $row['ups'];
		
		$info['downs'] = abs ($info['rep'] - $info['ups']);
		
		if ($this->use_ranks)
		{
			foreach($this->rep_ranks as $k => $v)
			{
				if ($info['rep'] >= $v['AMOUNT'])
				{
					$info['rep'] = $this->rep_ranks[ $k ]['TITLE'];
					break;
				}
			}
		}
		else
		{
			if (empty($info['rep']) and empty($info['ups']))
			{
				$info['rep'] = $ibforums->lang['no_changes'];
			}
			else
			{
				$info['rep'] .= " ".$ibforums->lang['rep_postfix'];
			}
		}
		
		$info['name'] = "<a href='{$ibforums->base_url}act=Profile&MID={$info['id']}'>".$info['name']."</a>";
		
		if ($ibforums->vars['rep_memstats'] and ( $ibforums->member['id'] != $this->mem['id'] ) )
		{
			$stuff = array( 'mid' => $memid, 't' => 's' );
			$info['change'] = $ibforums->lang['change'] . " " . $this->rep_html->rep_options_links( $stuff );
		}
		else
		{
			$info['change'] = "";
		}
		
		$this->output .= $this->rep_html->ShowTitle($info);
		
		$this->output .= $this->rep_html->ShowHeader();
		
		// Main list
		
		$DB->query("SELECT r.*, m.name, t.title FROM ibf_reputation r
					LEFT JOIN ibf_members m ON (m.id=r.from_id)
					LEFT JOIN ibf_topics t ON (r.topic_id=t.tid)
					WHERE r.member_id='$memid' ORDER BY r.msg_date DESC
					LIMIT ".$ibforums->input['st'].", ".$ibforums->vars['rep_per_page']);
		
		if (! $DB->get_num_rows() ) $this->output .= $this->rep_html->ShowNone();
		
		while (	$i = $DB->fetch_row() )
		{
			switch ($i['CODE'])
			{
				case '01':
					$i['img'] = $ibforums->vars['img_url']."/r_up.gif";
					break;
				case '02':
					$i['img'] = $ibforums->vars['img_url']."/r_down.gif";
					break;
			}
			
			$i['date'] = $std->get_date($i['msg_date'], 'LONG');
			
			// Switch - if rep was changed in topic, in profile or right here
			
			if ($i['topic_id'] == 'p')
			{
				$i['url'] = $ibforums->base_url."act=Profile&MID=".$i['member_id'] ;
				$i['title'] = $ibforums->lang['profile'];
			}
			else if ($i['topic_id'] == 's')
			{
				$i['url'] = $ibforums->base_url."act=rep&mid=".$i['member_id']."&CODE=03&t=s" ;
				$i['title'] = $ibforums->lang['stats'];
			}
			else
			{
				$i['url'] = $ibforums->base_url."showtopic=".$i['topic_id']."&view=findpost&p=".$i['post'] ;
				
				if ( empty($i['title']))
				{
					$i['title'] = "<font color='lightsteelblue'>{$ibforums->lang['no_topic']}</font>";
				}
			}
			
			// Getting name of the member that changed rep
			
			if ($i['vis'] != 0)
			{
				$i['name'] = "<a href='{$ibforums->base_url}act=rep&CODE=04&mid={$i['from_id']}'><b>{$i['name']}</b></a>" ;
			}
			else
			{
				if ($ibforums->member['g_access_cp'])
				{
					$i['name'] = "<a href='{$ibforums->base_url}act=rep&CODE=04&mid={$i['from_id']}'><b><font color='lightsteelblue'>{$i['name']}</b></a>,</font> ";
				}
				else
				{
					$i['name'] = "";
				}
				
				if ($i['CODE'] == '01' and $ibforums->vars['rep_good_anon']) $i['name'] .= "<font color='lightsteelblue'>{$ibforums->vars['rep_good_anon']}</font>";
				else if ($i['CODE'] == '02' and $ibforums->vars['rep_bad_anon'])  $i['name'] .= "<font color='lightsteelblue'>{$ibforums->vars['rep_bad_anon']}</font>";
				else $i['name'] .= "<font color='lightsteelblue'>{$ibforums->lang['is_anon']}</font>";
			}
			
			if ($ibforums->member['g_access_cp'] and $ibforums->member['id'] != $i['member_id']) $i['admin_undo'] = "<br><a href='{$ibforums->base_url}act=rep&CODE=delete&id={$i['msg_id']}&mid={$i['member_id']}'>{$ibforums->lang['undo_change']}</a>";
			
			if ($i['comment'] != '')
			{
				$i['comment'] = str_replace('#MEM_', '<b>', $i['comment']);
				$i['comment'] = str_replace('_#EMEM', '</b>', $i['comment']);
				$i['comment'] = preg_replace('/#DAT_(.+?)_#EDAT/e', "\$std->get_date('\\1', 'LONG')", $i['comment']);
				
				$i['message'] .= '<hr style="height:1px;color:#FFF">'.$i['comment'];
			}
			
			if ($ibforums->member['id'] == $i['member_id'] AND $ibforums->vars['rep_allow_comments']) $i['admin_undo'] = "<br><a href='{$ibforums->base_url}act=rep&CODE=comment&id={$i['msg_id']}'>{$ibforums->lang['comment_change']}</a>";
			
			$i['memid'] = $memid;
			
			$this->output .= $this->rep_html->ShowRow( $i );
		}
		
		// Rendering the back button... Oh yeah, it's intellectual! B)
		
		if ($ibforums->input['t'] == 'p')
		{
			$back = "{$ibforums->base_url}act=Profile&MID=".$memid ;
		}
		else if ($ibforums->input['t'] == 's')
		{
			$back = "{$ibforums->base_url}act=rep&CODE=03&t=s&mid=".$memid ;
		}
		else if (empty($ibforums->input['t']) or empty($ibforums->input['f']))
		{
			$back = "javascript:history.go(-1)";
		}
		else
		{
			$back = "{$ibforums->base_url}showtopic=".$ibforums->input['t'];
		}
		
		// Printing footer, printing pagelinks...
		
		$this->output .= $this->rep_html->ShowFooter($back);
		
		$this->output .= "<br />";
		$this->output .= $this->rep_html->Links($links, "");
		
		$this->nav = array( $ibforums->lang['snav'] );
		$this->page_title = $ibforums->lang['stitle'];
	}
	
	function mem_change_stats($memid)
	{
		global $ibforums, $DB, $std, $print;
		
		if (! $ibforums->member['g_access_cp'])
		{
			$pfix = ' AND r.vis=1 ';
			$field = 'rep_do_open';
		}
		else
		{
			$pfix = " ";
			$field = 'rep_do';
		}
		
		// Preparing pagelinks
		
		/* old depreciated way - no need for this query
		$DB->query("SELECT COUNT(msg_id) as total FROM ibf_reputation r WHERE from_id = '".$memid."'".$pfix);
		$max = $DB->fetch_row();
		
		$DB->free_result();
		*/
		
		$ibforums->input['st'] = intval($ibforums->input['st']);
		if (!isset($ibforums->input['st'])) $ibforums->input['st'] = 0;
		
		$links = $std->build_pagelinks(  array( 'TOTAL_POSS'  => $this->mem[ $field ],
												'PER_PAGE'    => $ibforums->vars['rep_per_page'],
												'CUR_ST_VAL'  => $ibforums->input['st'],
												'L_SINGLE'     => "",
												'L_MULTI'      => $ibforums->lang['multi_pages'],
												'BASE_URL'     => $ibforums->base_url."act=rep&CODE=04&mid=".$memid,
									  )
							   );
		
		$this->output .= $this->rep_html->Links($links, "");
		$this->output .= "<br />";
		
		// Counting +'s and -'s
		
		/* old depreciated way - no need for this query
		$DB->query("SELECT m.id, m.name, COUNT(r.from_id) as times
					FROM ibf_reputation r
					LEFT JOIN ibf_members m ON (m.id = r.from_id)
					WHERE r.from_id = '". $memid. "'". $pfix. "GROUP BY r.from_id");
					
		if (! $DB->get_num_rows() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_name_search_results') );
		}
		*/
		
		$info = array(
						'times' => $this->mem[ $field ],
						'id'    => $this->mem['id'],
						'name'  => $this->mem['name'],
					 );
		
		$DB->query( "SELECT COUNT(r.from_id) AS ups FROM ibf_reputation r ".
					"WHERE r.CODE='01' AND r.from_id = '". $memid. "'". $pfix);
		
		$row = $DB->fetch_row();
		$info['ups'] = $row['ups'];
		
		$info['downs'] = $info['times'] - $info['ups'];
		
		$info['name'] = "<a href='{$ibforums->base_url}act=Profile&MID={$info['id']}'>{$info['name']}</a>";
		
		$this->output .= $this->rep_html->ShowSelfTitle($info);
		
		$this->output .= $this->rep_html->ShowSelfHeader();
		
		$DB->query("SELECT r.*, m.name, t.title FROM ibf_reputation r
					LEFT JOIN ibf_members m ON (m.id=r.member_id)
					LEFT JOIN ibf_topics t ON (r.topic_id=t.tid)
					WHERE r.from_id='".$memid. "'". $pfix. "ORDER BY r.msg_date DESC
					LIMIT ".$ibforums->input['st'].", ".$ibforums->vars['rep_per_page']);
		
		if (! $DB->get_num_rows() ) $output .= $rep_html->ShowNone();
		
		while (	$i = $DB->fetch_row() )
		{
			switch ($i['CODE'])
			{
				case '01':
					$i['img'] = $ibforums->vars['img_url']."/r_up.gif";
					break;
				case '02':
					$i['img'] = $ibforums->vars['img_url']."/r_down.gif";
					break;
			}
			
			$i['date'] = $std->get_date($i['msg_date'], 'LONG');
			
			// Where???
			
			if ($i['topic_id'] == 'p')
			{
				$i['url'] = $ibforums->base_url."act=Profile&MID=".$i['member_id'] ;
				$i['title'] = $ibforums->lang['profile'];
			}
			else if ($i['topic_id'] == 's')
			{
				$i['url'] = $ibforums->base_url."act=rep&mid=".$i['member_id']."&CODE=03&t=s" ;
				$i['title'] = $ibforums->lang['self_stats'];
			}
			else
			{
				$i['url'] = $ibforums->base_url."showtopic=".$i['topic_id']."&view=findpost&p=".$i['post'] ;
				
				if ( empty($i['title']))
				{
					$i['title'] = "<font color='lightsteelblue'>{$ibforums->lang['no_topic']}</font>";
				}
			}
			
			if ($i['vis'] != 0)
			{
				$i['name'] = "<a href='{$ibforums->base_url}act=rep&CODE=03&mid={$i['member_id']}'><b>{$i['name']}</b></a>" ;
			}
			else
			{
				if ($ibforums->member['g_access_cp'])
				{
					$i['name'] = "<a href='{$ibforums->base_url}act=rep&CODE=03&mid={$i['member_id']}'><b><font color='lightsteelblue'>{$i['name']}<b></font></a>";
				}
				else
				{
					$i['name'] = "";
				}
			}
			
			if ($ibforums->member['g_access_cp'] and $ibforums->member['id'] != $i['member_id']) $i['admin_undo'] = "<br><a href='{$ibforums->base_url}act=rep&CODE=delete&id={$i['msg_id']}&mid={$i['member_id']}'>{$ibforums->lang['undo_change']}</a>";
			
			$i['memid'] = $memid;
			
			$this->output .= $this->rep_html->ShowRow( $i );
		}
		
		$back = "{$ibforums->base_url}showtopic=".$ibforums->input['t'];
		
		$this->output .= $this->rep_html->ShowFooter($back);
		
		$this->output .= "<br />";
		$this->output .= $this->rep_html->Links($links, "");
		
		$this->nav        = array( $ibforums->lang['snav'] );
		$this->page_title = $ibforums->lang['stitle'];
	}
	
	function delete($memid)
	{
		global $ibforums, $DB, $std, $print;
		
		if (! $ibforums->member['g_access_cp'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'moderate_no_permission' ) );
		}
		
		$DB->query("SELECT * FROM ibf_reputation WHERE msg_id = '".$ibforums->input['id']."' AND member_id = '".$memid."'");
		
		if (! $DB->get_num_rows() )
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'moderate_no_permission' ) );
		}
		
		$row = $DB->fetch_row();
		
		if ($row['member_id'] == $ibforums->member['id'])
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'rep_self' ) );
		}
		
		if ($row['CODE'] == '01')
		{
			$DB->query("UPDATE ibf_members SET rep = rep - 1 WHERE id = '".$memid."'");
		}
		else
		{
			$DB->query("UPDATE ibf_members SET rep = rep + 1 WHERE id = '".$memid."'");
		}
		
		$DB->query("DELETE FROM ibf_reputation WHERE msg_id = '".$ibforums->input['id']."'");
		
		$query = 'rep_do=rep_do-1';
		if ($row['vis']) $query .= ', rep_do_open=rep_do_open-1';
		
		$DB->query("UPDATE ibf_members SET $query WHERE id = {$row['from_id']}");
		
		$print->redirect_screen($ibforums->lang['del_success'], "act=rep&CODE=03&mid=".$memid );
		
		return;
	}
	
	function totals() //Showing board overall stats
	{
		global $ibforums, $DB, $std, $print;
		
		if ($ibforums->member['g_mem_info'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}
		
		if (isset($ibforums->input['max_results'])) $this->max_results = $ibforums->input['max_results'];
		else $this->max_results = 30;
		if (isset($ibforums->input['st']))          $this->first       = intval($ibforums->input['st']);
		else $this->first = 0;
		if (isset($ibforums->input['sort_key']))    $this->sort_key    = $ibforums->input['sort_key'];
		else $this->sort_key = 'rep';
		if (isset($ibforums->input['sort_order']))  $this->sort_order  = $ibforums->input['sort_order'];
		else $this->sort_order = 'desc';
		
		$times = 'rep_do';
		if (!$ibforums->member['g_access_cp']) $times .= '_open';
		
		
		$sort_key = array( 'name'     => 'sort_by_name',
						   'rep'      => 'sort_by_rep',
						   $times     => 'sort_by_rep_changes',
						 );
		
		$max_results = array( 10  => '10',
							  20  => '20',
							  30  => '30',
							  40  => '40',
							  50  => '50',
							);
		
		$sort_order = array( 'desc' => 'descending_order',
							 'asc'  => 'ascending_order',
							);
		
		$sort_key_html    = "<select name='sort_key' class='forminput'>\n";
		$max_results_html = "<select name='max_results' class='forminput'>\n";
		$sort_order_html  = "<select name='sort_order' class='forminput'>\n";
		
		foreach ($sort_order as $k => $v)
		{
			$sort_order_html .= $k == $this->sort_order ? "<option value='$k' selected>" . $ibforums->lang[ $sort_order[ $k ] ] . "\n"
														: "<option value='$k'>"          . $ibforums->lang[ $sort_order[ $k ] ] . "\n";
		}
		
		foreach ($sort_key as $k => $v)
		{
			$sort_key_html .= $k == $this->sort_key ? "<option value='$k' selected>"     . $ibforums->lang[ $sort_key[ $k ] ] . "\n"
													: "<option value='$k'>"              . $ibforums->lang[ $sort_key[ $k ] ] . "\n";
		}
		
		foreach ($max_results as $k => $v)
		{
			$max_results_html .= $k == $this->max_results ? "<option value='$k' selected>". $max_results[ $k ] . "\n"
														  : "<option value='$k'>"         . $max_results[ $k ] . "\n";
		}
		
		$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_KEY#>/"    , $sort_key_html."</select>"   , $ibforums->lang['sorting_text'] );
		$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_ORDER#>/"  , $sort_order_html."</select>" , $ibforums->lang['sorting_text'] );
		$ibforums->lang['sorting_text'] = preg_replace( "/<#MAX_RESULTS#>/" , $max_results_html."</select>", $ibforums->lang['sorting_text'] );
		
		$error = 0;
		
		if (! isset($sort_key[ $this->sort_key ]) )       $error = 1;
		if (! isset($sort_order[ $this->sort_order ]) )   $error = 1;
		if (! isset($max_results[ $this->max_results ]) ) $error = 1;
		
		if ($error == 1 )
		{
			$std->Error( array( LEVEL=> 5, MSG =>'incorrect_use') );
		}
		
		// Getting new since your last visit
		
		$DB->query("SELECT COUNT(DISTINCT(member_id)) AS total_members FROM ibf_reputation WHERE msg_date > '".$ibforums->member['last_visit']."'");
		$new_reps = $DB->fetch_row();
		
		if ($new_reps['total_members'] > 0)
		{
			$new = "<a href='{$ibforums->base_url}act=rep&CODE=getnew'>{$ibforums->lang['new']}</a>";
		}
		else
		{
			$new = "";
		}
		
		if ($ibforums->vars['rep_mems_limit'] > 0)
		{
			$max['total_members'] = $ibforums->vars['rep_mems_limit'];
			
			if ($this->first >= $ibforums->vars['rep_mems_limit']) $this->first = 0;
			
			if ($this->first + $this->max_results > $ibforums->vars['rep_mems_limit'])
			{
				$max_results = $ibforums->vars['rep_mems_limit'] - $this->first;
			}
			else
			{
				$max_results = $this->max_results;
			}
		}
		else
		{
			$DB->query("SELECT COUNT(id) AS total_members FROM ibf_members WHERE id > 0");
			$max = $DB->fetch_row();
			
			$DB->free_result();
			
			$max_results = $this->max_results;
		}
		
		$links = $std->build_pagelinks(  array( 'TOTAL_POSS'  => $max['total_members'],
												'PER_PAGE'    => $this->max_results,
												'CUR_ST_VAL'  => $this->first,
												'L_SINGLE'     => "",
												'L_MULTI'      => $ibforums->lang['multi_pages'],
												'BASE_URL'     => $ibforums->base_url."act=rep&CODE=totals&max_results={$this->max_results}&sort_order={$this->sort_order}&sort_key={$this->sort_key}"
									  )		   );
		
		$this->output = $this->rep_html->Links( $links, $new );
		$this->output .= "<br />";
		
		$this->output .= $this->rep_html->StatsLinks($ibforums->lang['btitle']);
		
		$newq = $DB->query("SELECT name, id, rep, allow_rep, allow_anon, $times AS times
							FROM ibf_members
							WHERE id > 0 ORDER BY ".$this->sort_key." ".$this->sort_order.
							" LIMIT ".$this->first.",".$max_results);
		
		while ($member = $DB->fetch_row($newq) )
		{
			$member['name'] = "<a href='{$ibforums->base_url}act=Profile&CODE=03&MID={$member['id']}'><b>{$member['name']}</b></a>" ;
			
			if ($ibforums->member['g_access_cp'])
			{
				if (! $member['allow_rep'] )
				{
					$member['name'] .= " ".$ibforums->lang['disallow_rep'];
				}
				else
				{
					if ($ibforums->vars['rep_allow_anon'])
					{
						if ( $member['allow_anon'] ) $member['name'] .= " ".$ibforums->lang['allow_anon'];
						else $member['name'] .= " ".$ibforums->lang['disallow_anon'];
					}
				}
			}
			
			if ($this->use_ranks)
			{
				foreach($this->rep_ranks as $k => $v)
				{
					if ($member['rep'] >= $v['AMOUNT'])
					{
						$member['rep'] = $this->rep_ranks[ $k ]['TITLE'];
						break;
					}
				}
				
				if (empty($member['rep'])) $member['rep'] = $ibforums->lang['no_changes'];
				else $member['rep'] .= " <a href='{$ibforums->base_url}act=rep&CODE=03&mid={$member['id']}'>{$ibforums->lang['details']}</a>";
			}
			else
			{
				if (is_numeric($member['rep']))
				{
					$member['rep'] .= " ".$ibforums->lang['rep_postfix'].
									  " <a href='{$ibforums->base_url}act=rep&CODE=03&mid={$member['id']}'>{$ibforums->lang['details']}</a>";
				}
				else $member['rep'] = $ibforums->lang['no_changes'];
			}
			
			if (empty($member['times'])) $member['times'] = $ibforums->lang['no_changes'];
			else $member['times'] .= " ".$ibforums->lang['rep_postfix'].
									 " <a href='{$ibforums->base_url}act=rep&CODE=04&mid={$member['id']}'>{$ibforums->lang['details']}</a>";
			
			$this->output .= $this->rep_html->ShowTotalsRow($member);
		}
		
		$this->output .= $this->rep_html->Page_end();
		
		$back = "javascript:history.go(-1)";
		
		$this->output .= $this->rep_html->ShowFooter($back);
		
		$this->output .= "<br />";
		$this->output .= $this->rep_html->Links( $links, $new );
		
		$this->nav        = array( $ibforums->lang['bnav'] );
		$this->page_title = $ibforums->lang['btitle'];
	}
	
	function getnew()
	{
		global $ibforums, $DB, $std, $print;
		
		if ($ibforums->member['g_mem_info'] != 1)
		{
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_permission' ) );
		}
		
		if (isset($ibforums->input['max_results'])) $this->max_results = $ibforums->input['max_results'];
		else $this->max_results = 30;
		if (isset($ibforums->input['st']))          $this->first       = intval($ibforums->input['st']);
		else $this->first = 0;
		if (isset($ibforums->input['sort_key']))    $this->sort_key    = $ibforums->input['sort_key'];
		else $this->sort_key = 'rep';
		if (isset($ibforums->input['sort_order']))  $this->sort_order  = $ibforums->input['sort_order'];
		else $this->sort_order = 'desc';
		
		$sort_key = array( 'name'     => 'sort_by_name',
						   'rep'      => 'sort_by_rep',
						   'times'    => 'sort_by_rep_changes',
						 );
		
		$max_results = array( 10  => '10',
							  20  => '20',
							  30  => '30',
							  40  => '40',
							  50  => '50',
							);
		
		$sort_order = array( 'desc' => 'descending_order',
							 'asc'  => 'ascending_order',
							);
		
		$sort_key_html    = "<select name='sort_key' class='forminput'>\n";
		$max_results_html = "<select name='max_results' class='forminput'>\n";
		$sort_order_html  = "<select name='sort_order' class='forminput'>\n";
		
		foreach ($sort_order as $k => $v)
		{
			$sort_order_html .= $k == $this->sort_order ? "<option value='$k' selected>" . $ibforums->lang[ $sort_order[ $k ] ] . "\n"
														: "<option value='$k'>"          . $ibforums->lang[ $sort_order[ $k ] ] . "\n";
		}
		
		foreach ($sort_key as $k => $v)
		{
			$sort_key_html .= $k == $this->sort_key ? "<option value='$k' selected>"     . $ibforums->lang[ $sort_key[ $k ] ] . "\n"
													: "<option value='$k'>"              . $ibforums->lang[ $sort_key[ $k ] ] . "\n";
		}
		
		foreach ($max_results as $k => $v)
		{
			$max_results_html .= $k == $this->max_results ? "<option value='$k' selected>". $max_results[ $k ] . "\n"
														  : "<option value='$k'>"         . $max_results[ $k ] . "\n";
		}
		
		$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_KEY#>/"    , $sort_key_html."</select>"   , $ibforums->lang['sorting_text'] );
		$ibforums->lang['sorting_text'] = preg_replace( "/<#SORT_ORDER#>/"  , $sort_order_html."</select>" , $ibforums->lang['sorting_text'] );
		$ibforums->lang['sorting_text'] = preg_replace( "/<#MAX_RESULTS#>/" , $max_results_html."</select>", $ibforums->lang['sorting_text'] );
		
		$error = 0;
		
		if (! isset($sort_key[ $this->sort_key ]) )       $error = 1;
		if (! isset($sort_order[ $this->sort_order ]) )   $error = 1;
		if (! isset($max_results[ $this->max_results ]) ) $error = 1;
		
		if ($error == 1 )
		{
			$std->Error( array( LEVEL=> 5, MSG =>'incorrect_use') );
		}
		
		$new = "<a href='{$ibforums->base_url}act=rep&CODE=totals'>{$ibforums->lang['totals']}</a>";
		$links = "";
		
		$this->output = $this->rep_html->Links( $links, $new );
		$this->output .= "<br />";
		
		$this->output .= $this->rep_html->StatsLinks($ibforums->lang['ntitle']);
		
		$DB->query( "SELECT r.*, m.name FROM ibf_reputation r ".
					"LEFT JOIN ibf_members m ON (m.id = r.member_id) ".
					"WHERE msg_date > '".$ibforums->member['last_visit']."'" );
		
		if (! $DB->get_num_rows() )
		{
			$this->output .= $this->rep_html->ShowNone();
		}
		else
		{
			$new_reps = array();
			
			while ( $row = $DB->fetch_row() )
			{
				/* if (!empty($row['name']))      */ $new_reps[ $row['member_id'] ]['name'] = $row['name'];
				/* if (!empty($row['member_id'])) */ $new_reps[ $row['member_id'] ]['id']   = $row['member_id'];
				
				if ($row['CODE'] == '01')
				{
					$new_reps[ $row['member_id'] ]['up'] ++ ;
				}
				else
				{
					$new_reps[ $row['member_id'] ]['down'] ++ ;
				}
				
				$new_reps[ $row['from_id'] ]['times'] ++ ;
			}
			
			ksort($new_reps);
			
			foreach ($new_reps as $member)
			{
				if (!$member['name']) continue;
				
				$member['name'] = "<a href='{$ibforums->base_url}act=Profile&CODE=03&MID={$member['id']}'><b>{$member['name']}</b></a>" ;
				
				if ($member['up']) $member['rep'] = "+".$member['up'];
				
				if ($member['down'])
				{
					if ($member['up']) $member['rep'] .= " | ";
					
					$member['rep'] .= "-".$member['down'];
				}
				
				if (empty($member['rep'])) $member['rep'] = $ibforums->lang['no_changes'];
				else $member['rep'] .= " ".$ibforums->lang['rep_postfix'].
									   " <a href='{$ibforums->base_url}act=rep&CODE=03&mid={$member['id']}'>{$ibforums->lang['details']}</a>";
				
				if (empty($member['times'])) $member['times'] = $ibforums->lang['no_changes'];
				else $member['times'] .= " ".$ibforums->lang['rep_postfix'].
										 " <a href='{$ibforums->base_url}act=rep&CODE=04&mid={$member['id']}'>{$ibforums->lang['details']}</a>";
				
				$this->output .= $this->rep_html->ShowTotalsRow($member);
			}
		}
		
		$back = "javascript:history.go(-1)";
		
		$this->output .= $this->rep_html->ShowFooter($back);
		
		$this->output .= "<br />";
		$this->output .= $this->rep_html->Links( $links, $new );
		
		$this->nav        = array( $ibforums->lang['nnav'] );
		$this->page_title = $ibforums->lang['ntitle'];
	}
	
	function edit_comment()
	{
		global $ibforums, $DB, $std;
		
		if ($ibforums->vars['rep_allow_comments'] != 1)
		{
			$std->Error( array( LEVEL=> 1, MSG =>'missing_files') );
		}
		
		$ibforums->input['id'] = intval($ibforums->input['id']);
		
		if (!$ibforums->input['id'])
		{
			$std->Error( array( LEVEL=> 1, MSG =>'missing_files') );
		}
		
		$DB->query("SELECT * FROM ibf_reputation WHERE msg_id='{$ibforums->input['id']}'");
		$i = $DB->fetch_row();
		
		if ($ibforums->member['id'] != $i['member_id'])
		{
			$std->Error( array( LEVEL=> 1, MSG =>'incorrect_use') );
		}
		
		$i['comment'] = $this->parser->unconvert($i['comment'], $ibforums->vars['rep_enable_ibc'], 0);
		$i['comment'] = preg_replace('/#MEM_(.+?)_#EDAT: /', '', $i['comment']);
		
		$this->output .= $this->rep_html->EditComment($i);
		
		$this->nav        = array( $ibforums->lang['do_comment'] );
		$this->page_title = $ibforums->lang['do_comment'];
	}
	
	function save_comment()
	{
		global $ibforums, $DB, $std, $print;
		
		if ($ibforums->vars['rep_allow_comments'] != 1)
		{
			$std->Error( array( LEVEL=> 1, MSG =>'missing_files') );
		}
		
		$ibforums->input['id'] = intval($ibforums->input['id']);
		
		if (!$ibforums->input['id'])
		{
			$std->Error( array( LEVEL=> 1, MSG =>'missing_files') );
		}
		
		$DB->query("SELECT * FROM ibf_reputation WHERE msg_id='{$ibforums->input['id']}'");
		$i = $DB->fetch_row();
		
		if ($ibforums->member['id'] != $i['member_id'])
		{
			$std->Error( array( LEVEL=> 1, MSG =>'incorrect_use') );
		}
		
		$ibforums->input['comment'] = $this->parser->convert( array( TEXT    => $ibforums->input['comment'],
																	 SMILIES => $ibforums->vars['rep_enable_emo'],
																	 CODE    => $ibforums->vars['rep_enable_ibc'],
																	 HTML    => 0
															)		);
		
		$ibforums->input['comment'] = trim($ibforums->input['comment']);
		
		if ($ibforums->input['comment'] != '')
		{
			$ibforums->input['comment'] = '#MEM_' . $ibforums->member['name'] . '_#EMEM, ' .
										  '#DAT_' . time() . '_#EDAT: ' . $ibforums->input['comment'];
		}
		
		$str = $DB->compile_db_update_string( array( 
													 'comment' => $ibforums->input['comment'],
											)      );
		
		$DB->query("UPDATE ibf_reputation SET $str WHERE msg_id='".$ibforums->input['id']."'");
		
		$print->redirect_screen($ibforums->lang['comment_success'], "act=rep&CODE=03&mid=".$i['member_id'] );
	}
	
	
	// ------------------
	// Utility Functions
	// ------------------
	
	function update_rep($new, $memid)
	{
		global $DB;
		
		$DB->query("UPDATE ibf_members SET rep='".$new."' WHERE id='$memid'");
	}
	
	function get_rep($memid)
	{
		global $DB;
		
		$DB->query("SELECT rep FROM ibf_members WHERE id='$memid'");
		$info = $DB->fetch_row();
		
		return $info['rep'];
	}
}

?>
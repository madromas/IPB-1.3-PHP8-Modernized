<?php

$fav = new fav;

class fav {

	var $output = "";
    var $html = "";
    var $base_url = "";
    var $nav = array();

	function __construct() {
    	global $ibforums, $DB, $print, $std;

        $this->base_url = $ibforums->base_url;
    	$this->html = $std->load_template('skin_fav');
		$this->nav[] = "Favorite topics";

        if(!$ibforums->member['id']) {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'fav_guest') );
        }

	    $refer = $_SERVER['HTTP_REFERER'];
        if(!preg_match("#".$ibforums->base_url."\?#",$refer)) $refer = "";
        $refer = preg_replace("#".$ibforums->base_url."\?#","",$refer);

        if(isset($ibforums->input['topic'])) {
        	$topic = $ibforums->input['topic'];
            $topic = intval($topic);

            $DB->query("SELECT tid FROM ".$ibforums->vars['sql_tbl_prefix']."topics WHERE tid=".$topic);

            if($DB->get_num_rows()) {
        		$DB->query("SELECT favorites AS f FROM ".$ibforums->vars['sql_tbl_prefix']."members WHERE id=".$ibforums->member['id']);
            	$favs = $DB->fetch_row();

            	$favlist = explode(",",$favs['f']);
            	if(!is_numeric($favlist[0]))
            		$favlist[0] = 0;
            	if(in_array($topic,$favlist)) {
            		if(count($favlist) == 1) {
                		$favs['f'] = str_replace("$topic","",$favs['f']);
                	} elseif($favlist[0] == $topic) {
                		$favs['f'] = str_replace("$topic,","",$favs['f']);
                	} else {
            			$favs['f'] = str_replace(",$topic","",$favs['f']);
                	}
                	$redirect = " deleted from ";
            	} else {
            		if($favlist[0] != 0) {
                		$favs['f'] .= ','.$topic;
                	} else {
                		$favs['f'] .= $topic;
                	}
            		$redirect = " added to ";
            	}
            	$DB->query("UPDATE ".$ibforums->vars['sql_tbl_prefix']."members SET favorites='".$favs['f']."' WHERE id=".$ibforums->member['id']);
            	$print->redirect_screen( "Subject".$redirect."Your favorite topics.", $refer );
            } else {
            	$e = "Invalid topic ID";
                $this->output .= $this->html->error($e);
                $print->add_output($this->output);
    			$print->do_output(array( 'TITLE' => $ibforums->vars['board_name'].$cp, 'JS' => 0, 'NAV' => $this->nav ) );
            }
		} else {
			$this->show_favs();
        }

    }//End function fav()

    function show_favs() {
    	global $ibforums, $print, $std, $DB;

        $DB->query("SELECT favorites AS f FROM ".$ibforums->vars['sql_tbl_prefix']."members WHERE id=".$ibforums->member['id']);
        $favs = $DB->fetch_row();

        if($favs['f'] == "") {
        	$e = "You haven't selected the topic you want to add.<br /><br />
            	  Add them by any click of the envelope image on the far left of the topic when viewed on the forum.
                  <br /> - <b>Or</b> - <br />
                  By clicking Add/Delete a Favorite Link while reviewing the topic ";
            $this->output .= $this->html->error($e);
        } else {
        	$DB->query("SELECT tid, title, starter_id, last_poster_id, last_post, starter_name, last_poster_name FROM ".$ibforums->vars['sql_tbl_prefix']."topics WHERE tid IN (".$favs['f'].")");
        	while($topic = $DB->fetch_row()) {
        		if($topic['last_post'] > $ibforums->member['last_visit']) {
                	$new[] = $topic;
                } else {
                	$nonew[] = $topic;
                }
        	}
            if(isset($new)) {
            	foreach($new as $n) {
                	$n['last_post'] = $std->get_date($n['last_post'],"LONG");
                	$html['new'] .= $this->html->topic_row($n);
                }
            } else {
            	$html['new'] = $this->html->none();
            }
            if(isset($nonew)) {
            	foreach($nonew as $n) {
                    $n['last_post'] = $std->get_date($n['last_post'],"LONG");
                	$html['nonew'] .= $this->html->topic_row($n);
            	}
            } else {
            	$html['nonew'] = $this->html->none();
            }
            $this->output .= $this->html->main($html);
        }

        $print->add_output($this->output);
    	$print->do_output(array( 'TITLE' => $ibforums->vars['board_name'].$cp, 'JS' => 0, 'NAV' => $this->nav ) );
    }//End of function show_favs()

}//End class fav

?>
<?php

/*
+--------------------------------------------------------------------------
|   Awards Module
|   ========================================
|   Modernized for PHP 8 Compatibility
+--------------------------------------------------------------------------
*/

$idx = new awards;

class awards {

    var $output     = "";
    var $page_title = "";
    var $html       = "";

    function __construct() {
        global $ibforums, $DB, $std, $print;
        
        //--------------------------------------------
        // Require the HTML and language modules
        //--------------------------------------------
        
        $ibforums->lang = $std->load_words($ibforums->lang, 'lang_awards', $ibforums->lang_id );
        
        $this->html = $std->load_template('skin_awards');
        
        // Fallback for non-cached skins
        if ( ! is_object($this->html) ) {
            require_once( ROOT_PATH . "Skin/s1/skin_awards.php" );
            $this->html = new skin_awards();
        }

        //--------------------------------------------
        // Main Logic
        //--------------------------------------------
        
        $mid = intval($ibforums->input['mid']);
        
        if ( ! $mid ) {
            $std->Error( array( 'LEVEL' => 1, 'ID' => 'no_user' ) );
        }

        $DB->query("SELECT name FROM ibf_members WHERE id='{$mid}'");
        $member = $DB->fetch_row();
        
        if ( ! $member['name'] ) {
            $std->Error( array( 'LEVEL' => 1, 'ID' => 'no_user' ) );
        }

        $this->page_title = $ibforums->lang['awards_title'] . " " . $member['name'];

        // Start Page
        $this->output .= $this->html->awards_page_top($member['name']);

        $DB->query("SELECT * FROM ibf_awards WHERE mid='{$mid}' ORDER BY id DESC");
        
        if ( $DB->get_num_rows() ) {
            while ( $row = $DB->fetch_row() ) {
                $this->output .= $this->html->awards_row($row);
            }
        } else {
            $this->output .= $this->html->awards_none();
        }

        $this->output .= $this->html->awards_page_bottom();

        //--------------------------------------------
        // Output to Board Skin
        //--------------------------------------------

        $print->add_output($this->output);
        $print->do_output( array( 'TITLE' => $this->page_title, 'NAV' => array( $this->page_title ) ) );
    }
}
?>
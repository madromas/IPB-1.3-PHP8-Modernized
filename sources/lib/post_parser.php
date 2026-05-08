<?php

/*
+--------------------------------------------------------------------------
|   Modernized Parser (Legacy IPB 1.3 Bridge)
|   ========================================
|   Modernized for PHP 8 compatibility.
|   Forced filtering for post_db_parse and convert_text.
+--------------------------------------------------------------------------
*/

class post_parser {

    var $error          = "";
    var $badwords       = array(); 
    var $in_sig         = "";

    function __construct($load=1) { 
        global $DB;
        
        if ($load != 0) {
            $DB->query("SELECT * from ibf_badwords");
            
            $this->badwords = is_array($this->badwords) ? $this->badwords : array();

            if ( $DB->get_num_rows() ) {
                while ( $r = $DB->fetch_row() ) {
                    $this->badwords[] = array( 
                        'type'    => stripslashes($r['type']),
                        'swop'    => stripslashes($r['swop']),
                        'm_exact' => $r['m_exact'],
                    );
                }
            }
        }
    }

    /**
     * Main Conversion Engine
     */
    function convert($in=array( 'TEXT' => "", 'SIGNATURE' => 0, 'MOD_FLAG' => FALSE)) {
        global $ibforums;

        $this->in_sig = $in['SIGNATURE'];
        $txt = $in['TEXT'];

        $txt = str_replace( array('&lt;p&gt;', '&lt;/p&gt;'), array('<p>', '</p>'), $txt );

// 2. MOVE CUSTOM TAGS HERE (Above the early return)
    if ( isset($in['MOD_FLAG']) && $in['MOD_FLAG'] == TRUE ) {
        $txt = preg_replace_callback("#\[mod\](.+?)\[/mod\]#si", function($m) {
            return method_exists($this, 'regex_mod_tag') ? $this->regex_mod_tag($m[1]) : $m[0];
        }, $txt);

        $txt = preg_replace_callback("#\[ex\](.+?)\[/ex\]#si", function($m) {
            return method_exists($this, 'regex_exclaime_tag') ? $this->regex_exclaime_tag($m[1]) : $m[0];
        }, $txt);
    } else {
        $txt = preg_replace("#\[(mod|ex)\](.+?)\[/(mod|ex)\]#si", '\\2', $txt);
    }

    // Syntax Highlighting
        if ($in['SIGNATURE'] != 1) {
            $txt = preg_replace_callback("#\[sql\](.+?)\[/sql\]#s", function($m) { return $this->regex_sql_tag($m[1]); }, $txt);
            $txt = preg_replace_callback("#\[html\](.+?)\[/html\]#s", function($m) { return $this->regex_html_tag($m[1]); }, $txt);
        }


        // If content starts with HTML tags, filter and return immediately
        $html_pattern = $ibforums->vars['html_detection_regex'] ?: '^<(p|div|span|ul|ol|table|br|iframe)';
        if ( preg_match( "/".$html_pattern."/i", trim($txt) ) ) {
            return $this->bad_words($txt); 
        }

        return $this->bad_words($txt);
    }

    /**
     * Wrapper used by legacy modules to strip basic tags
     */
    function convert_text($txt="") {
        $txt = htmlspecialchars($txt);
        return $this->bad_words($txt); 
    }

    /**
     * post_db_parse
     * Updated to force the bad word filter to run
     */
    function post_db_parse($t="", $use_html=0) {
        global $ibforums;

$t = preg_replace('/(?<!<a href=")<img([^>]+)src=["\'](uploads\/[^"\']+)["\']([^>]*)>(?!<\/a>)/i', '<a data-fancybox href="$2" target="_blank"><img$1src="$2"$3></a>', $t);

// Then apply the base URL fix we discussed for lofiversion compatibility
$t = str_replace('href="uploads/', 'href="'.$ibforums->vars['board_url'].'/uploads/', $t);
$t = str_replace('src="uploads/', 'src="'.$ibforums->vars['board_url'].'/uploads/', $t);
        
        return $this->bad_words($t); 
    }

    /**
     * Badwords Filter
     */
    function bad_words($text = "") {


    if ($text == "" || !is_array($this->badwords) || count($this->badwords) == 0) {
        return $text;
    }

    foreach($this->badwords as $r) {
        $word    = trim($r['type']); 
        $replace = ($r['swop'] == "") ? '######' : $r['swop'];
        
        $text = str_ireplace($word, $replace, $text);
    }

    return $text;
}

    function unconvert($txt="", $code=1, $html=0) {
        return trim(stripslashes($txt));
    }

    function regex_mod_tag($txt="") {
        return "<!--mod1--><div class='mod-notice-wrapper'><table class='mod-table'><tr><td class='mod-icon-blue'>M</td><td class='mod-content'>".$txt."</td></tr></table></div><!--mod2-->";
    }

    function regex_exclaime_tag($txt="") {
        return "<!--ex1--><div class='ex-notice-wrapper'><table class='ex-table'><tr><td class='ex-icon-red'>!</td><td class='ex-content'>".$txt."</td></tr></table></div><!--ex2-->";
    }

    function regex_html_tag($html="") {
        $html = htmlspecialchars($html);
        return "<pre class='prettyprint lang-html'>$html</pre>";
    }

    function regex_sql_tag($sql="") {
        $sql = htmlspecialchars($sql);
        return "<pre class='prettyprint lang-sql'>$sql</pre>";
    }

    function wrap_style($out=array()) {
        $data = array();
        $data['START'] = "<div class='post-block-wrapper'>";
        $data['END']   = "</div>";
        return $data;
    }
}
?>
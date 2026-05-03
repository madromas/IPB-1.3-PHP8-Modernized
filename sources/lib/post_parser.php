<?php

/*
+--------------------------------------------------------------------------
|   Modernized Parser (Legacy IPB 1.3 Bridge)
|   ========================================
|   Modernized for PHP 8 compatibility.
|   Removed: BBCode, Default Smilies, Legacy URL parsing.
|   Kept: Mod Tags, Syntax Highlighting, Bad Word Filter.
+--------------------------------------------------------------------------
*/

class post_parser {

    var $error          = "";
    var $badwords       = "";
    var $in_sig         = "";

   function __construct($load=0) {
        global $DB;
        
        if ($load != 0) {
    // Pre-load the bad words filter
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
     * The Main Conversion Engine
     * Now primarily detects if content is HTML and runs the Bad Word filter.
     */
    function convert($in=array( 'TEXT' => "", 'SIGNATURE' => 0, 'MOD_FLAG' => FALSE)) {
        global $ibforums;
        
        $this->in_sig = $in['SIGNATURE'];
        $txt = $in['TEXT'];

        $txt = str_replace( array('&lt;p&gt;', '&lt;/p&gt;'), array('<p>', '</p>'), $txt );

        // If the content starts with HTML tags (from EditorJS/TinyMCE), skip parsing
        $html_pattern = $ibforums->vars['html_detection_regex'] ?: '^<(p|div|span|ul|ol|table|br|iframe)';
        if ( preg_match( "/".$html_pattern."/i", trim($txt) ) ) {
            return $this->bad_words($txt); 
        }

        // Handle custom MadWay Admin/Mod Tags
        if ( $in['MOD_FLAG'] === TRUE) {
            $txt = preg_replace_callback("#\[mod\](.+?)\[/mod\]#s", function($m) { return $this->regex_mod_tag($m[1]); }, $txt);
            $txt = preg_replace_callback("#\[ex\](.+?)\[/ex\]#s", function($m) { return $this->regex_exclaime_tag($m[1]); }, $txt);
        } else {
            $txt = preg_replace("#\[(mod|ex)\](.+?)\[/(mod|ex)\]#is", '\\2', $txt);
        }

        // Syntax Highlighting for [sql] and [html] tags in posts
        if ($in['SIGNATURE'] != 1) {
            $txt = preg_replace_callback("#\[sql\](.+?)\[/sql\]#s", function($m) { return $this->regex_sql_tag($m[1]); }, $txt);
            $txt = preg_replace_callback("#\[html\](.+?)\[/html\]#s", function($m) { return $this->regex_html_tag($m[1]); }, $txt);
        }

        return $this->bad_words($txt);
    }

     /**
     * wrap_style
     * Returns the wrapper HTML for code, quote, and sql blocks.
     * Necessary for post_q_reply_post.php and others.
     */
    function wrap_style($out=array()) {
        
        $data = array();
        
        switch ($out['STYLE']) {
            case 'QUOTE':
                $data['START'] = "<div class='quote-wrapper'><b>QUOTE</b> " . ($out['EXTRA'] ?? "") . "<div class='quote-content'>";
                $data['END']   = "</div></div>";
                break;
            case 'CODE':
                $data['START'] = "<div class='code-wrapper'><b>CODE</b><div class='code-content'>";
                $data['END']   = "</div></div>";
                break;
            default:
                $data['START'] = "<div class='post-block-wrapper'>";
                $data['END']   = "</div>";
                break;
        }
        
        return $data;
    }
    
    /**
     * unconvert
     * Fixes the "Call to undefined method" fatal error.
     * Passes raw HTML back to TinyMCE/EditorJS.
     */
    function unconvert($txt="", $code=1, $html=0) {
        return trim(stripslashes($txt));
    }


    /**
     * convert_text
     * A wrapper used by some legacy modules to strip basic tags[cite: 4].
     */
    function convert_text($txt="") {
        return htmlspecialchars($txt);
    }

    /**
     * Final Security Pass
     * Relies on the HTMLPurifier integration you already set up.
     */
    function post_db_parse($t="", $use_html=0) {
        return $t; 
    }

    /**
     * Badwords Filter
     */
    function bad_words($text = "") {
        global $DB;
        if ($text == "" || !is_array($this->badwords)) return $text;

        usort($this->badwords, function($a, $b) {
            return strlen($b['type']) <=> strlen($a['type']);
        });

        foreach($this->badwords as $r) {
            $replace = ($r['swop'] == "") ? '######' : $r['swop'];
            $quoted = preg_quote($r['type'], "/");

            if ($r['m_exact'] == 1) {
                $text = preg_replace( "/(^|\b)".$quoted."(\b|!|\?|\.|,|$)/i", "$replace", $text );
            } else {
                $text = preg_replace( "/".$quoted."/i", "$replace", $text );
            }
        }
        return $text;
    }

    // --- Custom MadWay UI Elements ---

    function regex_mod_tag($txt="") {
        return "<!--mod1--><div class='mod-notice-wrapper'><table class='mod-table'><tr><td class='mod-icon-blue'>M</td><td class='mod-content'>".$txt."</td></tr></table></div><!--mod2-->";
    }

    function regex_exclaime_tag($txt="") {
        return "<!--ex1--><div class='ex-notice-wrapper'><table class='ex-table'><tr><td class='ex-icon-red'>!</td><td class='ex-content'>".$txt."</td></tr></table></div><!--ex2-->";
    }

    // --- Syntax Highlighting Bridge ---

    function regex_html_tag($html="") {
        $html = htmlspecialchars($html);
        return "<pre class='prettyprint lang-html'>$html</pre>";
    }

    function regex_sql_tag($sql="") {
        $sql = htmlspecialchars($sql);
        return "<pre class='prettyprint lang-sql'>$sql</pre>";
    }
}
?>

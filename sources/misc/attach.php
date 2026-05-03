<?php

/*
+--------------------------------------------------------------------------
|   Invision Power Board v1.3 Final
|   ========================================
|   by Matthew Mecham
|   (c) 2001 - 2003 Invision Power Services
|   http://www.invisionpower.com
|   ========================================
|   Web: http://www.invisionboard.com
|   Time: Thu, 20 Nov 2003 01:15:27 GMT
|   Release: 322f4d4bcd09dcb3058f62ae41ab3e8b
|   Email: matt@invisionpower.com
|   Licence Info: http://www.invisionboard.com/?license
+---------------------------------------------------------------------------
|
|   > Attachment Handler module
|   > Module written by Matt Mecham
|   > Date started: 10th March 2002
|
|	> Module Version Number: 1.0.0
+--------------------------------------------------------------------------
*/


$idx = new attach;

class attach {

    
    function __construct()
    {
        global $ibforums, $DB, $std, $print, $skin_universal;
        
		if ($std->my_getcookie('member_id') == 0) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}
		
		if ($std->my_getcookie('member_id') == 0 && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $ibforums->vars['board_url']) === false) {
			$std->Error( array( 'LEVEL' => 1, 'MSG' => 'no_guests' ) );
		}

        $ibforums->input['id'] = preg_replace( "/^(\d+)$/", "\\1", $ibforums->input['id'] );
        
        if ($ibforums->input['id'] == "")
        {
        	$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        }
        
        if ($ibforums->input['type'] == 'post')
        {
        	// Handle post attachments.
        	
        	$DB->query("SELECT pid, attach_id, attach_type, attach_file FROM ibf_posts WHERE pid='".$ibforums->input['id']."'");
        	
        	if ( !$DB->get_num_rows() )
        	{
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        	}
        	
        	$post = $DB->fetch_row();
        	
        	if ( $post['attach_id'] == "" )
        	{
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        	}
        	
        	$file = $ibforums->vars['upload_dir']."/".$post['attach_id'];
        	
        	if ( file_exists( $file ) and ( $post['attach_type'] != "" ) )
        	{
        		// Update the "hits"..
        		
        		$DB->query("UPDATE ibf_posts SET attach_hits=attach_hits+1 WHERE pid='".$post['pid']."'");
        		
        		// Set up the headers..
        		
        		@header( "Content-Type: ".$post['attach_type']."\nContent-Disposition: inline; filename=\"".$post['attach_file']."\"\nContent-Length: ".(string)(filesize( $file ) ) );
        		
        		// Open and display the file..
        		
        		$fh = fopen( $file, 'rb' );  // Set binary for Win even if it's an ascii file, it won't hurt.
        		fpassthru( $fh );
        		@fclose( $fh );
        		exit();
        	}
        	else
        	{
        		// File does not exist..
        		$std->Error( array( 'LEVEL' => 1, 'MSG' => 'missing_files' ) );
        	}
        }
        
    }
        
       
}

?>
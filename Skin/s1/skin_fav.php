<?php

class skin_fav {

function main($html) {
global $ibforums;
return <<<EOF
	<div class="tableborder">
	<table width="100%" cellpadding="4" cellspacing="1">
    	<tr>
    		<td class="maintitle" colspan="5">Favorite Topics</td>
    	</tr>
        <tr>
        	<td class="titlemedium" colspan="5">New messages since your last visit</td>
        </tr>
        <tr>
        	<td class="pformstrip">Topic</td>
            <td class="pformstrip">Topic Starter</td>
            <td class="pformstrip">Last Post by</td>
            <td class="pformstrip">Last Post Time</td>
            <td class="pformstrip">Delete</td>
        </tr>
        {$html['new']}
        <tr>
        	<td class="titlemedium" colspan="5">No new messages since your last visit</td>
        </tr>
        	<td class="pformstrip">Topic</td>
            <td class="pformstrip">Topic Starter</td>
            <td class="pformstrip">Last Poster</td>
            <td class="pformstrip">Last Post Time</td>
            <td class="pformstrip">Delete</td>
        </tr>
        {$html['nonew']}
    </table>
    </div>
EOF;
}

function topic_row($t) {
global $ibforums;
return <<<EOF
    <tr align="center">
        <td class="row1" align="left">
            <a href="{$ibforums->base_url}showtopic={$t['tid']}&view=getnewpost"><b>{$t['title']}</b></a>
        </td>
        <td class="row1"><a href="{$ibforums->base_url}showuser={$t['starter_id']}">{$t['starter_name']}</a></td>
        <td class="row1"><a href="{$ibforums->base_url}showuser={$t['last_poster_id']}">{$t['last_poster_name']}</a></td>
        <td class="row1"><small>{$t['last_post']}</small></td>
        <td class="row1" align="center">
    <a href="{$ibforums->base_url}act=fav&amp;topic={$t['tid']}" title="Remove Favorite">
        <i class="fa-solid fa-trash-can" style="color: #e74c3c;"></i>
    </a>
</td>
    </tr>
EOF;
}

function none() {
global $ibforums;
return <<<EOF
    <tr>
    	<td class="row1" style="text-align: center; padding: 5px; font-weight: 900;" colspan="5">Empty</td>
    </tr>
EOF;
}

function error($e) {
global $ibforums;
return <<<EOF
	<center>
    <div class="tableborder">
    	<table width="100%" cellpadding="0" cellspacing="1">
        	<tr align="center">
        		<td class="maintitle">Error</td>
        	</tr>
            <tr align="center">
        		<td class="row1" style="padding: 3px;">{$e}</td>
        	</tr>
        </table>
    </div>
    </center><br />
EOF;
}

}

?>
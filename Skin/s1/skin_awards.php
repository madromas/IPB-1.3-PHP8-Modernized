<?php

class skin_awards {

function awards_page_top($name) {
global $ibforums;
return <<<EOF
<div class="navstrip">{$ibforums->lang['awards_title']} $name</div>
<br />
<table width='100%' border='0' cellspacing='0' cellpadding='0' align='center' class='tableborder'>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='1' cellpadding='4'>
                <tr> 
                    <td class='maintitle' colspan='4'>{$ibforums->lang['awards_maintitle']}</td>
                </tr>
                <tr> 
                    <td width='20%' align='center' class='pformstrip'>{$ibforums->lang['award_name']}</td>
                    <td width='20%' align='center' class='pformstrip'>{$ibforums->lang['award_icon']}</td>
                    <td width='20%' align='center' class='pformstrip'>{$ibforums->lang['award_granted']}</td>
                    <td width='40%' align='center' class='pformstrip'>{$ibforums->lang['award_desc']}</td>
                </tr>
EOF;
}

function awards_row($row) {
global $ibforums;
return <<<EOF
                <tr> 
                    <td class='row1' align='center'><b>{$row['awardtitle']}</b></td>
                    <td class='row1' align='center'><img src='html/awards/{$row['awardimg']}' border='0' alt='Award'></td>
                    <td class='row1' align='center'>{$row['cid']}</td>
                    <td class='row1' align='left'>{$row['description']}</td>
                </tr>
EOF;
}

function awards_none() {
global $ibforums;
return <<<EOF
                <tr>
                    <td class='row1' colspan='4' align='center' style='padding:20px;'>{$ibforums->lang['no_awards']}</td>
                </tr>
EOF;
}

function awards_page_bottom() {
global $ibforums;
return <<<EOF
                <tr> 
                    <td class='pformstrip' colspan='4' height='5'>&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
EOF;
}

}
?>
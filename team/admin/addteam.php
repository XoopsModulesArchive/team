<?php
include '../../../include/cp_header.php';
include "functions.php";

xoops_cp_header();
if (isset($teamid)) {
    $team_handler =& xoops_getmodulehandler('team');
    $team =& $team_handler->get($teamid);
    $teamname = $team->getVar('teamname');
    $teamtype = $team->getVar('teamtype');
    $maps = $team->getVar('maps');
    $submit = "Edit";
}
else {
    $teamname = "Name";
    $teamtype = "Game";
    $maps = "3";
    $submit = "Add";
}
$uid = $xoopsUser->getVar("uid");
echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
echo "<tr><td class='bg6'><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
echo "<tr class='bg6'><td><img src='".XOOPS_ROOT_PATH."/images/addteam.gif'></td>";
echo "</tr></table>";
echo "<table width='100%' border='0' cellpadding='4' cellspacing='1'>
          <form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"Add\">
          <input type='hidden' name='op' value='saveteam'>";
echo "<input type='hidden' name='created' value=".time().">";
echo "<input type='hidden' name='uid' value=".$uid.">";
echo "<input type='hidden' name='submit' value=".$submit.">";
if (isset($teamid)) {
    echo "<input type='hidden' name='teamid' value=".$teamid.">";
}
echo "<tr><td><b>"._AM_TEAMNAME."</b></td><td><input type='text' name='name' size='20' maxlength='25' value='".$teamname."'</td></tr>
  		<tr><td><b>"._AM_TEAMTYPE."</b></td><td><input type='text' name='type' size='20' maxlength='25' value='".$teamtype."'</td></tr>
        <tr><td><b>"._AM_TEAMMAPSPERMATCH."</b></td><td><input type='text' name='maps' size='10' maxlength='15' value='".$maps."'</td></tr>
        <tr><td align='left'><input type=submit value='".$submit."'></form></td></tr>
        </table></td></tr></table>";
xoops_cp_footer();
?>
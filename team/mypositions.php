<?php
include("../../mainfile.php");
include("../../header.php");
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : null;
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : null;
if (isset($_POST)) {
	foreach ($_POST as $k => $v) {
		${$k} = $v;
	}
}
$team_handler =& xoops_getmodulehandler('team');
if ($xoopsUser) {
   if (isset($submit)) {
       $team =& $team_handler->get($teamid);
       if ($team->isTeamMember($xoopsUser->getVar("uid"))) {
           $tertiary = intval($tertiary);
           if ($tertiary > 0) {
               $terclause = ", tertiarypos='$tertiary'";
           }
           else {
               $terclause = ", tertiarypos=''";
           }
           $xoopsDB->query("UPDATE ".$xoopsDB->prefix("team_teamstatus")." SET primarypos=".intval($primary).", secondarypos=".intval($secondary)." ".$terclause." WHERE uid=".intval($uid)." AND teamid='$teamid'");
           $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("team_skills")." WHERE uid='$uid'");
           $teamskills = $team->getSkills();
           foreach ($teamskills as $skillid => $skillname) {
               if (isset($checked[$skillid])) {
                   $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("team_skills")." (uid, posid, teamid) VALUES ('$uid', '$skillid', '$teamid')");
               }
           }
           redirect_header("positions.php?teamid=".$teamid,3, _AM_TEAMPSSKILLSUPDATE);
       }
       else {
           redirect_header("positions.php?teamid=".$teamid,3, _AM_TEAMACCESSDENY);
       }
   }
  else {
   if (!isset($teamid)) {
       $teamid = getDefaultTeam();
   }
   $team =& $team_handler->get($teamid);
   echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
   echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
   echo "<tr class='head'><td align='right'>";
   if (($xoopsUser->isAdmin($xoopsModule->mid()))||($team->isTeamAdmin($xoopsUser->getVar("uid")))) {
       echo "<a href='memberadmin.php?teamid=".$teamid."'>"._AM_TEAMADMIN."</a> | ";
   }
   echo "<a href='roster.php?teamid=".$teamid."'>"._AM_TEAMROSTER."</a> | ";
   echo "<a href='positions.php?teamid=".$teamid."'>"._AM_TEAMPOSOVERVIEW."</a> | <a href='avstats.php?teamid=".$teamid."'>"._AM_TEAMAVAILSTATS2."</a>";
   echo "</td></tr></table>";
   echo "<tr><td><table width='100%' border='0' cellpadding='4' cellspacing='1'>";
   if (isset($uid)) {
       $thisuid = $xoopsUser->getVar("uid");
        if (!$team->isTeamAdmin($thisuid)) {
            $uid = $thisuid;
        }
   }
   else {
   		$uid = $xoopsUser->getVar("uid");
   }
   $thisUser=new XoopsUser($uid);
   $user = $thisUser->getVar("uname");
   $sql = "SELECT rank, primarypos, secondarypos, tertiarypos FROM ".$xoopsDB->prefix("team_teamstatus")." WHERE uid=".$uid." AND teamid=".$teamid;
   $myteamstatus = $xoopsDB->fetchArray($xoopsDB->query($sql));
   $rankid = $myteamstatus["rank"];
   $primary = $myteamstatus["primarypos"];
   $secondary = $myteamstatus["secondarypos"];
   $tertiary = $myteamstatus["tertiarypos"];
   $rank = getRank($rankid);
   echo "<tr class='head'><td><b>".$team->getVar('teamname')."</b></td><td></td></tr>";
   echo "<tr class='even'><td>"._AM_TEAMPLAYERNAME."</td><td><a href='profile.php?uid=".$uid."'>".$user."</a></td>";
   echo "<tr class='odd'><td>"._AM_TEAMPLAYERRANK."</td><td><font color='".$rank["color"]."'>".$rank["rank"]."</font></td>";
   echo "<tr class='head'><td colspan=2><b>"._AM_TEAMPOSITIONS."</b></td>";
   echo "<form method='post' action='mypositions.php' ENCTYPE=\"multipart/form-data\" NAME=\"Positions\">";
   echo "<input type='hidden' name='uid' value='".$uid."'>";
   echo "<tr class='odd'><td>"._AM_TEAMPRIMARYPOSITION."</td><td>";
   echo "<SELECT name='primary'>";
   $teampos = $team->getPositions();
   foreach ( $teampos as $posid => $posname ) {
        echo "<OPTION value=".$posid." ".selectcheck($primary,$posid).">".$posname."</OPTION>";
   }
   echo "</SELECT>";
   echo "</td></tr>";
   echo "<tr class='even'><td>"._AM_TEAMSECONDARYPOSITION."</td><td>";
   echo "<SELECT name='secondary'>";
   foreach ( $teampos as $posid => $posname ) {
        echo "<OPTION value=".$posid." ".selectcheck($secondary,$posid).">".$posname."</OPTION>";
   }
   echo "</SELECT>";
   echo "</td></tr>";
   echo "<tr class='odd'><td>"._AM_TEAMTERTIARYPOSITION."</td><td>";
   echo "<SELECT name='tertiary'><OPTION value=null ".selectcheck($tertiary,null).">"._AM_TEAMNONE."</OPTION>";
   foreach ( $teampos as $posid => $posname ) {
        echo "<OPTION value=".$posid." ".selectcheck($tertiary,$posid).">".$posname."</OPTION>";
   }
   echo "</SELECT>";
   echo "</td></tr>";
   echo "<tr class='head'><td colspan=2><b>"._AM_TEAMSKILLS.":</b></td></tr>";
   $teamskills = $team->getSkills();
   foreach ($teamskills as $skillid => $skillname) {
       if ((isset($class))&&($class=="even")) {
           $class = "odd";
       }
       else {
           $class = "even";
       }
       echo "<tr class=".$class."><td>".$skillname."</td>";
       echo "<td><input type='checkbox' name='checked[".$skillid."]' ".skillcheck($teamid, $uid, $skillid)."></td></tr>";
   }
   echo "</td></tr>";
   echo "<tr class='head'><td colspan=2><input type=hidden name='teamid' value=".$teamid.">";
   echo "<input type=hidden name='submit' value=1>";
   echo "<input type=submit value='Update'></form></td></tr>";
   echo "</table></td></tr></table>";
  }
}
else {
     redirect_header("../../index.php",3,_AM_TEAMSORRYRESTRICTEDAREA);
}
include_once(XOOPS_ROOT_PATH."/footer.php");
?>

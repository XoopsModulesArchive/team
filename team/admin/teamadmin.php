<?php
include '../../../include/cp_header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';
include_once('./functions.php');

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : 'default';
if (isset($_POST)) {
	foreach ($_POST as $k => $v) {
		${$k} = $v;
	}
}

xoops_cp_header();
$team_handler =& xoops_getmodulehandler('team');
$team =& $team_handler->get($teamid);
switch ($op) {
    case "addmember":
         $success=0;
         $failure=0;
         foreach ($addteammembers as $member_id) {
             if ($team->addTeamMember($member_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPLAYERSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPLAYERSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;
    case "delmember":
         $success=0;
         $failure=0;
         foreach ($removeteammembers as $teammember_id) {
             if ($team->delTeamMember($teammember_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPLAYERSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPLAYERSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;
    case "addmap":
         $success=0;
         $failure=0;
         foreach ($addteammaps as $map_id) {
             if ($team->addTeamMap($map_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMMAPSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMMAPSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;
    case "delmap":
         $success=0;
         $failure=0;
         foreach ($removeteammaps as $teammap_id) {
             if ($team->delTeamMap($teammap_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMMAPSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMMAPSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;
    case "addserver":
         $success=0;
         $failure=0;
         foreach ($addteamservers as $server_id) {
             if ($team->addTeamServer($server_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSERVERSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSERVERSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);

         break;
    case "delserver":
         $success=0;
         $failure=0;
         foreach ($removeteamserver as $teamserver_id) {
             if ($team->delTeamServer($teamserver_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSERVERSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSERVERSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;
    case "addsize":
         $success=0;
         $failure=0;
         foreach ($addteamsizes as $size_id) {
             if ($team->addTeamSize($size_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSIZESADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSIZESNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delsize":
         $success=0;
         $failure=0;
         foreach ($removeteamsizes as $size_id) {
             if ($team->delTeamSize($size_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSIZESREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSIZESNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "addside":
         $success=0;
         $failure=0;
         foreach ($addteamsides as $side_id) {
             if ($team->addTeamSide($side_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSIDESADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSIDESNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delside":
         $success=0;
         $failure=0;
         foreach ($removeteamsides as $side_id) {
             if ($team->delTeamSide($side_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMSIDESREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMSIDESNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "addrank":
         $success=0;
         $failure=0;
         foreach ($addteamranks as $rankid) {
             if ($team->addTeamRank($rankid)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMRANKSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMRANKSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delrank":
         $success=0;
         $failure=0;
         foreach ($removeteamranks as $rankid) {
             if ($team->delTeamRank($rankid)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMRANKSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMRANKSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "addpos":
         $success=0;
         $failure=0;
         foreach ($addteampos as $pos_id) {
             if ($team->addTeamPosition($pos_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPOSITIONSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPOSITIONSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delpos":
         $success=0;
         $failure=0;
         foreach ($removeteampos as $teampos_id) {
             if ($team->delTeamPosition($teampos_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPOSITIONSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPOSITIONSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "addskill":
         $success=0;
         $failure=0;
         foreach ($addteamskills as $pos_id) {
             if ($team->addTeamSkill($pos_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPOSITIONSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPOSITIONSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delskill":
         $success=0;
         $failure=0;
         foreach ($removeteamskills as $teampos_id) {
             if ($team->delTeamSkill($teampos_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMPOSITIONSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMPOSITIONSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "addladder":
         $success=0;
         $failure=0;
         foreach ($addteamladders as $ladder_id) {
             if ($team->addTeamLadder($ladder_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMLADDERSADDED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMLADDERSNOTADDED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "delladder":
         $success=0;
         $failure=0;
         foreach ($removeteamladders as $ladder_id) {
             if ($team->delTeamLadder($ladder_id)) {
                 $success++;
             }
             else {
                 $failure++;
             }
         }
         $feedback = $success." "._AM_TEAMLADDERSREMOVED."<br>";
         if ($failure) {
             $feedback .= $failure." "._AM_TEAMLADDERSNOTREMOVED."";
         }
         redirect_header("teamadmin.php?teamid=".$teamid,3,$feedback);
         break;

    case "default":
         $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/manageteam.gif";
         $url[0]["url"] = "index.php";
         $url[0]["text"] = _AM_TEAMCONFIG;
         $url[1]["url"] = "index.php?op=teammanager";
         $url[1]["text"] = _AM_TEAMMNGR;
         $url[2]["url"] = "";
         $url[2]["text"] = _AM_TEAMEDITTEAM;
         teamTableLink($img, $url);
         teamTableClose();
         teamTableOpen();
         echo "<td colspan=2>";
         include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
         $tform = new XoopsThemeForm(""._AM_TEAMOPTIONSFOR." ".$team->getVar('teamname'), "editteam", "index.php");
         $op_hidden = new XoopsFormHidden('op', "saveteam");
         $submit = new XoopsFormButton('', 'submit', 'Edit', 'submit');
         $action_hidden = new XoopsFormHidden('submit', "Edit");
         $teamid_hidden = new XoopsFormHidden('teamid', $teamid);
         $button_tray = new XoopsFormElementTray('' ,'');
         $name = new XoopsFormText(_AM_TEAMNAME, 'name', 20, 20, $team->getVar('teamname'), 'E');
         $type = new XoopsFormText(_AM_TEAMTYPE, 'type', 20, 20, $team->getVar('teamtype'), 'E');
         $maps_select = new XoopsFormSelect(_AM_TEAMMAPSPERMATCH, 'maps', $team->getVar('maps'));
         for ($i=1; $i <=5; $i++) {
             $maps_select->addOption($i);
         }
         $button_tray->addElement($submit);
         $tform->addElement($name);
         $tform->addElement($type);
         $tform->addElement($maps_select);
         $tform->addElement($op_hidden);
         $tform->addElement($action_hidden);
         $tform->addElement($teamid_hidden);
         $tform->addElement($button_tray);
         $tform->display();
         $allmembers = getAllMembers();
         $members = $team->getTeamMembers();
         $nomembers =& array_diff($allmembers, $members);
         echo "</td></tr>";
         $select[0] = "addteammembers";
         $select[1] = "removeteammembers";
         $ops[0] = "addmember";
         $ops[1] = "delmember";
         $lang[0] = _AM_TEAMNONMEMBERS;
         $lang[1] = _AM_TEAMMEMBERADMIN;
         $lang[2] = _AM_TEAMTEAMMEMBERS;
         teamItemManage($nomembers, $members, $teamid, $ops, $select, $lang);

         $allmaps = getAllMaps();
         $maps = $team->getTeamMaps();
         $nomaps =& array_diff($allmaps, $maps);
         $select[0] = "addteammaps";
         $select[1] = "removeteammaps";
         $ops[0] = "addmap";
         $ops[1] = "delmap";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMMAPSELECTION;
         $lang[2] = _AM_TEAMTEAMMAPS;
         teamItemManage($nomaps, $maps, $teamid, $ops, $select, $lang);

         $allpos = getAllPositions();
         $pos = $team->getPositions();
         $nopos =& array_diff($allpos, $pos);
         $select[0] = "addteampos";
         $select[1] = "removeteampos";
         $ops[0] = "addpos";
         $ops[1] = "delpos";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMPOSITIONSELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($nopos, $pos, $teamid, $ops, $select, $lang);

         $allskills = getAllSkills();
         $skills = $team->getSkills();
         $noskills =& array_diff($allskills, $skills);
         $select[0] = "addteamskills";
         $select[1] = "removeteamskills";
         $ops[0] = "addskill";
         $ops[1] = "delskill";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMPOSITIONSKILLSELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($noskills, $skills, $teamid, $ops, $select, $lang);

         $allservers = getAllServers();
         $servers = $team->getServers();
         $noservers =& array_diff($allservers, $servers);
         $select[0] = "addteamservers";
         $select[1] = "removeteamservers";
         $ops[0] = "addserver";
         $ops[1] = "delserver";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMSERVERSELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($noservers, $servers, $teamid, $ops, $select, $lang);

         $allsizes = getAllTeamsizes();
         $teamsizes = $team->getTeamSizes();
         $nosizes =& array_diff($allsizes, $teamsizes);
         $select[0] = "addteamsizes";
         $select[1] = "removeteamsizes";
         $ops[0] = "addsize";
         $ops[1] = "delsize";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMSIZESELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($nosizes, $teamsizes, $teamid, $ops, $select, $lang);

         $allsides = getAllTeamsides();
         $teamsides = $team->getSides();
         $nosides =& array_diff($allsides, $teamsides);
         $select[0] = "addteamsides";
         $select[1] = "removeteamsides";
         $ops[0] = "addside";
         $ops[1] = "delside";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMSIDESELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($nosides, $teamsides, $teamid, $ops, $select, $lang);

         $allranks = getAllTeamranks();
         $teamranks = $team->getRanks($teamid);
         $ranks = array();
         foreach ($teamranks as $rankid => $rank) {
         	$ranks[$rankid] = $rank["rank"];
         }
         $noranks =& array_diff($allranks, $ranks);
         $select[0] = "addteamranks";
         $select[1] = "removeteamranks";
         $ops[0] = "addrank";
         $ops[1] = "delrank";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMRANKSELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($noranks, $ranks, $teamid, $ops, $select, $lang);

         $allladders = getAllLadders();
         foreach ($allladders as $ladderid => $ladder) {
         	$all[$ladderid] = $ladder["ladder"];
         }
         $allladders = $all;
         $teamladders = $team->getLadders();
         $noladders =& array_diff($allladders, $teamladders);
         $select[0] = "addteamladders";
         $select[1] = "removeteamladders";
         $ops[0] = "addladder";
         $ops[1] = "delladder";
         $lang[0] = _AM_TEAMNONSELECTED;
         $lang[1] = _AM_TEAMLADDERSELECTION;
         $lang[2] = _AM_TEAMSELECTED;
         teamItemManage($noladders, $teamladders, $teamid, $ops, $select, $lang);

         teamTableClose();
         break;
}

xoops_cp_footer();
?>

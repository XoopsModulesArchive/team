<?php
include("../../mainfile.php");
include XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : null;
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : null;
if (isset($_POST)) {
	foreach ($_POST as $k => $v) {
		${$k} = $v;
	}
}
if ($xoopsUser) {
    $team_handler =& xoops_getmodulehandler('team','team');
   $uid = $xoopsUser->getVar("uid");
   include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
   $xoopsOption['template_main'] = 'team_positions.html';
   if (isset($mid)) {
       $match_handler =& xoops_getmodulehandler('match','team');
       $match =& $match_handler->get($mid);
       $teamid = $match->getVar('teamid');
       $team =& $team_handler->get($teamid);
       if (!$team->isTeamMember($uid)) {
           redirect_header('index.php', 3, _MD_TEAMACCESSDENY);
           exit();
       }
       $maps = $match->getMatchMaps();
       foreach ($maps as $mapno => $thismap) {
           $map[$mapno]["caption"] = getCaption($mapno);
           $map[$mapno]["name"] = is_object($thismap->map) ? $thismap->map->getVar('mapname') : "??";
       }
       $xoopsTpl->assign('maps', $map);
       if ($available = $match->getPositions("Yes")) {
           $team->positions(_MD_TEAMAVAILABLE, $available);
       }
       if ($latepos = $match->getPositions("LateYes")) {
           $team->positions(_MD_TEAMLATEPOSITIVE, $latepos);
       }
       if ($subs = $match->getPositions("Sub")) {
           $team->positions(_MD_TEAMSUBSTITUTES, $subs);
       }
       $xoopsTpl->assign('opponent', $match->getVar('opponent'));
       $xoopsTpl->assign('match', 1);
       $xoopsTpl->assign('mid', $mid);
       $xoopsTpl->assign('teamid', $teamid);
   }
   else {
       if (!isset($teamid)) {
           $teamid=getDefaultTeam();
       }
       $team =& $team_handler->get($teamid);
       $team->select();
       if (!$team->isTeamMember($uid)) {
           redirect_header("index.php",3,_MD_TEAMSORRYRESTRICTEDAREA);
       }
       else {
           $players = $team->getPlayerPositions();
           $team->positions($team->getVar('teamname'), $players);
           $xoopsTpl->assign('teamid', $teamid);
       }
   }
   if ($team->isTeamAdmin($uid)) {
       $xoopsTpl->assign('admin', "Yes");
   }
   $xoopsTpl->assign('allranks', getAllRanks());
   $xoopsTpl->assign('teamname', $team->getVar('teamname'));
   $xoopsTpl->assign('lang_teamnickname', _MD_TEAMNICKNAME);
   $xoopsTpl->assign('lang_teamversus', _MD_TEAMVERSUS);
   $xoopsTpl->assign('lang_teammatchlist', _MD_TEAMMATCHLIST);
   $xoopsTpl->assign('lang_teammatchdetails', _MD_TEAMMATCHDETAILS);
   $xoopsTpl->assign('lang_teammatchavailability', _MD_TEAMMATCHAVAILABILITY);
   $xoopsTpl->assign('lang_teamadmin', _MD_TEAMADMIN);
   $xoopsTpl->assign('lang_teammatchlist', _MD_TEAMMATCHLIST);
   $xoopsTpl->assign('lang_teamroster', _MD_TEAMROSTER);
   $xoopsTpl->assign('lang_teammypos', _MD_TEAMMYPOS);
   $xoopsTpl->assign('lang_teamavailstats2', _MD_TEAMAVAILSTATS2);
   $xoopsTpl->assign('lang_teamprimaryposition', _MD_TEAMPRIMARYPOSITION);
   $xoopsTpl->assign('lang_teamsecondary', _MD_TEAMSECONDARY);
   $xoopsTpl->assign('lang_teamtertiary', _MD_TEAMTERTIARY);
   $xoopsTpl->assign('lang_teamfirstpos', _MD_TEAMFIRSTPOS);
   $xoopsTpl->assign('lang_teamsecondpos', _MD_TEAMSECONDPOS);
   $xoopsTpl->assign('lang_teamthirdpos', _MD_TEAMTHIRDPOS);
}
else {
	redirect_header("index.php",3,_MD_TEAMSORRYRESTRICTEDAREA);
}
include(XOOPS_ROOT_PATH."/footer.php");
?>
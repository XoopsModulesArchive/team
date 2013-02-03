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
    $team_handler =& xoops_getmodulehandler('team');
   $uid = $xoopsUser->getVar("uid");
   include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
   $xoopsOption['template_main'] = 'team_positions.html';
   if (isset($mid)) {
       $match_handler =& xoops_getmodulehandler('match');
       $match =& $match_handler->get($mid);
       $teamid = $match->getVar('teamid');
       $team =& $team_handler->get($teamid);
       if (!$team->isTeamMember($uid)) {
           redirect_header('index.php', 3, _AM_TEAMACCESSDENY);
           exit();
       }
       $maps = $match->getMatchMaps();
       foreach ($maps as $mapno => $thismap) {
           $map[$mapno]["caption"] = getCaption($mapno);
           $map[$mapno]["name"] = is_object($thismap->map) ? $thismap->map->getVar('mapname') : "??";
       }
       $xoopsTpl->assign('maps', $map);
       if ($available = $match->getPositions("Yes")) {
           $team->positions(_AM_TEAMAVAILABLE, $available);
       }
       if ($latepos = $match->getPositions("LateYes")) {
           $team->positions(_AM_TEAMLATEPOSITIVE, $latepos);
       }
       if ($subs = $match->getPositions("Sub")) {
           $team->positions(_AM_TEAMSUBSTITUTES, $subs);
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
           redirect_header("index.php",3,_AM_TEAMSORRYRESTRICTEDAREA);
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
   $xoopsTpl->assign('lang_teamnickname', _AM_TEAMNICKNAME);
   $xoopsTpl->assign('lang_teamversus', _AM_TEAMVERSUS);
   $xoopsTpl->assign('lang_teammatchlist', _AM_TEAMMATCHLIST);
   $xoopsTpl->assign('lang_teammatchdetails', _AM_TEAMMATCHDETAILS);
   $xoopsTpl->assign('lang_teammatchavailability', _AM_TEAMMATCHAVAILABILITY);
   $xoopsTpl->assign('lang_teamadmin', _AM_TEAMADMIN);
   $xoopsTpl->assign('lang_teammatchlist', _AM_TEAMMATCHLIST);
   $xoopsTpl->assign('lang_teamroster', _AM_TEAMROSTER);
   $xoopsTpl->assign('lang_teammypos', _AM_TEAMMYPOS);
   $xoopsTpl->assign('lang_teamavailstats2', _AM_TEAMAVAILSTATS2);
   $xoopsTpl->assign('lang_teamprimaryposition', _AM_TEAMPRIMARYPOSITION);
   $xoopsTpl->assign('lang_teamsecondary', _AM_TEAMSECONDARY);
   $xoopsTpl->assign('lang_teamtertiary', _AM_TEAMTERTIARY);
   $xoopsTpl->assign('lang_teamfirstpos', _AM_TEAMFIRSTPOS);
   $xoopsTpl->assign('lang_teamsecondpos', _AM_TEAMSECONDPOS);
   $xoopsTpl->assign('lang_teamthirdpos', _AM_TEAMTHIRDPOS);
}
else {
	redirect_header("index.php",3,_AM_TEAMSORRYRESTRICTEDAREA);
}
include(XOOPS_ROOT_PATH."/footer.php");
?>
<?php
include '../../mainfile.php';

include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : getDefaultTeam();
$team_handler =& xoops_getmodulehandler('team');
$team =& $team_handler->get($teamid);
$xoopsOption['template_main'] = 'team_roster.html';
include XOOPS_ROOT_PATH.'/header.php';
if ($xoopsUser) {
    $uid = $xoopsUser->getVar("uid");
    if ($team->isTeamAdmin($uid)) {
        $xoopsTpl->assign('admin', 'Yes');
    }
    if ($team->isTeamMember($uid)) {
        $xoopsTpl->assign('teammember', 'Yes');
    }
}
$players = $team->getAllMembers();
$count = 0;
$ranks = getAllRanks();
$statuses = getAllStatus();
$positions = getAllShort();
$layout = getLayout();
foreach ($players as $key => $player) {
    $teamplayer[$key]["uname"] = $player["uname"];
    $teamplayer[$key]["user_from"] = $player["user_from"];
    $teamplayer[$key]["uid"]=$player["uid"];
    $teamplayer[$key]["status"]=$statuses[$player["status"]];
    $teamplayer[$key]["bio"] = $player["bio"];
    $teamplayer[$key]["rankcolor"] = $ranks[$player["rank"]]["color"];
    $teamplayer[$key]["rank"] = $ranks[$player["rank"]]["rank"];
    $avatarpath = XOOPS_URL.'/uploads/'.$player["user_avatar"];
    $teamplayer[$key]["avatar"] = $player["user_avatar"];
    if ($statuses[$player["status"]]=='Active') {
        $teamplayer[$key]["statuscolour"] = $layout["color_status_active"];
        $count++;
    }
    elseif ($statuses[$player["status"]]=='Inactive') {
        $teamplayer[$key]["statuscolour"] = $layout["color_status_inactive"];
    }
    elseif ($statuses[$player["status"]]=='On Leave') {
        $teamplayer[$key]["statuscolour"] = $layout["color_status_onleave"];
    }
    $teamplayer[$key]["JoinedDate"] = date (_SHORTDATESTRING , $player["user_regdate"]);
    if ((isset($class))&&($class=="even")) {
        $teamplayer[$key]["class"] = "odd";
    }
    else {
        $teamplayer[$key]["class"] = "even";
    }
    if (isset($player["primarypos"])) {
        $playerpositions = $positions[$player["primarypos"]].", ".$positions[$player["secondarypos"]];
        if (isset($positions[$player["tertiarypos"]]) && ($positions[$player["tertiarypos"]]!='-None-')) {
            $playerpositions .= ", ".$positions[$player["tertiarypos"]];
        }
        $teamplayer[$key]["positions"] = $playerpositions;
    }
    if (($player["user_from"]!=NULL)&&(file_exists('images/flags/'.$player["user_from"].'.gif'))) {
        $teamplayer[$key]["flag"] = 'Yes';
    }
    elseif ($player["user_from"]!=NULL) {
        $teamplayer[$key]["flag"] = 'No';
    }
}
$team->select();
$xoopsTpl->assign('goodcolor', $layout["color_perfect"]);
$xoopsTpl->assign('XOOPS_URL', XOOPS_URL);
$xoopsTpl->assign('players', $teamplayer);
$xoopsTpl->assign('actives', $count);
$xoopsTpl->assign('count', count($players));
$xoopsTpl->assign('teamid', $teamid);
$xoopsTpl->assign('teamname', $team->getVar('teamname'));
$xoopsTpl->assign('teamtype', $team->getVar('teamtype'));
$xoopsTpl->assign('lang_teamrosterfor', _AM_TEAMROSTERFOR);
$xoopsTpl->assign('lang_teamplaying', _AM_TEAMPLAYING);
$xoopsTpl->assign('lang_teamadmin', _AM_TEAMADMIN);
$xoopsTpl->assign('lang_teamposoverview', _AM_TEAMPOSOVERVIEW);
$xoopsTpl->assign('lang_teammypos', _AM_TEAMMYPOS);
$xoopsTpl->assign('lang_teamavailstats2', _AM_TEAMAVAILSTATS2);
$xoopsTpl->assign('lang_teamnickname', _AM_TEAMNICKNAME);
$xoopsTpl->assign('lang_teamnationality', _AM_TEAMNATIONALITY);
$xoopsTpl->assign('lang_teamrank', _AM_TEAMRANK);
$xoopsTpl->assign('lang_teamposition', _AM_TEAMPOSITION);
$xoopsTpl->assign('lang_teammembersince', _AM_TEAMMEMBERSINCE);
$xoopsTpl->assign('lang_teamstatus', _AM_TEAMSTATUS);
$xoopsTpl->assign('lang_teamtotalmembers', _AM_TEAMTOTALMEMBERS);
$xoopsTpl->assign('lang_teamactiveplayers', _AM_TEAMACTIVEPLAYERS);
$xoopsTpl->assign('teamversion', round($xoopsModule->getVar("version")/100, 2));
include_once(XOOPS_ROOT_PATH."/footer.php");
?>

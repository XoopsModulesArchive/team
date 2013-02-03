<?php
include '../../mainfile.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : null;
if (isset($_POST)) {
	foreach ($_POST as $k => $v) {
		${$k} = $v;
	}
}

if ($xoopsUser) {
    $team_handler =& xoops_getmodulehandler('team');
    $uid = $xoopsUser->getVar("uid");
    if (!isset($teamid)) {
        redirect_header("roster.php", 3, _AM_NOTEAMSELECTED);
    }
    else {
        $team =& $team_handler->get($teamid);
    }
    if (($team->isTeamAdmin($uid))||($xoopsUser->isAdmin($xoopsModule->mid()))) {
        switch ($op) {
            case "update":
            include XOOPS_ROOT_PATH.'/header.php';
            $rankerrors = 0;
            $statuserrors = 0;
            foreach ($user as $uid => $thisuser) {
                $uid = intval($uid);
                $rank = intval($thisuser["rank"]);
                $oldrank = $thisuser["oldrank"];
                $status = intval($thisuser["status"]);
                $oldstatus = $thisuser["oldstatus"];
                if ($status != $oldstatus) {
                    $sql = "UPDATE ".$xoopsDB->prefix("team_teamstatus")." SET status=$status WHERE uid=$uid AND teamid=$teamid";
                    if ($xoopsDB->query($sql)) {
                        if (!$xoopsDB->getAffectedRows()) {
                            $statuserrors++;
                        }
                    }
                    else {
                        redirect_header("memberadmin.php?teamid=".$teamid, 2, _AM_TEAMUSERSTATUSNOTUPDATED);
                    }
                }
                if ($rank != $oldrank) {
                    $sql = "UPDATE ".$xoopsDB->prefix("team_teamstatus")." SET rank=$rank WHERE uid=$uid AND teamid=$teamid";
                    if ($xoopsDB->query($sql)) {
                        if (!$xoopsDB->getAffectedRows()) {
                            $rankerrors++;
                        }
                    }
                    else {
                        redirect_header("memberadmin.php?teamid=".$teamid, 2, _AM_TEAMERRORUSERNOTUPDATED);
                    }
                }
            }
            if (($statuserrors > 0 ) || ($rankerrors > 0)) {
                redirect_header("memberadmin.php?teamid=".$teamid, 2, $statuserrors." "._AM_TEAMSTATUSERRORS."<br />".$rankerrors." "._AM_TEAMRANKERRORS);
            }
            else {
                redirect_header("memberadmin.php?teamid=".$teamid, 2, _AM_TEAMUSERRANKUPDATED."<br />"._AM_TEAMUSERSTATUSUPDATED);
            }
                 break;

           default:
            include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
            $xoopsOption['template_main'] = 'team_teamadmin.html';
            include XOOPS_ROOT_PATH.'/header.php';
            $players = $team->getAllMembers();
            $ranks = $team->getRanks();
            foreach ($ranks as $rankid => $rank) {
            	$allranks[$rankid] = $rank["rank"];
            }
            $statuses = getAllStatus();
            $count = 0;
            $layout = getLayout();
            foreach ($players as $key => $player) {
                $teamplayer[$key]["uname"] = $player["uname"];
                $teamplayer[$key]["uid"]=$player["uid"];
                $teamplayer[$key]["status"]=$statuses[$player["status"]];
                $teamplayer[$key]["statusid"] = $player["status"];
                $teamplayer[$key]["rankcolor"] = $ranks[$player["rank"]]["color"];
                $teamplayer[$key]["rank"] = $ranks[$player["rank"]]["rank"];
                $teamplayer[$key]["rankid"] = $player["rank"];
                if ($statuses[$player["status"]]=='Active') {
                    $teamplayer[$key]["statuscolour"] = "00AA00";
                    $count++;
                }
                elseif ($statuses[$player["status"]]=='Inactive') {
                    $teamplayer[$key]["statuscolour"] = "006600";
                }
                elseif ($statuses[$player["status"]]=='On Leave') {
                    $teamplayer[$key]["statuscolour"] = "bbbb10";
                }
                if ((isset($class))&&($class=="even")) {
                    $teamplayer[$key]["class"] = "odd";
                    $class = "odd";
                }
                else {
                    $teamplayer[$key]["class"] = "even";
                    $class = "even";
                }
            }
            $team->select();
            $xoopsTpl->assign('XOOPS_URL', XOOPS_URL);
            $xoopsTpl->assign('activecolor', $layout['color_perfect']);
            $xoopsTpl->assign('teammembers', $teamplayer);
            $xoopsTpl->assign('activecount', $count);
            $xoopsTpl->assign('totalcount', count($players));
            $xoopsTpl->assign('teamid', $teamid);
            $xoopsTpl->assign('teamname', $team->getVar('teamname'));
            $xoopsTpl->assign('teamtype', $team->getVar('teamtype'));
            $xoopsTpl->assign('allstatus', $statuses);
            $xoopsTpl->assign('allranks', $allranks);
            $xoopsTpl->assign('lang_administrationof', _AM_TEAMADMINISTRATIONOF);
            $xoopsTpl->assign('lang_teamplaying', _AM_TEAMPLAYING);
            $xoopsTpl->assign('lang_teamroster', _AM_TEAMROSTER);
            $xoopsTpl->assign('lang_posoverview', _AM_TEAMPOSOVERVIEW);
            $xoopsTpl->assign('lang_teammypos', _AM_TEAMMYPOS);
            $xoopsTpl->assign('lang_teamavailstats2', _AM_TEAMAVAILSTATS2);
            $xoopsTpl->assign('lang_teamnickname', _AM_TEAMNICKNAME);
            $xoopsTpl->assign('lang_teamrank', _AM_TEAMRANK);
            $xoopsTpl->assign('lang_teamstatus', _AM_TEAMSTATUS);
            $xoopsTpl->assign('lang_teamtotalmembers', _AM_TEAMTOTALMEMBERS);
            $xoopsTpl->assign('lang_teamactiveplayers', _AM_TEAMACTIVEPLAYERS);
            break;
          }
    }
    else {
        redirect_header("roster.php", 3, _AM_TEAMNOACCESSTOTHISTEAM);
    }
}
else {
    redirect_header("index.php", 3, _AM_TEAMNOTLOGGEDIN);
}
include_once(XOOPS_ROOT_PATH."/footer.php");
?>

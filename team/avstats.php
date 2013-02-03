<?php
include("../../mainfile.php");
include("../../header.php");
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : null;
if ($xoopsUser) {
    if (!isset($teamid)) {
        $teamid = getDefaultTeam();
    }
    include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
    $xoopsOption['template_main'] = 'team_avstats.html';
    $team_handler =& xoops_getmodulehandler('team');
    $team =& $team_handler->get($teamid);
    if (!$team->isTeamMember($xoopsUser->getVar("uid"))) {
        redirect_header('roster.php?teamid='.$teamid, 3, _AM_TEAMACCESSDENY);
        exit();
    }
    if (isset($limit)) {
        $limitstr = "LIMIT 0, ".$limit;
    }
    else {
        $limitstr = "";
    }
    $statuses = getAllStatus();
    $members = $team->getAllMembers();
    $player = array();
    $thislayout = getLayout();
    foreach ($members as $key => $member) {
        $status = $statuses[$member["status"]];
        if ($status=='Active') {
				$player[$key]["statuscolour"]=$thislayout["color_perfect"];
		}
		elseif ($status=='Inactive') {
				$player[$key]["statuscolour"]=$thislayout["color_warn"];
		}
		elseif ($status=='On Leave') {
				$player[$key]["statuscolour"]=$thislayout["color_good"];
        }
        else {
            $player[$key]["statuscolour"]=$thislayout["color_bad"];
        }
		$player[$key]["uname"]=$member["uname"];
		$player[$key]["uid"]=$member["uid"];
		$av=0;
  		$avperc=0;
		$noreply=0;
		$nav=0;
		$sub=0;
		$count=0;
		$total=0;
        $sql = "SELECT a.availability FROM ".$xoopsDB->prefix("team_availability")." a, ".$xoopsDB->prefix("team_matches")." m WHERE m.teamid=".$teamid." AND a.matchid=m.matchid AND a.userid=".$member["uid"]." AND m.matchresult<>'Pending' ORDER BY a.matchid DESC ".$limitstr;
		$result = $xoopsDB->query($sql);
		while ($myrow = $xoopsDB->fetchArray($result)) {
			$total++;
			$myav=$myrow["availability"];
			if (($myav=="Yes") OR ($myav=="LateYes")) {
				$av++;
			}
			elseif ($myav=="Not Set") {
				$noreply++;
			}
			elseif (($myav=="No") OR ($myav=="LateNo")) {
				$nav++;
			}
			elseif($myav=="Sub") {
				$sub++;
			}
		}
		if ($total!=0) {
			$avperc=$av/$total*100;
			$avperc=number_format($avperc,2);
			if ($avperc>66) {
				$avcolor=$thislayout["color_perfect"];
			}
			elseif ($avperc>50) {
				$avcolor=$thislayout["color_good"];
			}
			elseif ($avperc>33) {
				$avcolor=$thislayout["color_warn"];
			}
			else {
				$avcolor=$thislayout["color_bad"];
			}
			$noreply=$noreply/$total*100;
			$noreply=number_format($noreply,2);
			if ($noreply>75) {
				$noreplycolor=$thislayout["color_bad"];
			}
			elseif ($noreply>50) {
				$noreplycolor=$thislayout["color_warn"];
			}
			elseif ($noreply>25) {
				$noreplycolor=$thislayout["color_good"];
			}
			else {
				$noreplycolor=$thislayout["color_perfect"];
			}
			$nav=$nav/$total*100;
			$nav=number_format($nav,2);
			if ($nav>50) {
				$navcolor=$thislayout["color_bad"];
			}
			elseif ($nav>33) {
				$navcolor=$thislayout["color_warn"];
			}
			elseif ($nav>20) {
				$navcolor=$thislayout["color_good"];
			}
			else {
				$navcolor=$thislayout["color_perfect"];
			}
			$sub=$sub/$total*100;
			$sub=number_format($sub,2);
			if ($sub>66) {
				$subcolor=$thislayout["color_good"];
			}
			elseif ($sub>33) {
				$subcolor=$thislayout["color_warn"];
			}
			else {
				$subcolor=$thislayout["color_perfect"];
			}
		}
        if ((isset($class))&&($class=="even")) {
            $class = "odd";
        }
        else {
            $class = "even";
        }
        $player[$key]["class"] = $class;
        $player[$key]["status"] = $status;
        $player[$key]["av"] = $av;
        $player[$key]["avcolor"] = $avcolor;
        $player[$key]["avperc"] = $avperc."%";
        $player[$key]["nav"] = $nav."%";
        $player[$key]["navcolor"] = $navcolor;
        $player[$key]["sub"] = $sub."%";
        $player[$key]["subcolor"] = $subcolor;
        $player[$key]["noreply"] = $noreply."%";
        $player[$key]["noreplycolor"] = $noreplycolor;
        $player[$key]["total"] = $total;
    }
    $xoopsTpl->assign('players', $player);
    $xoopsTpl->assign('teamname', $team->getVar('teamname'));
    $team->select();
    if (($xoopsUser->isAdmin($xoopsModule->mid()))||($team->isTeamAdmin($uid))) {
        $xoopsTpl->assign('admin', 'Yes');
    }
    $xoopsTpl->assign('teamid', $teamid);
    $xoopsTpl->assign('teamname', $team->getVar('teamname'));
    $xoopsTpl->assign('lang_teamroster', _AM_TEAMROSTER);
    $xoopsTpl->assign('lang_teamadmin', _AM_TEAMADMIN);
    $xoopsTpl->assign('lang_teamavailstats', _AM_TEAMAVAILSTATS);
    $xoopsTpl->assign('lang_teamplaying', _AM_TEAMPLAYING);
    $xoopsTpl->assign('lang_teamposoverview', _AM_TEAMPOSOVERVIEW);
    $xoopsTpl->assign('lang_teammypos', _AM_TEAMMYPOS);
    $xoopsTpl->assign('lang_teamnickname', _AM_TEAMNICKNAME);
    $xoopsTpl->assign('lang_teamstatus', _AM_TEAMSTATUS);
    $xoopsTpl->assign('lang_teammatches', _AM_TEAMMATCHES);
    $xoopsTpl->assign('lang_teamavailable', _AM_TEAMAVAILABLE);
    $xoopsTpl->assign('lang_teamnotavailable', _AM_TEAMNOTAVAILABLE);
    $xoopsTpl->assign('lang_teamsub', _AM_TEAMSUB);
    $xoopsTpl->assign('lang_teamnoreply', _AM_TEAMNOREPLY);
    if (isset($limit)) {
        if ($limit==20) {
            $xoopsTpl->assign('link1', '?limit=10');
            $xoopsTpl->assign('link1txt', _AM_TEAMLAST10MATCHES);
            $xoopsTpl->assign('link2', '');
            $xoopsTpl->assign('link2txt', _AM_TEAMALLMATCHES);
        }
        elseif ($limit==10) {
            $xoopsTpl->assign('link2', '?limit=20');
            $xoopsTpl->assign('link2txt', _AM_TEAMLAST20MATCHES);
            $xoopsTpl->assign('link1', '');
            $xoopsTpl->assign('link1txt', _AM_TEAMALLMATCHES);
        }

    }
    else {
        $xoopsTpl->assign('link1', '?limit=10');
        $xoopsTpl->assign('link1txt', _AM_TEAMLAST10MATCHES);
        $xoopsTpl->assign('link2', '?limit=20');
        $xoopsTpl->assign('link2txt', _AM_TEAMLAST20MATCHES);
    }
}
include(XOOPS_ROOT_PATH."/footer.php");
?>
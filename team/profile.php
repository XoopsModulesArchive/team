<?php
include '../../mainfile.php';
include XOOPS_ROOT_PATH.'/header.php';
include "functions.php";
include XOOPS_ROOT_PATH."/language/".$xoopsConfig['language']."/user.php";
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/player.php';

$xoopsOption['template_main'] = 'team_userprofile.html';
$uid = intval($_GET['uid']);
if ($uid <= 0) {
	redirect_header('index.php', 3, _AM_TEAM_SELECTNG);
	exit();
}
if (is_object($xoopsUser)) {
	if ($uid == $xoopsUser->getVar('uid')) {
		$xoopsTpl->assign('user_ownpage', true);
		$xoopsTpl->assign('user_candelete', false);
		$thisUser =& $xoopsUser;
	} else {
		$member_handler =& xoops_gethandler('member');
		$thisUser =& $member_handler->getUser($uid);
		if (!is_object($thisUser) || !$thisUser->isActive() ) {
			redirect_header("index.php",3,_AM_TEAM_SELECTNG);
			exit();
		}
		$xoopsTpl->assign('user_ownpage', false);
	}
} else {
	$member_handler =& xoops_gethandler('member');
	$thisUser =& $member_handler->getUser($uid);
	if (!is_object($thisUser) || !$thisUser->isActive()) {
		redirect_header("index.php",3,_AM_TEAM_SELECTNG);
		exit();
	}
	$xoopsTpl->assign('user_ownpage', false);
}
$myts =& MyTextSanitizer::getInstance();
if (is_object($xoopsUser) && $xoopsUser->isAdmin()) {
	$xoopsTpl->assign('user_uid', $thisUser->getVar('uid'));
}
$xoopsTpl->assign('lang_allaboutuser', sprintf(_AM_TEAM_ALLABOUT,$thisUser->getVar('uname')));
$xoopsTpl->assign('user_avatarurl', XOOPS_URL.'/uploads/'.$thisUser->getVar('user_avatar'));
$xoopsTpl->assign('user_realname', $thisUser->getVar('name'));
$xoopsTpl->assign('user_websiteurl', '<a href="'.$thisUser->getVar('url', 'E').'" target="_blank">'.$thisUser->getVar('url').'</a>');
$xoopsTpl->assign('user_icq', $thisUser->getVar('user_icq'));
$xoopsTpl->assign('user_aim', $thisUser->getVar('user_aim'));
$xoopsTpl->assign('user_yim', $thisUser->getVar('user_yim'));
$xoopsTpl->assign('user_msnm', $thisUser->getVar('user_msnm'));
$xoopsTpl->assign('user_location', $thisUser->getVar('user_from'));
$xoopsTpl->assign('user_occupation', $thisUser->getVar('user_occ'));
$xoopsTpl->assign('user_interest', $thisUser->getVar('user_intrest'));
$xoopsTpl->assign('user_extrainfo', $myts->makeTareaData4Show($thisUser->getVar('bio', 'N'),0,1,1));
$xoopsTpl->assign('user_joindate', formatTimestamp($thisUser->getVar('user_regdate'),'s'));
$xoopsTpl->assign('user_posts', $thisUser->getVar('posts'));
$xoopsTpl->assign('user_signature', $myts->makeTareaData4Show($thisUser->getVar('user_sig', 'N'),0,1,1));

if ($thisUser->getVar('user_viewemail') == 1) {
	$xoopsTpl->assign('user_email', $thisUser->getVar('email', 'E'));
} else {
	if (is_object($xoopsUser)) {
		if ($xoopsUser->isAdmin() || ($xoopsUser->getVar("uid") == $thisUser->getVar("uid"))) {
			$xoopsTpl->assign('user_email', $thisUser->getVar('email', 'E'));
		} else {
			$xoopsTpl->assign('user_email', '&nbsp;');
		}
	}
}
if (is_object($xoopsUser)) {
	$xoopsTpl->assign('user_pmlink', "<a href=\"javascript:openWithSelfMain('".XOOPS_URL."/pmlite.php?send2=1&amp;to_userid=".$thisUser->getVar('uid')."', 'pmlite', 450, 380);\"><img src=\"".XOOPS_URL."/images/icons/pm.gif\" alt=\"".sprintf(_SENDPMTO,$thisUser->getVar('uname'))."\" /></a>");
} else {
	$xoopsTpl->assign('user_pmlink', '');
}
$userrank =& $thisUser->rank();
if ($userrank['image']) {
	$xoopsTpl->assign('user_rankimage', '<img src="'.XOOPS_URL.'/uploads/'.$userrank['image'].'" alt="" />');
}
$xoopsTpl->assign('user_ranktitle', $userrank['title']);
$date = $thisUser->getVar("last_login");
if (!empty($date)) {
	$xoopsTpl->assign('user_lastlogin', formatTimestamp($date,"m"));
}
$thisPlayer = new Player($thisUser->getVar("uid"));
$playerteams = $thisPlayer->getTeams();
$skills = array();
$team_handler =& xoops_getmodulehandler('team');
foreach ($playerteams as $statusid => $teamid) {
    $team =& $team_handler->get($teamid);
    $sql = "SELECT primarypos, secondarypos, tertiarypos FROM ".$xoopsDB->prefix("team_teamstatus")." WHERE uid=".$thisUser->getVar("uid")." AND teamid=".$teamid;
    $sqlresult = $xoopsDB->query($sql);
    $myteamstatus = $xoopsDB->fetchArray($sqlresult);
    $primary = getPosName($myteamstatus["primarypos"]);
    $secondary = getPosName($myteamstatus["secondarypos"]);
    $tertiary = getPosName($myteamstatus["tertiarypos"]);
    $sql = "SELECT posid FROM ".$xoopsDB->prefix("team_skills")." WHERE uid=".$thisUser->getVar("uid")." AND teamid=".$teamid;
    $result = $xoopsDB->query($sql);
    $k = 0;
    while ($myteamskills = $xoopsDB->fetchArray($result)) {
        $skills[$k]["skillname"] = getPosName($myteamskills["posid"]);
        $k++;
    }
    $teamrank = $thisPlayer->getRank($teamid);
    $teamrank = $teamrank["rank"];
    $xoopsTpl->append('thisteam', array('name' => $team->getVar('teamname'), 'teamrank' => $teamrank, 'teamstatus' => getPlayerStatus($statusid), 'primary' => $primary, 'secondary' => $secondary, 'tertiary' => $tertiary, 'skills' => $skills));
}
$xoopsTpl->assign('lang_profileforuser', _AM_TEAMPROFILEFOR." ".$thisUser->getVar("uname"));

if ($xoopsUser) {
      $teams = getTeams();
      $teammember = 0;
      foreach ($teams as $teamid => $teamname) {
          if ($team->isTeamMember($xoopsUser->getVar("uid"))) {
              $teammember = 1;
              $xoopsTpl->assign('teammember', true);
          }
      }
      if ($teammember==1) {
          $match[0] = $thisPlayer->getAvailabilities(1);
          $match[1] = $thisPlayer->getAvailabilities("");
          $match_handler =& xoops_getmodulehandler('match');
          foreach ($match as $matches) {

              if (count($matches)>0) {
                  foreach ($matches as $matchid => $availability) {
                      if ((isset($class))&&($class=="even")) {
                          $class = "odd";
                      }
                      else {
                          $class = "even";
                      }
                      $match =& $match_handler->get($matchid);
                      $matchdate = date('j/n-y', $match->getVar('matchdate'));
                      $teamname = $teams[$match->getVar('teamid')];
                      $match_array = array('matchid' => $matchid,
                                            'date' => $matchdate,
                                            'teamname' => $teamname,
                                            'opponent' => $match->getVar('opponent'),
                                            'size' => $match->getVar('teamsize'),
                                            'type' => $match->getVar('ladder'),
                                            'result' => $match->getVar('matchresult'),
                                            'availability' => $availability,
                                            'class' => $class);
                      if ($match->getVar('matchresult') == 'Pending') {
                          $xoopsTpl->assign('newmatches', true);
                          $xoopsTpl->append('thismatch', $match_array);
                      }
                      else {
                          $xoopsTpl->assign('prevmatches', true);
                          $xoopsTpl->append('prevmatch', $match_array);
                      }
                  }
              }
          }
      }
}
include_once(XOOPS_ROOT_PATH."/footer.php");
?>

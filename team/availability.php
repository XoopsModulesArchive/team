<?php
include '../../mainfile.php';
include XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/teammatch.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/team.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/teammap.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : null;
if (isset($_POST)) {
	foreach ($_POST as $k => $v) {
		${$k} = $v;
	}
}
function set($uid, $mid, $av) {
    global $xoopsDB;
    $uid = intval($uid);
    $mid = intval($mid);
       $sql = "INSERT INTO ".$xoopsDB->prefix("team_availability")." (userid, availability, matchid) VALUES ('$uid', ".$xoopsDB->quoteString($av).", '$mid')";
       $xoopsDB->query($sql);
       if ($xoopsDB->getInsertId()) {
           $notification_handler =& xoops_gethandler('notification');
           if (($av == "Yes")&&(!$notification_handler->isSubscribed ('match', $mid, 'new_lineup'))) {
               $notification_handler->subscribe ('match', $mid, 'new_lineup');
           }
           return true;
       }
       else {
           return false;
       }
}
function change($availid, $av, $mid) {
    global $xoopsDB;
    $availid = intval($availid);
    $mid = intval($mid);
    $sql = "UPDATE ".$xoopsDB->prefix("team_availability")." SET availability=".$xoopsDB->quoteString($av)." WHERE avid='$availid'";
    $xoopsDB->query($sql);
    if ($xoopsDB->getAffectedRows()>0) {
        $notification_handler =& xoops_gethandler('notification');
        if (($av == "Yes")&&(!$notification_handler->isSubscribed ('match', $mid, 'new_lineup'))) {
            $notification_handler->subscribe ('match', $mid, 'new_lineup');
        }
        return true;
    }
    else {
        return false;
    }
}
function comment($val1, $comm, $val2="") {
    if (!$val2) {
        $avid = intval($val1);
    }
    else {
        $userid = intval($val1);
        $matchid = intval($val2);
    }
    echo "<table width='100%' border='0' cellpadding='4' cellspacing='1'><tr class='head'><td>";
    echo "<form method='post' action='availability.php'>";
    echo "<input type='hidden' name='op' value='setcomment'>";
    echo "<input type='hidden' name='matchid' value=".$matchid.">";
    echo "<input type='hidden' name='userid' value=".$userid.">";
    echo "<input type='hidden' name='avid' value=".$avid.">";
    echo ""._AM_TEAMSUBREASSHORTEXPL." </td><td><input type='text' name='newcomment' value='".$comm."' size=20>";
    echo "</td><td><input type=submit value='Submit'></form></td></tr></table>";
}
function setcomment($mid, $uid, $aid, $comment) {
    global $xoopsDB;
    $mid = intval($mid);
    $uid = intval($uid);
    if ($aid) {
        $condition = "WHERE avid='".intval($aid)."'";
    }
    else {
        $condition = "WHERE matchid='$mid' AND userid='$uid'";
    }
    $sql = "UPDATE ".$xoopsDB->prefix("team_availability")." SET comment=".$xoopsDB->quoteString($comment)." ".$condition;
    $xoopsDB->query($sql);
}
if ($xoopsUser) {
   switch ($op) {
    case "reset":
		if ( !empty($ok) ) {
            if (empty($matchid)) {
                redirect_header('index.php',2,_AM_EMPTYNORESET);
                break;
            }
            $matchid = intval($matchid);
            $sql = "UPDATE ".$xoopsDB->prefix("team_availability")." SET availability='Not Set' WHERE matchid='$matchid'";
            $xoopsDB->query($sql);
            $rows = $xoopsDB->getAffectedRows();
            redirect_header("availability.php?mid=".$matchid, 3, $rows." "._AM_AVSRESAT);
       }
       else {
			xoops_confirm(array('op' => 'reset', 'matchid' => $matchid, 'ok' => 1), 'availability.php', _AM_RUSURERESET);
       }
       break;
    case "lock":
        $thismatch = new Match($matchid);
        if ($alock == 1) {
            $thismatch->lock();
        }
        else {
            $thismatch->unlock();
        }
        break;

    case "setcomment":
         $avid = intval($avid);
         setcomment($matchid, $userid, $avid, $newcomment);
         if (!$matchid) {
         	  $sql = "SELECT matchid FROM ".$xoopsDB->prefix("team_availability")." WHERE avid='$avid' ";
              $result = $xoopsDB->query($sql);
              while ( $myrow = $xoopsDB->fetchArray($result) ) {
                  $mid = $myrow["matchid"];
              }
         }
         redirect_header("availability.php?mid=".$matchid,3,_AM_TEAMAVAILABILITYSET);
         break;

    case "set":
        if (set($userid, $matchid, $availability)) {
            if ($availability=='Sub') {
                comment($userid, $comment, $matchid);
            }
            else {
                redirect_header("availability.php?mid=".$matchid,3,_AM_TEAMAVAILABILITYSET);
            }
        }
        else {
            redirect_header("availability.php?mid=".$matchid, 3, _AM_DBNOTUPDATED);
        }
        break;

    case "AvailOverride":
        global $xoopsDB;
        $sql = "UPDATE ".$xoopsDB->prefix("team_availability")." SET availability=".$xoopsDB->quoteString($availability)." WHERE userid=".intval($uid)." AND matchid=".intval($matchid);
        $result = $xoopsDB->query($sql);
        if ($xoopsDB->getAffectedRows($result)<1) {
            redirect_header("availability.php?mid=".$matchid,3,_AM_TEAMERRORUSERNOTUPDATED);
        }
        else {
            redirect_header("availability.php?mid=".$matchid,3,_AM_TEAMAVAILABILITYMODIFIED);
        }
        break;

    case "change":
     if (change($avid, $availability, $matchid)) {
         if ($availability=='Sub') {
             comment($avid, $comment);
         }
         else {
             redirect_header("availability.php?mid=".$matchid,3,_AM_TEAMAVAILABILITYMODIFIED);
         }
     }
     else {
         redirect_header("availability.php?mid=".$matchid, 3, _AM_DBNOTUPDATED);
     }
     break;

    case "default":
    default:
    if ($mid) {
         include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
         $xoopsOption['template_main'] = 'team_availability.html';
         $thislayout = getLayout();
         $xoopsTpl->assign('layout', array('perfect' => $thislayout["color_perfect"], 
                                            'good' => $thislayout["color_good"], 
                                            'warn' => $thislayout["color_warn"], 
                                            'bad' => $thislayout["color_bad"]));
         $mymatch = new Match($mid);
         $teamid = $mymatch->teamid();
         $myteam = new Team($teamid);
         if ((!$myteam->isTeamMember($xoopsUser->getVar("uid"))) && (!$xoopsUser->isAdmin($xoopsModule->mid()))) {
             redirect_header("index.php?teamid=".$teamid, 3, _AM_TEAMACCESSDENY);
             break;
         }
         $mdate = $mymatch->matchdate();
         $time = date("H:i", $mdate);
         if ($time!='21:00') {
             $xoopsTpl->assign('msize',3);
         }
         else {
             $xoopsTpl->assign('msize',2);
         }
         $maps = $mymatch->getMatchMaps();
         foreach ($maps as $mapno => $matchmapid) {
             $thismap = new MatchMap($matchmapid);
             $map[$mapno]["name"] = $thismap->mapname();
             $map[$mapno]["caption"] = getCaption($mapno);
         }
         $firstday = date( 'w', $mdate);
         if ($firstday==1) {
             $weekday=_AM_TEAMMONDAY;
         }
         elseif ($firstday==2) {
             $weekday=_AM_TEAMTUESDAY;
         }
         elseif ($firstday==3) {
             $weekday=_AM_TEAMWEDNESDAY;
         }
         elseif ($firstday==4) {
             $weekday=_AM_TEAMTHURSDAY;
         }
         elseif ($firstday==5) {
             $weekday=_AM_TEAMFRIDAY;
         }
         elseif ($firstday==6) {
             $weekday=_AM_TEAMSATURDAY;
         }
         else {
             $weekday=_AM_TEAMSUNDAY;
         }
         $lock=$mymatch->alock();
         if ($mymatch->matchresult()=='Pending') {
             $pending=1;
             $xoopsTpl->assign('pending', 1);
         }
         $yes=0;
         $no=0;
         $notsure=0;
         $noreply=0;
         $lateyes=0;
         $lateno=0;
         $avid = array();
         $navid = array();
         $subid = array();
         $notavailable = array();
         $available = array();
         $subs = array();
         $subcomment = array();
         $lateneg = array();
         $latepos = array();
         $latenegid = array();
         $lateposid = array();
         $result = $mymatch->getAvailabilities();
         while ( $myrow = $xoopsDB->fetchArray($result) ) {
             $comment=$myrow["comment"];
             $uid=$myrow["userid"];
             $myavail=$myrow["availability"];
             $nick=$myrow["uname"];
             if ($myavail=="Yes") {
                 $available[$yes]["name"]=$nick;
                 $available[$yes]["id"]=$uid;
                 $yes++;
             }
             elseif ($myavail=="No") {
                 $notavailable[$no]["name"]=$nick;
                 $notavailable[$no]["id"]=$uid;
                 $no++;
             }
             elseif ($myavail=="Sub") {
                 $subs[$notsure]["name"]=$nick;
                 $subs[$notsure]["id"]=$uid;
                 if (isset($comment)) {
                     $subs[$notsure]["comment"]= "- ".$comment;
                 }
                 $notsure++;
             }
             elseif ($myavail=="Not Set") {
                 $notreplied[$noreply]["name"]=$nick;
                 $notreplied[$noreply]["id"]=$uid;
                 $noreply++;
             }
             elseif ($myavail=="LateYes") {
                 $latepos[$lateyes]["name"]=$nick;
                 $latepos[$lateyes]["id"]=$uid;
                 $lateyes++;
             }
             elseif ($myavail=="LateNo") {
                 $lateneg[$lateno]["name"]=$nick;
                 $lateneg[$lateno]["id"]=$uid;
                 $lateno++;
             }
             if ($uid == $xoopsUser->getVar("uid") ) {
                 $myav = $myrow["availability"];
                 $xoopsTpl->assign('avid', $myrow["avid"]);
                 $xoopsTpl->assign('myav', $myav);
                 $xoopsTpl->assign('comment', $myrow["comment"]);
             }
         }
         $yestotal=$yes+$lateyes;
         $nototal=$no+$lateno;
         if ($yes > $no) {
             $max1 = $yes;
         }
         else {
             $max1 = $no;
         }
         if ($max1 < $notsure) {
             $max1 = $notsure;
         }
         if ($lateyes>$lateno) {
             $max2 = $lateyes;
         }
         else {
             $max2 = $lateno;
         }
         if ($max2 < $noreply) {
             $max2 = $noreply;
         }
         if ($yes < $max1) {
             for ($i=$yes; $i<$max1; $i++) {
                 $available[$i]["name"] = "&nbsp ";
             }
         }
         $i = $no;
         while ($i < $max1) {
             $notavailable[]["name"] = "&nbsp ";
             $i++;
         }
         $i = $notsure;
         while ($i < $max1) {
             $subs[]["name"] = "&nbsp ";
             $i++;
         }
         $i = $lateyes;
         while ($i < $max2) {
             $latepos[]["name"] = "&nbsp ";
             $i++;
         }
         $i = $lateno;
         while ($i < $max2) {
             $lateneg[]["name"] = "&nbsp ";
             $i++;
         }
         $i = $noreply;
         while ($i < $max2) {
             $notreplied[]["name"] = "&nbsp ";
             $i++;
         }
         //Availability Setup for teammembers only
         if ($myteam->isTeamMember($xoopsUser->getVar("uid"))) {
             $uid = $xoopsUser->getVar("uid");
             $uname = $xoopsUser->getVar("uname");
             $action = "set";
             if (isset($pending) && ($pending==1)) {
                 if (isset($myav)) {
                     $action = "change";
                     if ($myav=="Yes") {
                         $xoopsTpl->assign('avcheck', "selected");
                         $xoopsTpl->assign('navcheck', "");
                         $xoopsTpl->assign('subcheck', "");
                         $xoopsTpl->assign('greeting', _AM_TEAMHELLO." ".$uname.", "._AM_TEAMYOUSETAVAIL);
                     }
                     elseif ($myav=="No") {
                         $xoopsTpl->assign('avcheck', "");
                         $xoopsTpl->assign('navcheck', "selected");
                         $xoopsTpl->assign('subcheck', "");
                         $xoopsTpl->assign('greeting', _AM_TEAMHELLO." ".$uname.", "._AM_TEAMYOUSETNOTAVAIL);
                     }
                     elseif ($myav=="Sub") {
                         $xoopsTpl->assign('avcheck', "");
                         $xoopsTpl->assign('navcheck', "");
                         $xoopsTpl->assign('subcheck', "selected");
                         $xoopsTpl->assign('greeting', _AM_TEAMHELLO." ".$uname.", "._AM_TEAMYOUSETSUB);
                     }
                     else {
                         $xoopsTpl->assign('greeting', _AM_TEAMHELLO." ".$uname.", "._AM_TEAMYOUHAVENOTSETAVAIL);
                     }
                 }
                 if ($lock==1) {
                     $xoopsTpl->assign('greeting', _AM_TEAMAVAILHASLOCKADMIN);
                 }
                 $xoopsTpl->assign('action', $action);
             }
             else {
                 $xoopsTpl->assign('greeting', _AM_TEAMMATCHPLAYED);
             }
             $xoopsTpl->assign('uname', $uname);
             $xoopsTpl->assign('uid', $uid);
         }
         //Admin options
         if (($myteam->isMatchAdmin($uid))||($myteam->isTacticsAdmin($uid))) {
             $players = $myteam->getActiveMembers();
             $i=0;
             foreach ($players as $playerid => $playername) {
                 $playerarray[$i]["uid"] = $playerid;
                 $playerarray[$i]["uname"] = $playername;
                 $i++;
             }
             $xoopsTpl->assign('players', $playerarray);
             $xoopsTpl->assign('admin', 'Yes');
         }
         $xoopsTpl->assign('map', $map);
         $xoopsTpl->assign('opponent', $mymatch->opponent());
         $xoopsTpl->assign('teamname', $myteam->teamname());
         $xoopsTpl->assign('ladder', $mymatch->ladder());
         $xoopsTpl->assign('teamsize', $mymatch->teamsize());
         $xoopsTpl->assign('available', $available);
         $xoopsTpl->assign('yes', $yes);
         $xoopsTpl->assign('notavailable', $notavailable);
         $xoopsTpl->assign('no', $no);
         $xoopsTpl->assign('subs', $subs);
         $xoopsTpl->assign('notsure', $notsure);
         $xoopsTpl->assign('notreplied', $notreplied);
         $xoopsTpl->assign('noreply', $noreply);
         $xoopsTpl->assign('latepos', $latepos);
         $xoopsTpl->assign('lateyes', $lateyes);
         $xoopsTpl->assign('lateneg', $lateneg);
         $xoopsTpl->assign('lateno', $lateno);
         $xoopsTpl->assign('time', $time);
         $xoopsTpl->assign('mid', $mid);
         $xoopsTpl->assign('teamid', $teamid);
         $xoopsTpl->assign('weekday', $weekday);
         $xoopsTpl->assign('maps', $map);
         $xoopsTpl->assign('lock', $lock);
         $xoopsTpl->assign('day', date(_SHORTDATESTRING, $mdate));
         $xoopsTpl->assign('lang_teammatchlist', _AM_TEAMMATCHLIST);
         $xoopsTpl->assign('lang_teammatchdetails', _AM_TEAMMATCHDETAILS);
         $xoopsTpl->assign('lang_teammatchpositions', _AM_TEAMMATCHPOSITIONS);
         $xoopsTpl->assign('lang_against', _AM_TEAMAGAINST);
         $xoopsTpl->assign('lang_teamat', _AM_TEAMAT);
         $xoopsTpl->assign('lang_teamvs', _AM_TEAMVERSUS);
         $xoopsTpl->assign('lang_teamavailable', _AM_TEAMAVAILABLE);
         $xoopsTpl->assign('lang_teamnotavailable', _AM_TEAMNOTAVAILABLE);
         $xoopsTpl->assign('lang_teamsubs', _AM_TEAMSUBS);
         $xoopsTpl->assign('lang_teamlatepositive', _AM_TEAMLATEPOSITIVE);
         $xoopsTpl->assign('lang_teamlatenegative', _AM_TEAMLATENEGATIVE);
         $xoopsTpl->assign('lang_teamnoreply', _AM_TEAMNOREPLY);
         $xoopsTpl->assign('lang_teamsub', _AM_TEAMSUB);
         $xoopsTpl->assign('lang_teamlockavail', _AM_TEAMLOCKAVAIL);
         $xoopsTpl->assign('lang_teamunlockavail', _AM_TEAMUNLOCKAVAIL);
         $xoopsTpl->assign('lang_teamresetavail', _AM_TEAMRESETAVAIL);
         $xoopsTpl->assign('lang_teamoverride', _AM_TEAMOVERRIDE);
    }
    else {
        redirect_header("index.php",3,_AM_TEAMNOMATCHSELECTED);
    }
    break;
   }
}
else {
	redirect_header("../../index.php",3,_AM_TEAMSORRYRESTRICTEDAREA);
}
include(XOOPS_ROOT_PATH."/footer.php");

?>

<?php
// $Id: index.php,v 1.16 2006/06/09 14:32:47 mithyt2 Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
include '../../mainfile.php';
include XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : null;

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

// show form to upload a screenshot
function screenshotadd($matchmapid) {
    $op = "savescreenshot";
    $action = "Upload";
    $laddername = "";
    $ladderid = "";
    $laddervisible = 1;
    $matchmap_handler = xoops_getmodulehandler('matchmap');
    $thismap = $matchmap_handler->get($matchmapid);
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $pform = new XoopsThemeForm(_AM_ADD." "._AM_SCREENSHOTNAME, "screenshotform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('' ,'');
    $submit = new XoopsFormButton('', 'submit', _AM_UPLOAD, 'submit');
    $matchmapid_hidden = new XoopsFormHidden('matchmapid', $matchmapid);
    $op_hidden = new XoopsFormHidden('op', $op);
    $mid_hidden = new XoopsFormHidden('mid', $thismap->getVar('matchid'));
    $mapname_label = new XoopsFormLabel(_AM_TEAMMAPNAME, (is_object($thismap->map) ? $thismap->map->getVar('mapname') : ""));
    $mapside_label = new XoopsFormLabel(_AM_TEAMSIDENAME, getSide($thismap->getVar('side')));
    $file = new XoopsFormFile(_AM_SCREENSHOTNAME,"screenshot",200000);
    $button_tray->addElement($submit);
    $pform->addElement($mapname_label);
    $pform->addElement($mapside_label);
    $pform->addElement($matchmapid_hidden);
    $pform->addElement($op_hidden);
    $pform->addElement($mid_hidden);
    $pform->addElement($file);
    $pform->addElement($button_tray);
    $pform->setExtra("enctype=\"multipart/form-data\"");
    $pform->display();
}
$team_handler =& xoops_getmodulehandler('team');
$match_handler =& xoops_getmodulehandler('match');
switch ($op) {
    case "matchform":
    if ($xoopsUser) {
        echo "<h4>"._AM_CONFIG."</h4>";
        echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        if (isset($mid)) {
            $mymatch =& $match_handler->get($mid);
            $matchdate = $mymatch->getVar('matchdate');
            $teamid = $mymatch->getVar('teamid');
            $teamsize = $mymatch->getVar('teamsize');
            $opponent = $mymatch->getVar('opponent');
            $ladder = $mymatch->getVar('ladder');
            $matchresult = $mymatch->getVar('matchresult');
            $review = $mymatch->getVar('review');
            $server = $mymatch->getVar('server');
            $customserver = $mymatch->getVar('customserver');
            $showScreenshotLink = ($matchresult == "Pending") ? false : true;
        }
        if (!isset($teamid)) {
            include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
            $mform = new XoopsThemeForm(_AM_TEAMSELECT, "matchform", xoops_getenv('PHP_SELF'));
            $team_select = new XoopsFormSelect('Team', 'teamid', '1');
            $teams = $team_handler->getObjects();
            foreach (array_keys($teams) as $i) {
                $thisteam =& $teams[$i];
                if ($thisteam->isTeamAdmin($xoopsUser->getVar("uid"))) {
                    $team_select->addOption($thisteam->getVar("teamid"), $thisteam->getVar("teamname"));
                }
            }
            $button_tray = new XoopsFormElementTray('' ,'');
            $button_tray->addElement(new XoopsFormButton('', 'select', 'select', 'submit'));
            $op_hidden = new XoopsFormHidden('op', 'matchform');
            $mform->addElement($team_select);
            $mform->addElement($button_tray);
            $mform->addElement($op_hidden);
            $mform->display();
        }
        else {
            $team =& $team_handler->get($teamid);
            if ($team->isMatchAdmin($xoopsUser->getVar("uid"))) {
                $teamsizes = $team->getTeamSizes();
                $teamladders = $team->getLadders();
                include "include/matchform.inc.php";
            }
            else {
                redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
            }
        }
        echo"</td></tr></table>";
    }
    else {
        redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
    }
    break;

    case "screenshotform":
    if ($xoopsUser) {
        // display add screenshotform
        if (isset($_GET['action']) && $_GET['action'] == "add") {
            screenshotadd($_GET['matchmapid']);
        }
        echo "<h4>"._AM_CONFIG."</h4>";
        if (isset($mid)) {
            $mymatch =& $match_handler->get($mid);
            $nummaps = $mymatch->getMapCount();
            $opponent = $mymatch->getVar('opponent');
            include "include/screenshotform.inc.php";
        }
    }
    else {
        redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
    }
    break;

    case "savematch":
    if ($xoopsUser) {
        $team =& $team_handler->get($teamid);
        if (($day)&&($month)) {
            $clock = explode(":", $time);
            $hour = $clock[0];
            $minute = $clock[1];
            $matchdate = mktime($hour,$minute,0,$month,$day,$year);
        }
        $match =& $match_handler->create();
        $match->setVar('uid', $uid);
        $match->setVar('matchdate', $matchdate);
        $match->setVar('teamid', $teamid);
        $match->setVar('created', time());
        $match->setVar('teamsize', $teamsize);
        $match->setVar('opponent', $opponent);
        $match->setVar('ladder', $ladder);
        $match->setVar('review', $review);
        $match->setVar('alock', 0);
        $match->setVar('server', $server);
        $match->setVar('customserver', $customserver);
        if ($match_handler->insert($match)) {
            $matchid = $match->getVar('matchid');
            $error=0;
            $matchmap_handler = xoops_getmodulehandler('matchmap');
            for ($h=0; $h<$team->getVar('maps'); $h++) {
                $thismap = $matchmap_handler->create();
                $thismap->setVar('matchid', $matchid);
                $thismap->setVar('mapid', $map[$h]);
                $thismap->setVar('side', $side[$h]);
                $thismap->setVar('mapno', $h+1);
                if (!$matchmap_handler->insert($thismap)) {
                    $error++;
                }
            }
            $teammembers = $team->getActiveMembers();
            $availability_handler = xoops_getmodulehandler('availability');
            foreach ($teammembers as $member_id => $member_name) {
                $obj = $availability_handler->create();
                $obj->setVar('userid', $member_id);
                $obj->setVar('availability', 'Not Set');
                $obj->setVar('matchid', $matchid);
                if (!$availability_handler->insert($obj)) {
                    $error++;
                }
            }
            if ($error>0) {
                redirect_header('index.php?teamid='.$teamid,3,$error." Insert(s) Failed");
                break;
            }
            else {
                //Notification
                $matchcreator = new XoopsUser($uid);
                $creatorname = $matchcreator->getVar("uname");
                $teamname = $team->getVar('teamname');
                $tags = array();
                $tags['SIZE'] = $teamsize;
                $tags['TEAM_NAME'] = $teamname;
                $tags['OPPONENT'] = $opponent;
                $tags['CREATOR'] = $creatorname;
                $tags['MATCHDATE'] = date(_SHORTDATESTRING, $matchdate);
                $tags['MATCHTIME'] = date('H:i', $matchdate);
                $tags['DETAILS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/matchdetails.php?mid='.$matchid;
                $notification_handler =& xoops_gethandler('notification');
                $notification_handler->triggerEvent('team', $teamid, 'new_match', $tags);
                redirect_header('index.php?teamid='.$teamid,3,_AM_DBUPDATED);
                break;
            }
        }
        else {
            redirect_header('index.php?teamid='. $teamid, 3, "Error - Match not created");
            break;
        }
        if (count($error)>0) {
            echo "Error - Maps not created";
        }
        break;
    }
    else {
        redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
    }
    break;
    case "editmatch":
    if ($xoopsUser) {
        $team =& $team_handler->get($teamid);
        if (($day)&&($month)) {
            $clock = explode(":", $time);
            $hour = $clock[0];
            $minute = $clock[1];
            $matchdate = mktime($hour,$minute,0,$month,$day,$year);
        }
        $match =& $match_handler->get($mid);
        $match->setVar('matchdate', $matchdate);
        $match->setVar('teamid', $teamid);
        $match->setVar('teamsize', $teamsize);
        $match->setVar('opponent', $opponent);
        $match->setVar('ladder', $ladder);
        $match->setVar('review', $review);
        $match->setVar('matchresult', $matchresult);
        $match->setVar('server', $server);
        $match->setVar('customserver', $customserver);
        $error = array();
        if (!$match_handler->insert($match)) {
            redirect_header('index.php?teamid='.$teamid, 2,_AM_DBNOTUPDATED);
        }
        else {
            $maps = $match->getMatchMaps();
            $matchmap_handler = xoops_getmodulehandler('matchmap');
            for ($count=0; $count < $team->getVar('maps'); $count++) {
                if (isset($maps[$count+1])) {
                    $thismap = $maps[$count+1];
                }
                else {
                    $thismap = $matchmap_handler->create();
                }
                $thismap->setVar("mapno", $count+1);
                $thismap->setVar("matchid",$match->getVar('matchid'));
                $thismap->setVar("ourscore",$ourscore[$count]);
                $thismap->setVar("theirscore",$theirscore[$count]);
                $thismap->setVar("mapid",$map[$count]);
                $thismap->setVar("side",$side[$count]);
                if (!$matchmap_handler->insert($thismap)) {
                    $error[]=$thismap->map->getVar('mapname')." Not Updated";
                }
            }
        }
        if (count($error)>0) {
            $errormess = "";
            foreach ($error as $message) {
                $errormess .= $message;
            }
            redirect_header('index.php?teamid='.$teamid,2,_AM_DBNOTUPDATED."<br>".$errormess);
        }
        else {
            redirect_header('index.php?teamid='.$teamid,2,_AM_DBUPDATED);
        }
        break;
    }
    else {
        redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
    }
    break;

    // user has uploaded screenshot
    case "savescreenshot":
    if ($submit == _AM_UPLOAD) {
        // do some error checking:
        if (!eregi( "jpeg", $_FILES['screenshot']['type'])) $message = _AM_TEAMERRORNOTJPG;
        if ($_FILES['screenshot']['error'] == UPLOAD_ERR_INI_SIZE) $message = _AM_TEAMERRORMAXFILESIZEINI;
        if ($_FILES['screenshot']['error'] == UPLOAD_ERR_FORM_SIZE) $message = _AM_TEAMERRORMAXFILESIZEFORM;

        // on error redirect to error page
        if (isset($message)) {
            redirect_header("index.php?op=screenshotform&mid=".$_POST['mid'],5, $message);
            exit();
        }
        // copy file to destination
        if (!move_uploaded_file($_FILES['screenshot']['tmp_name'],"screenshots/".$_FILES['screenshot']['name'])) {
            redirect_header("index.php?op=screenshotform&mid=".$_POST['mid'],5, _AM_TEAMERRORCOULDNOTCOPY);
            exit();
        } else {
            $matchmapid = intval($matchmapid);
            // create thumbnail
            if (resizeToFile("screenshots/".$_FILES['screenshot']['name'],150,113,"screenshots/thumbs/".$_FILES['screenshot']['name'], 90)) {
                $sql = "UPDATE ".$xoopsDB->prefix("team_matchmaps")." SET screenshot = ".$xoopsDB->quoteString($_FILES['screenshot']['name'])." WHERE matchmapid = $matchmapid";
                if (!$xoopsDB->query($sql)) {
                    redirect_header("index.php?op=screenshotform&mid=".$_POST['mid'],3, _AM_TEAMERRORWHILESAVINGSCREENSHOT);
                    exit();
                }
                redirect_header("index.php?op=screenshotform&mid=".$_POST['mid'],3, _AM_SCREENSHOTUPLOADED);
                exit();
            }
            else {
                redirect_header("index.php?op=screenshotform&mid=".$_POST['mid'],5, _AM_TEAMERRORGDLIB);
                exit();
            }
        }
    }
    break;

    case "deletescreenshot":
    $matchmapid = intval($matchmapid);
    $matchmap_handler = xoops_getmodulehandler('matchmap');
    $thismap = $matchmap_handler->get($matchmapid);
    $mid = $thismap->getVar('matchid');
    $filename = $thismap->getVar('screenshot');
    $result = $matchmap_handler->updateAll("screenshot", "", new Criteria("matchmapid", $matchmapid), true);
    if (!$result) {
        redirect_header("index.php?op=screenshotform&mid=".$mid,5, _AM_TEAMERRORDELETESCREENSHOT);
    }
    if (!unlink("screenshots/".$filename)) {
        redirect_header("index.php?op=screenshotform&mid=".$mid,5, _AM_TEAMERRORDELETESCREENSHOTSERVER);
    }
    if (!unlink("screenshots/thumbs/".$filename)) {
        redirect_header("index.php?op=screenshotform&mid=".$mid,5, _AM_TEAMERRORDELETETHUMBNAIL);
    }
    redirect_header("index.php?op=screenshotform&mid=".$mid,3,_AM_TEAMSCREENSHOTDELETED);
    break;

    // display matchlist
    case "default":
    default:
    $teamid = isset($_GET['teamid']) && $_GET['teamid'] > 0 ? $_GET['teamid'] : getDefaultTeam();
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
    $xoopsOption['template_main'] = 'team_matchlist.html';
    $layout = getLayout();
    $curteam =& $team_handler->get($teamid);
    $curteam->select();
    $clause = "LIMIT ".$start." , 10";
    if ($start == 0) {
        $xoopsTpl->assign('prevstart', 0);
        $xoopsTpl->assign('nextstart', 10);
    }
    else {
        $prev = $start - 10;
        $next = $start + 10;
        $xoopsTpl->assign('prevstart', $prev);
        $xoopsTpl->assign('nextstart', $next);
    }
    $wins=0;
    $losses=0;
    $draws=0;
    $teammember = 0;
    if ($xoopsUser) {
        if ($curteam->isMatchAdmin($xoopsUser->getVar("uid"))) {
            $xoopsTpl->assign('admin', "yes");
            $xoopsTpl->assign('isTeamMember', "yes");
            $teammember = 1;
        }
        if ($curteam->isTeamMember($xoopsUser->getVar("uid"))) {
            $xoopsTpl->assign('isTeamMember', "yes");
            $teammember = 1;
        }
    }
    $mapno = $curteam->getVar('maps');
    for ($i = 1; $i <= $mapno; $i++) {
        $xoopsTpl->append('captions', array('caption' => getCaption($i)));
    }
    $count=0;
    // get all ladders
    $ladders = getAllLadders();
    foreach ( $ladders as $ladderid => $thisladder) {
        if ($thisladder["visible"] == 0) {
            $hidden_ladders[] = strtolower($thisladder["ladder"]);
        }
        elseif ($thisladder["scoresvisible"] == 0) {
            $hidden_scores[] = strtolower($thisladder["ladder"]);
        }
    }
    // fetch matches from database
    $matches = $curteam->getMatches($clause);
    $allshorts = getAllSideShort();
    $matchmap_handler = xoops_getmodulehandler('matchmap');
    foreach ( $matches as $mid => $match ) {
        // only draw match if its ladder is not in $hidden_ladders and match result is not pending
        if (!($match->getVar('matchresult') <> "Pending" && in_array(strtolower($match->getVar('ladder')), $hidden_ladders))) {
            $yes = $no = $wins = $losses = $noreply = 0;
            $pic = "";
            if (isset($all) OR ($count<10)) {
                if ($match->getVar('matchresult')!='Pending') {
                    $count++;
                }
                $mdate = date (_MEDIUMDATESTRING, $match->getVar('matchdate'));
                $weekday = date( 'D', $match->getVar('matchdate'));
                $type = $match->getVar('ladder')." <nobr>".$match->getVar('teamsize')." "._AM_TEAMVERSUS." ".$match->getVar('teamsize')."</nobr>";
                if ((isset($class))&&($class=="even")) {
                    $class = "odd";
                }
                else {
                    $class = "even";
                }
                $map = array();
                $nomaps = $curteam->getVar('maps');
                for ($count = 1; $count <= $nomaps; $count++) {
                    $mapno = $count;
                    $thismap = $matchmap_handler->getByMatchid($mid, $mapno);
                    if (is_object($thismap) && isset($allshorts[$thismap->getVar('side')])) {
                        $side = $allshorts[$thismap->getVar('side')];
                    }
                    else {
                        $side = "";
                    }
                    $mapname = is_object($thismap->map) ? $thismap->map->getVar('mapname') : "--";
                    //Only show scores for matches for non-members if its ladder allows it
                    if (!isset($mapname) || (!$teammember && in_array(strtolower($match->getVar('ladder')), $hidden_scores))) {
                        $map[$mapno]["ourscore"] = 0;
                        $map[$mapno]["theirscore"] = 0;
                        $map[$mapno]["name"] = "";
                    }
                    else {
                        $map[$mapno]["ourscore"] = $thismap->getVar('ourscore');
                        $map[$mapno]["theirscore"] = $thismap->getVar('theirscore');
                        $map[$mapno]["name"] = $mapname." (".$side.")";
                    }
                    if (($match->getVar('matchresult')=='Pending') OR (!$xoopsUser) ) {
                        $map[$mapno]["color"] = $layout["color_match_pending"];
                    }
                    else {
                        $map[$mapno]["color"] = $thismap->winner($layout);
                    }
                }
                if ($match->getVar('matchresult')=='Win') {
                    $matchcolor = $layout["color_match_win"];
                    $wins++;
                }
                elseif ($match->getVar('matchresult')=='Loss') {
                    $matchcolor = $layout["color_match_loss"];
                    $losses++;
                }
                elseif ($match->getVar('matchresult')=='Draw') {
                    $matchcolor = $layout["color_match_draw"];
                    $draws++;
                }
                else {
                    $matchcolor = $layout["color_match_pending"];
                }
                if ($xoopsUser) {
                    if ($teammember == 1) {
                        $yes=0;
                        $no=0;
                        $noreply=0;
                        $availabilities = $match->getAvailabilities();
                        while ( $myav = $xoopsDB->fetchArray($availabilities) ) {
                            if (($myav["availability"]=="Yes") OR ($myav["availability"]=="LateYes")) {
                                $yes++;
                            }
                            elseif (($myav["availability"]=="No") OR ($myav["availability"]=="LateNo")){
                                $no++;
                            }
                            elseif (($myav["availability"]=="Not Set") OR ($myav["availability"]=="Sub")) {
                                $noreply++;
                            }
                        }
                        if ($match->getVar('matchresult')!='Pending') {
                            $pic = "check";
                        }
                        elseif ($match->getVar('alock')==1) {
                            $pic = "padlock";
                        }
                        else {
                            $pic = "notepad";
                        }
                    }
                }
            }
            $xoopsTpl->append('match', array('mid' => $mid, 'opponent' => $match->getVar('opponent'), 'matchresult' => $match->getVar('matchresult'), 'matchcolor' => $matchcolor, 'weekday' => $weekday, 'mdate' => $mdate, 'class' => $class, 'type' => $type, 'map' => $map, 'yes' => $yes, 'no' => $no, 'noreply' => $noreply, 'pic' => $pic));
        } // if visible
    }
    $xoopsTpl->assign('wins', $wins);
    $xoopsTpl->assign('losses', $losses);
    $xoopsTpl->assign('draws', $draws);
    $xoopsTpl->assign('matchlistfor', _AM_TEAMMATCHLISTFOR);
    $xoopsTpl->assign('teamname', $curteam->getVar('teamname'));
    $xoopsTpl->assign('addmatch', _AM_SUBMITMATCH);
    $xoopsTpl->assign('teamdate', _AM_TEAMDATE);
    $xoopsTpl->assign('teamid', $teamid);
    $xoopsTpl->assign('teamopponent', _AM_TEAMOPPONENT);
    $xoopsTpl->assign('teammatchtype', _AM_TEAMMATCHTYPE);
    $xoopsTpl->assign('teamresult', _AM_TEAMRESULT);
    $xoopsTpl->assign('teamy', _AM_TEAMY);
    $xoopsTpl->assign('teamn',_AM_TEAMN);
    $xoopsTpl->assign('teamwins',_AM_TEAMWINS);
    $xoopsTpl->assign('teamlosses', _AM_TEAMLOSSES);
    $xoopsTpl->assign('teamdraws', _AM_TEAMDRAWS);
    $xoopsTpl->assign('teamallmatches', _AM_TEAMALLMATCHES);
    $xoopsTpl->assign('lang_prevmatches', _AM_TEAMPREVMATCHES);
    $xoopsTpl->assign('lang_nextmatches', _AM_TEAMNEXTMATCHES);
    break;
}
include_once XOOPS_ROOT_PATH.'/footer.php';
?>

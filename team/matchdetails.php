<?php
include '../../mainfile.php';
include XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/functions.php';

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : null;
$mapid = isset($_GET['mapid']) ? intval($_GET['mapid']) : null;
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
$match_handler =& xoops_getmodulehandler('match');
$mymatch =& $match_handler->get($mid);
$teamid = $mymatch->getVar('teamid');
$team_handler =& xoops_getmodulehandler('team');
$team =& $team_handler->get($teamid);
switch($op) {
    case "savelineup":
    $lineuppos_handler =& xoops_getmodulehandler('lineupposition');
    $lineup_handler =& xoops_getmodulehandler('lineup');
    if ($action=='Edit') {
        $lineups=explode(":",$lineupid);
        $count = count($lineups);
        $message = _AM_TEAMLINEEDITED;
        $edit = true;
    }
    elseif ($action=='Add') {
        $count = $teamsize;
        $message = _AM_TEAMLINEUPADDED;
        $edit = false;
    }
    $thislineup = $lineup_handler->get($matchmapid);
    $thislineup->setVar('general', $general);
    $thislineup->saveGeneral();
    //UPDATE database
    for ($i=0;$i<$count;$i++) {
        if ($edit) {
            $thislineuppos = $lineuppos_handler->create(false);
            $thislineuppos->setVar('lineupid', $lineups[$i]);
        }
        else {
            $thislineuppos = $lineuppos_handler->create();
        }
        $thislineuppos->setVar('posid', $posid[$i]);
        $thislineuppos->setVar('matchmapid', $matchmapid);
        $thislineuppos->setVar('posdesc', $posdesc[$i]);
        $thislineuppos->setVar('uid', $playerid[$i]);
        $lineuppos_handler->insert($thislineuppos);
        unset($thislineuppos);
    }

    //Notification
    $tags = array();
    $tags['SIZE'] = $mymatch->getVar('teamsize');
    $tags['TEAM_NAME'] = $team->getVar('teamname');
    $tags['MAPNAME'] = getMap($mapid);
    $tags['OPPONENT'] = $mymatch->getVar('opponent');
    $tags['MATCHDATE'] = date(_SHORTDATESTRING, $mymatch->getVar('matchdate'));
    $tags['MATCHTIME'] = date('H:i', $mymatch->getVar('matchdate'));
    $tags['DETAILS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/matchdetails.php?mid='.$mid;
    $notification_handler =& xoops_gethandler('notification');
    $notification_handler->triggerEvent('match', $mid, 'new_lineup', $tags);

    //Redirect
    redirect_header("matchdetails.php?mid=".$mid,3,$message);
    break;

    case "lineup":
    $lineuppos_handler =& xoops_getmodulehandler('lineupposition');
    $lineup_handler =& xoops_getmodulehandler('lineup');
    if ($xoopsUser && $team->isTacticsAdmin($xoopsUser->getVar("uid"))) {
        $playerid = array();
        $pos = array();
        $desc = array();
        if (isset($_GET['matchmapid'])) {
            $teamsize = $mymatch->getVar('teamsize');
            $lineup = $lineup_handler->get($_GET['matchmapid']);
            $map = is_object($lineup->map) ? $lineup->map->getVar('mapname') : "??";
            $general = $lineup->getVar('general');
            $positions = $lineup->getPositions();
            if (count($positions)>0) {
                $i=0;
                $lineupid = "";
                foreach ($positions as $key => $position) {
                    if ($lineupid) {
                        $lineupid .= ":";
                    }
                    $lineupid .= $position['lineupid'];
                    $pos[$i] = $position['posid'];
                    $desc[$i] = $position['posdesc'];
                    $playerid[$i] = $position['uid'];
                    $i++;
                }
                $action = "Edit";
            }
            else {
                $tactics_handler =& xoops_getmodulehandler('tactics');
                $position_handler =& xoops_getmodulehandler('tacticsposition');
                $tactics =& $tactics_handler->getByParams($teamid, $mapid, $teamsize);
                $tacid = $tactics->getVar('tacid');
                $general = $tactics->getVar('general');
                $positions = $tactics->getPositions();
                $action = "Add";
                $i=0;
                if (count($positions) > 0) {
                    foreach ($positions as $key => $tacposid) {
                        $thisposition =& $position_handler->get($tacposid);
                        $pos[$i] = $thisposition->getVar('posid');
                        $desc[$i] = $thisposition->getVar('posdesc');
                        $i++;
                    }
                }
            }
        }
        else {
            redirect_header("index.php",2,_AM_TEAMNOLINEUPSELECTED);
            break;
        }
        echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
        echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
        echo "<tr class='head'><td colspan=2><h3>";
        echo ""._AM_TEAMLINEUPFOR."".$team->getVar('teamname')." "._AM_TEAMVERSUS." ".$mymatch->getVar('opponent')." "._AM_TEAMON." ".$map;
        echo "</h3></td></tr>";
        echo "<tr><td colspan=2>";
        include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $mform = new XoopsThemeForm(_AM_TEAMLINEUPADDITION, "savelineup", xoops_getenv('PHP_SELF'));
        $players = $mymatch->getMatchPlayers();
        $subs = $mymatch->getMatchSubs();
        $generaltacs = new XoopsFormTextArea(_AM_TEAMGENERALTACS, "general", $general);
        $mform->addElement($generaltacs);
        $teampositions = $team->getPositions();
        for ($i=0;$i<$teamsize;$i++) {
            $thispos = 0;
            $player = 0;
            $thisdesc = "";
            if (isset($pos[$i])) {
                $thispos = $pos[$i];
            }
            if (isset($playerid[$i])) {
                $player = $playerid[$i];
            }
            if (isset($desc[$i])) {
                $thisdesc = $desc[$i];
            }
            $position_select[$i] = new XoopsFormSelect(_AM_TEAMPOSITION." ".($i+1), "posid[".$i."]", $thispos);
            foreach ($teampositions as $positionid => $positionname) {
                $position_select[$i]->addOption($positionid, $positionname);
            }
            $player_select[$i] = new XoopsFormSelect(_AM_TEAMPLAYER, "playerid[".$i."]", $player);
            $player_select[$i]->addOption(0, _AM_TEAMUNDECIDED);
            foreach ($players as $pid => $pname) {
                $player_select[$i]->addOption($pid, $pname);
            }
            $player_select[$i]->addOption(-1, "---");
            foreach ($subs as $pid => $pname) {
                $player_select[$i]->addOption($pid, "(Sub)".$pname);
            }
            $description[$i] = new XoopsFormTextArea(_AM_TEAMDESCRIPTION, "posdesc[".$i."]", $thisdesc);
            $mform->addElement($position_select[$i]);
            $mform->addElement($player_select[$i]);
            $mform->addElement($description[$i]);
        }
        $button_tray = new XoopsFormElementTray('' ,'');
        $submit = new XoopsFormButton('', 'action', $action, 'Submit');
        $button_tray->addElement($submit);
        $teamsize_hidden = new XoopsFormHidden("teamsize",$teamsize);
        $matchmapid_hidden = new XoopsFormHidden("matchmapid", $lineup->getVar('matchmapid'));
        $mapid_hidden = new XoopsFormHidden("mapid", $lineup->getVar('mapid'));
        $matchid_hidden = new XoopsFormHidden("mid", $lineup->getVar('matchid'));
        $op_hidden = new XoopsFormHidden("op","savelineup");
        if (isset($lineupid)) {
            $lineupid_hidden = new XoopsFormHidden("lineupid",$lineupid);
            $mform->addElement($lineupid_hidden);
        }
        $mform->addElement($teamsize_hidden);
        $mform->addElement($mapid_hidden);
        $mform->addElement($matchid_hidden);
        $mform->addElement($matchmapid_hidden);
        $mform->addElement($op_hidden);
        $mform->addElement($button_tray);
        $mform->display();
        echo "</table></td></tr></table>";
    }
    else {
        redirect_header("index.php",3,_AM_TEAMACCESSDENIED);
        break;
    }
    break;

    case "default":
    $layout = getLayout();
    include_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';
    $xoopsOption['template_main'] = 'team_matchdetails.html';
    $teamname = $team->getVar('teamname');
    $mdate = $mymatch->getVar('matchdate');
    $matchresult = $mymatch->getVar('matchresult');
    switch (strtolower($matchresult)) {
        case "win": $matchresultcolor = $layout["color_match_win"]; break;
        case "loss": $matchresultcolor = $layout["color_match_loss"]; break;
        case "draw": $matchresultcolor = $layout["color_match_draw"]; break;
        default: $matchresultcolor = $layout["color_match_pending"]; break;
    }
    $time = date("H:i", $mdate);
    $maps = $team->getVar('maps');
    $sides = getAllSides();
    $screenshotnumber = 0;
    $ourscoresum = 0; $theirscoresum = 0;
    $matchmap_handler = xoops_getmodulehandler('matchmap');
    $matchmaps = $matchmap_handler->getByMatchid($mid);
    for ($i = 1; $i <= $maps; $i++) {
        $thismap = isset($matchmaps[$i]) && is_object($matchmaps[$i]) ? $matchmaps[$i] : $matchmap_handler->create();
        $side = $sides[$thismap->getVar('side')];
        $map[$i]["matchmapid"] = $thismap->getVar('matchmapid');
        $map[$i]["name"] = is_object($thismap->map) ? $thismap->map->getVar('mapname')." (".$side.")" : "?? (".$side.")";
        $map[$i]["mapid"] = $thismap->getVar('mapid');
        $map[$i]["ourscore"] = $thismap->getVar('ourscore');
        $ourscoresum += $thismap->getVar('ourscore');
        $theirscoresum += $thismap->getVar('theirscore');
        $map[$i]["theirscore"] = $thismap->getVar('theirscore');
        $map[$i]["matchid"] = $thismap->getVar('matchid');
        $map[$i]["caption"] = getCaption($i);
        $map[$i]["screenshot"] = $thismap->getVar('screenshot');
        if ($map[$i]["screenshot"] != "") {
            $screenshotnumber++;
        }
        $map[$i]["tacid"] = $thismap->getTacid($teamid,$mymatch->getVar('teamsize'));
        if ($mymatch->getVar('matchresult')=='Pending') {
            $map[$i]["color"] = $layout["color_match_pending"];
        }
        else {
            $map[$i]["color"] = $thismap->winner($layout);
        }
    }
    if ($time!='21:00') {
        $xoopsTpl->assign('msize', 3);
    }
    else {
        $xoopsTpl->assign('msize', 2);
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
    $xoopsTpl->assign('ourscoresum', $ourscoresum);
    $xoopsTpl->assign('theirscoresum', $theirscoresum);
    $xoopsTpl->assign('matchresultcolor', $matchresultcolor);
    $xoopsTpl->assign('weekday', $weekday);
    $xoopsTpl->assign('teamname', $teamname);
    $xoopsTpl->assign('teamid', $teamid);
    $xoopsTpl->assign('day', date(_SHORTDATESTRING, $mdate));
    $xoopsTpl->assign('time', $time);
    $xoopsTpl->assign('screenshotnumber', $screenshotnumber);
    $xoopsTpl->assign('matchresult', $matchresult);
    $review = $mymatch->getVar('review');
    if (strlen($review) > 0)
    $xoopsTpl->assign('review', $review);
    if ($mymatch->getVar('server') != 0) {
        $matchserver = getServer($mymatch->getVar('server'));
        $xoopsTpl->assign('servername', $matchserver["name"]);
        $xoopsTpl->assign('ip', $matchserver["ip"]);
        $xoopsTpl->assign('port', $matchserver["port"]);
    } else {
        // custom server
        $xoopsTpl->assign('servername', $mymatch->getVar('customServer'));
    }
    $xoopsTpl->assign('opponent', $mymatch->getVar('opponent'));
    $xoopsTpl->assign('mid', $mid);
    $xoopsTpl->assign('teamsize', $mymatch->getVar('teamsize'));
    $xoopsTpl->assign('ladder', $mymatch->getVar('ladder'));
    $xoopsTpl->assign('lang_opponent', _AM_TEAMAGAINST);
    $xoopsTpl->assign('lang_availability', _AM_TEAMMATCHAVAILABILITY);
    $xoopsTpl->assign('lang_teammatchlist', _AM_TEAMMATCHLIST);
    $xoopsTpl->assign('lang_matchpositions', _AM_TEAMMATCHPOSITIONS);
    $xoopsTpl->assign('lang_at', _AM_TEAMAT);
    $xoopsTpl->assign('lang_matchtype', _AM_TEAMMATCHTYPE);
    $xoopsTpl->assign('lang_versus', _AM_TEAMVERSUS);
    $xoopsTpl->assign('lang_server', _AM_TEAMSERVER);
    $xoopsTpl->assign('lang_review', _AM_MATCHREVIEW);
    $xoopsTpl->assign('lang_lineupfor', _AM_TEAMLINEUPFOR);
    $xoopsTpl->assign('lang_lineup', _AM_TEAMLINEUP);
    $xoopsTpl->assign('lang_nolineupyet', _AM_TEAMNOLINEUPYET);
    $xoopsTpl->assign('lang_screenshots', _AM_SCREENSHOTS);
    $xoopsTpl->assign('lock', $mymatch->getVar('alock'));
    if ($mymatch->getVar('matchresult')=='Pending') {
        $xoopsTpl->assign('pending', 1);
    }
    else {
        $xoopsTpl->assign('pending', 0);
    }
    $allpos = getAllPos();
    $lineup_handler = xoops_getmodulehandler('lineup');
    foreach ($map as $thismap) {
        $thislineup = $lineup_handler->get($thismap["matchmapid"]);
        $general = $thislineup->getVar('general');
        $lineuppos = $thislineup->getPositions();
        $lineup = array();
        if (isset($general)) {
            $lineup[] = array("uname" => "",
            "posname" => _AM_TEAMGENERALTACS,
            "posdesc" => $general,
            "class" => "even");
        }
        $i=0;
        if (count($lineuppos)>0) {
            foreach ($lineuppos as $key => $thislineup) {
                $i++;
                if ((isset($class))&&($class=="odd")) {
                    $class = "even";
                }
                else {
                    $class = "odd";
                }
                if ($thislineup["uid"]) {
                    $thisuser = XoopsUser::getUnameFromId($thislineup["uid"]);
                    $thisuser = $i." ".$thisuser;
                }
                else {
                    $thisuser = $i." -";
                }
                $lineup[] = array("uname" => $thisuser,
                "posname" => $allpos[$thislineup["posid"]],
                "posdesc" => $thislineup["posdesc"],
                "class" => $class);
            }
            $edit = "edit";
        }
        else {
            $edit = "Set";
        }
        $xoopsTpl->append('map', array('mapid' => $thismap["mapid"], 'edit' => $edit, 'mapno' => $thismap["caption"], 'linenumbers' => count($lineuppos), 'mapname' => $thismap["name"], 'ourscore' => $thismap["ourscore"], 'theirscore' => $thismap["theirscore"], 'color' => $thismap["color"], 'tacid' => $thismap["tacid"], 'screenshot' => $thismap["screenshot"], 'lineup' => $lineup, 'matchmapid' => $thismap["matchmapid"]));
    }
    if ($xoopsUser && $team->isTeamMember($xoopsUser->getVar("uid"))) {
        $xoopsTpl->assign('isTeamMember', "yes");
    }

    if ($xoopsUser && $team->isTacticsAdmin($xoopsUser->getVar("uid"))) {
        $xoopsTpl->assign('admin', 'yes');
    }

    break;
}

include(XOOPS_ROOT_PATH."/footer.php");
?>
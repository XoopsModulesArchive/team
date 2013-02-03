<?php
// $Id: index.php,v 1.13 2006/06/09 14:32:47 mithyt2 Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
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
include '../../../include/cp_header.php';
include('../functions.php');
include('functions.php');

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$posid = isset($_GET['posid']) ? intval($_GET['posid']) : 'default';
$op = isset($_POST['op']) ? $_POST['op'] : $op;
if (!isset($_POST['action'])) {
    $action ="";
}

function ladderedit($id="") {
    global $xoopsDB;
    $op = "addladder";
    $action = "Add";
    $laddername = "";
    $ladderid = "";
    $laddervisible = 1;
    $scoresvisible = 0;
    if ($id) {
        $sql = "SELECT * FROM ".$xoopsDB->prefix("team_ladders")." WHERE ladderid=".intval($id);
        $result = $xoopsDB->query($sql);
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            $ladderid = $myrow["ladderid"];
            $laddername = $myrow["ladder"];
            $laddervisible = $myrow["visible"];
            $scoresvisible = $myrow["scoresvisible"];
            $op = "editladder";
            $action = "Edit";
        }
    }
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $pform = new XoopsThemeForm($action." Ladder", "ladderform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('' ,'');
    $submit = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden = new XoopsFormHidden('op', $op);
    $name = new XoopsFormText(_AM_TEAMLADDERNAME, 'laddername', 32, 32, $laddername, 'E');
    $visible = new XoopsFormRadioYN(_AM_TEAMLADDERVISIBLE, 'laddervisible', $laddervisible, _AM_YES, _AM_NO);
    $scores = new XoopsFormRadioYN(_AM_TEAMSCORESVISIBLE, 'scoresvisible', $scoresvisible, _AM_YES, _AM_NO);
    $id = new XoopsFormHidden('ladderid', $ladderid);
    $button_tray->addElement($submit);
    $pform->addElement($name);
    $pform->addElement($visible);
    $pform->addElement($scores);
    $pform->addElement($id);
    $pform->addElement($button_tray);
    $pform->addElement($op_hidden);
    $pform->display();
}

function posedit($id="") {
    global $xoopsDB;
    $op = "addpos";
    $action = "Add";
    $posid = "";
    $postype = "Pos";
    $posname = "";
    $posshort = " ";
    if ($id) {
        $sql = "SELECT * FROM ".$xoopsDB->prefix("team_positions")." WHERE posid=".intval($id);
        $result = $xoopsDB->query($sql);
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            $posid = $myrow["posid"];
            $postype = $myrow["postype"];
            $posname = $myrow["posname"];
            $posshort = $myrow["posshort"];
            $op = "editpos";
            $action = "Edit";
        }
    }
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $pform = new XoopsThemeForm($action." Position", "posform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('' ,'');
    $submit = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden = new XoopsFormHidden('op', $op);
    $name = new XoopsFormText(_AM_TEAMPOSITIONNAME, 'posname', 35, 35, $posname, 'E');
    $short = new XoopsFormText(_AM_TEAMPOSITIONSHORT, 'posshort', 20, 20, $posshort, 'E');
    $type = new XoopsFormRadio(_AM_TEAMPOSITIONTYPE, 'postype', $postype);
    $id = new XoopsFormHidden('posid', $posid);
    $button_tray->addElement($submit);
    $type->addOption("Pos", "Pos");
    $type->addOption("Skill", "Skill");
    $pform->addElement($name);
    $pform->addElement($short);
    $pform->addElement($type);
    $pform->addElement($id);
    $pform->addElement($button_tray);
    $pform->addElement($op_hidden);
    $pform->display();
}

function mapedit($id="") {
    global $xoopsDB;
    $op = "addmap";
    $action = _AM_ADD;
    $mapid = "";
    $mapname = "";
    if ($id!="") {
        $map_handler = xoops_getmodulehandler('map');
        list($mapid, $mapname) = $map_handler->get($id, false);
        $op = "editmap";
        $action = _AM_EDIT;
    }
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $mform = new XoopsThemeForm(_AM_MAPMNGR, "mapedit", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('' ,'');
    $submit = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden = new XoopsFormHidden('op', $op);
    $mapid_hidden = new XoopsFormHidden('mapid', $mapid);
    $name = new XoopsFormText(_AM_TEAMNEWMAPNAME, 'mapname', 25, 25, $mapname, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($name);
    $mform->addElement($button_tray);
    $mform->addElement($op_hidden);
    $mform->addElement($mapid_hidden);
    $mform->display();
}
function serverForm($action, $serverid="") {
    if ($action == 'Edit') {
        $submittext = _AM_TEAMEDITSERVER;
    }
    else {
        $submittext = _AM_TEAMADDSERVER;
    }
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $mform = new XoopsThemeForm(_AM_TEAMADDSERVER, "serverform", xoops_getenv('PHP_SELF'));
    $op_hidden = new XoopsFormHidden('op', "saveserver");
    $submit = new XoopsFormButton('', 'submit', $submittext, 'submit');
    $action_hidden = new XoopsFormHidden('action', $action);
    $button_tray = new XoopsFormElementTray('' ,'');
    if ($serverid!="") {
        $server = getServer($serverid);
        $name = $server["name"];
        $ip = $server["ip"];
        $port = $server["port"];
        $serverid_hidden = new XoopsFormHidden('serverid', $serverid);
        $mform->addElement($serverid_hidden);
    }
    else {
        $name = "Servername";
        $ip = "IP";
        $port = "Port";
    }
    $name = new XoopsFormText(_AM_TEAMSERVERNAME, 'servername', 30, 30, $name, 'E');
    $ip = new XoopsFormText(_AM_TEAMSERVERIP, 'serverip', 20, 20, $ip, 'E');
    $port = new XoopsFormText(_AM_TEAMSERVERPORT, 'serverport', 10, 10, $port, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($name);
    $mform->addElement($ip);
    $mform->addElement($port);
    $mform->addElement($op_hidden);
    $mform->addElement($action_hidden);
    $mform->addElement($button_tray);
    $mform->display();
}
function addSizeForm() {
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $mform = new XoopsThemeForm(_AM_TEAMADDSIZE, "serverform", xoops_getenv('PHP_SELF'));
    $op_hidden = new XoopsFormHidden('op', "savesize");
    $submit = new XoopsFormButton('', 'submit', _AM_TEAMADDSIZE, 'submit');
    $button_tray = new XoopsFormElementTray('' ,'');
    $teamsize = new XoopsFormText(_AM_TEAMSIZENAME, 'size', 20, 20, _AM_TEAMSIZENAME, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($teamsize);
    $mform->addElement($op_hidden);
    $mform->addElement($button_tray);
    $mform->display();
}

function addSideForm() {
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $mform = new XoopsThemeForm(_AM_TEAMADDSIDE, "sideform", xoops_getenv('PHP_SELF'));
    $op_hidden = new XoopsFormHidden('op', "saveside");
    $submit = new XoopsFormButton('', 'submit', _AM_TEAMADDSIDE, 'submit');
    $button_tray = new XoopsFormElementTray('' ,'');
    $teamside = new XoopsFormText(_AM_TEAMSIDENAME, 'side', 12, 20, _AM_TEAMSIDENAME, 'E');
    $sideshort = new XoopsFormText(_AM_TEAMSIDESHORT, 'sideshort', 5, 20, _AM_TEAMSIDESHORT, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($teamside);
    $mform->addElement($sideshort);
    $mform->addElement($op_hidden);
    $mform->addElement($button_tray);
    $mform->display();
}

function rankform($rankid="") {
    global $xoopsDB;
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $rform = new XoopsThemeForm(_AM_TEAMADDRANK, "rankform", xoops_getenv('PHP_SELF'));
    if ($rankid) {
        $sql = "SELECT rankid, rank, matches, tactics, color FROM ".$xoopsDB->prefix("team_rank")." WHERE rankid=".intval($rankid);
        $result = $xoopsDB->query($sql);
        $thisrank = $xoopsDB->fetchArray($result);
        $action = "Edit";
        $submittext = _AM_TEAMEDITRANK;
        $rankid_hidden = new XoopsFormHidden('rankid', $rankid);
        $rform->addElement($rankid_hidden);
    }
    else {
        $action = "Add";
        $submittext = _AM_TEAMADDRANK;
        $thisrank["rank"] = "Input Rank";
        $thisrank["tactics"] = 0;
        $thisrank["matches"] = 0;
        $thisrank["color"] = "Green";
    }
    $op_hidden = new XoopsFormHidden('op', "saverank");
    $action_hidden = new XoopsFormHidden('action', $action);
    $submit = new XoopsFormButton('', 'submit', $submittext, 'submit');
    $button_tray = new XoopsFormElementTray('' ,'');
    $rank = new XoopsFormText(_AM_TEAMRANK, 'rank', 20, 20, $thisrank["rank"], 'E');
    $tactics = new XoopsFormRadioYN(_AM_TEAMTACTICSRANK, 'tactics', $thisrank["tactics"], _YES, _NO);
    $matches = new XoopsFormRadioYN(_AM_TEAMMATCHRANK, 'matches', $thisrank["matches"], _YES, _NO);
    $color = new XoopsFormText(_AM_TEAMRANKCOLOR, 'color', 20, 20, $thisrank["color"], 'E');
    $button_tray->addElement($submit);
    $rform->addElement($rank);
    $rform->addElement($op_hidden);
    $rform->addElement($action_hidden);
    $rform->addElement($tactics);
    $rform->addElement($matches);
    $rform->addElement($color);
    $rform->addElement($button_tray);
    $rform->display();
}

function layoutform($data) {
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $lform = new XoopsThemeForm("Edit Layout", "layform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('' ,'');
    $submit = new XoopsFormButton('', 'submit',_AM_SAVE, 'submit');
    $op_hidden = new XoopsFormHidden('op', "savelayout");
    $color_status_active = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSACTIVE, 'color_status_active', 11, 11, $data['color_status_active'], 'E');
    $color_status_inactive = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSINACTIVE, 'color_status_inactive', 11, 11, $data['color_status_inactive'], 'E');
    $color_status_onleave = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSONLEAVE, 'color_status_onleave', 11, 11, $data['color_status_onleave'], 'E');
    $color_match_win = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSWIN, 'color_match_win', 11, 11, $data['color_match_win'], 'E');
    $color_match_loss = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSLOSS, 'color_match_loss', 11, 11, $data['color_match_loss'], 'E');
    $color_match_draw = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSDRAW, 'color_match_draw', 11, 11, $data['color_match_draw'], 'E');
    $color_match_pending = new XoopsFormText(_AM_TEAMLAYOUTCOLORSTATUSPENDING, 'color_match_pending', 11, 11, $data['color_match_pending'], 'E');
    $color_perfect = new XoopsFormText(_AM_TEAMLAYOUTPERFECTCOLOR, 'color_perfect', 11, 11, $data['color_perfect'], 'E');
    $color_good = new XoopsFormText(_AM_TEAMLAYOUTGOODCOLOR, 'color_good', 11, 11, $data['color_good'], 'E');
    $color_warn = new XoopsFormText(_AM_TEAMLAYOUTWARNCOLOR, 'color_warn', 11, 11, $data['color_warn'], 'E');
    $color_bad = new XoopsFormText(_AM_TEAMLAYOUTBADCOLOR, 'color_bad', 11, 11, $data['color_bad'], 'E');
    $button_tray->addElement($submit);
    $lform->addElement($color_status_active);
    $lform->addElement($color_status_inactive);
    $lform->addElement($color_status_onleave);
    $lform->addElement($color_match_win);
    $lform->addElement($color_match_loss);
    $lform->addElement($color_match_draw);
    $lform->addElement($color_match_pending);
    $lform->addElement($color_perfect);
    $lform->addElement($color_good);
    $lform->addElement($color_warn);
    $lform->addElement($color_bad);
    $lform->addElement($button_tray);
    $lform->addElement($op_hidden);
    $lform->display();
}

xoops_cp_header();
$team_handler =& xoops_getmodulehandler('team');
switch($op){
    case "savelayout":
    if ($_POST['submit'] == _AM_SAVE) {
        $sql = "UPDATE ".$xoopsDB->prefix("team_layout")." SET color_status_active = ".$xoopsDB->quoteString($_POST['color_status_active'])."," .
        " color_status_inactive=".$xoopsDB->quoteString($_POST['color_status_inactive'])."," .
        " color_status_onleave=".$xoopsDB->quoteString($_POST['color_status_onleave'])."," .
        " color_match_win=".$xoopsDB->quoteString($_POST['color_match_win'])."," .
        " color_match_loss=".$xoopsDB->quoteString($_POST['color_match_loss'])."," .
        " color_match_draw=".$xoopsDB->quoteString($_POST['color_match_draw'])."," .
        " color_match_pending=".$xoopsDB->quoteString($_POST['color_match_pending'])."," .
        " color_perfect = ".$xoopsDB->quoteString($_POST['color_perfect'])."," .
        " color_good = ".$xoopsDB->quoteString($_POST['color_good'])."," .
        " color_warn = ".$xoopsDB->quoteString($_POST['color_warn'])."," .
        " color_bad = ".$xoopsDB->quoteString($_POST['color_bad']).
        " WHERE layoutid = 1";

        if (!$xoopsDB->query($sql)) {
            redirect_header("index.php?op=layoutmanager",3, _AM_TEAMERRORWHILESAVINGLAYOUT);
        }
        redirect_header("index.php?op=layoutmanager",3, _AM_TEAMLAYOUTSAVED);
    }
    break;

    case "saveteam":
    if ($_POST['submit']=="Add") {
        $thisteam =& $team_handler->create();
        $message = _AM_TEAMTEAMADDED;
    }
    else {
        $thisteam =& $team_handler->get($_POST['teamid']);
        $message = _AM_TEAMTEAMEDITED;
    }
    $thisteam->setVar("teamname", $_POST['name']);
    $thisteam->setVar("teamtype", $_POST['type']);
    $thisteam->setVar("maps", $_POST['maps']);
    if (!getDefaultTeam()) {
        $thisteam->setVar('defteam', 1);
    }
    else {
        $thisteam->setVar('defteam', 0);
    }
    if ($team_handler->insert($thisteam)) {
        redirect_header("teamadmin.php?teamid=".$thisteam->getVar('teamid'), 3, $_POST['name']." ".$message);
        break;
    }
    else {
        $errors=1;
    }
    if (isset($errors)) {
        redirect_header("index.php?op=teammanager",3, _AM_TEAMERRORWHILESAVINGTEAM);
    }
    break;

    case "saverank":
    if ($_POST['action']=="Add") {
        $sql = "INSERT INTO ".$xoopsDB->prefix("team_rank")." (rank, tactics, matches, color) VALUES (".$xoopsDB->quoteString($_POST['rank']).", ".intval($_POST['tactics']).", ".intval($_POST['matches']).", ".$xoopsDB->quoteString($_POST['color']).")";
        $comment = $_POST['rank']." Added";
    }
    elseif ($_POST['action'] == "Edit") {
        $sql = "UPDATE ".$xoopsDB->prefix("team_rank")." SET rank = ".$xoopsDB->quoteString($_POST['rank']).", matches=".intval($_POST['matches']).", tactics=".intval($_POST['tactics']).", color=".$xoopsDB->quoteString($_POST['color'])."  WHERE rankid=".intval($_POST['rankid']);
        $comment = $_POST['rank']." Edited";
    }
    if (!$xoopsDB->query($sql)) {
        $comment = _AM_TEAMERRORWHILESAVINGRANK;
    }
    redirect_header("index.php?op=rankmanager",3, $comment);
    break;

    case "addpos":
    $sql = "INSERT INTO ".$xoopsDB->prefix("team_positions")." (postype, posname, posshort) VALUES (".$xoopsDB->quoteString($_POST['postype']).", ".$xoopsDB->quoteString($_POST['posname']).", ".$xoopsDB->quoteString($_POST['posshort']).")";
    $xoopsDB->query($sql);
    redirect_header("index.php?op=positionmanager",3, _AM_TEAMPOSITIONADDED);
    break;

    case "editpos":
    if (isset($_POST['postype'])) {
        $sql = "UPDATE ".$xoopsDB->prefix("team_positions")." SET postype=".$xoopsDB->quoteString($_POST['postype']).", posname=".$xoopsDB->quoteString($_POST['posname']).", posshort=".$xoopsDB->quoteString($_POST['posshort'])." WHERE posid=".intval($_POST['posid']);
        $xoopsDB->query($sql);
        redirect_header("index.php?op=positionmanager",3,_AM_TEAMPOSITIONMODIFIED);
        break;
    }
    break;

    case "deletepos":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['posid'])) {
            redirect_header('index.php?op=positionmanager',2,_AM_EMPTYNODELETE);
            break;
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_positions")." WHERE posid=".intval($_POST['posid']);
        $xoopsDB->query($sql);
        redirect_header("index.php?op=positionmanager",3,_AM_TEAMPOSITIONDELETED);
        break;
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deletepos', 'posid' => $_POST['posid'], 'ok' => 1), 'index.php', _AM_RUSUREDELPOS);
    }
    break;

    case "addladder":
    $sql = "INSERT INTO ".$xoopsDB->prefix("team_ladders")." (ladder, visible, scoresvisible) VALUES (".$xoopsDB->quoteString($_POST['laddername']).", ".intval($_POST['laddervisible']).", ".intval($_POST['scoresvisible']).")";
    $xoopsDB->query($sql);
    redirect_header("index.php?op=laddermanager",3, _AM_TEAMLADDERADDED);
    break;

    case "editladder":
    $sql = "UPDATE ".$xoopsDB->prefix("team_ladders")." SET ladder=".$xoopsDB->quoteString($_POST['laddername']).", visible=".intval($_POST['laddervisible']).", scoresvisible=".intval($_POST['scoresvisible'])." WHERE ladderid=".intval($_POST['ladderid']);
    $xoopsDB->query($sql);
    redirect_header("index.php?op=laddermanager",3,_AM_TEAMLADDERMODIFIED);
    break;

    case "deleteladder":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['ladderid'])) {
            redirect_header('index.php?op=laddermanager',2,_AM_EMPTYNODELETE);
            break;
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_ladders")." WHERE ladderid=".intval($_POST['ladderid']);
        $xoopsDB->query($sql);
        redirect_header("index.php?op=laddermanager",3,_AM_TEAMLADDERDELETED);
        break;
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deleteladder', 'ladderid' => intval($_GET['ladderid']), 'ok' => 1), 'index.php', _AM_RUSUREDELLADDER);
    }
    break;

    case "addmap":
    $sql = "INSERT INTO ".$xoopsDB->prefix("team_mappool")." (mapname) VALUES (".$xoopsDB->quoteString($_POST['mapname']).")";
    $xoopsDB->query($sql);
    redirect_header("index.php?op=mappoolmanager",3, $_POST['mapname']." "._AM_TEAMADDEDTOMAPPOOL."");
    break;

    case "editmap":
    if (isset($_POST['mapname'])) {
        $sql = "UPDATE ".$xoopsDB->prefix("team_mappool")." SET mapname=".$xoopsDB->quoteString($_POST['mapname'])." WHERE mapid=".intval($_POST['mapid']);
        $xoopsDB->query($sql);
        redirect_header("index.php?op=mappoolmanager",3,_AM_TEAMMAPNAMEMODIF);
        break;
    }
    break;

    case "deletemap":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['mapid'])) {
            redirect_header('index.php?op=mappoolmanager',2,_AM_EMPTYNODELETE);
            exit();
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_mappool")." WHERE mapid=".intval($_POST['mapid']);
        $xoopsDB->query($sql);
        redirect_header("index.php?op=mappoolmanager",3,_AM_TEAMMAPDELFROMPOOLMAP);
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deletemap', 'mapid' => intval($_POST['mapid']), 'ok' => 1), 'index.php', _AM_RUSUREDELMAP);
    }
    break;

    case "deleteteam":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['teamid'])) {
            redirect_header('index.php?op=teammanager',2,_AM_EMPTYNODELETE);
            exit();
        }
        $teamid = intval($_POST['teamid']);
        $team = $team_handler->get($teamid);
        $team_handler->delete($team);
        redirect_header("index.php?op=teammanager",3,_AM_TEAMDELETED);
    }
    else {
        echo "<h4>"._AM_TEAMCONFIG."</h4>";
        xoops_confirm(array('op' => 'deleteteam', 'teamid' => $_POST['teamid'], 'ok' => 1), 'index.php', _AM_RUSUREDELTEAM);
    }
    break;

    case "deleteserver":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['serverid'])) {
            redirect_header('index.php?op=default',2,_AM_EMPTYNODELETE);
            exit();
        }
        $serverid = intval($_POST['serverid']);
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_server")." WHERE serverid=$serverid";
        if ($xoopsDB->query($sql)) {
            $sql = "DELETE FROM ".$xoopsDB->prefix("server_bookings")." WHERE serverid=$serverid";
            if ($xoopsDB->query($sql)) {
                redirect_header("index.php?op=servermanager",3,_AM_TEAMSERVERDELETED);
            }
            else {
                redirect_header("index.php?op=servermanager",3,_AM_TEAMSERVERDELBOOKNOT);
            }
        }
        else {
            redirect_header("index.php?op=servermanager",3,_AM_TEAMERRSERVERNOTDEL);
        }
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deleteserver', 'serverid' => $_POST['serverid'], 'ok' => 1), 'index.php', _AM_RUSUREDELSERVER);
    }
    break;

    case "deleterank":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['rankid'])) {
            redirect_header('index.php?op=rankmanager',2,_AM_EMPTYNODELETE);
            exit();
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_rank")." WHERE rankid=".intval($_POST['rankid']);
        if ($xoopsDB->query($sql)) {
            redirect_header('index.php?op=rankmanager',1,_AM_DBUPDATED);
            exit();
        }
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deleterank', 'rankid' => $_POST['rankid'], 'ok' => 1), 'index.php', _AM_RUSUREDELRANK);
    }
    break;

    case "deletematch":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['matchid'])) {
            redirect_header('index.php?op=matchmanager',2,_AM_EMPTYNODELETE);
            break;
        }
        $matchid = intval($_POST['matchid']);
        $match_handler = xoops_getmodulehandler('match');
        $match = $match_handler->get($matchid);
        $match_handler->delete($match);
        redirect_header('index.php?op=matchmanager',1,_AM_DBUPDATED);
        break;
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deletematch', 'matchid' => $_POST['matchid'], 'ok' => 1), 'index.php', _AM_RUSUREDEL);
    }
    break;

    case "matchmanager":
    if (isset($_POST['teamid'])) {
        $teamid = intval($_POST['teamid']);
        $sql = "SELECT * FROM ".$xoopsDB->prefix("team_matches")." WHERE teamid=$teamid ORDER BY matchdate DESC";
        $team = getTeam($teamid);
    }
    else {
        $sql = "SELECT * FROM ".$xoopsDB->prefix("team_matches")." ORDER BY matchdate DESC";
        $teamid = getDefaultTeam();
    }
    $result = $xoopsDB->query($sql);
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/managematch.gif";
    $rightlink[0]["url"] = "../index.php?op=matchform";
    $rightlink[0]["text"] = _AM_TEAMADDMATCH;
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_MATCHMNGR;
    teamTableLink($img, $url, $rightlink);
    echo "<th><b>"._AM_TEAMDATE."</b></th><th><b>"._AM_TEAMOPPONENT."</b></th><th><b>"._AM_TEAMMATCHTYPE."</b></th><th><b>"._AM_TEAMRESULT."</b></th><th></th><th></th>";
    echo "</tr>\n";
    while ( $myrow = $xoopsDB->fetchArray($result) ) {
        if (!isset($class) || $class == 'even') {
            $class = 'odd';
        }
        else {
            $class = 'even';
        }
        $mid=$myrow["matchid"];
        $mdate = $myrow["matchdate"];
        $mdate=date(_SHORTDATESTRING, $mdate);
        echo "<tr class='".$class."'><td>". $mdate ."</td><td>".$myrow["opponent"] ."</td><td>". $myrow["ladder"] ."</td><td>";
        echo $myrow["matchresult"] ."</td>";
        echo "<td><form method='post' action='../index.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMatch\">";
        echo "<input type=hidden name='op' value='matchform'>";
        echo "<input type=hidden name='mid' value='".$mid."'>";
        echo "<input type=submit value='"._AM_EDIT."'></form></td>";
        echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
        echo "<input type=hidden name='matchid' value='".$mid."'>";
        echo "<input type=hidden name='op' value='deletematch'>
                       <input type=submit value='"._AM_DELETE."'></form></td>";
        echo "</tr>\n";
    }
    echo "</table></td></tr></table>";
    break;

    case "rankmanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/managerank.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_TEAMRANKMNGR;
    teamTableLink($img, $url);
    echo "<td colspan='5'>";
    if (isset($_POST['rankid'])) {
        rankform($_POST['rankid']);
    }
    else {
        rankform();
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMRANKID."</b></th><th><b>"._AM_TEAMRANK."</b></th><th><b>"._AM_TEAMTACTICSRANK."</b></th><th><b>"._AM_TEAMMATCHRANK."</b></th><th><b>"._AM_TEAMRANKCOLOR."</th><th></th><th></th>";
    $sql = "SELECT rankid, rank, matches, tactics, color FROM ".$xoopsDB->prefix("team_rank");
    $result = $xoopsDB->query($sql);
    while ( $myrow = $xoopsDB->fetchArray($result) ) {
        if (isset($class) && $class == 'even') {
            $class = 'odd';
        }
        else {
            $class = 'even';
        }
        $rankid=$myrow["rankid"];
        $rankname = $myrow["rank"];
        $tactics = $myrow["tactics"];
        if ($tactics == 1) {
            $tactics = "Yes";
        }
        else {
            $tactics = "No";
        }
        $matches = $myrow["matches"];
        if ($matches == 1) {
            $matches = "Yes";
        }
        else {
            $matches = "No";
        }
        $color = $myrow["color"];
        echo "</tr><tr class='".$class."'><td>". $rankid ."</td><td>".$rankname."</td><td>". $tactics ."</td>";
        echo "<td>".$matches."</td><td>".$color;
        echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMatch\">";
        echo "<input type=hidden name='op' value='rankmanager'>";
        echo "<input type=hidden name='rankid' value='".$rankid."'>";
        echo "<input type=submit value='"._AM_EDIT."'></form></td>";
        echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
        echo "<input type=hidden name='rankid' value=".$rankid.">";
        echo "<input type=hidden name='op' value='deleterank'>
                       <input type=submit value='"._AM_DELETE."'></form></td>";
    }
    teamTableClose();
    break;

    case "posorderedit":
    foreach ($_POST['posorder'] as $posid => $posorder) {
        $sql = "UPDATE ".$xoopsDB->prefix("team_positions")." SET posorder=".intval($posorder)." WHERE posid=".intval($posid);
        $xoopsDB->query($sql);
    }

    case "laddermanager":
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_ladders");
    $result = $xoopsDB->query($sql);
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/ladders.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_LADDERMNGR;
    teamTableLink($img, $url);
    echo "<td colspan=2>";
    if (isset($_GET['ladderid'])) {
        ladderedit($_GET['ladderid']);
    }
    else {
        ladderedit("");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMLADDERNAME."</b></th><th><b>"._AM_TEAMLADDERVISIBLE."</b></th><th>"._AM_TEAMSCORESVISIBLE."</th><th>"._AM_EDIT."</th><th>"._AM_DELETE."</th>";
    while ( $myrow = $xoopsDB->fetchArray($result) ) {
        if (isset($class) && $class == 'even') {
            $class = 'odd';
        }
        else {
            $class = 'even';
        }
        $ladderid=$myrow["ladderid"];
        $laddername = $myrow["ladder"];
        $laddervisible = $myrow["visible"] == 0 ? _AM_NO : _AM_YES;
        $scoresvisible = $myrow["scoresvisible"] == 0 ? _AM_NO : _AM_YES;
        echo "<tr class='".$class."'><td>".$laddername."</td><td>";
        echo $laddervisible ."</td><td>";
        echo $scoresvisible ."</td>";
        echo "<td><a href='index.php?op=laddermanager&ladderid=".$ladderid."'>";
        echo ""._AM_EDIT."</td>";
        echo "<td><a href='index.php?op=deleteladder&ladderid=".$ladderid."'>";
        echo ""._AM_DELETE."</td>";
        echo "</tr>\n";
    }
    echo "<tr><td colspan=3></td><td colspan=3></td>";
    teamTableClose();
    break;

    case "positionmanager":
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_positions")." ORDER BY postype ASC, posorder ASC";
    $result = $xoopsDB->query($sql);
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/positions.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_POSMNGR;
    teamTableLink($img, $url);
    echo "<td colspan=4>";
    if (isset($_GET['posid'])) {
        posedit($_GET['posid']);
    }
    else {
        posedit("");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMPOSITIONSHORT."</b></th><th><b>"._AM_TEAMPOSITIONNAME."</b></th><th><b>"._AM_TEAMTYPE2."</b></th><th><b>"._AM_TEAMORDER."</b></th><th>"._AM_EDIT."</th><th>"._AM_DELETE."</th>";
    echo "<form method='post' action='index.php?op=posorderedit'></tr>\n";
    while ( $myrow = $xoopsDB->fetchArray($result) ) {
        if (isset($class) && $class == 'even') {
            $class = 'odd';
        }
        else {
            $class = 'even';
        }
        $posid=$myrow["posid"];
        $posname = $myrow["posname"];
        $posshort = $myrow["posshort"];
        $postype = $myrow["postype"];
        $posorder = $myrow["posorder"];
        echo "<tr class='".$class."'><td>".$posshort."</td><td>";
        echo $posname ."</td>";
        echo "<td>".$postype."</td>";
        echo "<td><input type=text size='4' name='posorder[".$posid."]' value='".$posorder."'></td>";
        echo "<td><a href='index.php?op=positionmanager&posid=".$posid."'>";
        echo ""._AM_EDIT."</td>";
        echo "<td><a href='index.php?op=deletepos&posid=".$posid."'>";
        echo ""._AM_DELETE."</td>";
        echo "</tr>\n";
    }
    echo "<tr><td colspan=3></td><td colspan=3><input type=submit value='Set Order'></form></td>";
    teamTableClose();
    break;

    case "setdefault":
    $team =& $team_handler->get($_POST['teamid']);
    if ($team_handler->setDefault($team)) {
        redirect_header("index.php?op=teammanager",3,$team->getVar('teamname')." "._AM_TEAMSETASDEFAULTTEAM."");
        break;
    }
    else {
        redirect_header("index.php?op=teammanager",2,_AM_TEAMERRORDEFAULTTEAMNOTCHANGED);
    }
    break;

    case "mappoolmanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/maps.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_MAPMNGR;
    teamTableLink($img, $url);
    echo "<td colspan='2'>";
    if (isset($_POST['mapid'])) {
        mapedit($_POST['mapid']);
    }
    else {
        mapedit("");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMMAPID."</b></th><th><b>"._AM_TEAMMAPNAME."</b></th><th><b>"._AM_EDIT."</b></th><th><b>"._AM_DELETE."</b></th>";
    $mapsql = "SELECT * FROM ".$xoopsDB->prefix("team_mappool")." ORDER BY mapname ASC";
    if ( $result = $xoopsDB->query($mapsql) ) {
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            }
            else {
                $class = 'even';
            }
            $mapid=$myrow["mapid"];
            $mapname = $myrow["mapname"];
            echo "</tr><tr class='".$class."'><td>".$mapid."</td><td>";
            echo $mapname ."</td>";
            echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMap\">";
            echo "<input type=hidden name='mapid' value='".$mapid."'>";
            echo "<input type=hidden name='op' value='mappoolmanager'>";
            echo "<input type=submit value='"._AM_EDIT."'></form></td>";
            echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
            echo "<input type=hidden name='op' value='deletemap'>";
            echo "<input type=hidden name='mapid' value='".$mapid."'>";
            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
        }
    }
    teamTableClose();
    break;

    case "teammanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/maps.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_TEAMMNGR;
    $rightlink[0]["url"] = "addteam.php";
    $rightlink[0]["text"] = _AM_TEAMADDTEAM;
    teamTableLink($img, $url, $rightlink);
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMTEAMID."</b></th><th><b>"._AM_TEAMNAME."</b></th><th><b>"._AM_TEAMTYPE2."</b></th><th><b>"._AM_TEAMMAPSMATCH."</b></th><th><b>"._AM_TEAMDEFAULT."</b></th><th><b>"._AM_DELETE."</b></th>";
    $teams = $team_handler->getObjects(null, false, false);
    foreach ($teams as $myrow) {
        if (isset($class) && $class == 'even') {
            $class = 'odd';
        }
        else {
            $class = 'even';
        }
        $teamid=$myrow["teamid"];
        $teamname = $myrow["teamname"];
        $teamtype = $myrow["teamtype"];
        $maps = $myrow["maps"];
        echo "</tr><tr class='".$class."'><td>".$teamid."</td><td>";
        echo "<a href='teamadmin.php?teamid=".$teamid."'>";
        echo $teamname ."</a></td>";
        echo "<td>".$teamtype."</td>";
        echo "<td>".$maps."</td>";
        echo "<td>";
        if ($myrow["defteam"]==1) {
            echo "Default";
        }
        else {
            echo "<form method='post' action='index.php?op=setdefault' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyTeam\">";
            echo "<input type=hidden name='teamid' value='".$teamid."'>";
            echo "<input type=submit value='Set Default'></form>";
        }
        echo "</td>";
        echo "<td><form method='post' action='index.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
        echo "<input type=hidden name='teamid' value='".$teamid."'>";
        echo "<input type=hidden name='op' value='deleteteam'>
                       <input type=submit value='"._AM_DELETE."'></form></td>";
    }
    teamTableClose();
    break;

    case "saveserver":
    if ($_POST['action']=="Add") {
        $sql = "INSERT INTO ".$xoopsDB->prefix("team_server")." (servername, serverip, serverport) VALUES (".$xoopsDB->quoteString($_POST['servername']).", ".$xoopsDB->quoteString($_POST['serverip']).", ".intval($_POST['serverport']).")";
        $comment = $_POST['servername']." "._AM_TEAMADDED."";
    }
    elseif ($_POST['action'] == "Edit") {
        $sql = "UPDATE ".$xoopsDB->prefix("team_server")." SET serverip = ".$xoopsDB->quoteString($_POST['serverip']).", servername=".$xoopsDB->quoteString($_POST['servername']).", serverport=".intval($_POST['serverport'])."  WHERE serverid=".intval($_POST['serverid']);
        $comment = $_POST['servername']." "._AM_TEAMEDITED."";
    }
    if (!$xoopsDB->query($sql)) {
        $comment = _AM_TEAMERRORWHILESAVINGSERVER;
    }
    echo $comment;

    case "servermanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/servers.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_SERVERMNGR;
    teamTableLink($img, $url);
    echo "<td>";
    if (isset($_POST['serverid'])) {
        serverForm("Edit", $_POST['serverid']);
    }
    else {
        serverForm("Add", "");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMSERVERNAME."</b></th><th><b>"._AM_TEAMSERVERIP."</b></th><th><b>"._AM_TEAMSERVERPORT."</b></th><th><b>"._AM_EDIT."</b></th><th><b>"._AM_DELETE."</b></th>";
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_server")." ORDER BY servername ASC";
    if ( $result = $xoopsDB->query($sql) ) {
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            }
            else {
                $class = 'even';
            }
            $serverid=$myrow["serverid"];
            $servername = $myrow["servername"];
            $serverip = $myrow["serverip"];
            $serverport = $myrow["serverport"];
            echo "</tr><tr class='".$class."'><td>".$servername."</td><td>";
            echo $serverip ."</td>";
            echo "<td>".$serverport."</td>";
            echo "<td><form method='post' action='index.php'>";
            echo "<input type=hidden name='op' value='servermanager'>";
            echo "<input type=hidden name='serverid' value='".$serverid."'>";
            echo "<input type=submit value='"._AM_EDIT."'></form></td>";
            echo "<td><form method='post' action='index.php'>";
            echo "<input type=hidden name='serverid' value='".$serverid."'>";
            echo "<input type=hidden name='op' value='deleteserver'>";
            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
        }
    }
    teamTableClose();
    break;

    case "deletesize":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['size_id'])) {
            redirect_header('index.php?op=default',2,_AM_EMPTYNODELETE);
            break;
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_sizes")." WHERE sizeid=".intval($_POST['size_id']);
        if ($xoopsDB->query($sql)) {
            redirect_header("index.php?op=sizemanager",3,_AM_TEAMSIZEDELETED);
            break;
        }
        else {
            redirect_header("index.php?op=sizemanager",3,_AM_TEAMERRSIZENOTDEL);
            break;
        }
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deletesize', 'size_id' => $_POST['size_id'], 'ok' => 1), 'index.php?op=sizemanager', _AM_RUSUREDELSIZE);
    }
    break;

    case "savesize":
    $sql = "INSERT INTO ".$xoopsDB->prefix("team_sizes")." (size) VALUES (".$xoopsDB->quoteString($_POST['size']).")";
    $comment = $_POST['size']." "._AM_TEAMADDED."";
    if (!$xoopsDB->query($sql)) {
        $comment = _AM_TEAMERRORWHILESAVINGSIZE;
    }
    echo $comment;

    case "sizemanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/teamsizes.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_TEAMSIZEMNGR;
    teamTableLink($img, $url);
    echo "<td>";
    if (isset($_GET['size_id'])) {
        addSizeForm("Edit", $_GET['size_id']);
    }
    else {
        addSizeForm("Add", "");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMSIZEID."</b></th><th><b>"._AM_TEAMSIZES."</b></th><th><b>"._AM_DELETE."</b></th>";
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_sizes")." ORDER BY size ASC";
    if ( $result = $xoopsDB->query($sql) ) {
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            }
            else {
                $class = 'even';
            }
            $size_id=$myrow["sizeid"];
            $size = $myrow["size"];
            echo "</tr><tr class='".$class."'><td>".$size_id."</td><td>";
            echo $size ."</td>";
            echo "<td><form method='post' action='index.php'>";
            echo "<input type=hidden name='size_id' value='".$size_id."'>";
            echo "<input type=hidden name='op' value='deletesize'>";
            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
            echo "</tr>\n";
        }
    }
    teamTableClose();
    break;

    case "deleteside":
    if ( !empty($_POST['ok']) ) {
        if (empty($_POST['side_id'])) {
            redirect_header('index.php?op=default',2,_AM_EMPTYNODELETE);
            exit();
        }
        $sql = "DELETE FROM ".$xoopsDB->prefix("team_sides")." WHERE sideid=".intval($_POST['side_id']);
        if ($xoopsDB->query($sql)) {
            redirect_header("index.php?op=sidemanager",3,_AM_TEAMSIDEDELETED);
        }
        else {
            redirect_header("index.php?op=sidemanager",3,_AM_TEAMERRSIDENOTDEL);
        }
    }
    else {
        echo "<h4>"._AM_CONFIG."</h4>";
        xoops_confirm(array('op' => 'deleteside', 'side_id' => $_POST['side_id'], 'ok' => 1), 'index.php?op=sidemanager', _AM_RUSUREDELSIDE);
    }
    break;

    case "saveside":
    $sql = "INSERT INTO ".$xoopsDB->prefix("team_sides")." (side, sideshort) VALUES (".$xoopsDB->quoteString($_POST['side']).", ".$xoopsDB->quoteString($_POST['sideshort']).")";
    $comment = $_POST['side']." "._AM_TEAMADDED."";
    if (!$xoopsDB->query($sql)) {
        $comment = _AM_TEAMERRORWHILESAVINGSIDE;
    }
    echo $comment;

    case "sidemanager":
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/teamsides.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_TEAMSIDEMNGR;
    teamTableLink($img, $url);
    echo "<td>";
    if (isset($_GET['side_id'])) {
        addSideForm("Edit", $_GET['side_id']);
    }
    else {
        addSideForm("Add", "");
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
    echo "<th><b>"._AM_TEAMSIDEID."</b></th><th><b>"._AM_TEAMSIDES."</b></th><th><b>"._AM_TEAMSIDESHORT."</th><th><b>"._AM_DELETE."</b></th>";
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_sides")." ORDER BY side ASC";
    if ( $result = $xoopsDB->query($sql) ) {
        while ( $myrow = $xoopsDB->fetchArray($result) ) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            }
            else {
                $class = 'even';
            }
            $side_id=$myrow["sideid"];
            $side = $myrow["side"];
            $sideshort = $myrow["sideshort"];
            echo "</tr><tr class='".$class."'><td>".$side_id."</td><td>";
            echo $side ."</td>";
            echo "<td>".$sideshort."</td>";
            echo "<td><form method='post' action='index.php'>";
            echo "<input type=hidden name='side_id' value='".$side_id."'>";
            echo "<input type=hidden name='op' value='deleteside'>";
            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
        }
    }
    teamTableClose();
    break;

    case "layoutmanager":
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_layout");
    $result = $xoopsDB->query($sql);
    $myrow = $xoopsDB->fetchArray($result);
    $img = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/managelayout.gif";
    $url[0]["url"] = "index.php";
    $url[0]["text"] = _AM_TEAMCONFIG;
    $url[1]["url"] = "";
    $url[1]["text"] = _AM_TEAMLAYOUTMNGR;
    teamTableLink($img, $url);
    echo "<td colspan='5'>";
    layoutform($myrow);
    echo "</td>";
    teamTableClose();
    break;


    case "default":
    default:
    echo "<h4>"._AM_TEAMCONFIG."</h4>";
    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo " - <b><a href='index.php?op=teammanager'>"._AM_TEAMMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=matchmanager'>"._AM_MATCHMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=layoutmanager'>"._AM_TEAMLAYOUTMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=rankmanager'>"._AM_TEAMRANKMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=mappoolmanager'>"._AM_MAPMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=positionmanager'>"._AM_POSMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=sizemanager'>"._AM_TEAMSIZEMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=sidemanager'>"._AM_TEAMSIDEMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=servermanager'>"._AM_SERVERMNGR."</a></b><br /><br />";
    echo " - <b><a href='index.php?op=laddermanager'>"._AM_LADDERMNGR."</a></b><br /><br />";
    echo "</td></tr></table>";
    break;
}
xoops_cp_footer();
?>
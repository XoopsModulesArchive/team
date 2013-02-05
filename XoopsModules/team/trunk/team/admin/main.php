<?php
// $Id: main.php,v 1.13 2006/06/09 14:32:47 mithyt2 Exp $
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
require_once "admin_header.php";
include '../../../include/cp_header.php';
include('../functions.php');
include('functions.php');

$op    = isset($_GET['op']) ? $_GET['op'] : 'default';
$posid = isset($_GET['posid']) ? intval($_GET['posid']) : 'default';
$op    = isset($_POST['op']) ? $_POST['op'] : $op;
if (!isset($_POST['action'])) {
    $action = "";
}

function ladderedit($id = "")
{
    global $xoopsDB;
    $op            = "addladder";
    $action        = "Add";
    $laddername    = "";
    $ladderid      = "";
    $laddervisible = 1;
    $scoresvisible = 0;
    if ($id) {
        $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_ladders") . " WHERE ladderid=" . intval($id);
        $result = $xoopsDB->query($sql);
        while ($myrow = $xoopsDB->fetchArray($result)) {
            $ladderid      = $myrow["ladderid"];
            $laddername    = $myrow["ladder"];
            $laddervisible = $myrow["visible"];
            $scoresvisible = $myrow["scoresvisible"];
            $op            = "editladder";
            $action        = "Edit";
        }
    }
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $pform       = new XoopsThemeForm($action . " Ladder", "ladderform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('', '');
    $submit      = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden   = new XoopsFormHidden('op', $op);
    $name        = new XoopsFormText(_AM_TEAMLADDERNAME, 'laddername', 32, 32, $laddername, 'E');
    $visible     = new XoopsFormRadioYN(_AM_TEAMLADDERVISIBLE, 'laddervisible', $laddervisible, _AM_YES, _AM_NO);
    $scores      = new XoopsFormRadioYN(_AM_TEAMSCORESVISIBLE, 'scoresvisible', $scoresvisible, _AM_YES, _AM_NO);
    $id          = new XoopsFormHidden('ladderid', $ladderid);
    $button_tray->addElement($submit);
    $pform->addElement($name);
    $pform->addElement($visible);
    $pform->addElement($scores);
    $pform->addElement($id);
    $pform->addElement($button_tray);
    $pform->addElement($op_hidden);
    $pform->display();
}

function posedit($id = "")
{
    global $xoopsDB;
    $op       = "addpos";
    $action   = "Add";
    $posid    = "";
    $postype  = "Pos";
    $posname  = "";
    $posshort = " ";
    if ($id) {
        $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_positions") . " WHERE posid=" . intval($id);
        $result = $xoopsDB->query($sql);
        while ($myrow = $xoopsDB->fetchArray($result)) {
            $posid    = $myrow["posid"];
            $postype  = $myrow["postype"];
            $posname  = $myrow["posname"];
            $posshort = $myrow["posshort"];
            $op       = "editpos";
            $action   = "Edit";
        }
    }
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $pform       = new XoopsThemeForm($action . " Position", "posform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('', '');
    $submit      = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden   = new XoopsFormHidden('op', $op);
    $name        = new XoopsFormText(_AM_TEAMPOSITIONNAME, 'posname', 35, 35, $posname, 'E');
    $short       = new XoopsFormText(_AM_TEAMPOSITIONSHORT, 'posshort', 20, 20, $posshort, 'E');
    $type        = new XoopsFormRadio(_AM_TEAMPOSITIONTYPE, 'postype', $postype);
    $id          = new XoopsFormHidden('posid', $posid);
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

function mapedit($id = "")
{
    //global $xoopsDB;
    $op      = "addmap";
    $action  = _AM_ADD;
    $mapid   = "";
    $mapname = "";
    if ($id != "") {
        $map_handler = xoops_getmodulehandler('map', 'team');
        $mapArray    = $map_handler->get($id);
//        list($mapid, $mapname) = $mapArray;
        $mapid   = $mapArray->getVar('mapid');
        $mapname = $mapArray->getVar('mapname');
        $op      = "editmap";
        $action  = _AM_EDIT;
    }
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $mform        = new XoopsThemeForm(_AM_MAPMNGR, "mapedit", xoops_getenv('PHP_SELF'));
    $button_tray  = new XoopsFormElementTray('', '');
    $submit       = new XoopsFormButton('', 'select', $action, 'submit');
    $op_hidden    = new XoopsFormHidden('op', $op);
    $mapid_hidden = new XoopsFormHidden('mapid', $mapid);
    $name         = new XoopsFormText(_AM_TEAMNEWMAPNAME, 'mapname', 25, 25, $mapname, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($name);
    $mform->addElement($button_tray);
    $mform->addElement($op_hidden);
    $mform->addElement($mapid_hidden);
    $mform->display();
}

function serverForm($action, $serverid = "")
{
    if ($action == 'Edit') {
        $submittext = _AM_TEAMEDITSERVER;
    } else {
        $submittext = _AM_TEAMADDSERVER;
    }
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $mform         = new XoopsThemeForm(_AM_TEAMADDSERVER, "serverform", xoops_getenv('PHP_SELF'));
    $op_hidden     = new XoopsFormHidden('op', "saveserver");
    $submit        = new XoopsFormButton('', 'submit', $submittext, 'submit');
    $action_hidden = new XoopsFormHidden('action', $action);
    $button_tray   = new XoopsFormElementTray('', '');
    if ($serverid != "") {
        $server          = getServer($serverid);
        $name            = $server["name"];
        $ip              = $server["ip"];
        $port            = $server["port"];
        $serverid_hidden = new XoopsFormHidden('serverid', $serverid);
        $mform->addElement($serverid_hidden);
    } else {
        $name = "Servername";
        $ip   = "IP";
        $port = "Port";
    }
    $name = new XoopsFormText(_AM_TEAMSERVERNAME, 'servername', 30, 30, $name, 'E');
    $ip   = new XoopsFormText(_AM_TEAMSERVERIP, 'serverip', 20, 20, $ip, 'E');
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

function addSizeForm($action = _AM_ADD, $sizeId = "")
{
    global $xoopsDB;
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $size = 0;

    $mform = new XoopsThemeForm(_AM_TEAMADDSIZE, "sizeform", xoops_getenv('PHP_SELF'));

    if ($sizeId != "") {
        $sql           = "SELECT size FROM " . $xoopsDB->prefix("team_sizes") . " WHERE sizeid=" . intval($sizeId);
        $result        = $xoopsDB->query($sql);
        $size          = $xoopsDB->fetchArray($result)['size'];
        $sizeid_hidden = new XoopsFormHidden('sizeid', $sizeId);
        $mform->addElement($sizeid_hidden);
        $action = _AM_EDIT;
    }

    $action_hidden = new XoopsFormHidden('action', $action);
    $op_hidden     = new XoopsFormHidden('op', "savesize");
    $submit        = new XoopsFormButton('', 'submit', $action, 'submit');
    $button_tray   = new XoopsFormElementTray('', '');
    $teamsize      = new XoopsFormText(_AM_TEAMSIZENAME, 'size', 20, 20, $size, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($teamsize);
    $mform->addElement($op_hidden);
    $mform->addElement($action_hidden);
    $mform->addElement($button_tray);
    $mform->display();
}

function addSideForm($action = _AM_ADD, $sideId = "")
{
    global $xoopsDB;
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

    $sideText      = _AM_TEAMADDSIDE;
    $sideShortText = _AM_TEAMSIDESHORT;

    $mform = new XoopsThemeForm(_AM_TEAMADDSIDE, "sideform", xoops_getenv('PHP_SELF'));

    if ($sideId) {
        $sql
                       =
            "SELECT side, sideshort FROM " . $xoopsDB->prefix("team_sides") . " WHERE sideid=" . intval($sideId);
        $result        = $xoopsDB->query($sql);
        $currentSide   = $xoopsDB->fetchArray($result);
        $sideText      = $currentSide['side'];
        $sideShortText = $currentSide['sideshort'];
        $sideid_hidden = new XoopsFormHidden('sizeid', $sideId);
        $mform->addElement($sideid_hidden);
        $action = _AM_EDIT;
    }

    $action_hidden = new XoopsFormHidden('action', $action);
    $op_hidden     = new XoopsFormHidden('op', "saveside");
    $submit        = new XoopsFormButton('', 'submit', $action, 'submit');
    $button_tray   = new XoopsFormElementTray('', '');
    $teamside      = new XoopsFormText(_AM_TEAMSIDENAME, 'side', 12, 20, $sideText, 'E');
    $sideshort     = new XoopsFormText(_AM_TEAMSIDESHORT, 'sideshort', 5, 20, $sideShortText, 'E');
    $button_tray->addElement($submit);
    $mform->addElement($teamside);
    $mform->addElement($sideshort);
    $mform->addElement($op_hidden);
    $mform->addElement($button_tray);
    $mform->addElement($action_hidden);
    $mform->display();
}

function rankform($rankid = "")
{
    global $xoopsDB;
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $rform = new XoopsThemeForm(_AM_TEAMADDRANK, "rankform", xoops_getenv('PHP_SELF'));
    if ($rankid) {
        $sql
                       =
            "SELECT rankid, rank, matches, tactics, color FROM " . $xoopsDB->prefix("team_rank") . " WHERE rankid="
                . intval($rankid);
        $result        = $xoopsDB->query($sql);
        $thisrank      = $xoopsDB->fetchArray($result);
        $action        = "Edit";
        $submittext    = _AM_TEAMEDITRANK;
        $rankid_hidden = new XoopsFormHidden('rankid', $rankid);
        $rform->addElement($rankid_hidden);
    } else {
        $action              = "Add";
        $submittext          = _AM_TEAMADDRANK;
        $thisrank["rank"]    = "Input Rank";
        $thisrank["tactics"] = 0;
        $thisrank["matches"] = 0;
        $thisrank["color"]   = "#007700";
    }
    $op_hidden     = new XoopsFormHidden('op', "saverank");
    $action_hidden = new XoopsFormHidden('action', $action);
    $submit        = new XoopsFormButton('', 'submit', $submittext, 'submit');
    $button_tray   = new XoopsFormElementTray('', '');
    $rank          = new XoopsFormText(_AM_TEAMRANK, 'rank', 20, 20, $thisrank["rank"], 'E');
    $tactics       = new XoopsFormRadioYN(_AM_TEAMTACTICSRANK, 'tactics', $thisrank["tactics"], _YES, _NO);
    $matches       = new XoopsFormRadioYN(_AM_TEAMMATCHRANK, 'matches', $thisrank["matches"], _YES, _NO);
    $color         = new XoopsFormColorPicker(_AM_TEAMRANKCOLOR, 'color', $thisrank["color"]);
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

function layoutform($data)
{
    include XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
    $lform       = new XoopsThemeForm("Edit Layout", "layform", xoops_getenv('PHP_SELF'));
    $button_tray = new XoopsFormElementTray('', '');
    $submit      = new XoopsFormButton('', 'submit', _AM_SAVE, 'submit');
    $op_hidden   = new XoopsFormHidden('op', "savelayout");

    $color_status_active   = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSACTIVE, 'color_status_active', $data['color_status_active']);
    $color_status_inactive = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSINACTIVE, 'color_status_inactive', $data['color_status_inactive'], 'E');
    $color_status_onleave  = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSONLEAVE, 'color_status_onleave', $data['color_status_onleave']);
    $color_match_win       = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSWIN, 'color_match_win', $data['color_match_win']);
    $color_match_loss      = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSLOSS, 'color_match_loss', $data['color_match_loss']);
    $color_match_draw      = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSDRAW, 'color_match_draw', $data['color_match_draw']);
    $color_match_pending   = new XoopsFormColorPicker(_AM_TEAMLAYOUTCOLORSTATUSPENDING, 'color_match_pending', $data['color_match_pending']);
    $color_perfect         = new XoopsFormColorPicker(_AM_TEAMLAYOUTPERFECTCOLOR, 'color_perfect', $data['color_perfect']);
    $color_good            = new XoopsFormColorPicker(_AM_TEAMLAYOUTGOODCOLOR, 'color_good', $data['color_good']);
    $color_warn            = new XoopsFormColorPicker(_AM_TEAMLAYOUTWARNCOLOR, 'color_warn', $data['color_warn']);
    $color_bad             = new XoopsFormColorPicker(_AM_TEAMLAYOUTBADCOLOR, 'color_bad', $data['color_bad']);
    $button_tray->addElement($submit);

    //$lform->addElement($color_status_active0);

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
$team_handler =& xoops_getmodulehandler('team', 'team');
switch ($op) {
    case "savelayout":
        if ($_POST['submit'] == _AM_SAVE) {
            $sql = "UPDATE " . $xoopsDB->prefix("team_layout") . " SET color_status_active = "
                . $xoopsDB->quoteString($_POST['color_status_active']) . "," . " color_status_inactive="
                . $xoopsDB->quoteString($_POST['color_status_inactive']) . "," . " color_status_onleave="
                . $xoopsDB->quoteString($_POST['color_status_onleave']) . "," . " color_match_win="
                . $xoopsDB->quoteString($_POST['color_match_win']) . "," . " color_match_loss="
                . $xoopsDB->quoteString($_POST['color_match_loss']) . "," . " color_match_draw="
                . $xoopsDB->quoteString($_POST['color_match_draw']) . "," . " color_match_pending="
                . $xoopsDB->quoteString($_POST['color_match_pending']) . "," . " color_perfect = "
                . $xoopsDB->quoteString($_POST['color_perfect']) . "," . " color_good = "
                . $xoopsDB->quoteString($_POST['color_good']) . "," . " color_warn = "
                . $xoopsDB->quoteString($_POST['color_warn']) . "," . " color_bad = "
                . $xoopsDB->quoteString($_POST['color_bad']) . " WHERE layoutid = 1";

            if (!$xoopsDB->query($sql)) {
                redirect_header("main.php?op=layoutmanager", 3, _AM_TEAMERRORWHILESAVINGLAYOUT);
            }
            redirect_header("main.php?op=layoutmanager", 3, _AM_TEAMLAYOUTSAVED);
        }
        break;

    case "saveteam":
        if ($_POST['submit'] == "Add") {
            $thisteam =& $team_handler->create();
            $message  = _AM_TEAMTEAMADDED;
        } else {
            $thisteam =& $team_handler->get(intval($_POST['teamid']));
            $message  = _AM_TEAMTEAMEDITED;
        }
        $thisteam->setVar("teamname", $_POST['name']);
        $thisteam->setVar("teamtype", $_POST['type']);
        $thisteam->setVar("maps", $_POST['maps']);
        if (!getDefaultTeam()) {
            $thisteam->setVar('defteam', 1);
        } else {
            $thisteam->setVar('defteam', 0);
        }
        if ($team_handler->insert($thisteam)) {
            redirect_header("teamadmin.php?teamid=" . $thisteam->getVar('teamid'), 3, $_POST['name'] . " " . $message);
            break;
        } else {
            $errors = 1;
        }
        if (isset($errors)) {
            redirect_header("main.php?op=teammanager", 3, _AM_TEAMERRORWHILESAVINGTEAM);
        }
        break;

    case "saverank":
        if ($_POST['action'] == "Add") {
            $sql     = "INSERT INTO " . $xoopsDB->prefix("team_rank") . " (rank, tactics, matches, color) VALUES ("
                . $xoopsDB->quoteString($_POST['rank']) . ", " . intval($_POST['tactics']) . ", "
                . intval($_POST['matches']) . ", " . $xoopsDB->quoteString($_POST['color']) . ")";
            $comment = $_POST['rank'] . " Added";
        } elseif ($_POST['action'] == "Edit") {
            $sql
                     =
                "UPDATE " . $xoopsDB->prefix("team_rank") . " SET rank = " . $xoopsDB->quoteString($_POST['rank'])
                    . ", matches=" . intval($_POST['matches']) . ", tactics=" . intval($_POST['tactics']) . ", color="
                    . $xoopsDB->quoteString($_POST['color']) . "  WHERE rankid=" . intval($_POST['rankid']);
            $comment = $_POST['rank'] . " Edited";
        }
        if (!$xoopsDB->query($sql)) {
            $comment = _AM_TEAMERRORWHILESAVINGRANK;
        }
        redirect_header("main.php?op=rankmanager", 3, $comment);
        break;

    case "addpos":
        $sql = "INSERT INTO " . $xoopsDB->prefix("team_positions") . " (postype, posname, posshort) VALUES ("
            . $xoopsDB->quoteString($_POST['postype']) . ", " . $xoopsDB->quoteString($_POST['posname']) . ", "
            . $xoopsDB->quoteString($_POST['posshort']) . ")";
        $xoopsDB->query($sql);
        redirect_header("main.php?op=positionmanager", 3, _AM_TEAMPOSITIONADDED);
        break;

    case "editpos":
        if (isset($_POST['postype'])) {
            $sql = "UPDATE " . $xoopsDB->prefix("team_positions") . " SET postype="
                . $xoopsDB->quoteString($_POST['postype']) . ", posname=" . $xoopsDB->quoteString($_POST['posname'])
                . ", posshort=" . $xoopsDB->quoteString($_POST['posshort']) . " WHERE posid=" . intval($_POST['posid']);
            $xoopsDB->query($sql);
            redirect_header("main.php?op=positionmanager", 3, _AM_TEAMPOSITIONMODIFIED);
            break;
        }
        break;

    case "deletepos":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['posid'])) {
                redirect_header('main.php?op=positionmanager', 2, _AM_EMPTYNODELETE);
                break;
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_positions") . " WHERE posid=" . intval($_POST['posid']);
            $xoopsDB->query($sql);
            redirect_header("main.php?op=positionmanager", 3, _AM_TEAMPOSITIONDELETED);
            break;
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'    => 'deletepos',
                    'posid' => $_GET['posid'],
                    'ok'    => 1
                ), 'main.php', _AM_RUSUREDELPOS
            );
        }
        break;

    case "addladder":
        $sql = "INSERT INTO " . $xoopsDB->prefix("team_ladders") . " (ladder, visible, scoresvisible) VALUES ("
            . $xoopsDB->quoteString($_POST['laddername']) . ", " . intval($_POST['laddervisible']) . ", "
            . intval($_POST['scoresvisible']) . ")";
        $xoopsDB->query($sql);
        redirect_header("main.php?op=laddermanager", 3, _AM_TEAMLADDERADDED);
        break;

    case "editladder":
        $sql
            =
            "UPDATE " . $xoopsDB->prefix("team_ladders") . " SET ladder=" . $xoopsDB->quoteString($_POST['laddername'])
                . ", visible=" . intval($_POST['laddervisible']) . ", scoresvisible=" . intval($_POST['scoresvisible'])
                . " WHERE ladderid=" . intval($_POST['ladderid']);
        $xoopsDB->query($sql);
        redirect_header("main.php?op=laddermanager", 3, _AM_TEAMLADDERMODIFIED);
        break;

    case "deleteladder":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['ladderid'])) {
                redirect_header('main.php?op=laddermanager', 2, _AM_EMPTYNODELETE);
                break;
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_ladders") . " WHERE ladderid=" . intval($_POST['ladderid']);
            $xoopsDB->query($sql);
            redirect_header("main.php?op=laddermanager", 3, _AM_TEAMLADDERDELETED);
            break;
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'       => 'deleteladder',
                    'ladderid' => intval($_GET['ladderid']),
                    'ok'       => 1
                ), 'main.php', _AM_RUSUREDELLADDER
            );
        }
        break;

    case "addmap":
        $sql = "INSERT INTO " . $xoopsDB->prefix("team_mappool") . " (mapname) VALUES ("
            . $xoopsDB->quoteString($_POST['mapname']) . ")";
        $xoopsDB->query($sql);
        redirect_header("main.php?op=mappoolmanager", 3, $_POST['mapname'] . " " . _AM_TEAMADDEDTOMAPPOOL . "");
        break;

    case "editmap":
        if (isset($_POST['mapname'])) {
            $sql = "UPDATE " . $xoopsDB->prefix("team_mappool") . " SET mapname="
                . $xoopsDB->quoteString($_POST['mapname']) . " WHERE mapid=" . intval($_POST['mapid']);
            $xoopsDB->query($sql);
            redirect_header("main.php?op=mappoolmanager", 3, _AM_TEAMMAPNAMEMODIF);
            break;
        }
        break;

    case "deletemap":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['mapid'])) {
                redirect_header('main.php?op=mappoolmanager', 2, _AM_EMPTYNODELETE);
                exit();
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_mappool") . " WHERE mapid=" . intval($_POST['mapid']);
            $xoopsDB->query($sql);
            redirect_header("main.php?op=mappoolmanager", 3, _AM_TEAMMAPDELFROMPOOLMAP);
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'    => 'deletemap',
                    'mapid' => intval($_GET['mapid']),
                    'ok'    => 1
                ), 'main.php', _AM_RUSUREDELMAP
            );
        }
        break;

    case "deleteteam":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['teamid'])) {
                redirect_header('main.php?op=teammanager', 2, _AM_EMPTYNODELETE);
                exit();
            }
            $teamid = intval($_POST['teamid']);
            $team   = $team_handler->get($teamid);
            $team_handler->delete($team);
            redirect_header("main.php?op=teammanager", 3, _AM_TEAMDELETED);
        } else {
            echo "<h4>" . _AM_TEAMCONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'     => 'deleteteam',
                    'teamid' => intval($_GET['teamid']),
                    'ok'     => 1
                ), 'main.php', _AM_RUSUREDELTEAM
            );
        }
        break;

    case "deleteserver":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['serverid'])) {
                redirect_header('main.php?op=default', 2, _AM_EMPTYNODELETE);
                exit();
            }
            $serverid = intval($_POST['serverid']);
            $sql      = "DELETE FROM " . $xoopsDB->prefix("team_server") . " WHERE serverid=$serverid";
            if ($xoopsDB->query($sql)) {
                $sql = "DELETE FROM " . $xoopsDB->prefix("server_bookings") . " WHERE serverid=$serverid";
                if ($xoopsDB->query($sql)) {
                    redirect_header("main.php?op=servermanager", 3, _AM_TEAMSERVERDELETED);
                } else {
                    redirect_header("main.php?op=servermanager", 3, _AM_TEAMSERVERDELBOOKNOT);
                }
            } else {
                redirect_header("main.php?op=servermanager", 3, _AM_TEAMERRSERVERNOTDEL);
            }
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'       => 'deleteserver',
                    'serverid' => intval($_GET['serverid']),
                    'ok'       => 1
                ), 'main.php', _AM_RUSUREDELSERVER
            );
        }
        break;

    case "deleterank":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['rankid'])) {
                redirect_header('main.php?op=rankmanager', 2, _AM_EMPTYNODELETE);
                exit();
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_rank") . " WHERE rankid=" . intval($_POST['rankid']);
            if ($xoopsDB->query($sql)) {
                redirect_header('main.php?op=rankmanager', 1, _AM_DBUPDATED);
                exit();
            }
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'     => 'deleterank',
                    'rankid' => intval($_GET['rankid']),
                    'ok'     => 1
                ), 'main.php', _AM_RUSUREDELRANK
            );
        }
        break;

    case "deletematch":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['matchid'])) {
                redirect_header('main.php?op=matchmanager', 2, _AM_EMPTYNODELETE);
                break;
            }
            $matchid       = intval($_POST['matchid']);
            $match_handler = xoops_getmodulehandler('match', 'team');
            $match         = $match_handler->get($matchid);
            $match_handler->delete($match);
            redirect_header('main.php?op=matchmanager', 1, _AM_DBUPDATED);
            break;
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'      => 'deletematch',
                    'matchid' => intval($_GET['matchid']),
                    'ok'      => 1
                ), 'main.php', _AM_RUSUREDEL
            );
        }
        break;

    case "matchmanager":
        if (isset($_POST['teamid'])) {
            $teamid = intval($_POST['teamid']);
            $sql
                    =
                "SELECT * FROM " . $xoopsDB->prefix("team_matches") . " WHERE teamid=$teamid ORDER BY matchdate DESC";
            $team   = getTeam($teamid);
        } else {
            $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_matches") . " ORDER BY matchdate DESC";
            $teamid = getDefaultTeam();
        }
        $result = $xoopsDB->query($sql);

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=matchmanager');
        $indexAdmin->addItemButton(_AM_TEAMADDMATCH, '../index.php?op=matchform', 'add');
        echo $indexAdmin->renderButton('left', '');

        teamTableClose();
        teamTableOpen();

        echo"<th><b>" . _AM_TEAMDATE . "</b></th><th><b>" . _AM_TEAMOPPONENT . "</b></th><th><b>" . _AM_TEAMMATCHTYPE
            . "</b></th><th><b>" . _AM_TEAMRESULT . "</b></th><th><b>" . _AM_ACTION . "</b></th>";
        echo "</tr>\n";


        while ($myrow = $xoopsDB->fetchArray($result)) {
            if (!isset($class) || $class == 'even') {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $mid   = $myrow["matchid"];
            $mdate = $myrow["matchdate"];
            $mdate = date(_SHORTDATESTRING, $mdate);
            echo"<tr align = 'center' class='" . $class . "'><td>" . $mdate . "</td><td>" . $myrow["opponent"]
                . "</td><td>" . $myrow["ladder"] . "</td><td>";
            echo $myrow["matchresult"] . "</td>";


            echo "<td>";
//        echo "<td><form method='post' action='../main.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMatch\">";
//        echo "<input type=hidden name='op' value='matchform'>";
//        echo "<input type=hidden name='mid' value='".$mid."'>";
//        echo "<input type=submit value='"._AM_EDIT."'></form></td>";
//
//        echo "<td><form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
//        echo "<input type=hidden name='matchid' value='".$mid."'>";
//        echo "<input type=hidden name='op' value='deletematch'>
//                       <input type=submit value='"._AM_DELETE."'></form></td>";


            echo"<a href='../index.php?op=matchform&mid=$mid' title=''" . _EDIT . "><img src=" . $pathIcon16
                . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
<a href='main.php?op=deletematch&matchid=$mid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
            echo "</td>";
            echo "</tr>\n";
        }
        echo "</table></td></tr></table>";
        break;

    case "rankmanager":

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=rankmanager');


        echo "<td colspan='5'>";
        if (isset($_GET['rankid'])) {
            rankform(intval($_GET['rankid']));
        } else {
            rankform();
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMRANKID . "</b></th><th><b>" . _AM_TEAMRANK . "</b></th><th><b>" . _AM_TEAMTACTICSRANK
            . "</b></th><th><b>" . _AM_TEAMMATCHRANK . "</b></th><th><b>" . _AM_TEAMRANKCOLOR . "</th><th>" . _AM_ACTION
            . "</th>";
        $sql    = "SELECT rankid, rank, matches, tactics, color FROM " . $xoopsDB->prefix("team_rank");
        $result = $xoopsDB->query($sql);
        while ($myrow = $xoopsDB->fetchArray($result)) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $rankid   = $myrow["rankid"];
            $rankname = $myrow["rank"];
            $tactics  = $myrow["tactics"];
            if ($tactics == 1) {
                $tactics = "Yes";
            } else {
                $tactics = "No";
            }
            $matches = $myrow["matches"];
            if ($matches == 1) {
                $matches = "Yes";
            } else {
                $matches = "No";
            }
            $color = $myrow["color"];

            echo"</tr><tr align = 'center' class='" . $class . "'><td>" . $rankid . "</td><td>" . $rankname
                . "</td><td>" . $tactics . "</td>";
            echo "<td>" . $matches . "</td>";
//        echo "<td>".$color."</td>";
            echo"<td align='center'><span style=\"background-color:" . $color . "\">&nbsp;&nbsp;&nbsp;</span> -> "
                . $color . "</td>";

            echo "<td>";
//        echo "<td><form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMatch\">";
//        echo "<input type=hidden name='op' value='rankmanager'>";
//        echo "<input type=hidden name='rankid' value='".$rankid."'>";
//        echo "<input type=submit value='"._AM_EDIT."'></form></td>";
//        echo "<td><form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
//        echo "<input type=hidden name='rankid' value=".$rankid.">";
//        echo "<input type=hidden name='op' value='deleterank'> <input type=submit value='"._AM_DELETE."'></form></td>";

            echo"<a href='main.php?op=rankmanager&rankid=$rankid' title=''" . _EDIT . "><img src=" . $pathIcon16
                . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
<a href='main.php?op=deleterank&rankid=$rankid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
            echo "</td>";
            echo "</tr>\n";
        }
        teamTableClose();
        break;

    case "posorderedit":
        foreach ($_POST['posorder'] as $posid => $posorder) {
            $sql
                =
                "UPDATE " . $xoopsDB->prefix("team_positions") . " SET posorder=" . intval($posorder) . " WHERE posid="
                    . intval($posid);
            $xoopsDB->query($sql);
        }
        redirect_header("main.php?op=positionmanager", 3, _AM_TEAMPOSITIONMODIFIED);
        break;

    case "laddermanager":
        $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_ladders");
        $result = $xoopsDB->query($sql);

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=laddermanager');

        echo "<td colspan=2>";
        if (isset($_GET['ladderid'])) {
            ladderedit(intval($_GET['ladderid']));
        } else {
            ladderedit("");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMLADDERNAME . "</b></th><th><b>" . _AM_TEAMLADDERVISIBLE . "</b></th><th>"
            . _AM_TEAMSCORESVISIBLE . "</th><th>" . _AM_ACTION . "</th>";
        while ($myrow = $xoopsDB->fetchArray($result)) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $ladderid      = $myrow["ladderid"];
            $laddername    = $myrow["ladder"];
            $laddervisible = $myrow["visible"] == 0 ? _AM_NO : _AM_YES;
            $scoresvisible = $myrow["scoresvisible"] == 0 ? _AM_NO : _AM_YES;
            echo "<tr align = 'center' class='" . $class . "'><td>" . $laddername . "</td><td>";
            echo $laddervisible . "</td><td>";
            echo $scoresvisible . "</td>";

            echo "<td>";
//        echo "<td><a href='main.php?op=laddermanager&ladderid=".$ladderid."'>";
//        echo ""._AM_EDIT."</td>";
//        echo "<td><a href='main.php?op=deleteladder&ladderid=".$ladderid."'>";
//        echo ""._AM_DELETE."</td>";
//        echo "</tr>\n";
            echo"<a href='main.php?op=laddermanager&ladderid=$ladderid' title=''" . _EDIT . "><img src=" . $pathIcon16
                . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
<a href='main.php?op=deleteladder&ladderid=$ladderid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
            echo "</td>";
            echo "</tr>\n";

        }
        echo "<tr><td colspan=3></td><td colspan=3></td>";
        teamTableClose();
        break;

    case "positionmanager":
        $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_positions") . " ORDER BY postype ASC, posorder ASC";
        $result = $xoopsDB->query($sql);

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=positionmanager');

        echo "<td colspan=4>";
        if (isset($_GET['posid'])) {
            posedit(intval($_GET['posid']));
        } else {
            posedit("");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMPOSITIONSHORT . "</b></th><th><b>" . _AM_TEAMPOSITIONNAME . "</b></th><th><b>"
            . _AM_TEAMTYPE2 . "</b></th><th><b>" . _AM_TEAMORDER . "</b></th><th>" . _AM_ACTION . "</th>";
        echo "<form method='post' action='main.php?op=posorderedit'></tr>\n";
        while ($myrow = $xoopsDB->fetchArray($result)) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $posid    = $myrow["posid"];
            $posname  = $myrow["posname"];
            $posshort = $myrow["posshort"];
            $postype  = $myrow["postype"];
            $posorder = $myrow["posorder"];
            echo "<tr align = 'center' class='" . $class . "'><td>" . $posshort . "</td><td>";
            echo $posname . "</td>";
            echo "<td>" . $postype . "</td>";
            echo "<td><input type=text size='4' name='posorder[" . $posid . "]' value='" . $posorder . "'></td>";


            echo "<td>";
//        echo "<td><a href='main.php?op=positionmanager&posid=".$posid."'>";
//        echo ""._AM_EDIT."</td>";
//        echo "<td><a href='main.php?op=deletepos&posid=".$posid."'>";
//        echo ""._AM_DELETE."</td>";


            echo"<a href='main.php?op=positionmanager&posid=$posid' title=''" . _EDIT . "><img src=" . $pathIcon16
                . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
<a href='main.php?op=deletepos&posid=$posid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
            echo "</td>";


            echo "</tr>\n";

        }
        echo "<tr><td colspan=3></td><td colspan=3><input type=submit value='Set Order'></form></td>";
        teamTableClose();
        break;

    case "setdefault":
        $team =& $team_handler->get(intval($_POST['teamid']));
        if ($team_handler->setDefault($team)) {
            redirect_header(
                "main.php?op=teammanager", 3, $team->getVar('teamname') . " " . _AM_TEAMSETASDEFAULTTEAM . ""
            );
            break;
        } else {
            redirect_header("main.php?op=teammanager", 2, _AM_TEAMERRORDEFAULTTEAMNOTCHANGED);
        }
        break;

    case "mappoolmanager":

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=mappoolmanager');

        echo "<td colspan='2'>";
        if (isset($_GET['mapid'])) {
            mapedit(intval($_GET['mapid']));
        } else {
            mapedit("");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMMAPID . "</b></th><th><b>" . _AM_TEAMMAPNAME . "</b></th><th><b>" . _AM_ACTION
            . "</b></th>";
        $mapsql = "SELECT * FROM " . $xoopsDB->prefix("team_mappool") . " ORDER BY mapname ASC";
        if ($result = $xoopsDB->query($mapsql)) {
            while ($myrow = $xoopsDB->fetchArray($result)) {
                if (isset($class) && $class == 'even') {
                    $class = 'odd';
                } else {
                    $class = 'even';
                }
                $mapid   = $myrow["mapid"];
                $mapname = $myrow["mapname"];
                echo "</tr><tr align = 'center' class='" . $class . "'><td>" . $mapid . "</td><td>";
                echo $mapname . "</td>";


                echo "<td>";
//            echo "<td><form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyMap\">";
//            echo "<input type=hidden name='mapid' value='".$mapid."'>";
//            echo "<input type=hidden name='op' value='mappoolmanager'>";
//            echo "<input type=submit value='"._AM_EDIT."'></form></td>";
//            echo "<td><form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
//            echo "<input type=hidden name='op' value='deletemap'>";
//            echo "<input type=hidden name='mapid' value='".$mapid."'>";
//            echo "<input type=submit value='"._AM_DELETE."'></form></td>";

                echo"<a href='main.php?op=mappoolmanager&mapid=$mapid' title=''" . _EDIT . "><img src=" . $pathIcon16
                    . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
    <a href='main.php?op=deletemap&mapid=$mapid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                    . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
                echo "</td>";
                echo "</tr>\n";
            }
        }
        teamTableClose();
        break;

    case "teammanager":

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=teammanager');
        $indexAdmin->addItemButton(_AM_TEAMADDTEAM, 'addteam.php', 'add');
        echo $indexAdmin->renderButton('left', '');

        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMTEAMID . "</b></th><th><b>" . _AM_TEAMNAME . "</b></th><th><b>" . _AM_TEAMTYPE2
            . "</b></th><th><b>" . _AM_TEAMMAPSMATCH . "</b></th><th><b>" . _AM_TEAMDEFAULT . "</b></th><th><b>"
            . _AM_ACTION . "</b></th>";
        $teams = $team_handler->getObjects(null, false, false);
        foreach ($teams as $myrow) {
            if (isset($class) && $class == 'even') {
                $class = 'odd';
            } else {
                $class = 'even';
            }
            $teamid   = $myrow["teamid"];
            $teamname = $myrow["teamname"];
            $teamtype = $myrow["teamtype"];
            $maps     = $myrow["maps"];
            echo "</tr><tr align='center' class='" . $class . "'><td>" . $teamid . "</td><td>";
            echo "<a href='teamadmin.php?teamid=" . $teamid . "'>";
            echo $teamname . "</a></td>";
            echo "<td>" . $teamtype . "</td>";
            echo "<td>" . $maps . "</td>";
            echo "<td>";
            if ($myrow["defteam"] == 1) {
                echo "Default";
            } else {
                echo "<form method='post' action='main.php?op=setdefault' ENCTYPE=\"multipart/form-data\" NAME=\"ModifyTeam\">";
                echo "<input type=hidden name='teamid' value='" . $teamid . "'>";
                echo "<input type=submit value='Set Default'></form>";
            }
            echo "</td>";
            echo "<td>";
//        echo "<form method='post' action='main.php' ENCTYPE=\"multipart/form-data\" NAME=\"delete\">";
//        echo "<input type=hidden name='teamid' value='".$teamid."'>";
//        echo "<input type=hidden name='op' value='deleteteam'>";
//        echo "<input type=submit value='"._AM_DELETE."'></form>
            echo"<a href='teamadmin.php?teamid=$teamid' title=''" . _EDIT . "><img src=" . $pathIcon16 . '/edit.png'
                . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
<a href='main.php?op=deleteteam&teamid=$teamid' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
            echo "</td>";
            echo "</tr>\n";
        }
        teamTableClose();
        break;

    case "saveserver":
        if ($_POST['action'] == "Add") {
            $sql     = "INSERT INTO " . $xoopsDB->prefix("team_server") . " (servername, serverip, serverport) VALUES ("
                . $xoopsDB->quoteString($_POST['servername']) . ", " . $xoopsDB->quoteString($_POST['serverip']) . ", "
                . intval($_POST['serverport']) . ")";
            $comment = $_POST['servername'] . " " . _AM_TEAMADDED . "";
        } elseif ($_POST['action'] == "Edit") {
            $sql     = "UPDATE " . $xoopsDB->prefix("team_server") . " SET serverip = "
                . $xoopsDB->quoteString($_POST['serverip']) . ", servername="
                . $xoopsDB->quoteString($_POST['servername']) . ", serverport=" . intval($_POST['serverport'])
                . "  WHERE serverid=" . intval($_POST['serverid']);
            $comment = $_POST['servername'] . " " . _AM_TEAMEDITED . "";
        }
        if (!$xoopsDB->query($sql)) {
            $comment = _AM_TEAMERRORWHILESAVINGSERVER;
        }
        echo $comment;

    case "servermanager":

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=servermanager');

        echo "<td>";
        if (isset($_GET['serverid'])) {
            serverForm("Edit", intval($_GET['serverid']));
        } else {
            serverForm("Add", "");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMSERVERNAME . "</b></th><th><b>" . _AM_TEAMSERVERIP . "</b></th><th><b>"
            . _AM_TEAMSERVERPORT . "</b></th><th><b>" . _AM_ACTION . "</b></th>";
        $sql = "SELECT * FROM " . $xoopsDB->prefix("team_server") . " ORDER BY servername ASC";
        if ($result = $xoopsDB->query($sql)) {
            while ($myrow = $xoopsDB->fetchArray($result)) {
                if (isset($class) && $class == 'even') {
                    $class = 'odd';
                } else {
                    $class = 'even';
                }
                $serverid   = $myrow["serverid"];
                $servername = $myrow["servername"];
                $serverip   = $myrow["serverip"];
                $serverport = $myrow["serverport"];
                echo "</tr><tr align = 'center' class='" . $class . "'><td>" . $servername . "</td><td>";
                echo $serverip . "</td>";
                echo "<td>" . $serverport . "</td>";


                echo "<td>";
//            echo "<td><form method='post' action='main.php'>";
//            echo "<input type=hidden name='op' value='servermanager'>";
//            echo "<input type=hidden name='serverid' value='".$serverid."'>";
//            echo "<input type=submit value='"._AM_EDIT."'></form></td>";
//            echo "<td><form method='post' action='main.php'>";
//            echo "<input type=hidden name='serverid' value='".$serverid."'>";
//            echo "<input type=hidden name='op' value='deleteserver'>";
//            echo "<input type=submit value='"._AM_DELETE."'></form></td>";

                echo"<a href='main.php?op=servermanager&serverid=$serverid' title=''" . _EDIT . "><img src="
                    . $pathIcon16 . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
    <a href='main.php?op=deleteserver&serverid=$serverid' title=''" . _DELETE . "><img src=" . $pathIcon16
                    . '/delete.png' . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
                echo "</td>";
                echo "</tr>\n";

            }
        }
        teamTableClose();
        break;

    case "deletesize":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['size_id'])) {
                redirect_header('main.php?op=default', 2, _AM_EMPTYNODELETE);
                break;
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_sizes") . " WHERE sizeid=" . intval($_POST['size_id']);
            if ($xoopsDB->query($sql)) {
                redirect_header("main.php?op=sizemanager", 3, _AM_TEAMSIZEDELETED);
                break;
            } else {
                redirect_header("main.php?op=sizemanager", 3, _AM_TEAMERRSIZENOTDEL);
                break;
            }
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'      => 'deletesize',
                    'size_id' => intval($_GET['size_id']),
                    'ok'      => 1
                ), 'main.php?op=sizemanager', _AM_RUSUREDELSIZE
            );
        }
        break;

    case "savesize":

        if ($_POST['action'] == "Add") {
            $sql     = "INSERT INTO " . $xoopsDB->prefix("team_sizes") . " (size) VALUES ("
                . $xoopsDB->quoteString($_POST['size']) . ")";
            $comment = intval($_POST['size']) . " " . _AM_TEAMSIZESADDED . "";
        } elseif ($_POST['action'] == "Edit") {
            $sql
                     =
                "UPDATE " . $xoopsDB->prefix("team_sizes") . " SET size = " . $xoopsDB->quoteString($_POST['size'])
                    . "  WHERE sizeid=" . intval($_POST['sizeid']);
            $comment = sprintf(_AM_TEAM_SIZE_EDITED_OK, intval($_POST['sizeid']));
        }
        if (!$xoopsDB->query($sql)) {
            $comment = _AM_TEAMERRORWHILESAVINGSIZE;
        }
        redirect_header("main.php?op=sizemanager", 3, $comment);
        break;

    case "sizemanager":

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=sizemanager');

        echo "<td>";
        if (isset($_GET['size_id'])) {
            addSizeForm("Edit", intval($_GET['size_id']));
        } else {
            addSizeForm("Add", "");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMSIZEID . "</b></th><th><b>" . _AM_TEAMSIZES . "</b></th><th><b>" . _AM_ACTION
            . "</b></th>";
        $sql = "SELECT * FROM " . $xoopsDB->prefix("team_sizes") . " ORDER BY size ASC";
        if ($result = $xoopsDB->query($sql)) {
            while ($myrow = $xoopsDB->fetchArray($result)) {
                if (isset($class) && $class == 'even') {
                    $class = 'odd';
                } else {
                    $class = 'even';
                }
                $size_id = $myrow["sizeid"];
                $size    = $myrow["size"];
                echo "</tr><tr align = 'center' class='" . $class . "'><td>" . $size_id . "</td><td>";
                echo $size . "</td>";

                echo "<td>";
//            echo "<td><form method='post' action='main.php'>";
//            echo "<input type=hidden name='size_id' value='".$size_id."'>";
//            echo "<input type=hidden name='op' value='deletesize'>";
//            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
                echo"<a href='main.php?op=sizemanager&size_id=$size_id' title=''" . _EDIT . "><img src=" . $pathIcon16
                    . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
    <a href='main.php?op=deletesize&size_id=$size_id' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                    . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
                echo "</td>";
                echo "</tr>\n";

            }
        }
        teamTableClose();
        break;

    case "deleteside":
        if (!empty($_POST['ok'])) {
            if (empty($_POST['side_id'])) {
                redirect_header('main.php?op=default', 2, _AM_EMPTYNODELETE);
                exit();
            }
            $sql = "DELETE FROM " . $xoopsDB->prefix("team_sides") . " WHERE sideid=" . intval($_POST['side_id']);
            if ($xoopsDB->query($sql)) {
                redirect_header("main.php?op=sidemanager", 3, _AM_TEAMSIDEDELETED);
            } else {
                redirect_header("main.php?op=sidemanager", 3, _AM_TEAMERRSIDENOTDEL);
            }
        } else {
            echo "<h4>" . _AM_CONFIG . "</h4>";
            xoops_confirm(
                array(
                    'op'      => 'deleteside',
                    'side_id' => intval($_GET['side_id']),
                    'ok'      => 1
                ), 'main.php?op=sidemanager', _AM_RUSUREDELSIDE
            );
        }
        break;

    case "saveside":

        if ($_POST['action'] == "Add") {
            $sql     = "INSERT INTO " . $xoopsDB->prefix("team_sides") . " (side) VALUES ("
                . $xoopsDB->quoteString($_POST['side']) . ", " . $xoopsDB->quoteString($_POST['sideshort']) . ")";
            $comment = intval($_POST['size']) . " " . _AM_TEAMSIDESADDED . "";
        } elseif ($_POST['action'] == "Edit") {
            $sql
                     =
                "UPDATE " . $xoopsDB->prefix("team_sides") . " SET side = " . $xoopsDB->quoteString($_POST['side'])
                    . ", sideshort = " . $xoopsDB->quoteString($_POST['sideshort']) . "  WHERE sideid="
                    . intval($_POST['sizeid']);
            $comment = sprintf(_AM_TEAM_SIDE_EDITED_OK, $_POST['side']);
        }
        if (!$xoopsDB->query($sql)) {
            $comment = _AM_TEAMERRORWHILESAVINGSIZE;
        }
        redirect_header("main.php?op=sidemanager", 3, $comment);
        break;

    case "sidemanager":
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=sidemanager');

        echo "<td>";
        if (isset($_GET['side_id'])) {
            addSideForm("Edit", intval($_GET['side_id']));
        } else {
            addSideForm("Add", "");
        }
        echo "</td>";
        teamTableClose();
        teamTableOpen();
        echo"<th><b>" . _AM_TEAMSIDEID . "</b></th><th><b>" . _AM_TEAMSIDES . "</b></th><th><b>" . _AM_TEAMSIDESHORT
            . "</th><th><b>" . _AM_ACTION . "</b></th>";
        $sql = "SELECT * FROM " . $xoopsDB->prefix("team_sides") . " ORDER BY side ASC";
        if ($result = $xoopsDB->query($sql)) {
            while ($myrow = $xoopsDB->fetchArray($result)) {
                if (isset($class) && $class == 'even') {
                    $class = 'odd';
                } else {
                    $class = 'even';
                }
                $side_id   = $myrow["sideid"];
                $side      = $myrow["side"];
                $sideshort = $myrow["sideshort"];
                echo "</tr><tr align = 'center' class='" . $class . "'><td>" . $side_id . "</td><td>";
                echo $side . "</td>";
                echo "<td>" . $sideshort . "</td>";


                echo "<td>";
//            echo "<td><form method='post' action='main.php'>";
//            echo "<input type=hidden name='side_id' value='".$side_id."'>";
//            echo "<input type=hidden name='op' value='deleteside'>";
//            echo "<input type=submit value='"._AM_DELETE."'></form></td>";
                echo"<a href='main.php?op=sidemanager&side_id=$side_id' title=''" . _EDIT . "><img src=" . $pathIcon16
                    . '/edit.png' . " alt=' " . _EDIT . " ' title=' " . _EDIT . " ' /></a>
    <a href='main.php?op=deleteside&side_id=$side_id' title=''" . _DELETE . "><img src=" . $pathIcon16 . '/delete.png'
                    . " alt=' " . _DELETE . " ' title=' " . _DELETE . " ' /></a>";
                echo "</td>";
                echo "</tr>\n";

            }
        }
        teamTableClose();
        break;

    case "layoutmanager":
        $sql    = "SELECT * FROM " . $xoopsDB->prefix("team_layout");
        $result = $xoopsDB->query($sql);
        $myrow  = $xoopsDB->fetchArray($result);

        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=layoutmanager');


        echo "<td colspan='5'>";
        layoutform($myrow);
        echo "</td>";
        teamTableClose();
        break;


    case "default":
    default:
        echo "<h4>" . _AM_TEAMCONFIG . "</h4>";
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo " - <b><a href='main.php?op=teammanager'>" . _AM_TEAMMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=matchmanager'>" . _AM_MATCHMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=layoutmanager'>" . _AM_TEAMLAYOUTMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=rankmanager'>" . _AM_TEAMRANKMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=mappoolmanager'>" . _AM_MAPMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=positionmanager'>" . _AM_POSMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=sizemanager'>" . _AM_TEAMSIZEMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=sidemanager'>" . _AM_TEAMSIDEMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=servermanager'>" . _AM_SERVERMNGR . "</a></b><br /><br />";
        echo " - <b><a href='main.php?op=laddermanager'>" . _AM_LADDERMNGR . "</a></b><br /><br />";
        echo "</td></tr></table>";
        break;
}

include_once "admin_footer.php";
<?php
// $Id: tactics.php,v 1.11 2006/06/09 14:32:47 mithyt2 Exp $
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
include '../../mainfile.php';

define('dirname', $xoopsModule->dirname());
include_once XOOPS_ROOT_PATH . '/modules/' . dirname . '/class/player.php';
include_once XOOPS_ROOT_PATH . '/modules/' . dirname . '/functions.php';

$op = isset($_GET['op']) ? $_GET['op'] : 'default';
$tacid = isset($_GET['tacid']) ? intval($_GET['tacid']) : null;
$mapid = isset($_GET['mapid']) ? intval($_GET['mapid']) : null;
$teamsize = isset($_GET['teamsize']) ? intval($_GET['teamsize']) : null;
$teamid = isset($_GET['teamid']) ? intval($_GET['teamid']) : null;
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
$tactics_handler =& xoops_getmodulehandler('tactics');
if ($xoopsUser) {
    $uid = $xoopsUser->getVar("uid");
    if (!isset($teamid)) {
        if (!isset($tacid)) {
            $thisplayer = new Player($uid);
            $team = $thisplayer->getTeams();
            foreach ($team as $statusid => $teamid) {
                $teamid = $teamid;
            }
            if (!isset($teamid)) {
                redirect_header("index.php", 3, _AM_TEAMACCESSDENY);
            }
        }
        else {
            $tactic =& $tactics_handler->get($tacid);
            $teamid = $tactic->getVar('teamid');
        }
    }
    $team_handler =& xoops_getmodulehandler('team');
    $team =& $team_handler->get($teamid);
    if ($team->isTacticsAdmin($uid)) {
        $admin = 'Yes';
    }
    else {
        $admin = 'No';
    }
    if ($team->isTeamMember($uid)) {
        switch($op) {
            case "display":
                if (isset($tacid)) {
                    include(XOOPS_ROOT_PATH.'/header.php');
                    $tactic =& $tactics_handler->get($tacid);
                    $tactic->show();
                }
                else {
                    redirect_header("tactics.php",2,_AM_TEAMNOTACTICSSEL);
                    break;
                }
                break;

            case "mantactics":
                include(XOOPS_ROOT_PATH.'/header.php');
                if (isset($mapid)&& isset($teamid) && isset($teamsize)) {
                    $tactic =& $tactics_handler->getByParams($teamid, $mapid, $teamsize);
                    $action = "Add";
                }
                elseif (isset($tacid)) {
                    $tactic =& $tactics_handler->get($tacid);
                    $action = "Edit";
                }
                $teamsize = $tactic->getVar('teamsize');
                include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
                $mform = new XoopsThemeForm($teamsize." "._AM_TEAMVERSUS." ".$teamsize." "._AM_TEAMTACTICSFOR." ".$team->getVar('teamname')." "._AM_TEAMON." ".(is_object($tactic->map) ? $tactic->map->getVar('mapname') : "??"), "savetactics", xoops_getenv('PHP_SELF'));
                $general = new XoopsFormTextArea(_AM_TEAMGENERALTACS, "general", $tactic->getVar('general'));
                $teampositions = $team->getPositions();
                if ($tactic->getVar('tacid')) {
                    $tacticspositions = $tactic->getPositions();
                }
                $mform->addElement($general);
                $tacpos = "";
                $position_handler =& xoops_getmodulehandler('tacticsposition');
                for ($i=0;$i<$teamsize;$i++) {
                    if (isset($tacticspositions[$i])) {
                        $thispos =& $position_handler->get($tacticspositions[$i]);
                        $thisposid = $thispos->getVar('posid');
                        $thisposdesc = $thispos->getVar('posdesc');
                        $tacpos .= $thispos->getVar('tacposid').":";
                    }
                    else {
                        $thisposid = 0;
                        $thisposdesc = "";
                    }
                    $position_select[$i] = new XoopsFormSelect(_AM_TEAMPOSITION .($i+1), "posid[".$i."]", $thisposid);
                    foreach ($teampositions as $positionid => $positionname) {
                        $position_select[$i]->addOption($positionid, $positionname);
                    }
                    $description[$i] = new XoopsFormTextArea(_AM_TEAMDESCRIPTION, "posdesc[".$i."]", $thisposdesc);
                    $mform->addElement($position_select[$i]);
                    $mform->addElement($description[$i]);
                }
                $button_tray = new XoopsFormElementTray('' ,'');
                $submit = new XoopsFormButton('', 'action', $action, 'Submit');
                $button_tray->addElement($submit);
                if (isset($tacpos)) {
                    $tacpos_hidden = new XoopsFormHidden("tacpos",$tacpos);
                    $mform->addElement($tacpos_hidden);
                }
                $teamsize_hidden = new XoopsFormHidden("teamsize",$tactic->getVar('teamsize'));
                $tacid_hidden = new XoopsFormHidden("tacid", $tactic->getVar('tacid'));
                $mapid_hidden = new XoopsFormHidden("mapid", $tactic->getVar('mapid'));
                $teamid_hidden = new XoopsFormHidden("teamid", $tactic->getVar('teamid'));
                $op_hidden = new XoopsFormHidden("op","savetactics");
                $mform->addElement($teamsize_hidden);
                $mform->addElement($tacid_hidden);
                $mform->addElement($teamid_hidden);
                $mform->addElement($mapid_hidden);
                $mform->addElement($op_hidden);
                $mform->addElement($button_tray);
                $mform->display();
                break;

            case "savetactics":
                if ($action=='Edit') {
                    $tactic =& $tactics_handler->get($tacid);
                }
                else {
                    $tactic =& $tactics_handler->create();
                    $tactic->setVar('mapid', $mapid);
                    $tactic->setVar('teamid', $teamid);
                    $tactic->setVar('teamsize', $teamsize);
                }
                $tactic->setVar('general', $general);
                if ($tactics_handler->insert($tactic)) {
                    $tacid = $tactic->getVar('tacid');
                    $tacpos=explode(":",$tacpos);
                    $tacserrors = 0;
                    $position_handler =& xoops_getmodulehandler('tacticsposition');
                    for ($i=0;$i<$teamsize;$i++) {
                        $thispos =& $position_handler->create();
                        if (isset($tacpos[$i]) && $tacpos[$i]) {
                            $thispos->setVar('tacposid', $tacpos[$i]);
                            $thispos->unsetNew();
                        }
                        $thispos->setVar('posid', $posid[$i]);
                        $thispos->setVar('posdesc', $posdesc[$i]);
                        $thispos->setVar('tacid', $tacid);
                        if (!$position_handler->insert($thispos)) {
                            $tacserros++;
                        }
                    }
                    if ($tacserrors > 0) {
                        redirect_header("tactics.php?teamid=".$teamid, 3, $tacserrors._AM_TEAMTACTICSERRORS);
                    }
                    else {
                        if ($action == 'Add') {
                            redirect_header("tactics.php?teamid=".$teamid,3,_AM_TEAMTACTICSADDED);
                        }
                        else {
                            redirect_header("tactics.php?teamid=".$teamid,3,_AM_TEAMTACTICSEDITED);
                        }
                    }
                }
                else {
                    redirect_header("tactics.php?teamid=".$teamid,3, _AM_TEAMGENERALTACSERROR);
                }
                break;

            case "default":
            default:
                $xoopsOption['template_main'] = "team_tactics_list.html";
                include(XOOPS_ROOT_PATH.'/header.php');
                $teamsizes = $team->getTeamSizes();
                $count = count($teamsizes);
                if ($team->isTeamAdmin($uid)) {
                    $colspan = $count * 2;
                    $firstspan = 2;
                }
                else {
                    $colspan = $count;
                    $firstspan = 1;
                }
                $headspan = $colspan - 1;

                foreach ($teamsizes as $teamsize) {
                    $sizes[] = $teamsize;
                }

                $maps = $team->getTeamMaps();
                foreach (array_keys($maps) as $mapid) {
                    for ($i=0;$i<$count;$i++) {
                        $tactics[$mapid][$sizes[$i]] =& $tactics_handler->getByParams($teamid, $mapid, $sizes[$i]);
                    }
                }
                $team->select();
                $xoopsTpl->assign('teamsizes', $sizes);
                $xoopsTpl->assign('team', $team->toArray());
                $xoopsTpl->assign('headspan', $headspan);
                $xoopsTpl->assign('firstspan', $firstspan);
                $xoopsTpl->assign('maps', $maps);
                $xoopsTpl->assign('tactics', $tactics);
                $xoopsTpl->assign('admin', $admin);
                break;
        }
    }
    else {
        redirect_header("index.php?teamid=".$teamid,3,_AM_TEAMACCESSDENY);
    }
}
else {
    redirect_header("index.php?teamid=".$teamid,3,_AM_TEAMMEMBAREA);
}
include_once XOOPS_ROOT_PATH.'/footer.php';
?>

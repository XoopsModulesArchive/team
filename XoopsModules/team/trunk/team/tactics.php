<?php
// $Id: tactics.php,v 1.6 2004/03/21 15:28:18 mithyt2 Exp $
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
include(XOOPS_ROOT_PATH.'/header.php');
define('dirname', $xoopsModule->dirname());
include_once XOOPS_ROOT_PATH . '/modules/' . dirname . '/class/team.php';
include_once XOOPS_ROOT_PATH . '/modules/' . dirname . '/class/tactics.php';
include_once XOOPS_ROOT_PATH . '/modules/' . dirname . '/class/tacticsposition.php';
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
           $tactic = new Tactics($tacid);
           $teamid = $tactic->teamid();
       }
    }
    $team = new Team($teamid);
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
                     $tactic = new Tactics($tacid);
                     $tactic->show();
                 }
                 else {
                     redirect_header("tactics.php",2,_AM_TEAMNOTACTICSSEL);
                     break;
                 }
                 break;

            case "mantactics":
                 if (isset($mapid)&& isset($teamid) && isset($teamsize)) {
                     $tactic = new Tactics($teamid, $mapid, $teamsize);
                     $action = "Add";
                 }
                 elseif (isset($tacid)) {
                     $tactic = new Tactics($tacid);
                     $action = "Edit";
                 }
                 $teamsize = $tactic->teamsize();
                 include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
                 $mform = new XoopsThemeForm($teamsize." "._AM_TEAMVERSUS." ".$teamsize." "._AM_TEAMTACTICSFOR." ".$team->teamname()." "._AM_TEAMON." ".$tactic->mapname(), "savetactics", xoops_getenv('PHP_SELF'));
                 $general = new XoopsFormTextArea(_AM_TEAMGENERALTACS, "general", $tactic->general());
                 $teampositions = $team->getPositions();
                 if ($tactic->tacid()) {
                     $tacticspositions = $tactic->getPositions();
                 }
                 $mform->addElement($general);
                 $tacpos = "";
                 for ($i=0;$i<$teamsize;$i++) {
                     if (isset($tacticspositions[$i])) {
                         $thispos = new TacticsPosition($tacticspositions[$i]);
                         $thisposid = $thispos->posid();
                         $thisposdesc = $thispos->posdesc();
                         $tacpos .= $thispos->tacposid.":";
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
                 $teamsize_hidden = new XoopsFormHidden("teamsize",$tactic->teamsize());
                 $tacid_hidden = new XoopsFormHidden("tacid", $tactic->tacid());
                 $mapid_hidden = new XoopsFormHidden("mapid", $tactic->mapid());
                 $teamid_hidden = new XoopsFormHidden("teamid", $tactic->teamid());
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
                     $tactic = new Tactics($tacid);
                 }
                 else {
                     $tactic = new Tactics();
                     $tactic->setMapid($mapid);
                     $tactic->setTeamid($teamid);
                     $tactic->setTeamsize($teamsize);
                 }
                 $tactic->setGeneral($general);
                 if ($tacid = $tactic->store()) {
                     $tacpos=explode(":",$tacpos);
                     $tacserrors = 0;
                     for ($i=0;$i<$teamsize;$i++) {
                         $thispos = new TacticsPosition();
                         if ($tacpos[$i]) {
                             $thispos->setTacposid($tacpos[$i]);
                         }
                         $thispos->setPosid($posid[$i]);
                         $thispos->setPosdesc($posdesc[$i]);
                         $thispos->setTacid($tacid);
                         if (!$thispos->store()) {
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
            echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
            echo "<tr><td><table width='100%' border='0' cellpadding='2' cellspacing='1'>";
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
            echo "<tr class='head'><td colspan='".$headspan."'>"._AM_TEAMTACTICSFOR." <b>".$team->teamname()."</b> "._AM_TEAMPLAYING." <b>".$team->teamtype()."</b></td><td align=right><a href='roster.php?teamid=".$teamid."'>"._AM_TEAMROSTER."</a> | <a href='index.php?teamid=".$teamid."' target='_self'>"._AM_TEAMMATCHLIST."</a></td>";
            echo "<tr class='outer' align='center'>";
            foreach ($teamsizes as $teamsizeid => $teamsize) {
                echo "<td colspan=".$firstspan." ><b>";
                echo $teamsize." "._AM_TEAMVERSUS." ".$teamsize;
                echo "</b></td>";
                $sizes[] = $teamsize;
            }
            echo "</tr>";
            $maps = $team->getTeamMapIDs();
            foreach ($maps as $mapid => $mapname) {
                if ($mapname!="-Not Played-") {
                    if ((isset($class))&&($class=="odd")) {
                        $class = "even";
                    }
                    else {
                        $class = "odd";
                    }
                    echo "<tr class='".$class."'>";
                    for ($i=0;$i<$count;$i++) {
                        echo "<td>";
                        $thistactics = new Tactics($teamid, $mapid, $sizes[$i]);
                        if (isset($thistactics->tacid)) {
                            echo "<a href='tactics.php?op=display&tacid=".$thistactics->tacid()."'>";
                        }
                        echo $mapname;
                        if (isset($thistactics->tacid)) {
                            echo "</a></td>";
                            if ($admin=='Yes') {
                                echo "<td align='right'><a href='tactics.php?op=mantactics&tacid=".$thistactics->tacid()."'>";
                                echo "<img src='images/edit.gif' border='0' alt='Edit'></a></td>";
                            }
                        }
                        else {
                            if ($admin=='Yes') {
                                echo "<td align='right'><a href='tactics.php?op=mantactics&mapid=".$mapid."&teamid=".$teamid."&teamsize=".$sizes[$i]."'>";
                                echo "<img src='images/addtactic.gif' border='0' alt='Add'></a>";
                            }
                        }
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table></td></tr></table>";
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

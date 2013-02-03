<?php
// $Id: matchform.inc.php,v 1.6 2006/06/09 14:32:47 mithyt2 Exp $
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
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
if (isset($matchdate)) {
    $date = $matchdate;
    $op = _AM_EDITMATCH;
    $op_hidden = new XoopsFormHidden('op', 'editmatch');
}
else {
    $curday = date("d");
    $curmonth = date("m");
    $curyear = date("Y");
    $date = mktime(21,0,0,$curmonth, $curday, $curyear);
    $op = _AM_SUBMITMATCH;
    $op_hidden = new XoopsFormHidden('op', 'savematch');
}
$mform = new XoopsThemeForm($op." for ".$team->getVar('teamname'), "matchform", xoops_getenv('PHP_SELF'));
$uid_hidden = new XoopsFormHidden('uid', $xoopsUser->getVar('uid'));
$mid_hidden = new XoopsFormHidden('mid', $mid);
$team_hidden = new XoopsFormHidden('teamid', $teamid);

for ($i = 1; $i <= 31; $i++) {
    $dayarray[$i] = $i;
}
$curday = date('d',$date);
$day_select = new XoopsFormSelect(_AM_DAYC, 'day', $curday);
$day_select->addOptionArray($dayarray);

for ($xmonth=1; $xmonth<13; $xmonth++) {
    $month = date('F', mktime(0,0,0,$xmonth,0,0));
    $monthno = date('n', mktime(0,0,0,$xmonth,0,0));
    $mvalue[$monthno]=$month;
}
$curmonth = date('n',$date);
$monthselect = new XoopsFormSelect(_AM_MONTHC,'month',$curmonth);
$monthselect->addOptionArray($mvalue);

$curyear = date('Y',$date);
$xyear = $curyear;
for ($i=1; $i<6; $i++) {
    $yvalue[$xyear]=$xyear;
    $xyear++;
}
$yearselect = new XoopsFormSelect(_AM_YEARC,'year',$curyear);
$yearselect->addOptionArray($yvalue);

$clock = date('H:i',$date);
$clock = new XoopsFormText(_AM_TIMEC, 'time', 10, 10, $clock, 'E');
$button_tray = new XoopsFormElementTray('' ,'');
$button_tray->addElement(new XoopsFormButton('', 'save', _NW_POST, 'submit'));

$teamsize_select = new XoopsFormSelect(_AM_TEAMSIZE, 'teamsize', $teamsize);
foreach ($teamsizes as $size_id => $ts) {
    $teamsize_select->addOption($ts);
}
$opponent = new XoopsFormText(_AM_TEAMOPPONENT, 'opponent', 25, 25, $opponent, 'E');

$ladder_select = new XoopsFormSelect(_AM_TEAMMATCHTYPE, 'ladder', $ladder);
foreach ($teamladders as $ladder_id => $tl) {
    $ladder_select->addOption($tl);
}

/*
$ladder_select = new XoopsFormSelect(_AM_TEAMMATCHTYPE, 'ladder', $ladder);
$ladder_select->addOption("Ladder");
$ladder_select->addOption("Scrim");
$ladder_select->addOption("Practice");
*/
$result_select = new XoopsFormSelect(_AM_TEAMMATCHRESULT, 'matchresult', $matchresult);
$result_select->addOption("Pending");
$result_select->addOption("Win");
$result_select->addOption("Loss");
$result_select->addOption("Draw");

$nummaps = $team->getVar('maps');
$matchmap_handler = xoops_getmodulehandler('matchmap', 'team');
$matchmaps = $matchmap_handler->getByMatchid($mid);
$teamsides = $team->getSides();
for ($mapno=1; $mapno <= $nummaps; $mapno++) {
    if ($op==_AM_EDITMATCH) {
        $thismap = isset($matchmaps[$mapno]) ? $matchmaps[$mapno] : $matchmap_handler->create();
        $our[$mapno] = new XoopsFormText(_AM_TEAMUS, 'ourscore[]', 10, 10, $thismap->getVar('ourscore', 'E'));
        $their[$mapno] = new XoopsFormText(_AM_TEAMTHEM, 'theirscore[]', 10, 10, $thismap->getVar('theirscore', 'E'));
        $mapid = $thismap->getVar('mapid');
        $thisside = $thismap->getVar('side');
    }
    $map_select[$mapno] = new XoopsFormSelect(getCaption($mapno), 'map[]', $mapid);
    $side[$mapno] = new XoopsFormSelect(_AM_TEAMSIDE, 'side[]', $thisside);
    foreach ($teamsides as $sideid => $sidename) {
        $side[$mapno]->addOption($sideid, $sidename);
    }
}

$teammaps = $team->getTeamMaps();
for ($mapno=1; $mapno <= $nummaps; $mapno++) {
    $map_select[$mapno]->addOption(0, _AM_TEAMUNDECIDED);
    foreach ($teammaps as $mapid => $mapname) {
        $map_select[$mapno]->addOption($mapid, $mapname);
    }
}
$server_select = new XoopsFormSelect(_AM_TEAMSERVER, 'server', $server);
$myserver = $team->getServers();
$server_select->addOption(0, _AM_CUSTOMSERVER);
foreach ($myserver as $serverid=>$servername) {
    $server_select->addOption($serverid, $servername);
}

$customserver_label = new XoopsFormLabel('', _AM_MAYCHOOSECUSTOMSERVER);
$customserver_text = new XoopsFormText('', 'customserver', 40, 40, $customserver);

$review_tarea = new XoopsFormTextArea(_AM_MATCHREVIEW, 'review', $review);

$mform->addElement($uid_hidden);
$mform->addElement($mid_hidden);
$mform->addElement($op_hidden);
$mform->addElement($team_hidden);
$mform->addElement($day_select);
$mform->addElement($monthselect);
$mform->addElement($yearselect);
$mform->addElement($clock);
$mform->addElement($opponent);
$mform->addElement($teamsize_select);
$mform->addElement($ladder_select);
if ($op == _AM_EDITMATCH) {
    $mform->addElement($result_select);
}
for ($mapno = 1; $mapno <= $nummaps; $mapno++) {
    $mform->addElement($map_select[$mapno]);
    $mform->addElement($side[$mapno]);
    if ($op==_AM_EDITMATCH) {
        $mform->addElement($our[$mapno]);
        $mform->addElement($their[$mapno]);
    }
}
$mform->addElement($server_select);
$mform->addElement($customserver_label);
$mform->addElement($customserver_text);
$mform->addElement($review_tarea);
if ($mid > 0) {
    $screenshots_label = new XoopsFormLabel(_AM_SCREENSHOTS, "<a href=\"index.php?op=screenshotform&mid=$mid\">"._AM_ADDSCREENSHOTS."</a>");
    $mform->addElement($screenshots_label);
}
$mform->addElement($button_tray);
$mform->display();
?>

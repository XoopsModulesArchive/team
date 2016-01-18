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
    $op = _MD_EDITMATCH;
    $op_hidden = new XoopsFormHidden('op', 'editmatch');
}
else {
    $curday = date("d");
    $curmonth = date("m");
    $curyear = date("Y");
    $date = mktime(21,0,0,$curmonth, $curday, $curyear);
    $op = _MD_SUBMITMATCH;
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
$day_select = new XoopsFormSelect(_MD_DAYC, 'day', $curday);
$day_select->addOptionArray($dayarray);

for ($xmonth=1; $xmonth<13; $xmonth++) {
    $month = date('F', mktime(0,0,0,$xmonth,0,0));
    $monthno = date('n', mktime(0,0,0,$xmonth,0,0));
    $mvalue[$monthno]=$month;
}
$curmonth = date('n',$date);
$monthselect = new XoopsFormSelect(_MD_MONTHC,'month',$curmonth);
$monthselect->addOptionArray($mvalue);

$curyear = date('Y',$date);
$xyear = $curyear;
for ($i=1; $i<6; $i++) {
    $yvalue[$xyear]=$xyear;
    $xyear++;
}
$yearselect = new XoopsFormSelect(_MD_YEARC,'year',$curyear);
$yearselect->addOptionArray($yvalue);

$clock = date('H:i',$date);
$clock = new XoopsFormText(_MD_TIMEC, 'time', 10, 10, $clock, 'E');
$button_tray = new XoopsFormElementTray('' ,'');
$button_tray->addElement(new XoopsFormButton('', 'save', _MD_NW_POST, 'submit'));

$teamsize = isset($teamsize)?$teamsize:"";
$teamsize_select = new XoopsFormSelect(_MD_TEAMSIZE, 'teamsize', $teamsize);
foreach ($teamsizes as $size_id => $ts) {
    $teamsize_select->addOption($ts);
}
$opponent = isset($opponent)?$opponent:"";
$opponent = new XoopsFormText(_MD_TEAMOPPONENT, 'opponent', 25, 25, $opponent, 'E');

$ladder = isset($ladder)?$ladder:"";
$ladder_select = new XoopsFormSelect(_MD_TEAMMATCHTYPE, 'ladder', $ladder);
foreach ($teamladders as $ladder_id => $tl) {
    $ladder_select->addOption($tl);
}

/*
$ladder_select = new XoopsFormSelect(_MD_TEAMMATCHTYPE, 'ladder', $ladder);
$ladder_select->addOption("Ladder");
$ladder_select->addOption("Scrim");
$ladder_select->addOption("Practice");
*/
$matchresult = isset($matchresult)?$matchresult:"";
$result_select = new XoopsFormSelect(_MD_TEAMMATCHRESULT, 'matchresult', $matchresult);
$result_select->addOption("Pending");
$result_select->addOption("Win");
$result_select->addOption("Loss");
$result_select->addOption("Draw");

$nummaps = $team->getVar('maps');
$matchmap_handler = xoops_getmodulehandler('matchmap', 'team');
$matchmaps = $matchmap_handler->getByMatchid($mid);
$teamsides = $team->getSides();
for ($mapno=1; $mapno <= $nummaps; $mapno++) {
    if ($op==_MD_EDITMATCH) {
        $thismap = isset($matchmaps[$mapno]) ? $matchmaps[$mapno] : $matchmap_handler->create();
        $our[$mapno] = new XoopsFormText(_MD_TEAMUS, 'ourscore[]', 10, 10, $thismap->getVar('ourscore', 'E'));
        $their[$mapno] = new XoopsFormText(_MD_TEAMTHEM, 'theirscore[]', 10, 10, $thismap->getVar('theirscore', 'E'));
        $mapid = $thismap->getVar('mapid');
        $thisside = $thismap->getVar('side');
    }
    $mapid = isset($mapid)?$mapid:"";
    $map_select[$mapno] = new XoopsFormSelect(getCaption($mapno), 'map[]', $mapid);
    $thisside = isset($thisside)?$thisside:"";
    $side[$mapno] = new XoopsFormSelect(_MD_TEAMSIDE, 'side[]', $thisside);
    foreach ($teamsides as $sideid => $sidename) {
        $side[$mapno]->addOption($sideid, $sidename);
    }
}

$teammaps = $team->getTeamMaps();
for ($mapno=1; $mapno <= $nummaps; $mapno++) {
    $map_select[$mapno]->addOption(0, _MD_TEAMUNDECIDED);
    foreach ($teammaps as $mapid => $mapname) {
        $map_select[$mapno]->addOption($mapid, $mapname);
    }
}
$server = isset($server)?$server:"";
$server_select = new XoopsFormSelect(_MD_TEAMSERVER, 'server', $server);
$myserver = $team->getServers();
$server_select->addOption(0, _MD_CUSTOMSERVER);
foreach ($myserver as $serverid=>$servername) {
    $server_select->addOption($serverid, $servername);
}

$customserver_label = new XoopsFormLabel('', _MD_MAYCHOOSECUSTOMSERVER);
$customserver = isset($customserver)?$customserver:"";
$customserver_text = new XoopsFormText('', 'customserver', 40, 40, $customserver);
$review = isset($review)?$review:"";
$review_tarea = new XoopsFormTextArea(_MD_MATCHREVIEW, 'review', $review);

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
if ($op == _MD_EDITMATCH) {
    $mform->addElement($result_select);
}
for ($mapno = 1; $mapno <= $nummaps; $mapno++) {
    $mform->addElement($map_select[$mapno]);
    $mform->addElement($side[$mapno]);
    if ($op==_MD_EDITMATCH) {
        $mform->addElement($our[$mapno]);
        $mform->addElement($their[$mapno]);
    }
}
$mform->addElement($server_select);
$mform->addElement($customserver_label);
$mform->addElement($customserver_text);
$mform->addElement($review_tarea);
if ($mid > 0) {
    $screenshots_label = new XoopsFormLabel(_MD_SCREENSHOTS, "<a href=\"index.php?op=screenshotform&mid=$mid\">"._MD_ADDSCREENSHOTS."</a>");
    $mform->addElement($screenshots_label);
}
$mform->addElement($button_tray);
$mform->display();
?>

<?php
// $Id: availability.php,v 1.5 2006/06/09 14:32:47 mithyt2 Exp $
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

function sh_availability() {
    global $xoopsUser;
    if (is_object($xoopsUser)) {
        $userid = $xoopsUser->getVar('uid');
        $block = array();
        $block['title'] = "Availability for ".$xoopsUser->getVar("uname");
        $block['content']  = "<table border='0' cellspacing='1'><div align='left'>";
        $team_handler = xoops_getmodulehandler('team', 'team');
        $teamnames = $team_handler->getList();
        $availability_handler = xoops_getmodulehandler('availability', 'team');
        $availabilities = $availability_handler->getPendingByUser($userid);
        foreach ($availabilities as $myrow) {
            $weekday = date( 'D', $myrow["matchdate"]);
            $day= date(_MEDIUMDATESTRING, $myrow["matchdate"]);
            $teamid = $myrow["teamid"];
            $teamname = $teamnames[$teamid];
            if ($myrow["availability"]=="Not Set") {
                $notset=1;
                $match=1;
                $fontcl="Orange";
                $avail="No Reply";
            }
            elseif (($myrow["availability"]=="No") OR ($myrow["availability"]=="LateNo")) {
                $match=1;
                $fontcl="Red";
                $avail="No";
            }
            elseif (($myrow["availability"]=="Yes") OR ($myrow["availability"]=="LateYes")) {
                $match=1;
                $fontcl="green";
                $avail="Yes";
            }
            elseif ($myrow["availability"]=="Sub") {
                $match=1;
                $fontcl="blue";
                $avail="Sub";
            }
            if ((isset($class))&&($class=="odd")) {
                $class = "even";
            }
            else {
                $class = "odd";
            }
            $block['content'] .= "<tr class=".$class."><td><font color='".$fontcl."'>".$weekday." ".$day." ".$teamname." vs ". $myrow["opponent"]."</font> - <a href='".XOOPS_URL."/modules/team/availability.php?mid=".$myrow["matchid"]."' target='_self'>".$avail."</a> - <a href='".XOOPS_URL."/modules/team/matchdetails.php?mid=".$myrow["matchid"]."' target='_self'>"._BL_TEAMMATCHDETAILS."</td></tr>";
        }
        if (!isset($notset)) {
            $block['content'] .= "<tr><th><font color='green'>"._BL_TEAMNOUNSETAVAIL."</font></th></tr>";
        }
        if (!isset($match)) {
            $block['content'] .= "<tr><th><font color='green'></br>"._BL_TEAMNOUPCOMEMATCHES."</font></th></tr>";
        }
        $block['content'] .= "</div></table>";
        return $block;
    }
    return false;
}
?>
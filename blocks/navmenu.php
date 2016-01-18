<?php
// $Id: navmenu.php,v 1.4 2006/06/09 14:32:47 mithyt2 Exp $
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

function sh_navmenu() {
    global $xoopsDB, $xoopsUser;
	if (is_object($xoopsUser)) {
		$uid = $xoopsUser->getVar('uid');
    }
    else {
        $uid = 0;
    }
    $block = array();
    $block['title'] = _BL_TEAMMENU;
    $block['content']  = "<table border='0' cellspacing='1'><tr><td id='mainmenu'>";
    $team_handler = xoops_getmodulehandler('team', 'team');
    $criteria = new CriteriaCompo();
    $criteria->setSort("defteam DESC, teamname");
    $teams = $team_handler->getObjects($criteria, false, false);
    foreach ($teams as $myrow) {
        $teamid = $myrow["teamid"];
        $teamname = $myrow["teamname"];
        $teamtype = $myrow["teamtype"];
        if (isset($counter)) {
            $class = 'menuMain';
        }
        else {
            $class = 'menuTop';
        }
        $block['content'] .= "<a class='".$class."' href='".XOOPS_URL."/modules/team/index.php?teamid=".$teamid."' target='_self'>".$teamname."</a>";
        $block['content'] .= "<a class='menuSub' href='".XOOPS_URL."/modules/team/index.php?teamid=".$teamid."' target='_self'>"._BL_TEAMMATCHES."</a>";
        $block['content'] .= "<a class='menuSub' href='".XOOPS_URL."/modules/team/roster.php?teamid=".$teamid."' target='_self'>"._BL_TEAMROSTER."</a>";
        $sql = "SELECT rank FROM ".$xoopsDB->prefix("team_teamstatus")." WHERE teamid=$teamid AND uid=$uid";
        $statusresult = $xoopsDB->query($sql);
        $teammember = $xoopsDB->fetchArray($statusresult);
        if ($teammember["rank"]!=NULL) {
            $allow = true;
        }
        else {
            $allow = false;
        }
        if ($allow) {
            $block['content'] .= "<a class='menuSub' href='".XOOPS_URL."/modules/team/tactics.php?teamid=".$teamid."' target='_self'>"._BL_TEAMTACTICS."</a>";
        }
    }
	$block['content'] .= "</td></tr></table>";
 	return $block;
}
?>
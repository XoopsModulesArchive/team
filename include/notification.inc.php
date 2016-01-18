<?php
// $Id: notification.inc.php,v 1.6 2006/06/09 14:32:47 mithyt2 Exp $
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

function team_notify_iteminfo($category, $item_id)
{
    $pathparts = explode("/", dirname(__FILE__));
	$moduleDirName = $pathparts[array_search('modules', $pathparts)+1];
	$item_id = intval($item_id);

	if ($category=='global') {
		$item['name'] = '';
		$item['url'] = '';
		return $item;
	}

	global $xoopsDB;
	if ($category=='team') {
		// Assume we have a valid team id
		$team_handler = xoops_getmodulehandler('team', 'team');
		$team = $team_handler->get($item_id);
		if ($team->isNew() ) {
		    return false;
		}
		$item['name'] = $team->getVar('teamname');
		$item['url'] = XOOPS_URL . '/modules/' . $moduleDirName . '/index.php?teamid=' . $item_id;
		return $item;
	}

	if ($category=='match') {
		// Assume we have a valid team id
		$sql = 'SELECT t.teamname, m.opponent FROM '.$xoopsDB->prefix('team_matches') . ' m, ' . $xoopsDB->prefix('team_team') . ' t WHERE t.teamid = m.teamid AND m.matchid = '. $item_id . ' limit 1';
		$result = $xoopsDB->query($sql); // TODO: error check
		$result_array = $xoopsDB->fetchArray($result);
		$item['name'] = $result_array['teamname']." vs. ".$result_array["opponent"];
		$item['url'] = XOOPS_URL . '/modules/' . $moduleDirName . '/matchdetails.php?mid=' . $item_id;
		return $item;
	}

}
?>

<?php
// $Id: screenshotform.inc.php,v 1.4 2006/06/09 14:32:47 mithyt2 Exp $
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
$mform = new XoopsThemeForm(_AM_SCREENSHOTS, "screenshotform", xoops_getenv('PHP_SELF'));
$uid_hidden = new XoopsFormHidden('uid', $xoopsUser->getVar('uid'));
$mid_hidden = new XoopsFormHidden('mid', $mid);

$matchmap_handler = xoops_getmodulehandler('matchmap', 'team');

// Output list with maps for selected match
echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><th>"._AM_TEAMMAPNAME."</th><th>"._AM_TEAMSIDENAME."</th><th>"._AM_SCREENSHOTNAME."</th><th>"._AM_EDIT."</th></tr>";
for ($mapno=1; $mapno <= $nummaps; $mapno++) {
    if ((isset($class))&&($class=="even")) {
        $class = "odd";
    }
    else {
        $class = "even";
    }
	echo "<tr class=\"$class\">";
	$thismap = $matchmap_handler->getByMatchid($mid, $mapno);
	echo "<td>".(is_object($thismap->map) ? $thismap->map->getVar('mapname') : "??")."</td>";
	echo "<td>".getSide($thismap->getVar('side'))."</td>";
	if (strlen($thismap->getVar('screenshot')) > 0) {
		echo "<td>".$thismap->getVar('screenshot')."<br /><img src=\"screenshots/thumbs/".$thismap->getVar('screenshot')."\" alt=\"\" border=\"0\" /></td>";
		echo "<td><a href=\"index.php?op=deletescreenshot&matchmapid=".$thismap->getVar('matchmapid')."\">"._AM_DELETE."</a></td>";
	} else {
		echo "<td>&nbsp;</td>";
		echo "<td><a href=\"index.php?op=screenshotform&action=add&mid=$mid&matchmapid=".$thismap->getVar('matchmapid')."\">"._AM_ADD."</a></td>";
	}
}
echo"</table>";

?>

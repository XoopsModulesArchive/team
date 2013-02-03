<?php
// $Id: screenshotform.inc.php,v 1.1 2003/12/04 00:26:46 jace303 Exp $
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

include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
$mform = new XoopsThemeForm(_AM_SCREENSHOTS, "screenshotform", xoops_getenv('PHP_SELF'));
$uid_hidden = new XoopsFormHidden('uid', $xoopsUser->getVar('uid'));
$mid_hidden = new XoopsFormHidden('mid', $mid);

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
	$thismap = new MatchMap($mid, $mapno);
	echo "<td>".$thismap->mapname."</td>";
	echo "<td>".getSide($thismap->side)."</td>";
	if (strlen($thismap->screenshot) > 0) {
		echo "<td>".$thismap->screenshot."<br /><img src=\"screenshots/thumbs/".$thismap->screenshot."\" alt=\"\" border=\"0\" /></td>";
		echo "<td><a href=\"index.php?op=deletescreenshot&matchmapid=".$thismap->matchmapid()."\">"._AM_DELETE."</a></td>";
	} else {
		echo "<td>&nbsp;</td>";
		echo "<td><a href=\"index.php?op=screenshotform&action=add&mid=$mid&matchmapid=".$thismap->matchmapid()."\">"._AM_ADD."</a></td>";
	}
}
echo"</table>";

?>

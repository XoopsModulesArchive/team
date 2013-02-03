<?php
function teamTableLink($img="", $url=array(), $rightlink=array()) {
    teamTableOpen();
    echo "<td><img src='".$img."'></td>";
    if ($rightlink) {
        foreach ($rightlink as $key => $link) {
            echo "<td align=right><a href='".$link["url"]."'>".$link["text"]."</td>";
        }
    }
    echo "</tr><tr>";
    echo "<td align=left>";
    foreach ($url as $key => $link) {
        if (isset($first)) {
            echo " >> >> ";
        }
        if ($link["url"]) {
            echo "<a href='".$link["url"]."'>";
        }
        echo $link["text"];
        if ($link["url"]) {
            echo "</a>";
        }
        $first = 1;
    }
    echo "</td>";
    teamTableClose();
    teamTableOpen();
}

function teamTableOpen() {
    echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'><tr><td>";
    echo "<tr><td><table width='100%' border='0' cellpadding='4' cellspacing='1'>";
    echo "<tr>";
}

function teamTableClose() {
    echo "</tr></table></td></tr></table>";
}

function teamItemManage ($nomembers, $members, $teamid, $op, $select, $lang) {
    echo "<tr><th><b>".$lang[0]."</b></th><th align=center><b>".$lang[1]."</b></th><th><b>".$lang[2]."</b></th>";
    echo "</tr>\n";
    echo '<tr><td class="even"><form action="teamadmin.php" method="post">';
    echo '<select name="'.$select[0].'[]" size="10" multiple="multiple">'."\n";
    foreach ($nomembers as $member_id => $member_name) {
        echo '<option value="'.$member_id.'">'.$member_name.'</option>'."\n";
    }
    echo '</select>';
    echo "</td><td align='center' class='odd'>
         <input type='hidden' name='op' value='".$op[0]."' />
		<input type='hidden' name='teamid' value='".$teamid."' />
		<input type='submit' name='submit' value='"._AM_ADDBUTTON."' />
		</form><br />
		<form action='teamadmin.php' method='post' />
		<input type='hidden' name='op' value='".$op[1]."' />
		<input type='hidden' name='teamid' value='".$teamid."' />
		<input type='submit' name='submit' value='"._AM_DELBUTTON."' />
		</td>
		<td class='even'>";
    echo "<select name='".$select[1]."[]' size='10' multiple='multiple'>";
    foreach ($members as $member_id => $member_name) {
        echo '<option value="'.$member_id.'">'.$member_name.'</option>'."\n";
    }
    echo "</select>";
    echo '</form></td></tr>';
}

function getAllMembers() {
    $member_handler = xoops_gethandler('member');
    $allmembers = $member_handler->getUserList();
    return $allmembers;
}
function getAllMaps() {
    $map_handler = xoops_getmodulehandler('map');
    return $map_handler->getList();
}
function getAllPositions() {
    global $xoopsDB;
    $sql = "SELECT posid, posname FROM ".$xoopsDB->prefix("team_positions")." WHERE postype='Pos' ORDER BY posorder, posname ASC";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $allpos = array();
    while ($row=$xoopsDB->fetchArray($result)) {
        $allpos[$row["posid"]]=$row["posname"];
        $count++;
    }
    return $allpos;
}
function getAllSkills() {
    global $xoopsDB;
    $sql = "SELECT posid, posname FROM ".$xoopsDB->prefix("team_positions")." WHERE postype='Skill' ORDER BY posorder, posname ASC";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $allskills = array();
    while ($row=$xoopsDB->fetchArray($result)) {
        $allskills[$row["posid"]]=$row["posname"];
        $count++;
    }
    return $allskills;
}
function getAllServers() {
    global $xoopsDB;
    $sql = "SELECT serverid, servername FROM ".$xoopsDB->prefix("team_server")." ORDER BY servername ASC";
    $result = $xoopsDB->query($sql);
    $count = 0;
    while ($row=$xoopsDB->fetchArray($result)) {
        $allservers[$row["serverid"]]=$row["servername"];
        $count++;
    }
    return $allservers;
}
function getAllTeamsizes() {
    global $xoopsDB;
    $sql = "SELECT sizeid, size FROM ".$xoopsDB->prefix("team_sizes")." ORDER BY size";
    $result = $xoopsDB->query($sql);
    $teamsizes=array();
    while ($row=$xoopsDB->fetchArray($result)) {
        $teamsizes[$row["sizeid"]]=$row["size"];
    }
    return $teamsizes;
}

function getAllTeamsides() {
    global $xoopsDB;
    $sql = "SELECT sideid, side FROM ".$xoopsDB->prefix("team_sides")." ORDER BY side";
    $result = $xoopsDB->query($sql);
    $teamsides=array();
    while ($row=$xoopsDB->fetchArray($result)) {
        $teamsides[$row["sideid"]]=$row["side"];
    }
    return $teamsides;
}

function getAllTeamranks() {
    global $xoopsDB;
    $sql = "SELECT rankid, rank FROM ".$xoopsDB->prefix("team_rank")." ORDER BY rank";
    $result = $xoopsDB->query($sql);
    $teamranks=array();
    while ($row=$xoopsDB->fetchArray($result)) {
        $teamranks[$row["rankid"]]=$row["rank"];
    }
    return $teamranks;
}
?>
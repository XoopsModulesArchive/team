<?php
// resize picture and copy it to destination
function resizeToFile ($sourcefile, $dest_x, $dest_y, $targetfile, $jpegqual) {

	/* Get the dimensions of the source picture */
   $picsize=getimagesize("$sourcefile");

   $source_x  = $picsize[0];
   $source_y  = $picsize[1];
   $source_id = imageCreateFromJPEG("$sourcefile");

	/* Create a new image object (not neccessarily true colour) */

   $target_id=imagecreatetruecolor($dest_x, $dest_y);

	/* Resize the original picture and copy it into the just created image
   	object. */

   $target_pic=imagecopyresampled($target_id,$source_id,0,0,0,0,$dest_x,$dest_y,$source_x,$source_y);

	/* Create a jpeg with the quality of "$jpegqual" out of the
   	image object "$target_pic".
   	This will be saved as $targetfile */

   imagejpeg ($target_id,"$targetfile",$jpegqual);

   return true;
 }

function getServer($serverid) {
    global $xoopsDB;
    $server = array();
    $serverid = intval($serverid);
    $sql = "SELECT serverid, servername, serverip, serverport FROM ".$xoopsDB->prefix("team_server")." WHERE serverid=$serverid";
    $serverresult = $xoopsDB->query($sql);
    $myserver = $xoopsDB->fetchArray($serverresult);
    $server["id"] = $myserver["serverid"];
    $server["name"] = $myserver["servername"];
    $server["ip"] = $myserver["serverip"];
    $server["port"] = $myserver["serverport"];
    return $server;
}
function getTeams() {
    global $xoopsDB;
    $team = array();
    $team_handler = xoops_getmodulehandler('team', 'team');
    $criteria = new CriteriaCompo();
    $criteria->setSort("defteam DESC, teamname");
    return $team_handler->getList($criteria);
}

function getDefaultTeam() {
    global $xoopsDB;
    $team_handler = xoops_getmodulehandler('team', 'team');
    $criteria = new Criteria("defteam", 1);
    $teams = $team_handler->getObjects($criteria, false);
    if (isset($teams[0])) {
        return $teams[0]->getVar('teamid');
    }
    return 0;
}

function getPosName($posid) {
    global $xoopsDB;
    if ($posid==NULL) {
        return false;
    }
    elseif ($posid==0) {
        return false;
    }
    else {
        $posid = intval($posid);
        $sql = "SELECT posname FROM ".$xoopsDB->prefix("team_positions")." WHERE posid=$posid";
        $result = $xoopsDB->query($sql);
        $posname = $xoopsDB->fetchArray($result);
        return $posname["posname"];
    }
}

function getShort($posid) {
    global $xoopsDB;
    $posid = intval($posid);
    $sql = "SELECT posshort FROM ".$xoopsDB->prefix("team_positions")." WHERE posid=$posid";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $posshort = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $posshort=$row["posshort"];
        $count++;
    }
    return $posshort;
}

function getAllShort() {
    global $xoopsDB;
    $sql = "SELECT posid, posshort FROM ".$xoopsDB->prefix("team_positions")." ORDER BY posid";
    $result = $xoopsDB->query($sql);
    $posshort = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $posshort[$row["posid"]]=$row["posshort"];
    }
    return $posshort;
}

function getAllPos() {
    global $xoopsDB;
    $sql = "SELECT posid, posname FROM ".$xoopsDB->prefix("team_positions")." ORDER BY posid";
    $result = $xoopsDB->query($sql);
    $posshort = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $posshort[$row["posid"]]=$row["posname"];
    }
    return $posshort;
}

function getSideShort($sideid) {
    global $xoopsDB;
    $sideid = intval($sideid);
    $sql = "SELECT sideshort FROM ".$xoopsDB->prefix("team_sides")." WHERE sideid=$sideid";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $short = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $short=$row["sideshort"];
        $count++;
    }
    return $short;
}

function getAllSideShort() {
    global $xoopsDB;
    $sql = "SELECT sideid, sideshort FROM ".$xoopsDB->prefix("team_sides")." ORDER BY sideid";
    $result = $xoopsDB->query($sql);
    $sides = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $sides[$row["sideid"]]=$row["sideshort"];
    }
    return $sides;
}

function getAllLadders() {
    global $xoopsDB;
    $sql = "SELECT ladderid, ladder, visible, scoresvisible FROM ".$xoopsDB->prefix("team_ladders");
    $result = $xoopsDB->query($sql);
    $ladders = array();
    while ($row = $xoopsDB->fetchArray($result)) {
          $ladders[$row["ladderid"]] = array('ladder' => $row["ladder"], 'visible' => $row["visible"], 'scoresvisible' => $row["scoresvisible"]);
    }
    return $ladders;
}


function getSide($sideid) {
    global $xoopsDB;
    $sideid = intval($sideid);
    $sql = "SELECT side FROM ".$xoopsDB->prefix("team_sides")." WHERE sideid=$sideid";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $side = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $side=$row["side"];
        $count++;
    }
    return $side;
}

function getAllSides() {
    global $xoopsDB;
    $sql = "SELECT sideid, side FROM ".$xoopsDB->prefix("team_sides")." ORDER BY sideid";
    $result = $xoopsDB->query($sql);
    $sides = "";
    while ($row=$xoopsDB->fetchArray($result)) {
        $sides[$row["sideid"]]=$row["side"];
    }
    return $sides;
}

function skillcheck($teamid, $uid, $skillid) {
    global $xoopsDB;
    $teamid = intval($teamid);
    $uid = intval($uid);
    $skillid = intval($skillid);
    $sql = "SELECT skillid FROM ".$xoopsDB->prefix("team_skills")." WHERE teamid=$teamid AND uid=$uid AND posid=$skillid";
    $result = $xoopsDB->query($sql);
    if (($xoopsDB->getRowsNum($result))>0) {
        return "checked";
    }
    else {
        return false;
    }
}

function getStatus($status) {
    global $xoopsDB;
    $status = intval($status);
    $sql = "SELECT status FROM ".$xoopsDB->prefix("team_status")." WHERE statusid=".$status;
    $result = $xoopsDB->query($sql);
    $mystatus = $xoopsDB->fetchArray($result);
    return $mystatus["status"];
}

function getAllStatus() {
    global $xoopsDB;
    $status = array();
    $sql = "SELECT statusid, status FROM ".$xoopsDB->prefix("team_status")." ORDER BY statusid";
    $result = $xoopsDB->query($sql);
    while ($mystatus = $xoopsDB->fetchArray($result)) {
        $status[$mystatus["statusid"]] = $mystatus["status"];
    }
    return $status;
}

function getAllRanks() {
    global $xoopsDB;
    $sql = "SELECT rankid, rank, color FROM ".$xoopsDB->prefix("team_rank")." ORDER BY rankid";
    $result = $xoopsDB->query($sql);
    while ($myrank = $xoopsDB->fetchArray($result)) {
        $rank[$myrank["rankid"]]["rank"] = $myrank["rank"];
        $rank[$myrank["rankid"]]["color"] = $myrank["color"];
    }
    return $rank;
}

function getRank($rank) {
    global $xoopsDB;
    $rank = intval($rank);
    $sql = "SELECT rank, color FROM ".$xoopsDB->prefix("team_rank")." WHERE rankid=".$rank;
    $result = $xoopsDB->query($sql);
    $myrank = $xoopsDB->fetchArray($result);
    return $myrank;
}

function getPlayerStatus($statusid) {
    global $xoopsDB;
    $statusid = intval($statusid);
    $sql = "SELECT status FROM ".$xoopsDB->prefix("team_teamstatus")." WHERE statusid=$statusid";
    $result = $xoopsDB->query($sql);
    while ($teamstatus = $xoopsDB->fetchArray($result)) {
        $playerstatus = getStatus($teamstatus["status"]);
    }
    return $playerstatus;
}

function getPlayerRank($teamid, $uid) {
    global $xoopsDB;
    $teamid = intval($teamid);
    $uid = intval($uid);
    $sql = "SELECT rank FROM ".$xoopsDB->prefix("team_teamstatus")." WHERE teamid=$teamid AND uid=$uid";
    $result = $xoopsDB->query($sql);
    $playerrank = $xoopsDB->fetchArray($result);
    return getRank($playerrank["rank"]);
}

function getMap($mapid) {
    global $xoopsDB;
    $mapid = intval($mapid);
    $sql = "SELECT mapname FROM ".$xoopsDB->prefix("team_mappool")." WHERE mapid=".$mapid;
    if ($result = $xoopsDB->query($sql)) {
        $result = $xoopsDB->fetchArray($result);
        return $result["mapname"];
    }
    else {
        return _AM_TEAMUNDECIDED;
    }
}

function getCaption($mapno) {
    if ($mapno == 1) {
        return _AM_TEAMFIRSTMAP;
    }
    elseif ($mapno == 2) {
        return _AM_TEAMSECONDMAP;
    }
    elseif ($mapno == 3) {
        return _AM_TEAMTHIRDMAP;
    }
    elseif ($mapno == 4) {
        return _AM_TEAMFOURTHMAP;
    }
    elseif ($mapno == 5) {
        return _AM_TEAMFIFTHMAP;
    }
}

/****************************
/* Fetches and returns layout data from database
 */
function getLayout() {
    global $xoopsDB;
    $sql = "SELECT * FROM ".$xoopsDB->prefix("team_layout");
    $result = $xoopsDB->query($sql);
    $mylayout = $xoopsDB->fetchArray($result);
    return $mylayout;
}

function selectcheck($val1, $val2) {
    if ($val1==$val2) {
        return "selected";
    }
    else {
        return false;
    }

}
?>

<?php
// Class for Team management for Team Module
// $Id: team.php,v 0.1 Date: 13/10/2003, Author: Mithrandir
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamTeam extends XoopsObject
{
    var $db;
    //Constructor
    function TeamTeam($teamid=-1)
    {
        $this->initVar('teamid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('teamname', XOBJ_DTYPE_TXTBOX);
        $this->initVar('teamtype', XOBJ_DTYPE_TXTBOX);
        $this->initVar('maps', XOBJ_DTYPE_INT);
        $this->initVar('defteam', XOBJ_DTYPE_INT);
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    function getMatches($clause=null) {
        $sql = "SELECT * FROM ".$this->db->prefix("team_matches")." WHERE teamid= ".$this->getVar('teamid')." ORDER BY matchdate DESC ".$clause." ";
        $result = $this->db->query($sql);
        $matches = array();
        $match_handler =& xoops_getmodulehandler('match');
        while ($row = $this->db->fetchArray($result)) {
            $thismatch =& $match_handler->create(false);
            $thismatch->assignVars($row);
            $matches[$row["matchid"]] = $thismatch;
            unset($thismatch);
        }
        return $matches;
    }

    function getTeamMaps() {
        $sql = "SELECT m.mapname, m.mapid FROM ".$this->db->prefix("team_mappool")." m, ".$this->db->prefix("team_teammaps")." tm WHERE m.mapid=tm.mapid AND tm.teamid=".$this->getVar('teamid')." ORDER BY m.mapname ASC";
        $result = $this->db->query($sql);
        $maps=array();
        while ($row=$this->db->fetchArray($result)) {
            $maps[$row["mapid"]]=$row["mapname"];
        }
        return $maps;
    }

    function getTeamMembers() {
        $sql = "SELECT u.uname, u.uid FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("users")." u WHERE u.uid=ts.uid AND ts.teamid=".$this->getVar('teamid')." ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $members=array();
        while ($row=$this->db->fetchArray($result)) {
            $members[$row["uid"]]=$row["uname"];
        }
        return $members;
    }

    function getPositions() {
        $sql = "SELECT p.posname, p.posid FROM ".$this->db->prefix("team_positions")." p, ".$this->db->prefix("team_teampositions")." tp WHERE p.posid=tp.posid AND tp.teamid=".$this->getVar('teamid')." AND p.postype='Pos' ORDER BY p.posorder ASC";
        $result = $this->db->query($sql);
        $pos=array();
        while ($row=$this->db->fetchArray($result)) {
            $pos[$row["posid"]]=$row["posname"];
        }
        return $pos;
    }

    function getSkills() {
        $sql = "SELECT p.posname, p.posid FROM ".$this->db->prefix("team_positions")." p, ".$this->db->prefix("team_teampositions")." tp WHERE p.posid=tp.posid AND tp.teamid=".$this->getVar('teamid')." AND p.postype='Skill' ORDER BY p.posorder ASC";
        $result = $this->db->query($sql);
        $pos=array();
        while ($row=$this->db->fetchArray($result)) {
            $pos[$row["posid"]]=$row["posname"];
        }
        return $pos;
    }

    function getPlayerPositions() {
        $sql = "SELECT u.uid, u.uname, ts.primarypos, ts.secondarypos, ts.tertiarypos, p.posid FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("team_positions")." p WHERE u.uid=ts.uid AND ts.teamid=".$this->getVar('teamid')." AND ts.primarypos=p.posid ORDER BY ts.status, p.posorder ASC";
        $result = $this->db->query($sql);
        $players=array();
        while ($row=$this->db->fetchArray($result)) {
            $players[$row["uid"]]["name"]=$row["uname"];
            $players[$row["uid"]]["primary"]=$row["primarypos"];
            $players[$row["uid"]]["secondary"]=$row["secondarypos"];
            $players[$row["uid"]]["tertiary"]=$row["tertiarypos"];
            $sql = "SELECT s.posid, p.posshort FROM ".$this->db->prefix("team_skills")." s, ".$this->db->prefix("team_positions")." p WHERE s.uid=".$row["uid"]." AND s.teamid=".$this->getVar('teamid')." AND s.posid=p.posid ORDER BY p.posorder ASC";
            $thisresult = $this->db->query($sql);
            $skills = array();
            while ($skill=$this->db->fetchArray($thisresult)) {
                $skills[$skill["posid"]] = $skill["posshort"];
            }
            $players[$row["uid"]]["skills"] = $skills;
        }
        return $players;
    }

    function getShortList() {
        $sql = "SELECT p.posid, p.posshort FROM ".$this->db->prefix("team_positions")." p, ".$this->db->prefix("team_teampositions")." tp WHERE tp.posid=p.posid AND tp.teamid=".$this->getVar('teamid');
        $result = $this->db->query($sql);
        $posshort = array();
        while ($row=$this->db->fetchArray($result)) {
            $posshort[$row["posid"]]=$row["posshort"];
        }
        return $posshort;
    }

    function getTeamSizes() {
        $sql = "SELECT s.sizeid, s.size FROM ".$this->db->prefix("team_teamsizes")." ts, ".$this->db->prefix("team_sizes")." s WHERE ts.sizeid=s.sizeid AND ts.teamid=".$this->getVar('teamid')." ORDER BY s.size";
        $result = $this->db->query($sql);
        $teamsizes=array();
        while ($row=$this->db->fetchArray($result)) {
            $teamsizes[$row["sizeid"]]=$row["size"];
        }
        return $teamsizes;
    }

    function getSides() {
        $sql = "SELECT s.sideid, s.side FROM ".$this->db->prefix("team_teamsides")." ts, ".$this->db->prefix("team_sides")." s WHERE ts.sideid=s.sideid AND ts.teamid=".$this->getVar('teamid')." ORDER BY s.side";
        $result = $this->db->query($sql);
        $teamsides=array();
        while ($row=$this->db->fetchArray($result)) {
            $teamsides[$row["sideid"]]=$row["side"];
        }
        return $teamsides;
    }

    function getLadders() {
        $sql = "SELECT l.ladderid, l.ladder FROM ".$this->db->prefix("team_teamladders")." tl, ".$this->db->prefix("team_ladders")." l WHERE tl.ladderid=l.ladderid AND tl.teamid=".$this->getVar('teamid')." ORDER BY l.ladder";
        $result = $this->db->query($sql);
        $teamladders=array();
        while ($row=$this->db->fetchArray($result)) {
            $teamladders[$row["ladderid"]]=$row["ladder"];
        }
        return $teamladders;
    }

    function getRanks() {
        $sql = "SELECT r.rankid, r.rank, r.color FROM ".$this->db->prefix("team_teamrank")." tr, ".$this->db->prefix("team_rank")." r WHERE tr.rankid=r.rankid AND tr.teamid=".$this->getVar('teamid')." ORDER BY r.rank";
        $result = $this->db->query($sql);
        $teamranks=array();
        while ($row=$this->db->fetchArray($result)) {
            $teamranks[$row["rankid"]]["rank"]=$row["rank"];
            $teamranks[$row["rankid"]]["color"]=$row["color"];
        }
        return $teamranks;
    }

    function getServers() {
        $sql = "SELECT s.servername, s.serverid FROM ".$this->db->prefix("team_server")." s, ".$this->db->prefix("team_teamservers")." ts WHERE s.serverid=ts.serverid AND ts.teamid='".$this->getVar('teamid')."' ORDER BY s.servername ASC";
        $result = $this->db->query($sql);
        $servers=array();
        while ($row=$this->db->fetchArray($result)) {
            $servers[$row["serverid"]]=$row["servername"];
        }
        return $servers;
    }

    function getActiveMembers() {
        $sql = "SELECT u.uname, u.uid, ts.teamid FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("users")." u WHERE u.uid=ts.uid AND ts.teamid=".$this->getVar('teamid')." AND ts.status='1' ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $members=array();
        while ($row=$this->db->fetchArray($result)) {
            $members[$row["uid"]]=$row["uname"];
        }
        return $members;
    }
    function getAllMembers() {
        $sql = "SELECT t.uid, t.teamid, t.status, t.rank, t.primarypos, t.secondarypos, t.tertiarypos FROM ".$this->db->prefix("team_teamstatus")." t WHERE t.teamid=".$this->getVar('teamid')." AND t.status>0 ORDER BY t.status ASC";
        $result = $this->db->query($sql);
        $i=0;
        while ($row = $this->db->fetchArray($result)) {
            $members[$i]["uid"] = $row["uid"];
            $members[$i]["status"] = $row["status"];
            $members[$i]["rank"] = $row["rank"];
            $members[$i]["primarypos"] = $row["primarypos"];
            $members[$i]["secondarypos"] = $row["secondarypos"];
            $members[$i]["tertiarypos"] = $row["tertiarypos"];
            $uids[$row['uid']] = $i;
            $i++;
        }
        $member_handler = xoops_gethandler('member');
        $users = $member_handler->getUsers(new Criteria('uid', "(".implode(',', array_keys($uids)).")", "IN"));
        if (count($users) > 0) {
            foreach (array_keys($users) as $i) {
                $key = $uids[$users[$i]->getVar('uid')];
                $members[$key]["uname"] = $users[$i]->getVar("uname");
                $members[$key]['user_avatar'] = $users[$i]->getVar('user_avatar');
                $members[$key]['user_from'] = $users[$i]->getVar('user_from');
                $members[$key]['user_regdate'] = $users[$i]->getVar('user_regdate');
                $members[$key]['user_icq'] = $users[$i]->getVar('user_icq');
                $members[$key]['bio'] = $users[$i]->getVar('bio');
            }
        }
        return $members;
    }


    function isTeamAdmin($uid) {
        global $xoopsModule;
        $thisUser = new XoopsUser($uid);
        if ($thisUser->isAdmin($xoopsModule->mid())) {
            return true;
        }
        $sql = "SELECT r.matches, r.tactics FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("team_rank"). " r WHERE ts.teamid=".$this->getVar('teamid')." AND ts.uid=$uid AND ts.rank=r.rankid";
        $result = $this->db->query($sql);
        $teamadmin = $this->db->fetchArray($result);
        if (($teamadmin["matches"]==1)&&($teamadmin["tactics"]==1)) {
            return true;
        }
        else {
            return false;
        }
    }

    function isMatchAdmin($uid) {
        global $xoopsModule;
        $thisUser = new XoopsUser($uid);
        if ($thisUser->isAdmin($xoopsModule->mid())) {
            return true;
        }
        $sql = "SELECT r.matches FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("team_rank"). " r WHERE ts.teamid=".$this->getVar('teamid')." AND ts.uid=$uid AND ts.rank=r.rankid";
        $result = $this->db->query($sql);
        $teamadmin = $this->db->fetchArray($result);
        if ($teamadmin["matches"]==1) {
            return true;
        }
        else {
            return false;
        }
    }

    function isTacticsAdmin($uid) {
        $sql = "SELECT r.tactics FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("team_rank"). " r WHERE ts.teamid=".$this->getVar('teamid')." AND ts.uid=$uid AND ts.rank=r.rankid";
        $result = $this->db->query($sql);
        $teamadmin = $this->db->fetchArray($result);
        if ($teamadmin["tactics"]==1) {
            return true;
        }
        else {
            return false;
        }
    }

    function isTeamMember($uid) {
        $uid = intval($uid);
        $sql = "SELECT rank FROM ".$this->db->prefix("team_teamstatus")." WHERE teamid=".$this->getVar('teamid')." AND uid=$uid";
        $result = $this->db->query($sql);
        $teammember = $this->db->fetchArray($result);
        if (count($teammember)>=1) {
            if ($teammember["rank"]!=NULL) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    function select() {
        global $xoopsTpl;
        $allteams = getTeams();
        if (count($allteams)>1) {
            $xoopsTpl->assign('lang_selecttitle', _AM_TEAMTEAMSELECTION);
            $xoopsTpl->assign('lang_selectcaption', _AM_TEAMTEAM);
            $xoopsTpl->assign('lang_submit', _AM_TEAMSELECT);
            $xoopsTpl->assign('lang_teamid', "teamid");
            $xoopsTpl->assign('showselect', 1);
            $xoopsTpl->assign('teams', $allteams);
            $xoopsTpl->assign('selected', $this->getVar('teamid'));
        }
    }

    function addTeamMember($memberid) {
        $memberid = intval($memberid);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamstatus")." (uid, teamid, status, rank) VALUES ($memberid, ".$this->getVar('teamid').", '1', '3')";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            $result = $this->db->query("SELECT matchid FROM ".$this->db->prefix("team_matches")." WHERE teamid=".$this->getVar('teamid')." AND matchresult='Pending' ORDER BY matchdate DESC");
            while ($match = $this->db->fetchArray($result)) {
                $this->db->query("INSERT INTO ".$this->db->prefix("team_availability")." (userid, availability, matchid) VALUES ($memberid, 'Not Set', ".$match["matchid"].")");
                if (!$this->db->getInsertId()) {
                    $error = 1;
                }
            }
            if (isset($error)) {
                return false;
            }
            else {
                return true;
            }
        }
    }
    function delTeamMember($uid) {
        $uid = intval($uid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamstatus")." WHERE uid=$uid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            $result = $this->db->query("SELECT a.avid FROM ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("team_matches")." m, ".$this->db->prefix("team_teamstatus")." ts WHERE ts.uid=$uid AND ts.teamid=".$this->getVar('teamid')." AND ts.uid=a.userid AND a.matchid=m.matchid AND m.matchresult='Pending'");
            while ($availability = $this->db->fetchArray($result)) {
                $this->db->query("DELETE FROM ".$this->db->prefix("team_availability")." WHERE avid=".$availability["avid"]);
                if (!$this->db->getAffectedRows()) {
                    $error = 1;
                }
            }
            if (isset($error)) {
                return false;
            }
            else {
                return true;
            }
        }
    }

    function addTeamPosition($positionid) {
        $positionid = intval($positionid);
        $sql = "INSERT INTO ".$this->db->prefix("team_teampositions")." (posid, teamid) VALUES ($positionid, ".$this->getVar('teamid').")";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamPosition($positionid) {
        $positionid = intval($positionid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teampositions")." WHERE posid=$positionid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamSkill($positionid) {
        $positionid = intval($positionid);
        $sql = "INSERT INTO ".$this->db->prefix("team_teampositions")." (posid, teamid) VALUES ($positionid, ".$this->getVar('teamid').")";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamSkill($positionid) {
        $positionid = intval($positionid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teampositions")." WHERE posid=$positionid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamServer($serverid) {
        $serverid = intval($serverid);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamservers")." (serverid, teamid) VALUES ($serverid, ".$this->getVar('teamid').")";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamServer($serverid) {
        $serverid = intval($serverid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamservers")." WHERE serverid=$serverid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamMap($mapid) {
        $mapid = intval($mapid);
        $sql = "INSERT INTO ".$this->db->prefix("team_teammaps")." (mapid, teamid) VALUES ($mapid, ".$this->getVar('teamid').")";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }
    function delTeamMap($mapid) {
        $mapid = intval($mapid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teammaps")." WHERE mapid=$mapid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamSize($size_id) {
        $size_id = intval($size_id);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamsizes")." (teamid, sizeid) VALUES (".$this->getVar('teamid').", $size_id)";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamSize($sizeid) {
        $sizeid = intval($sizeid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamsizes")." WHERE sizeid=$sizeid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamSide($side_id) {
        $side_id = intval($side_id);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamsides")." (teamid, sideid) VALUES (".$this->getVar('teamid').", $side_id)";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamSide($sideid) {
        $sideid = intval($sideid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamsides")." WHERE sideid=$sideid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamRank($rank_id) {
        $rank_id = intval($rank_id);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamrank")." (teamid, rankid) VALUES (".$this->getVar('teamid').", $rank_id)";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamRank($rankid) {
        $rankid = intval($rankid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamrank")." WHERE rankid=$rankid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function addTeamLadder($ladder_id) {
        $ladder_id = intval($ladder_id);
        $sql = "INSERT INTO ".$this->db->prefix("team_teamladders")." (teamid, ladderid) VALUES (".$this->getVar('teamid').", $ladder_id)";
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function delTeamLadder($ladderid) {
        $ladderid = intval($ladderid);
        $sql = "DELETE FROM ".$this->db->prefix("team_teamladders")." WHERE ladderid=$ladderid AND teamid=".$this->getVar('teamid');
        if (!$this->db->query($sql)) {
            return false;
        }
        else {
            return true;
        }
    }

    function positions($caption, $array) {
        global $xoopsTpl;
        $teampositions = $this->getPositions();
        $teamskills = $this->getSkills();
        $shortlist = $this->getShortList();
        $players = array();
        foreach ($teampositions as $posid => $posshort) {
            $pricount[$posid] = 0;
            $seccount[$posid] = 0;
            $tercount[$posid] = 0;
            $positions[$posid] = $shortlist[$posid];
        }
        foreach ($teamskills as $posid => $posshort) {
            $skillcount[$posid] = 0;
            $skills[$posid] = $shortlist[$posid];
        }
        foreach ($array as $uid => $player) {
            $pos = array();
            if ($player["primary"]) {
                $pos[0]["posshort"] = $shortlist[$player["primary"]];
                $pos[0]["priority"] = 1;
                $count = $pricount[$player["primary"]];
                $count++;
                $pricount[$player["primary"]] = $count;
            }
            if ($player["secondary"]) {
                $pos[1]["posshort"] = $shortlist[$player["secondary"]];
                $pos[1]["priority"] = 2;
                $count = $seccount[$player["secondary"]];
                $count++;
                $seccount[$player["secondary"]] = $count;
            }
            if (isset($player["tertiary"]) && ($player["tertiary"] > 0)) {
                $pos[2]["posshort"] = $shortlist[$player["tertiary"]];
                $pos[2]["priority"] = 3;
                $count = $tercount[$player["tertiary"]];
                $count++;
                $tercount[$player["tertiary"]] = $count;
            }
            foreach ($player["skills"] as $key => $posshort) {
                $pos[]["posshort"] = $posshort;
                $count = $skillcount[$key];
                $count++;
                $skillcount[$key] = $count;
            }
            if ((isset($class))&&($class=="odd")) {
                $class = "even";
            }
            else {
                $class = "odd";
            }
            $players[] = array('uid' => $uid, 'name' => $player["name"], 'class' => $class, 'positions' => $pos);
        }
        $table = array('caption' => $caption, 'players' => $players, 'pricount' => $pricount, 'seccount' => $seccount, 'tercount' => $tercount, 'skillcount' => $skillcount);
        $xoopsTpl->append('tables', $table);
        $xoopsTpl->assign('teampos', array('posshort' => $positions));
        $xoopsTpl->assign('teamskills', array('posshort' => $skills));
        $xoopsTpl->assign('numcells', count($teampositions)+count($teamskills)+1);
        $xoopsTpl->assign('width', 100/(count($teampositions)+ count($teamskills)+1));
        $xoopsTpl->assign('numskills', count($teamskills));
    }
    
    /**
    * Returns an array representation of the object
    *
    * @return array
    */
    function toArray() {
        $ret = array();
        $vars = $this->getVars();
        foreach (array_keys($vars) as $i) {
            $ret[$i] = $this->getVar($i);
        }
        return $ret;
    }
}

class TeamTeamHandler extends XoopsPersistableObjectHandler {

    function TeamTeamHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_team", "TeamTeam", "teamid", "teamname");
    }

    /**
    * delete a team from the database
    *
    * @param object $team reference to the {@link TeamTeam} to delete
    * @param bool $force
    * @return bool FALSE if failed.
    */
    function delete(&$team, $force = false)
    {
        if (parent::delete($team, $force)) {
            $teamid = $team->getVar('teamid');
            $sql = "DELETE FROM ".$this->db->prefix("team_teamstatus")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $sql = "DELETE FROM ".$this->db->prefix("team_teamrank")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $sql = "DELETE FROM ".$this->db->prefix("team_teampositions")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $sql = "DELETE FROM ".$this->db->prefix("team_teammaps")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $sql = "DELETE FROM ".$this->db->prefix("team_teamservers")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $sql = "DELETE FROM ".$this->db->prefix("team_teamsizes")." WHERE teamid = $teamid";
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $module_id = $GLOBALS['xoopsModule']->getVar('mid');
            xoops_notification_deletebyitem ($module_id, 'team', $teamid);
        }
        return true;
    }

    function setDefault($team) {
        $this->updateAll("defteam", 0);
        return $this->updateAll("defteam", 1, new Criteria("teamid", $team->getVar('teamid')));
    }
}
?>

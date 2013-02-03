<?php
// Class for Player management for Team Module
// $Id: player.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
class Player extends XoopsUser
{
    var $_teams = array();
    var $_positions = array();
    var $db;

    //Constructor
	function Player($id=-1)
	{
          $this->db =& Database::getInstance();
          $this->XoopsUser($id);
	}

    function getTeams() {
        $player=array();
        $sql = "SELECT statusid, teamid FROM ".$this->db->prefix("team_teamstatus")." WHERE uid='".$this->getVar("uid")."'";
        $result = $this->db->query($sql);
        while ($teammember = $this->db->fetchArray($result)) {
            $player[$teammember["statusid"]] = $teammember["teamid"];
        }
        return $player;
    }

    function getAvailabilities($pending) {
        if ($pending==1) {
            $type = "AND m.matchresult='Pending'";
        }
        else {
            $type = "AND m.matchresult<>'Pending'";
        }
        $availability = array();
        $sql = "SELECT a.matchid, a.availability, m.matchdate, m.matchresult FROM ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("team_matches")." m WHERE m.matchid=a.matchid AND a.userid=".$this->getVar("uid")." ".$type." ORDER BY m.matchdate DESC";
        $result = $this->db->query($sql);
        while ($availabilities =$this->db->fetchArray($result)) {
            $availability[$availabilities["matchid"]] = $availabilities["availability"];
        }
        return $availability;
    }

    function getRank($teamid) {
        $thisrank=array();
        $teamid = intval($teamid);
        $sql = "SELECT r.rankid, r.rank FROM ".$this->db->prefix("team_teamstatus")." ts, ".$this->db->prefix("team_rank")." r WHERE ts.rank=r.rankid AND ts.teamid=$teamid AND ts.uid='".$this->getVar("uid")."'";
        $result = $this->db->query($sql);
        while ($rank = $this->db->fetchArray($result)) {
            $thisrank["rank"] = $rank["rank"];
            $thisrank["rankid"] = $rank["rankid"];
        }
        return $thisrank;
    }

}
?>

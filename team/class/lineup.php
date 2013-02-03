<?php
// Class for Lineup management for Team Module
// $Id: lineup.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class Lineup
{
    var $lineuptable;
    var $positionstable;
    var $maptable;
	var $db;
    var $matchid;
    var $mapname;
    var $mapid;
    var $general;

    //Constructor
	function Lineup($matchid=0, $mapid=null)
	{
		$this->db =& Database::getInstance();
        $this->positionstable = $this->db->prefix("team_lineups_positions");
        $this->maptable = $this->db->prefix("team_mappool");
		if ( is_array($matchid) ) {
			$this->makeLineup($matchid);
		} elseif ($matchid!=0) {
            $this->mapid = $mapid;
            $this->matchid = $matchid;
            $this->mapname = $this->getMapname();
            $this->general = $this->fetchGeneral();
		}
	}
    function getPositions() {
        $array = array();
        $sql = "SELECT lineupid, uid, posid, posdesc FROM ".$this->db->prefix("team_lineups_positions")." WHERE matchid=".$this->matchid." AND mapid=". $this->mapid." ORDER BY lineupid";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $array[] = array("lineupid" =>$row["lineupid"], "uid" => $row["uid"], "posid" => $row["posid"], "posdesc" => $row["posdesc"]);
        }
        if (count($array)>0) {
            return $array;
        }
        else {
            return array();
        }
    }

	function makeLineup($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
    }
    
    function getMapname()
    {
        $sql = "SELECT mapname FROM ".$this->maptable." WHERE mapid=".$this->mapid;
        $result = $this->db->query($sql);
        $row = $this->db->fetchArray($result);
        return $row["mapname"];
    }

    function fetchGeneral()
    {
        $sql = "SELECT general FROM ".$this->db->prefix("team_matchmaps")." WHERE mapid=".$this->mapid." AND matchid=".$this->matchid;
        $result = $this->db->query($sql);
        $row = $this->db->fetchArray($result);
        $this->general = $row["general"];
        return $row["general"];
    }
    
    function saveGeneral()
    {
        if ( !isset($this->matchid) ) {
            return false;
        }
        else {
            $sql = "UPDATE ".$this->db->prefix("team_matchmaps")." SET general=".$this->db->quoteString($this->general)."
                 WHERE matchid=".intval($this->matchid)." AND mapid=".intval($this->mapid);
        }
        if (!$result = $this->db->query($sql)) {
			return false;
		}
        return true;   
    }

	function setMatchid($value)
	{
		$this->matchid = intval($value);
	}

	function setMapid($value)
	{
		$this->mapid = intval($value);
	}

	function setGeneral($value)
	{
		$this->general = $value;
	}

    function setMapname($value)
    {
        $this->mapname = $value;
    }

	function matchid()
	{
		return $this->matchid;
	}

	function mapid()
	{
		return $this->mapid;
	}

    function mapname()
    {
        return $this->mapname;
    }

    function general()
    {
        return $this->general;
    }
    
    function show() {
        $team = new Team($this->teamid);
        echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
        echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
        echo "<tr class='head'><td colspan=2><h3>";
        echo $this->teamsize." "._AM_TEAMVERSUS." ".$this->teamsize." "._AM_TEAMTACTICSFOR." ".$team->teamname." "._AM_TEAMON." ".$this->mapname;
        echo "</h3></td></tr>";
        echo "<tr><td colspan=2>";
        include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $mform = new XoopsThemeForm(_AM_TEAMTACTICSDISPLAY, "display", xoops_getenv('PHP_SELF'));
        $general = new XoopsFormLabel(_AM_TEAMGENERALTACS, $this->general);
        $mform->addElement($general);
        $positions = $this->getPositions();
        $posshortlist = getShortList($this->teamid);
        foreach ($positions as $key => $tacposid) {
            $thispos = new TacticsPosition($tacposid);
            $posshort = $posshortlist[$thispos->posid()];
            $position[$key] = new XoopsFormLabel($posshort, $thispos->posdesc());
            $mform->addElement($position[$key]);
        }
        $mform->display();
        echo "</table></td></tr></table>";
    }
}
?>

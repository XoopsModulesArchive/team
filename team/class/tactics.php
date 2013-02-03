<?php
// Class for Tactics management for Team Module
// $Id: tactics.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class Tactics
{
    var $tacticstable;
    var $positionstable;
    var $maptable;
	var $db;
    var $tacid;
    var $teamsize;
    var $teamid;
    var $general;
    var $mapname;
    var $mapid;

    //Constructor
	function Tactics($tacid=0, $mapid=null, $teamsize=null)
	{
		$this->db =& Database::getInstance();
		$this->tacticstable = $this->db->prefix("team_tactics");
        $this->positionstable = $this->db->prefix("team_tactics_positions");
        $this->maptable = $this->db->prefix("team_mappool");
		if ( is_array($tacid) ) {
			$this->makeTactics($tacid);
		} elseif (($tacid!=0)&&($teamsize!=null)) {
            $this->teamsize = intval($teamsize);
            $this->mapid = intval($mapid);
            $this->teamid = intval($tacid);
			$this->fetchTactics();
		}
        elseif ($tacid!=0) {
            $this->getTactics(intval($tacid));
        }
	}
    function getPositions() {
        $sql = "SELECT tacposid FROM ".$this->positionstable." WHERE tacid=".$this->tacid." ORDER BY tacid";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetchArray($result)) {
            $array[] = $row["tacposid"];
        }
        if (isset($array)) {
            return $array;
        }
        else {
            return false;
        }
    }

    function getTactics($tacid) {
        $sql = "SELECT t.tacid, t.mapid, t.teamsize, t.teamid, t.general, m.mapname FROM ".$this->tacticstable." t, ".$this->maptable." m WHERE t.mapid=m.mapid AND t.tacid=".$tacid;
        $result = $this->db->query($sql);
        if ($this->db->getRowsNum($result)>0) {
            $array=$this->db->fetchArray($result);
            $this->makeTactics($array);
        }
    }
    
 	function fetchTactics()
	{
        $sql = "SELECT t.tacid, t.general, m.mapname FROM ".$this->tacticstable." t, ".$this->maptable." m WHERE t.mapid=m.mapid AND teamid=".$this->teamid." AND t.mapid=".$this->mapid." AND teamsize=".$this->teamsize;
        $result = $this->db->query($sql);
        if ($this->db->getRowsNum($result)>0) {
            $array=$this->db->fetchArray($result);
            if (count($array)!=0) {
                $this->makeTactics($array);
            }
        }
        else {
            $sql = "SELECT mapname FROM ".$this->maptable." WHERE mapid=".$this->mapid();
            $result = $this->db->query($sql);
            $thismap = $this->db->fetchArray($result);
            $this->setMapname($thismap["mapname"]);
        }
	}

	function makeTactics($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}

	function setTacid($value)
	{
		$this->tacid = intval($value);
	}

	function setMapid($value)
	{
		$this->mapid = intval($value);
	}
 
    function setMapname($value)
    {
        $this->mapname = $value;
    }

	function setTeamsize($value)
	{
		$this->teamsize = intval($value);
	}

	function setTeamid($value)
	{
		$this->teamid = intval($value);
	}

	function setGeneral($value)
	{
		$this->general = $value;
	}

	function store()
 	{
		if ( !isset($this->tacid) ) {
            $sql = "INSERT INTO ".$this->tacticstable."
            (mapid, teamsize, teamid, general)
            VALUES (".intval($this->mapid).", ".intval($this->teamsize).", ".intval($this->teamid).", ".$this->db->quoteString($this->general).")";
		} else {
			$sql = "UPDATE ".$this->tacticstable."
            SET mapid=".intval($this->mapid).",
            teamsize=".intval($this->teamsize).",
            teamid=".intval($this->teamid).",
            general=".$this->db->quoteString($this->general)."
            WHERE tacid = ".intval($this->tacid);
			$newtacid = $this->tacid;
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newtacid) ) {
			$newtacid = $this->db->getInsertId();
			$this->tacid = $newtacid;
		}
		return $newtacid;
	}

	function tacid()
	{
		return $this->tacid;
	}

	function mapid()
	{
		return $this->mapid;
	}

    function mapname()
    {
        return $this->mapname;
    }

	function teamsize()
	{
		return $this->teamsize;
	}

	function teamid()
	{
		return $this->teamid;
	}

	function general()
	{
		return $this->general;
	}

    function show() {
        $team = new Team($this->teamid);
        echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
        echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
        echo "<tr class='head'><td><h3>";
        echo $this->teamsize." "._AM_TEAMVERSUS." ".$this->teamsize." "._AM_TEAMTACTICSFOR." ".$team->teamname." "._AM_TEAMON." ".$this->mapname;
        echo "</h3></td><td align='right'>";
        echo "<a href='tactics.php?op=mantactics&tacid=".$this->tacid()."'>";
        echo "<img src='images/edit.gif' border='0' alt='Edit'></a></td></tr>";
        echo "<tr><td colspan=2>";
        include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $mform = new XoopsThemeForm(_AM_TEAMTACTICSDISPLAY, "display", xoops_getenv('PHP_SELF'));
        $general = new XoopsFormLabel(_AM_TEAMGENERALTACS, $this->general);
        $mform->addElement($general);
        $positions = $this->getPositions();
        $posshortlist = $team->getShortList();
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

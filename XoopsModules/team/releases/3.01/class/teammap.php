<?php
// Class for Match Map management for Team Module
// $Id: teammap.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class XoopsMatchMap extends XoopsObject 
{
    var $table;
	var $db;
    var $matchmapid;
	var $matchid;
    var $mapname;
    var $mapid;
    var $mapno;
    var $ourscore;
    var $theirscore;
    var $side;
    var $general;
	var $screenshot;

    //Constructor
	function XoopsMatchMap($matchid=null, $mapno=null)
	{
		$this->db =& Database::getInstance();
		$this->table = $this->db->prefix("team_matchmaps");
		if ( is_array($matchid) ) {
			$this->makeMatchMap($matchid);
		} elseif ($mapno!=null) {
			$this->getMatchMap(intval($matchid), intval($mapno));
		}
        else {
            $this->fetchMatchMap(intval($matchid));
        }
	}

	function getMatchMap($matchid, $mapno)
	{
		$sql = "SELECT map.matchmapid, map.mapno, map.mapid, map.matchid, map.ourscore, map.theirscore, pool.mapname, map.side, map.general, map.screenshot FROM ".$this->table." map, ".$this->db->prefix("team_mappool")." pool WHERE map.mapid=pool.mapid AND matchid=".$matchid." AND mapno=".$mapno;
		$array = $this->db->fetchArray($this->db->query($sql));
        if ($array) {
            $this->makeMatchMap($array);
        }
	}

 	function fetchMatchMap($matchmapid)
	{
		$sql = "SELECT map.matchmapid, map.mapno, map.mapid, map.matchid, map.ourscore, map.theirscore, pool.mapname, map.side, map.general, map.screenshot FROM ".$this->table." map, ".$this->db->prefix("team_mappool")." pool WHERE map.mapid=pool.mapid AND map.matchmapid=".$matchmapid;
		$array = $this->db->fetchArray($this->db->query($sql));
        if ($array) {
            $this->makeMatchMap($array);
        }
	}

	function makeMatchMap($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}
	function setMatchmapId($value)
	{
		$this->matchmapid = intval($value);
	}

	function setMatchId($value)
	{
		$this->matchid = intval($value);
	}

	function setMapid($value)
	{
		$this->mapid = intval($value);
	}
 
    function setMapname($value)
    {
        $this->mapname = $value;
    }
    
	function setMapno($value)
	{
		$this->mapno = intval($value);
	}

	function setOurscore($value)
	{
		$this->ourscore = intval($value);
	}

	function setTheirscore($value)
	{
		$this->theirscore = intval($value);
	}
	function setSide($value)
	{
		$this->side = intval($value);
	}

	function setScreenshot($value)
	{
		$this->screenshot = $value;
	}

	function store()
 	{
		if ( !isset($this->matchmapid) ) {
            $sql = "INSERT INTO ".$this->table."
            (matchid, mapid, mapno, side)
            VALUES (".intval($this->matchid).", ".intval($this->mapid).", ".intval($this->mapno).", ".intval($this->side).")";
		} else {
			$sql = "UPDATE ".$this->table."
            SET matchid=".intval($this->matchid).",
            mapid=".intval($this->mapid).",
            mapno=".intval($this->mapno).",
            ourscore=".intval($this->ourscore).",
            theirscore=".intval($this->theirscore).",
            side=".intval($this->side)."
            WHERE matchmapid = ".intval($this->matchmapid);
			$newmatchmapid = $this->matchmapid;
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newmatchmapid) ) {
			$newmatchmapid = $this->db->getInsertId();
			$this->matchmapid = $newmatchmapid;
		}
		return $newmatchmapid;
	}

	function matchmapid()
	{
		return $this->matchmapid;
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

    function mapno()
	{
		return $this->mapno;
	}
	function ourscore()
	{
		return $this->ourscore;
	}

	function theirscore()
	{
		return $this->theirscore;
	}
 
    function side()
    {
        return $this->side;
    }

    function screenshot()
    {
        return $this->screenshot;
    }
    
    //Find winner of a map
    function winner($layout) {
        $our = $this->ourscore;
        $their = $this->theirscore;
        $winner= $our-$their;
        if ($winner>=1) {
            return $layout["color_match_win"];
        }
        elseif ($winner<=-1){
            return $layout["color_match_loss"];
        }
        else {
            return $layout["color_match_draw"];
        }
    }

    function getTacid($teamid,$teamsize) {
        $teamid = intval($teamid);
        $teamsize = intval($teamsize);
		$sql = "SELECT tacid FROM ".$this->db->prefix("team_tactics")."
         WHERE mapid=".$this->mapid." AND teamid=".$teamid." AND teamsize=".$teamsize;
		$array = $this->db->fetchArray($this->db->query($sql));
		return $array["tacid"];
    }
}
class XoopsMatchMapHandler extends XoopsObjectHandler {
    
}
?>
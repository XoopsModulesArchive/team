<?php
// Class for Lineup management for Team Module
// $Id: lineupposition.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class LineupPosition
{
    var $table;
	var $db;
    var $lineupid;
    var $uid;
    var $posid;
    var $posdesc;
    var $matchid;
    var $mapid;

    //Constructor
	function LineupPosition($lineupid=0)
	{
		$this->db =& Database::getInstance();
        $this->table = $this->db->prefix("team_lineups_positions");
		if ( is_array($lineupid) ) {
			$this->makeLineupPosition($lineupid);
		} elseif ($lineupid!=0){
            $this->getLineupPosition(intval($lineupid));
        }
	}

    function getLineupPosition($lineupid) {
        $sql = "SELECT lineupid, posid, posdesc, uid, matchid, mapid FROM ".$this->table." WHERE lineupid=".$lineupid;
        $result = $this->db->query($sql);
        $array=$this->db->fetchArray($result);
        $this->makeLineupPosition($array);
    }
    
	function makeLineupPosition($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}

	function setLineupid($value)
	{
		$this->lineupid = intval($value);
	}

	function setUid($value)
	{
		$this->uid = intval($value);
	}
 
    function setPosdesc($value)
    {
        $this->posdesc = $value;
    }

	function setPosid($value)
	{
		$this->posid = intval($value);
	}

	function setMatchid($value)
	{
		$this->matchid = intval($value);
	}

	function setMapid($value)
	{
		$this->mapid = intval($value);
	}

  	function store()
 	{
		if ( !isset($this->lineupid) ) {
            $sql = "INSERT INTO ".$this->table."
            (posid, posdesc, uid, matchid, mapid)
            VALUES (".intval($this->posid).", ".$this->db->quoteString($this->posdesc).", ".intval($this->uid).", ".intval($this->matchid).", ".intval($this->mapid).")";
		} else {
			$sql = "UPDATE ".$this->table."
            SET uid=".intval($this->uid).",
            posdesc=".$this->db->quoteString($this->posdesc).",
            posid=".inval($this->posid).",
            matchid=".intval($this->matchid).",
            mapid=".intval($this->mapid)."
            WHERE lineupid = ".intval($this->lineupid);
			$newlineupid = $this->lineupid;
		}
		if (!$result = $this->db->queryF($sql)) {
			return false;
		}
		if ( empty($newlineupid) ) {
			$newlineupid = $this->db->getInsertId();
			$this->lineupid = $newlineupid;
		}
		return $newlineupid;
	}

	function lineupid()
	{
		return $this->lineupid;
	}

	function posid()
	{
		return $this->posid;
	}

    function posdesc()
    {
        return $this->posdesc;
    }

	function uid()
	{
		return $this->uid;
	}
 
    function matchid()
    {
        return $this->matchid;
    }
    
    function mapid()
    {
        return $this->mapid;
    }

}
?>

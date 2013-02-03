<?php
// Class for Tactics management for Team Module
// $Id: tacticsposition.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class TacticsPosition
{
    var $table;
	var $db;
    var $tacposid;
    var $tacid;
    var $posid;
    var $posdesc;

    //Constructor
	function TacticsPosition($tacposid=0)
	{
		$this->db =& Database::getInstance();
        $this->table = $this->db->prefix("team_tactics_positions");
		if ( is_array($tacposid) ) {
			$this->makeTacticsPosition($tacposid);
		} elseif ($tacposid!=0){
            $this->getTacticsPosition(intval($tacposid));
        }
	}

    function getTacticsPosition($tacposid) {
        $sql = "SELECT tacposid, posid, posdesc, tacid FROM ".$this->table." WHERE tacposid=".$tacposid;
        $result = $this->db->query($sql);
        $array=$this->db->fetchArray($result);
        $this->makeTacticsPosition($array);
    }
    
	function makeTacticsPosition($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}

	function setTacid($value)
	{
		$this->tacid = intval($value);
	}

	function setTacposid($value)
	{
		$this->tacposid = intval($value);
	}
 
    function setPosdesc($value)
    {
        $this->posdesc = $value;
    }

	function setPosid($value)
	{
		$this->posid = intval($value);
	}

	function store()
 	{
		if ( !isset($this->tacposid) ) {
            $sql = "INSERT INTO ".$this->table."
            (posid, posdesc, tacid)
            VALUES (".intval($this->posid).", ".$this->db->quoteString($this->posdesc).", ".intval($this->tacid).")";
		} else {
			$sql = "UPDATE ".$this->table."
            SET tacid=".intval($this->tacid).",
            posdesc=".$this->db->quoteString($this->posdesc).",
            posid=".intval($this->posid)."
            WHERE tacposid = ".intval($this->tacposid);
			$newtacposid = $this->tacposid;
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newtacposid) ) {
			$newtacposid = $this->db->getInsertId();
			$this->tacposid = $newtacposid;
		}
		return $newtacposid;
	}

	function tacid()
	{
		return $this->tacid;
	}

	function posid()
	{
		return $this->posid;
	}

    function posdesc()
    {
        return $this->posdesc;
    }

	function tacposid()
	{
		return $this->tacposid;
	}

}
?>

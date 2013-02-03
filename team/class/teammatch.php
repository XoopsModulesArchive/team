<?php
// Class for Match management for Team Module
// $Id: teammatch.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class Match
{
    var $table;
	var $db;
	var $matchid;
	var $uid;
	var $matchdate;
    var $teamid;
	var $created;
    var $teamsize;
	var $opponent;
	var $ladder;
 	var $matchresult;
 	var $review;
	var $server;
	var $customserver;
	var $alock;

    //Constructor
	function Match($matchid=-1)
	{
		$this->db =& Database::getInstance();
		$this->table = $this->db->prefix("team_matches");
		if ( is_array($matchid) ) {
			$this->makeMatch($matchid);
		} elseif ( $matchid != -1 ) {
			$this->getMatch(intval($matchid));
		}
	}

	function getMatch($matchid)
	{
		$sql = "SELECT * FROM ".$this->table." WHERE matchid=".$matchid."";
		$array = $this->db->fetchArray($this->db->query($sql));
		$this->makeMatch($array);
	}


	function makeMatch($array)
	{
		foreach ( $array as $key=>$value ){
			$this->$key = $value;
		}
	}

    function setMatchMapId($value)
	{
		$this->matchid = intval($value);
	}
 
	function setMatchId($value)
	{
		$this->matchid = intval($value);
	}
	function setOpponent($value)
	{
		$this->opponent = $value;
	}

	function setUid($value)
	{
		$this->uid = intval($value);
	}
	function setTeamsize($value)
	{
		$this->teamsize = intval($value);
	}
	function setTeamid($value)
	{
		$this->teamid = intval($value);
	}

	function setCreated($value)
	{
		$this->created = intval($value);
	}

	function setMatchdate($value)
	{
		$this->matchdate = intval($value);
	}

	function setMatchresult($value)
	{
		$this->matchresult = $value;
	}

	function setReview($value)
	{
		$this->review = $value;
	}

	function setLadder($value)
	{
		$this->ladder = $value;
	}

	function setServer($value)
	{
		$this->server = intval($value);
	}

	function setCustomServer($value)
	{
		$this->customserver = $value;
	}

	function setAlock($value=0)
	{
		$this->alock = intval($value);
	}


	function store()
 	{
   		global $myts;
        $myts =& MyTextSanitizer::getInstance();
        $opponent = "";
        if ( isset($this->opponent) && $this->opponent != "" ) {
			$opponent = $myts->makeTboxData4Save($this->opponent);
		}
		// If server from list specified do not save customserver
		if ($this->server != 0) {$customserver = '';} else {$customserver = $myts->makeTboxData4Save($this->customserver);};
		if ( !isset($this->matchid) ) {
            $sql = "INSERT INTO ".$this->table."
            (uid, matchdate, teamid, created, teamsize, opponent, ladder, matchresult, review, server, customserver, alock)
            VALUES (".intval($this->uid).", ".intval($this->matchdate).", ".intval($this->teamid).", ".intval($this->created).", ".intval($this->teamsize).", ".$this->db->quoteString($opponent).", ".$this->db->quoteString($this->ladder).", 'Pending', ".$this->db->quoteString($this->review).", ".intval($this->server).", ".$this->db->quoteString($customserver).",0)";
		} else {
			$sql = "UPDATE ".$this->table."
            SET uid=".intval($this->uid).",
            opponent=".$this->db->quoteString($opponent).",
            matchdate=".intval($this->matchdate).",
            matchresult=".$this->db->quoteString($this->matchresult).",
            teamsize=".intval($this->teamsize).",
            review=".$this->db->quoteString($this->review).",
            ladder=".$this->db->quoteString($this->ladder).",
            server=".intval($this->server).",
            customserver=".$this->db->quoteString($customserver).",
            alock=".intval($this->alock)." WHERE matchid = ".intval($this->matchid);
			$newmatchid = $this->matchid;
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newmatchid) ) {
			$newmatchid = $this->db->getInsertId();
			$this->matchid = $newmatchid;
		}
		return $newmatchid;
	}

	function deleteMatch()
	{
		$sql = "DELETE FROM ".$this->table." WHERE matchid = ".intval($this->matchid);
		if( $this->db->query($sql) ) {
			$sql = "DELETE FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid = ".$this->matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
			$sql = "DELETE FROM ".$this->db->prefix("team_availability")." WHERE matchid = ".$this->matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
			$sql = "DELETE FROM ".$this->db->prefix("team_lineups_positions")." WHERE matchid = ".$this->matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $module_id = $xoopsModule->getVar('mid');
            xoops_notification_deletebyitem ($module_id, 'match', $this->matchid);
		}
		return true;
	}
	function uid()
	{
		return $this->uid;
	}

	function uname()
	{
		return XoopsUser::getUnameFromId($this->uid);
	}
	function matchid()
	{
		return $this->matchid;
	}
	function teamsize()
	{
		return $this->teamsize;
	}
	function teamid()
	{
		return $this->teamid;
	}
	function opponent()
	{
		return $this->opponent;
	}

	function created()
	{
		return $this->created;
	}

	function matchdate()
	{
		return $this->matchdate;
	}
	function matchresult()
	{
		return $this->matchresult;
	}

	function review()
	{
		return $this->review;
	}

	function ladder()
	{
		return $this->ladder;
	}

	function server()
	{
		return $this->server;
	}

	function customserver()
	{
		return $this->customserver;
	}
 	function alock()
	{
		return $this->alock;
	}
    function getMatchPlayers() {
        $sql = "SELECT u.uid, u.uname FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a WHERE u.uid=a.userid AND a.matchid=".$this->matchid." AND (a.availability='Yes' OR a.availability='LateYes') ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        while ($row=$this->db->fetchArray($result)) {
            $players[$row["uid"]]=$row["uname"];
        }
        return $players;
    }

    function getMatchSubs() {
        $sql = "SELECT u.uid, u.uname FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a WHERE u.uid=a.userid AND a.matchid=".$this->matchid." AND a.availability='Sub' ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        while ($row=$this->db->fetchArray($result)) {
            $players[$row["uid"]]=$row["uname"];
        }
        return $players;
    }
    
    function getPositions($criteria) {
        $sql = "SELECT u.uid, u.uname, ts.primarypos, ts.secondarypos, ts.tertiarypos FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("team_teamstatus")." ts WHERE u.uid=a.userid AND u.uid=ts.uid AND ts.teamid=".$this->teamid." AND a.matchid=".$this->matchid." AND a.availability = ".$this->db->quoteString($criteria)." ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        if ($this->db->getRowsNum($result)>0) {
            while ($row=$this->db->fetchArray($result)) {
                $players[$row["uid"]]["name"]=$row["uname"];
                $players[$row["uid"]]["primary"]=$row["primarypos"];
                $players[$row["uid"]]["secondary"]=$row["secondarypos"];
                $players[$row["uid"]]["tertiary"]=$row["tertiarypos"];
                $sql = "SELECT s.posid, p.posshort FROM ".$this->db->prefix("team_skills")." s, ".$this->db->prefix("team_positions")." p WHERE s.uid=".$row["uid"]." AND s.posid=p.posid AND s.teamid=".$this->teamid." ORDER BY p.posorder ASC";
                $thisresult = $this->db->query($sql);
                $skills = array();
                while ($skill=$this->db->fetchArray($thisresult)) {
                    $skills[$skill["posid"]] = $skill["posshort"];
                }
                $players[$row["uid"]]["skills"] = $skills;
            }
            return $players;
        }
        else {
            return false;
        }
    }

    function getMatchMaps() {
        $sql = "SELECT mapno, matchmapid FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid = ".$this->matchid." ORDER BY mapno";
        $result = $this->db->query($sql);
        $maps = array();
        while ($thismap = $this->db->fetchArray($result)) {
            $maps[$thismap["mapno"]] = $thismap["matchmapid"];
        }
        return $maps;
    }
    
    function lock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=1 WHERE matchid=".$this->matchid;
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->teamid,3,_AM_TEAMMATCHLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->matchid, 3, _AM_DBNOTUPDATED);
        }
    }
    function unlock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=0 WHERE matchid=".$this->matchid;
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->teamid,3,_AM_TEAMMATCHUNLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->matchid, 3, _AM_DBNOTUPDATED);
        }
    }
    function getAvailabilities() {
        $sql = "SELECT a.avid, a.userid, a.availability, a.comment, u.uname FROM ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("users")." u WHERE a.matchid=".$this->matchid." AND u.uid=a.userid ORDER BY u.uname ASC";
        return $this->db->query($sql);
    }

	// Returns number of maps for this match
    function getMapCount() {
        $sql = "SELECT COUNT(matchmapid) as maps FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid=".$this->matchid;
		$result = $this->db->query($sql);
		$row=$this->db->fetchArray($result);
        return $row["maps"];
    }


}
?>

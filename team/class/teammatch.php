<?php
// Class for Match management for Team Module
// $Id: teammatch.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //

class XoopsMatch extends XoopsObject 
{
    var $table;
	var $db;

    //Constructor
	function XoopsMatch($matchid=-1)
	{
		$this->db =& Database::getInstance();
		$this->table = $this->db->prefix("team_matches");
		$this->initVar('matchid', XOBJ_DTYPE_INT);
		$this->initVar('uid', XOBJ_DTYPE_INT);
		$this->initVar('matchdate', XOBJ_DTYPE_INT);
		$this->initVar('teamid', XOBJ_DTYPE_INT);
		$this->initVar('created', XOBJ_DTYPE_INT);
		$this->initVar('teamsize', XOBJ_DTYPE_INT, 0);
		$this->initVar('opponent', XOBJ_DTYPE_TXTBOX);
		$this->initVar('ladder', XOBJ_DTYPE_TXTBOX, "");
		$this->initVar('matchresult', XOBJ_DTYPE_TXTBOX);
		$this->initVar('review', XOBJ_DTYPE_TXTBOX);
		$this->initVar('server', XOBJ_DTYPE_INT);
		$this->initVar('customserver', XOBJ_DTYPE_TXTBOX);
		$this->initVar('alock', XOBJ_DTYPE_INT);
		if ( is_array($matchid) ) {
			$this->assignVars($matchid);
		} elseif ( $matchid != -1 ) {
		    $match_handler =& xoops_gethandler('match');
            $match =& $match_handler->get($matchid);
            foreach ($match->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            unset($match);
		}
	}

	function uname()
	{
		return XoopsUser::getUnameFromId($this->getVar('uid'));
	}

	function getMatchPlayers() {
        $sql = "SELECT u.uid, u.uname FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a WHERE u.uid=a.userid AND a.matchid=".$this->getVar('matchid')." AND (a.availability='Yes' OR a.availability='LateYes') ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        while ($row=$this->db->fetchArray($result)) {
            $players[$row["uid"]]=$row["uname"];
        }
        return $players;
    }

    function getMatchSubs() {
        $sql = "SELECT u.uid, u.uname FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a WHERE u.uid=a.userid AND a.matchid=".$this->getVar('matchid')." AND a.availability='Sub' ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        while ($row=$this->db->fetchArray($result)) {
            $players[$row["uid"]]=$row["uname"];
        }
        return $players;
    }
    
    function getPositions($criteria) {
        $sql = "SELECT u.uid, u.uname, ts.primarypos, ts.secondarypos, ts.tertiarypos FROM ".$this->db->prefix("users")." u, ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("team_teamstatus")." ts WHERE u.uid=a.userid AND u.uid=ts.uid AND ts.teamid=".$this->getVar('teamid')." AND a.matchid=".$this->getVar('matchid')." AND a.availability = ".$this->db->quoteString($criteria)." ORDER BY u.uname ASC";
        $result = $this->db->query($sql);
        $players=array();
        if ($this->db->getRowsNum($result)>0) {
            while ($row=$this->db->fetchArray($result)) {
                $players[$row["uid"]]["name"]=$row["uname"];
                $players[$row["uid"]]["primary"]=$row["primarypos"];
                $players[$row["uid"]]["secondary"]=$row["secondarypos"];
                $players[$row["uid"]]["tertiary"]=$row["tertiarypos"];
                $sql = "SELECT s.posid, p.posshort FROM ".$this->db->prefix("team_skills")." s, ".$this->db->prefix("team_positions")." p WHERE s.uid=".$row["uid"]." AND s.posid=p.posid AND s.teamid=".$this->getVar('teamid')." ORDER BY p.posorder ASC";
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
        $sql = "SELECT mapno, matchmapid FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid = ".$this->getVar('matchid')." ORDER BY mapno";
        $result = $this->db->query($sql);
        $maps = array();
        while ($thismap = $this->db->fetchArray($result)) {
            $maps[$thismap["mapno"]] = $thismap["matchmapid"];
        }
        return $maps;
    }
    
    function lock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=1 WHERE matchid=".$this->getVar('matchid');
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->teamid,3,_AM_TEAMMATCHLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->matchid, 3, _AM_DBNOTUPDATED);
        }
    }
    function unlock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=0 WHERE matchid=".$this->getVar('matchid');
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->teamid,3,_AM_TEAMMATCHUNLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->matchid, 3, _AM_DBNOTUPDATED);
        }
    }
    function getAvailabilities() {
        $sql = "SELECT a.avid, a.userid, a.availability, a.comment, u.uname FROM ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("users")." u WHERE a.matchid=".$this->getVar('matchid')." AND u.uid=a.userid ORDER BY u.uname ASC";
        return $this->db->query($sql);
    }

	// Returns number of maps for this match
    function getMapCount() {
        $sql = "SELECT COUNT(matchmapid) as maps FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid=".$this->getVar('matchid');
		$result = $this->db->query($sql);
		$row=$this->db->fetchArray($result);
        return $row["maps"];
    }


}

class XoopsMatchHandler extends XoopsObjectHandler {
    /**
     * create a new match object
     * 
     * @param bool $isNew flag the new objects as "new"?
     * @return object {@link XoopsMatch}
     */
    function &create($isNew = true)
    {
        $match = new XoopsMatch();
        if ($isNew) {
            $match->setNew();
        }
        return $match;
    }

    /**
     * retrieve a match
     * 
     * @param int $id ID of the match
     * @return mixed reference to the {@link XoopsMatch} object, FALSE if failed
     */
     
    function &get($matchid)
	{
	    $matchid = intval($matchid);
	    if ($matchid > 0) {
	        $sql = "SELECT * FROM ".$this->db->prefix("team_matches")." WHERE matchid=$matchid";
	        if (!$result = $this->db->query($sql)) {
                return false;
            }
            $match =& $this->create(false);
            $match->assignVars($this->db->fetchArray($result));
            return $match;
	    }
	    return false;
	}
	/*
    * Save match in database
    * @param object $match reference to the {@link XoopsMatch} object
    * @param bool $force 
    * @return bool FALSE if failed, TRUE if already present and unchanged or successful
    */
    function insert(&$match, $force = false) {
		// If server from list specified do not save customserver
		if ($match->getVar('server') != 0) {
		    $match->setVar('customserver', '');
		} 
		if (strtolower(get_class($match)) != 'xoopsmatch') {
            return false;
        }
        if (!$match->isDirty()) {
            return true;
        }
        if (!$match->cleanVars()) {
            return false;
        }
        foreach ($match->cleanVars as $k => $v) {
            ${$k} = $v;
        }
		if ($match->isNew()) {
            $sql = "INSERT INTO ".$this->db->prefix("team_matches")."
            (uid, matchdate, teamid, created, teamsize, opponent, ladder, matchresult, review, server, customserver, alock)
            VALUES ($uid, $matchdate, $teamid, $created, $teamsize, ".$this->db->quoteString($opponent).", ".$this->db->quoteString($ladder).", 'Pending', ".$this->db->quoteString($review).", $server, ".$this->db->quoteString($customserver).",0)";
		} else {
			$sql = "UPDATE ".$this->db->prefix("team_matches")."
            SET uid=$uid,
            opponent=".$this->db->quoteString($opponent).",
            matchdate=$matchdate,
            matchresult=".$this->db->quoteString($matchresult).",
            teamsize=$teamsize,
            review=".$this->db->quoteString($review).",
            ladder=".$this->db->quoteString($ladder).",
            server=$server,
            customserver=".$this->db->quoteString($customserver).",
            alock=$alock WHERE matchid = $matchid";
			$newmatchid = $match->getVar('matchid');
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if ( empty($newmatchid) ) {
			$newmatchid = $this->db->getInsertId();
			$match->setVar('matchid', $newmatchid);
		}
		return $newmatchid;
	}
    /**
    * delete a match from the database
    *
    * @param object $match reference to the {@link XoopsMatch} to delete
    * @param bool $force
    * @return bool FALSE if failed.
    */
	function delete(&$match, $force = false)
	{
	    global $xoopsModule;
	    $matchid = intval($match->getVar('matchid'));
		$sql = "DELETE FROM ".$this->db->prefix("team_matches")." WHERE matchid = $matchid";
		if( $this->db->query($sql) ) {
			$sql = "DELETE FROM ".$this->db->prefix("team_matchmaps")." WHERE matchid = ".$matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
			$sql = "DELETE FROM ".$this->db->prefix("team_availability")." WHERE matchid = ".$matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
			$sql = "DELETE FROM ".$this->db->prefix("team_lineups_positions")." WHERE matchid = ".$matchid;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $module_id = $xoopsModule->getVar('mid');
            xoops_notification_deletebyitem ($module_id, 'match', $matchid);
            return true;
		}
		return false;
	}
}
?>
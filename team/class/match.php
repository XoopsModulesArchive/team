<?php
// Class for Match management for Team Module
// $Id: teammatch.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamMatch extends XoopsObject
{
    var $table;
	var $db;

    //Constructor
	function TeamMatch($matchid=-1)
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
		$this->initVar('matchresult', XOBJ_DTYPE_TXTBOX, "Pending");
		$this->initVar('review', XOBJ_DTYPE_TXTBOX);
		$this->initVar('server', XOBJ_DTYPE_INT);
		$this->initVar('customserver', XOBJ_DTYPE_TXTBOX);
		$this->initVar('alock', XOBJ_DTYPE_INT);
		if ( is_array($matchid) ) {
			$this->assignVars($matchid);
		} elseif ( $matchid != -1 ) {
		    $match_handler =& xoops_getmodulehandler('match');
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
        $matchmap_handler = xoops_getmodulehandler('matchmap', 'team');
        return $matchmap_handler->getByMatchid($this->getVar('matchid'));
    }

    function lock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=1 WHERE matchid=".$this->getVar('matchid');
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->getVar('teamid'),3,_AM_TEAMMATCHLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->getVar('matchid'), 3, _AM_DBNOTUPDATED);
        }
    }
    function unlock() {
        $sql = "UPDATE ".$this->db->prefix("team_matches")." SET alock=0 WHERE matchid=".$this->getVar('matchid');
        $this->db->query($sql);
        if ($this->db->getAffectedRows()>0) {
            redirect_header("index.php?teamid=".$this->getVar('teamid'),3,_AM_TEAMMATCHUNLOCKED);
        }
        else {
            redirect_header("availability.php?mid=".$this->getVar('matchid'), 3, _AM_DBNOTUPDATED);
        }
    }
    function getAvailabilities() {
        $sql = "SELECT a.avid, a.userid, a.availability, a.comment, u.uname FROM ".$this->db->prefix("team_availability")." a, ".$this->db->prefix("users")." u WHERE a.matchid=".$this->getVar('matchid')." AND u.uid=a.userid ORDER BY u.uname ASC";
        return $this->db->query($sql);
    }

	// Returns number of maps for this match
    function getMapCount() {
        $map_handler = xoops_getmodulehandler('matchmap', 'team');
        return $map_handler->getCount(new Criteria("matchid", $this->getVar('matchid')));
    }


}

class TeamMatchHandler extends XoopsPersistableObjectHandler {

    function TeamMatchHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_matches", "TeamMatch", "matchid");
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
		if( parent::delete($match, $force) ) {
		    $matchid = intval($match->getVar('matchid'));
		    $criteria = new Criteria("matchid", $matchid);
		    $map_handler = xoops_getmodulehandler('matchmap', 'team');
		    if (!$map_handler->deleteAll($criteria, $force)) {
                return false;
            }
            $availability_handler = xoops_getmodulehandler('availability', 'team');
		    if (!$availability_handler->deleteAll($criteria, $force)) {
                return false;
            }
            $lineup_handler = xoops_getmodulehandler('lineupposition', 'team');
            if (!$lineup_handler->deleteAll($criteria, $force)) {
                return false;
            }
            global $xoopsModule;
            $module_id = $xoopsModule->getVar('mid');
            xoops_notification_deletebyitem ($module_id, 'match', $matchid);
            return true;
		}
		return false;
	}
}
?>
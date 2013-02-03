<?php
// Class for Tactics management for Team Module
// $Id: tactics.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamTactics extends XoopsObject
{
    var $tacticstable;
    var $positionstable;
    var $maptable;
	var $db;
	var $map;

    //Constructor
	function TeamTactics($tacid=0, $mapid=null, $teamsize=null)
	{
        $this->initVar('tacid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('teamsize', XOBJ_DTYPE_INT);
		$this->initVar('teamid', XOBJ_DTYPE_INT);
		$this->initVar('general', XOBJ_DTYPE_TXTAREA);
		$this->initVar('mapid', XOBJ_DTYPE_INT);

		$this->db =& Database::getInstance();
		$this->tacticstable = $this->db->prefix("team_tactics");
        $this->positionstable = $this->db->prefix("team_tactics_positions");
        $this->maptable = $this->db->prefix("team_mappool");
		if ( is_array($tacid) ) {
			$this->assignVars($tacid);
		} elseif (($tacid!=0)&&($teamsize!=null)) {
		    //$tacid is actually a teamid
		    $tactics_handler = xoops_getmodulehandler('tactics');
            $tactics =& $tactics_handler->getByParams($tacid, $mapid, $teamsize);
            foreach ($tactics->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            unset($tactics);
		}
        elseif ($tacid!=0) {
            $tactics_handler = xoops_getmodulehandler('tactics');
            $tactics =& $tactics_handler->get($tacid);
            foreach ($tactics->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            unset($tactics);
		}
	}
    function getPositions() {
        $sql = "SELECT tacposid FROM ".$this->positionstable." WHERE tacid=".$this->getVar('tacid')." ORDER BY tacid";
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

    function show() {
        $position_handler =& xoops_getmodulehandler('tacticsposition');
        $team_handler =& xoops_getmodulehandler('team');
        $team =& $team_handler->get($this->getVar('teamid'));
        echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
        echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
        echo "<tr class='head'><td><h3>";
        echo $this->getVar('teamsize')." "._AM_TEAMVERSUS." ".$this->getVar('teamsize')." "._AM_TEAMTACTICSFOR." ".$team->getVar('teamname')." "._AM_TEAMON." ".$this->map->getVar('mapname');
        echo "</h3></td><td align='right'>";
        echo "<a href='tactics.php?op=mantactics&tacid=".$this->getVar('tacid')."'>";
        echo "<img src='images/edit.gif' border='0' alt='Edit'></a></td></tr>";
        echo "<tr><td colspan=2>";
        include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $mform = new XoopsThemeForm(_AM_TEAMTACTICSDISPLAY, "display", xoops_getenv('PHP_SELF'));
        $general = new XoopsFormLabel(_AM_TEAMGENERALTACS, $this->getVar('general'));
        $mform->addElement($general);
        $positions = $this->getPositions();
        $posshortlist = $team->getShortList();
        foreach ($positions as $key => $tacposid) {
            $thispos =& $position_handler->get($tacposid);
            $posshort = $posshortlist[$thispos->getVar('posid')];
            $position[$key] = new XoopsFormLabel($posshort, $thispos->getVar('posdesc'));
            $mform->addElement($position[$key]);
        }
        $mform->display();
        echo "</table></td></tr></table>";
    }
}

class TeamTacticsHandler extends XoopsPersistableObjectHandler {

    function TeamTacticsHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_tactics", "TeamTactics", "tacid");
    }

    /**
     * retrieve a tactics
     *
     * @param int $tacid ID of the tactics
     * @return mixed reference to the {@link XoopsTactics} object, FALSE if failed
     */
    function &get($tacid) {
        $ret = parent::get($tacid, true);
        $map_handler = xoops_getmodulehandler('map', 'team');
        $map = $map_handler->get($ret->getVar('mapid'));
        $ret->map = $map;
        return $ret;
    }
    /**
     * retrieve a tactics without tacid
     *
     * @param int $teamid ID of team
     * @param int $mapid ID of map
     * @param int $teamsize number of players for this tactics
     * @return mixed reference to the {@link XoopsTactics} object, FALSE if failed
     */
 	function getByParams($teamid, $mapid, $teamsize)
	{
	    $teamid = intval($teamid);
	    $mapid = intval($mapid);
	    $teamsize = intval($teamsize);
        $sql = "SELECT t.*, m.mapname FROM ".$this->db->prefix("team_tactics")." t, ".$this->db->prefix("team_mappool")." m WHERE t.mapid=m.mapid AND teamid=$teamid AND t.mapid=$mapid AND teamsize=".$teamsize;
        $result = $this->db->query($sql);
        $tactics =& $this->create(false);
        $tactics->setVar('teamid', $teamid);
        $tactics->setVar('mapid', $mapid);
        $tactics->setVar('teamsize', $teamsize);
        if ($this->db->getRowsNum($result)>0) {
            $tactics->assignVars($this->db->fetchArray($result));
            return $tactics;
        }
        else {
            $map_handler = xoops_getmodulehandler('map', 'team');
            $map = $map_handler->get($mapid);
            $tactics->map = $map;
            return $tactics;
        }
	}
}
?>
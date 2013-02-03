<?php
// Class for Match Map management for Team Module
// $Id: teammap.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamMatchMap extends XoopsObject
{
    var $map;

    //Constructor
	function TeamMatchMap()
	{
	    $this->initVar('matchmapid', XOBJ_DTYPE_INT);
	    $this->initVar('matchid', XOBJ_DTYPE_INT);
	    $this->initVar('mapid', XOBJ_DTYPE_INT);
	    $this->initVar('mapno', XOBJ_DTYPE_INT);
	    $this->initVar('ourscore', XOBJ_DTYPE_INT);
	    $this->initVar('theirscore', XOBJ_DTYPE_INT);
	    $this->initVar('side', XOBJ_DTYPE_INT);
	    $this->initVar('general', XOBJ_DTYPE_TXTAREA);
	    $this->initVar('screenshot', XOBJ_DTYPE_TXTBOX, "");

		$this->db =& Database::getInstance();
	}

    //Find winner of a map
    function winner($layout) {
        $our = $this->getVar("ourscore");
        $their = $this->getVar("theirscore");
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
         WHERE mapid=".$this->getVar('mapid')." AND teamid=".$teamid." AND teamsize=".$teamsize;
		$array = $this->db->fetchArray($this->db->query($sql));
		return $array["tacid"];
    }
}
class TeamMatchMapHandler extends XoopsPersistableObjectHandler {
    function TeamMatchMapHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_matchmaps", "TeamMatchMap", "matchmapid");
    }

    function get($id, $as_object = true) {
        $ret = parent::get($id, $as_object);
        $map_handler = xoops_getmodulehandler('map', 'team');
        $map = $map_handler->get($ret->getVar('mapid'));
        $ret->map = $map;
        return $ret;
    }

    function create($isNew = true) {
        $ret = parent::create($isNew);
        $map_handler = xoops_getmodulehandler('map', 'team');
        $map = $map_handler->create(true);
        $ret->map = $map;
        return $ret;
    }

    function getByMatchid($matchid, $mapno=null) {
        $criteria = new Criteria("matchid", intval($matchid));
        if (!is_null($mapno)) {
            $criteria = new CriteriaCompo($criteria);
            $criteria->add(new Criteria("mapno", intval($mapno)));
        }
        $criteria->setSort("mapno");
        $objs = $this->getObjects($criteria);
        $ret = array();
        if (count($objs) > 0) {
            foreach (array_keys($objs) as $i) {
                $mapids[] = $objs[$i]->getVar('mapid');
            }
            $map_handler = xoops_getmodulehandler('map');
            $maps = $map_handler->getObjects(new Criteria("mapid", "(".implode(',', array_unique($mapids)).")", "IN"), true);
            foreach (array_keys($objs) as $i) {
                $objs[$i]->map = isset($maps[$objs[$i]->getVar('mapid')]) ? $maps[$objs[$i]->getVar('mapid')] : $map_handler->create(false);
                $ret[$objs[$i]->getVar('mapno')] = $objs[$i];
            }
        }
        return is_null($mapno) ? $ret : (isset($ret[$mapno]) ? $ret[$mapno] : $this->create());
    }
}
?>
<?php
// Class for Lineup management for Team Module
// $Id: lineup.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class Lineup extends XoopsObject
{
	var $map;

    //Constructor
	function Lineup()
	{
        $this->initVar('matchmapid', XOBJ_DTYPE_INT);
        $this->initVar('matchid', XOBJ_DTYPE_INT);
        $this->initVar('mapid', XOBJ_DTYPE_INT);
        $this->initVar('general', XOBJ_DTYPE_TXTAREA);
	}

    function getPositions() {
        $lineuppos_handler = xoops_getmodulehandler('lineupposition', 'team');
        $criteria = new CriteriaCompo(new Criteria("matchmapid", $this->getVar('matchmapid')));
        $criteria->setSort("lineupid");
        return $lineuppos_handler->getObjects($criteria, false, false);
    }

    function getMapname()
    {
        $map_handler = xoops_getmodulehandler('map', 'team');
        $map_list = $map_handler->getList(new Criteria("mapid", $this->getVar('mapid')));
        return isset($map_list[$this->getVar('mapid')]) ? $map_list[$this->getVar('mapid')] : "";
    }

    function fetchGeneral()
    {
        $map_handler = xoops_getmodulehandler('matchmap', 'team');
        $criteria = new CriteriaCompo("matchmapid", $this->getVar('matchmapid'));
        $map = $map_handler->getObjects($criteria);
        if (isset($map[0])) {
            $this->setVar('general', $map[0]->getVar('general', 'n'));
            return $map[0]->getVar('general');
        }
        return "";
    }

    function saveGeneral()
    {
        if (!$this->getVar('matchid')) {
            return false;
        }
        else {
            $map_handler = xoops_getmodulehandler('matchmap', 'team');
            $criteria = new CriteriaCompo("matchmapid", $this->getVar('matchmapid'));
            $map = $map_handler->getObjects($criteria);
            if (isset($map[0])) {
                $map[0]->setVar('general', $this->getVar('general'));
                return $map_handler->insert($map[0]);
            }
        }
        return false;
    }

    function show() {
        $team_handler =& xoops_getmodulehandler('team');
        $team =& $team_handler->get($this->getVar('teamid'));
        echo "<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>";
        echo "<tr><td><table width='100%' border='0' cellpadding='0' cellspacing='0'>";
        echo "<tr class='head'><td colspan=2><h3>";
        echo $this->getVar('teamsize')." "._AM_TEAMVERSUS." ".$this->getVar('teamsize')." "._AM_TEAMTACTICSFOR." ".$team->getVar('teamname')." "._AM_TEAMON." ".$this->getVar('mapname');
        echo "</h3></td></tr>";
        echo "<tr><td colspan=2>";
        include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
        $mform = new XoopsThemeForm(_AM_TEAMTACTICSDISPLAY, "display", xoops_getenv('PHP_SELF'));
        $general = new XoopsFormLabel(_AM_TEAMGENERALTACS, $this->getVar('general'));
        $mform->addElement($general);
        $positions = $this->getPositions();
        $posshortlist = $team->getShortList();
        foreach ($positions as $key => $tacpos) {
            $posshort = $posshortlist[$tacpos['posid']];
            $position[$key] = new XoopsFormLabel($posshort, $tacpos['posdesc']);
            $mform->addElement($position[$key]);
        }
        $mform->display();
        echo "</table></td></tr></table>";
    }
}

class TeamLineupHandler extends XoopsObjectHandler {
    /**
     * retrieve a lineup
     *
     * @param array $matchmapid ID of matchmap
     * @return mixed reference to the {@link Lineup} object, FALSE if failed
     */
    function &get($matchmapid) {
        $ret = new Lineup();
        $matchmap_handler = xoops_getmodulehandler('matchmap', 'team');
        $matchmap = $matchmap_handler->get($matchmapid);
        $ret->setVar("matchmapid", $matchmap->getVar('matchmapid'));
        $ret->setVar("matchid", $matchmap->getVar('matchid'));
        $ret->setVar("mapid", $matchmap->getVar('mapid'));
        $ret->setVar('general', $matchmap->getVar('general'));
        $ret->map = $matchmap->map;
        return $ret;
    }
}
?>
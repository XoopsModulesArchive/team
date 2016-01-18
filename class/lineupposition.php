<?php
// Class for Lineup management for Team Module
// $Id: lineupposition.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamLineupPosition extends XoopsObject
{

    //Constructor
	function TeamLineupPosition($lineupid=0)
	{
        $this->initVar('lineupid', XOBJ_DTYPE_INT);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('posid', XOBJ_DTYPE_INT);
        $this->initVar('matchmapid', XOBJ_DTYPE_INT);
        $this->initVar('posdesc', XOBJ_DTYPE_TXTBOX);
	}
}

class TeamLineupPositionHandler extends XoopsPersistableObjectHandler {

    function TeamLineupPositionHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_lineups_positions", "TeamLineupPosition", "lineupid");
    }
}
?>
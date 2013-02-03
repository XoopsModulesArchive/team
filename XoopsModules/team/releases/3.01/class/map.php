<?php
// Class for Match Map management for Team Module
// $Id: teammap.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamMap extends XoopsObject
{
    //Constructor
	function TeamMap()
	{
	    $this->initVar('mapid', XOBJ_DTYPE_INT);
	    $this->initVar('mapname', XOBJ_DTYPE_TXTBOX);
	}
}
class TeamMapHandler extends XoopsPersistableObjectHandler {
    function TeamMapHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_mappool", "TeamMap", "mapid", "mapname");
    }
}
?>
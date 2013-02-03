<?php
// Class for Tactics management for Team Module
// $Id: tacticsposition.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
class TeamTacticsPosition extends XoopsObject
{
	var $db;

    //Constructor
	function TeamTacticsPosition($tacposid=0)
	{
		$this->db =& Database::getInstance();
        $this->initVar('tacposid', XOBJ_DTYPE_INT);
        $this->initVar('tacid', XOBJ_DTYPE_INT);
        $this->initVar('posid', XOBJ_DTYPE_INT);
        $this->initVar('posdesc', XOBJ_DTYPE_TXTAREA);
		if ( is_array($tacposid) ) {
			$this->assignVars($tacposid);
		} elseif ($tacposid!=0){
		    $position_handler =& xoops_getmodulehandler('tacticsposition');
            $position =& $position_handler->get($lineupid);
            foreach ($position->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
            unset($position);
        }
	}
}
class TeamTacticsPositionHandler extends XoopsPersistableObjectHandler {
    function TeamTacticsPositionHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_tactics_positions", "TeamTacticsPosition", "tacposid");
    }
}
?>
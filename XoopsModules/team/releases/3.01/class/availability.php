<?php
// Class for Match Map management for Team Module
// $Id: teammap.php,v 0.1 Date: 13/10/2003, Author: Mithrandir                                         //
if (!defined("XOOPS_ROOT_PATH")) {
    die("Xoops root path not defined");
}
if (!class_exists("XoopsPersistableObjectHandler")) {
    include_once XOOPS_ROOT_PATH."/modules/team/class/object.php";
}
/*
CREATE TABLE `team_availability` (
  `avid` mediumint(11) unsigned NOT NULL auto_increment,
  `userid` int(12) unsigned NOT NULL default '0',
  `availability` varchar(12) NOT NULL,
  `comment` varchar(25),
  `matchid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`avid`),
  UNIQUE KEY `avid` (`avid`)
) TYPE=MyISAM;
*/
class TeamAvailability extends XoopsObject
{
    //Constructor
	function TeamAvailability()
	{
	    $this->initVar('avid', XOBJ_DTYPE_INT);
	    $this->initVar('userid', XOBJ_DTYPE_INT);
	    $this->initVar('availability', XOBJ_DTYPE_TXTBOX);
	    $this->initVar('comment', XOBJ_DTYPE_TXTBOX);
	    $this->initVar('matchid', XOBJ_DTYPE_INT);
	}
}
class TeamAvailabilityHandler extends XoopsPersistableObjectHandler {
    function TeamAvailabilityHandler($db) {
        $this->XoopsPersistableObjectHandler($db, "team_availability", "TeamAvailability", "avid");
    }

    function getPendingByUser($uid) {
        $match_handler = xoops_getmodulehandler('match', 'team');
		$sql = "SELECT * FROM ".$this->table." a, ".$match_handler->table." m WHERE a.userid=".intval($uid)." AND a.matchid=m.matchid AND m.matchresult='Pending' ORDER BY m.matchdate DESC";
		$ret = array();
		$result= $this->db->query($sql);
		while ($myrow = $this->db->fetchArray($result)) {
		    $ret[] = $myrow;
		}
		return $ret;
    }
}
?>
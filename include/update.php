<?php

function xoops_module_update_team($module) {
    $lineuppos_handler = xoops_getmodulehandler('lineupposition', 'team');
    $sql = "ALTER TABLE ".$lineuppos_handler->table." ADD `matchmapid` int AFTER `posid`";
    $lineuppos_handler->db->query($sql);

    $matchmap_handler = xoops_getmodulehandler('matchmap', 'team');

    $sql = "UPDATE ".$lineuppos_handler->table." p, ".$matchmap_handler->table." m SET p.matchmapid=m.matchmapid WHERE p.matchid=m.matchid AND p.mapid=m.mapid";
    if ($lineuppos_handler->db->query($sql)) {
        $sql = "ALTER TABLE ".$lineuppos_handler->table." DROP `matchid`, DROP `mapid`";
        return $matchmap_handler->db->query($sql);
    }
    return false;
}
?>
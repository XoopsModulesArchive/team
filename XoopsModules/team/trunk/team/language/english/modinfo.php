<?php
// $Id: modinfo.php,v 1.2 2003/12/04 00:02:59 jace303 Exp $
// Module Info

// The name of this module
define('_MI_MATCH_NAME','Team');

// A brief description of this module
define('_MI_MATCH_DESC','Creates a section for Teams, Matches and Availability');

// Names of blocks for this module (Not all module has blocks)
define('_MI_MATCH_BNAME1','Availabilities');
define('_MI_MATCH_BNAME2','Team Menu');

// Sub menus in main menu block
define('_MI_MATCH_SMNAME2','Matches');
define('_MI_MATCH_SMNAME3','Roster');
define('_MI_MATCH_SMNAME4','Tactics');

// Names of admin menu items
define('_MI_MATCH_ADMENU6', 'Manage Teams');
define('_MI_MATCH_ADMENU4', 'Manage Maps');
define('_MI_MATCH_ADMENU2', 'Manage Matches');
define('_MI_MATCH_ADMENU3', 'Manage Positions');
define('_MI_MATCH_ADMENU7', 'Manage Servers');
define('_MI_MATCH_ADMENU8', 'Manage TeamSizes');
define('_MI_MATCH_ADMENU9', 'Manage Sides');
define('_MI_MATCH_ADMENU10', 'Manage Ranks');

//Added 10/9-2003 Mithrandir for Notification
define('_MI_TEAM_MATCH_NOTIFY', 'Match');
define('_MI_TEAM_MATCH_NOTIFYDSC', 'Notification options that apply to the current match');

define('_MI_TEAM_NEWMATCH_NOTIFY', 'New Match');
define('_MI_TEAM_NEWMATCH_NOTIFYCAP', 'Notify me of new matches for the current team.');
define('_MI_TEAM_NEWMATCH_NOTIFYDSC', 'Receive notification when a new match is created for the current team.');
define('_MI_TEAM_NEWMATCH_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New match for team');

define('_MI_TEAM_NEWLINEUP_NOTIFY', 'New Lineup');
define('_MI_TEAM_NEWLINEUP_NOTIFYCAP', 'Notify me when lineup for the current match is set.');
define('_MI_TEAM_NEWLINEUP_NOTIFYDSC', 'Receive notification when the lineup is created for the current match.');
define('_MI_TEAM_NEWLINEUP_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Lineup for match set');

//Added 17/10-2003 - v1.30 - Mithrandir
define('_MI_TEAM_SECTIONS', 'Sections Enabled');
define('_MI_TEAM_SECTIONSDESC', 'Select the sections you want to be enabled in the Team Module');

// Added 17.11.2003 - Jace
define('_MI_MATCH_ADMENU11','Manage Layout');

// Added 24.11.2003 - Jace
define('_MI_MATCH_ADMENU12','Manage Ladders');
?>

ALTER TABLE `team_matchmaps` ADD `screenshot` VARCHAR( 64 ) AFTER `theirscore` ;

CREATE TABLE `team_ladders` (
`ladderid` TINYINT NOT NULL AUTO_INCREMENT ,
`ladder` VARCHAR( 32 ) NOT NULL ,
`visible` TINYINT DEFAULT '1' NOT NULL ,
`scoresvisible` TINYINT DEFAULT '1' NOT NULL ,
PRIMARY KEY ( `ladderid` ) 
);

CREATE TABLE `team_teamladders` (
  `teamladderid` int(11) NOT NULL auto_increment,
  `ladderid` int(11) NOT NULL,
  `teamid` int(11) NOT NULL,
  PRIMARY KEY  (`teamladderid`)
);

INSERT INTO `team_ladders` ( `ladderid` , `ladder` , `visible`) VALUES ('', 'Ladder', '1');
INSERT INTO `team_ladders` ( `ladderid` , `ladder` , `visible`) VALUES ('', 'Scrimm', '1');
INSERT INTO `team_ladders` ( `ladderid` , `ladder` , `visible`) VALUES ('', 'Practice', '0');

ALTER TABLE `team_layout` ADD `color_perfect` VARCHAR( 12 ) ,
ADD `color_good` VARCHAR( 12 ) ,
ADD `color_warn` VARCHAR( 12 ) ,
ADD `color_bad` VARCHAR( 12 ) ;
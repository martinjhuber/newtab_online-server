
/* NEW Table: grid_definition_versions */

DROP TABLE IF EXISTS `grid_definition_versions`;
CREATE TABLE `grid_definition_versions` (
  `fk_user` int(11) NOT NULL,
  `grid_definition_version` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_user`),
  UNIQUE KEY `fk_user_UNIQUE` (`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* NEW StoredProc: grid_definition_version_get */

DROP PROCEDURE IF EXISTS `newtab`.`grid_definition_version_get`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `grid_definition_version_get`(
	IN user_id INT,
    OUT version INT)
BEGIN
	SET version = NULL;
    
	SELECT gdv.grid_definition_version
    INTO version
    FROM grid_definition_versions gdv
    WHERE gdv.fk_user = user_id; 

END$$
DELIMITER ;

/* UPDATE StoredProc: grid_definition_get */

DROP PROCEDURE IF EXISTS `newtab`.`grid_definitions_get`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `grid_definitions_get`(
	IN user_id INT,
    OUT version INT)
BEGIN

	CALL grid_definition_version_get(user_id, version);
    
	SELECT
		id as tileId,
		gridId,
        orderId,
        width,
        height,
        href,
        `text`,
        color,
        imageBase64,
        imageScale
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND `status` = 'Y'
    ORDER BY gridId ASC, orderId ASC; 

END$$
DELIMITER ;

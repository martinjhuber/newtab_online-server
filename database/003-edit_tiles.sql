/* NEW StoredProc: grid_definition_version_increment */

DROP PROCEDURE IF EXISTS `newtab`.`grid_definition_version_increment`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `grid_definition_version_increment`(
	IN user_id INT(11)
)
BEGIN

	UPDATE grid_definition_versions SET grid_definition_version = grid_definition_version + 1 WHERE fk_user = user_id;

END$$
DELIMITER ;

/* NEW StoredProc: tile_create */

DROP PROCEDURE IF EXISTS `newtab`.`tile_create`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_create`(
	IN user_id INT(11),
	IN input_gridId INT(11),
    IN input_width INT(11),
    IN input_height INT(11),
    IN input_text VARCHAR(256),
    IN input_href VARCHAR(512),
    IN input_color VARCHAR(20),
    OUT result INT)
BEGIN
	DECLARE newOrderId INT DEFAULT 0;
    
	SELECT MAX(orderId) + 1
    INTO newOrderId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.gridId = input_gridId AND `status` = 'Y';
    
	IF newOrderId IS NULL THEN
		SET newOrderId = 0;
    END IF;
    
    INSERT INTO grid_definitions (fk_user, gridId, orderId, width, height, href, `text`, color)
    VALUES (user_id, input_gridId, newOrderId, input_width, input_height, input_href, input_text, input_color);

	CALL grid_definition_version_increment(user_id);

	SET result = 1;
END$$
DELIMITER ;

/* NEW StoredProc: tile_delete */

DROP PROCEDURE IF EXISTS `newtab`.`tile_delete`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_delete`(
	IN user_id INT(11),
    IN tile_id INT(11),
    OUT result INT(11)
)
BEGIN
	
    DECLARE currentGridId INT DEFAULT 0;
    DECLARE currentOrderId INT DEFAULT 0;
    
    -- get current grid and order ids
	SELECT gridId, orderId
    INTO currentGridId, currentOrderId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.id = tile_id AND `status` = 'Y';
    
    UPDATE grid_definitions
    SET 
		updated = current_timestamp(),
		`status` = 'N'
    WHERE fk_user = user_id AND id = tile_id AND `status` = 'Y';
    
	IF ROW_COUNT() > 0 THEN
    
		-- move all elements one order back
		UPDATE grid_definitions
        SET
			updated = current_timestamp(),
            orderId = orderId - 1
		WHERE 
			fk_user = user_id AND gridId = currentGridId AND orderId > currentOrderId AND `status` = 'Y';
    
		SET result = 1;
        CALL grid_definition_version_increment(user_id);
	ELSE
		SET result = -1;
    END IF;
    
END$$
DELIMITER ;


/* NEW StoredProc: tile_edit */

DROP PROCEDURE IF EXISTS `newtab`.`tile_edit`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_edit`(
	IN user_id INT(11),
    IN tile_id INT(11),
    IN input_width INT(11),
    IN input_height INT(11),
    IN input_text VARCHAR(256),
    IN input_href VARCHAR(512),
    IN input_color VARCHAR(20),
    IN input_imageBase64 MEDIUMTEXT,
    IN input_imageScale INT(11),
    OUT result INT(11)
)
sp:BEGIN
	DECLARE checkTileId INT(11) DEFAULT NULL;
    	
    SELECT id
    INTO checkTileId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.id = tile_id AND `status` = 'Y';
    
    IF checkTileId IS NULL THEN
		SET result = -1;	/* tile that is for the given user and is active cannot be found */
        LEAVE sp;
    END IF;

	UPDATE grid_definitions
    SET
		updated = current_timestamp(),
        width = input_width,
        height = input_height,
		`text` = input_text,
        href = input_href,
        color = input_color,
        imageScale = input_imageScale
	WHERE
		fk_user = user_id AND id = tile_id;
	
    IF input_imageBase64 IS NOT NULL THEN
		UPDATE grid_definitions
		SET imageBase64 = input_imageBase64
		WHERE fk_user = user_id AND id = tile_id;
    END IF;
    
	CALL grid_definition_version_increment(user_id);
	SET result = 1;

END$$
DELIMITER ;


/* NEW StoredProc: tile_move */

DROP PROCEDURE IF EXISTS `newtab`.`tile_move`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_move`(
	IN user_id INT(11),
    IN tile_id INT(11),
    IN input_gridId INT(11),
    OUT result INT(11)
)
BEGIN
	DECLARE newOrderId INT DEFAULT 0;
    DECLARE currentGridId INT DEFAULT 0;
    DECLARE currentOrderId INT DEFAULT 0;
    
    -- get new order id
	SELECT MAX(orderId) + 1
    INTO newOrderId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.gridId = input_gridId AND `status` = 'Y';

	IF newOrderId IS NULL THEN
		SET newOrderId = 0;
	END IF;

	-- get current grid and order ids
	SELECT gridId, orderId
    INTO currentGridId, currentOrderId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.id = tile_id AND `status` = 'Y';

	-- if tile was found
	IF currentGridId IS NOT NULL AND currentOrderID IS NOT NULL THEN
    
		-- move tile to the new grid
		UPDATE grid_definitions
		SET 
			updated = current_timestamp(),
			gridId = input_gridId,
			orderId = newOrderId
		WHERE fk_user = user_id AND id = tile_id AND gridId <> input_gridId AND `status` = 'Y';

		-- reorder remaining tiles with a higher order id
		UPDATE grid_definitions
        SET
			updated = current_timestamp(),
            orderId = orderId - 1
		WHERE fk_user = user_id AND gridId = currentGridId AND orderId > currentGridId AND `status` = 'Y';
	
        CALL grid_definition_version_increment(user_id);
		SET result = 1;
	ELSE
		SET result = -1;
    END IF;

END$$
DELIMITER ;



/* NEW StoredProc: tile_remove_image */

DROP PROCEDURE IF EXISTS `newtab`.`tile_remove_image`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_remove_image`(
	IN user_id INT(11),
    IN tile_id INT(11),
    OUT result INT(11)
)
BEGIN

    UPDATE grid_definitions
    SET 
		updated = current_timestamp(),
		imageBase64 = null
    WHERE fk_user = user_id AND id = tile_id AND `status` = 'Y';
    
	IF ROW_COUNT() > 0 THEN
		SET result = 1;
        CALL grid_definition_version_increment(user_id);
	ELSE
		SET result = -1;
    END IF;

END$$
DELIMITER ;


/* NEW StoredProc: tile_reorder */

DROP PROCEDURE IF EXISTS `newtab`.`tile_reorder`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tile_reorder`(
	IN user_id INT(11),
    IN tile_id INT(11),
    IN input_orderId INT(11),
    OUT result INT(11)
)
BEGIN

    DECLARE currentGridId INT DEFAULT 0;
    DECLARE currentOrderId INT DEFAULT 0;
    
    -- get current grid and order ids
	SELECT gridId, orderId
    INTO currentGridId, currentOrderId
    FROM grid_definitions gd
    WHERE gd.fk_user = user_id AND gd.id = tile_id AND `status` = 'Y';

	-- reorder affected tiles
	IF input_orderId > currentOrderId THEN
        UPDATE grid_definitions
        SET orderId = orderId - 1
        WHERE fk_user = user_id AND gridId = currentGridId AND orderId > currentOrderId AND orderId <= input_orderId AND `status` = 'Y';
    ELSEIF input_orderId < currentOrderId THEN
        UPDATE grid_definitions
        SET orderId = orderId + 1
        WHERE fk_user = user_id AND gridId = currentGridId AND orderId < currentOrderId AND orderId >= input_orderId AND `status` = 'Y';
    END IF;

	-- set order id of selected tile
	UPDATE grid_definitions
	SET orderId = input_orderId
	WHERE fk_user = user_id AND id = tile_id AND `status` = 'Y';

	IF ROW_COUNT() > 0 THEN
		SET result = 1;
        CALL grid_definition_version_increment(user_id);
    ELSE
		SET result = -1;
    END IF;

END$$
DELIMITER ;



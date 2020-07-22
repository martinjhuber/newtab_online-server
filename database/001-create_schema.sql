CREATE DATABASE  IF NOT EXISTS `newtab` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `newtab`;
-- MySQL dump 10.13  Distrib 8.0.18, for Win64 (x86_64)
--
-- Host: localhost    Database: newtab
-- ------------------------------------------------------
-- Server version	8.0.18

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `grid_definitions`
--

DROP TABLE IF EXISTS `grid_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grid_definitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `fk_user` int(11) NOT NULL,
  `gridId` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `href` varchar(512) NOT NULL,
  `text` varchar(256) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `imageBase64` mediumtext,
  `imageScale` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `idx_grid_definition_user_status` (`fk_user`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `fk_user` int(11) NOT NULL,
  `token` varchar(36) NOT NULL,
  `expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_tokens_token_status_expiry` (`token`,`status`,`expiry`),
  KEY `idx_user_tokens_token_user` (`token`,`fk_user`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_name_password_status` (`name`,`password`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'newtab'
--
/*!50003 DROP PROCEDURE IF EXISTS `grid_definitions_get` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `grid_definitions_get`(
	IN user_id INT)
BEGIN

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

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `user_get` */;
ALTER DATABASE `newtab` CHARACTER SET utf8 COLLATE utf8_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_get`(
	IN user_id INT)
BEGIN

	SELECT
		u.id,
        u.`name`,
        u.`status` 
	FROM users u
    WHERE u.id = user_id;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `newtab` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
/*!50003 DROP PROCEDURE IF EXISTS `user_login` */;
ALTER DATABASE `newtab` CHARACTER SET utf8 COLLATE utf8_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_login`(
	IN input_name VARCHAR(255),
    IN input_password VARCHAR(255),
    OUT user_id INT,
    OUT token VARCHAR(36),
    OUT result INT)
BEGIN
    SET user_id = NULL;

	SELECT u.id
    INTO user_id
	FROM users u
	WHERE u.`name` = input_name AND u.`password` = input_password AND u.`status` = 'Y';
    
	IF (user_id IS NOT NULL) THEN
		BEGIN
			SET token = UUID();
			INSERT INTO user_tokens (created, fk_user, token, expiry) VALUES (UTC_TIMESTAMP, user_id, token, null);	# TODO: Set proper expiry timestamp
			SET result = 1;
		END;
	ELSE
		SET result = 0;
	END IF;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `newtab` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
/*!50003 DROP PROCEDURE IF EXISTS `user_logout` */;
ALTER DATABASE `newtab` CHARACTER SET utf8 COLLATE utf8_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_logout`(
	IN user_token VARCHAR(36))
BEGIN

	UPDATE user_tokens
	SET `status` = 'N'
	WHERE token = user_token;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `newtab` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
/*!50003 DROP PROCEDURE IF EXISTS `user_token_verify` */;
ALTER DATABASE `newtab` CHARACTER SET utf8 COLLATE utf8_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `user_token_verify`(
	IN check_token VARCHAR(36),
    IN extendExpiry BOOLEAN,
    OUT user_id INT,
    OUT result INT)
BEGIN
	SET user_id = NULL;
    SET result = 0;

	SELECT lt.fk_user
    INTO user_id
    FROM user_tokens lt
    WHERE lt.token = check_token
		AND lt.`status` = 'Y'
		AND (lt.expiry IS NULL OR lt.expiry > UTC_TIMESTAMP);

	IF (extendExpiry IS NULL OR extendExpiry = TRUE) THEN
		BEGIN
			UPDATE user_tokens
            SET expiry = NULL		# TODO: Set proper expiry date
            WHERE token = check_token AND fk_user = user_id;
		END;
	END IF;
    
    IF (user_id IS NOT NULL) THEN
		SET result = 1;
	END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `newtab` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-07-22 19:55:01

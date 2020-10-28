-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.41-log - MySQL Community Server (GPL)
-- Операционная система:         Win32
-- HeidiSQL Версия:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных pages_manager
CREATE DATABASE IF NOT EXISTS `pages_manager` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `pages_manager`;

-- Дамп структуры для таблица pages_manager.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Дамп данных таблицы pages_manager.groups: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` (`id`, `name`) VALUES
	(1, 'superadmin'),
	(2, 'admin'),
	(3, 'user'),
	(4, 'viewer');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;

-- Дамп структуры для таблица pages_manager.rights_access
CREATE TABLE IF NOT EXISTS `rights_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pages_manager.rights_access: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `rights_access` DISABLE KEYS */;
INSERT INTO `rights_access` (`id`, `description`) VALUES
	(1, 'write'),
	(2, 'read'),
	(3, 'denied');
/*!40000 ALTER TABLE `rights_access` ENABLE KEYS */;

-- Дамп структуры для таблица pages_manager.rights_groups
CREATE TABLE IF NOT EXISTS `rights_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pages_manager.rights_groups: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `rights_groups` DISABLE KEYS */;
INSERT INTO `rights_groups` (`id`, `name`) VALUES
	(1, 'superadmin'),
	(2, 'admin'),
	(3, 'user'),
	(4, 'viewer');
/*!40000 ALTER TABLE `rights_groups` ENABLE KEYS */;

-- Дамп структуры для таблица pages_manager.rights_objects
CREATE TABLE IF NOT EXISTS `rights_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '1 - страница; 2 - скрипт; 3-блок или элемент',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pages_manager.rights_objects: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `rights_objects` DISABLE KEYS */;
INSERT INTO `rights_objects` (`id`, `name`, `type`) VALUES
	(1, 'permissions.php', 1),
	(2, 'users.php', 1);
/*!40000 ALTER TABLE `rights_objects` ENABLE KEYS */;

-- Дамп структуры для таблица pages_manager.rights_permissions
CREATE TABLE IF NOT EXISTS `rights_permissions` (
  `object_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `access_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`group_id`),
  KEY `group` (`group_id`),
  KEY `access` (`access_id`),
  CONSTRAINT `access` FOREIGN KEY (`access_id`) REFERENCES `rights_access` (`id`),
  CONSTRAINT `group` FOREIGN KEY (`group_id`) REFERENCES `rights_groups` (`id`),
  CONSTRAINT `object` FOREIGN KEY (`object_id`) REFERENCES `rights_objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pages_manager.rights_permissions: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `rights_permissions` DISABLE KEYS */;
INSERT INTO `rights_permissions` (`object_id`, `group_id`, `access_id`) VALUES
	(1, 2, 3),
	(1, 3, 3),
	(1, 4, 3);
/*!40000 ALTER TABLE `rights_permissions` ENABLE KEYS */;

-- Дамп структуры для таблица pages_manager.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(512) DEFAULT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `lastlogin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `contact_phone` varchar(256) NOT NULL,
  `contact_mail` varchar(256) NOT NULL,
  `work_mail` varchar(256) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `group_id` int(11) DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pages_manager.users: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `enabled`, `lastlogin`, `contact_phone`, `contact_mail`, `work_mail`, `contact_name`, `position`, `group_id`) VALUES
	(1, 'admin', '4828140403f6eaee3b5af62a0b09ae61', 1, '2020-10-27 22:05:31', '', 'kuzaevel@mail.ru', '', 'Админ', 'Админ', 1),
	(2, 'ivanov', '4828140403f6eaee3b5af62a0b09ae61', 1, '2020-10-27 20:47:06', '+7 342 296-27-56 (доб. 558)', 'kuzaevel@mail.ru', '', 'Иванов М.М.', '', 3),
	(3, 'petrov', '4828140403f6eaee3b5af62a0b09ae61', 1, '2020-10-27 18:21:45', '+7 342 296-27-56 (доб. 559)', 'kuzaevel@mail.ru', '', 'Петров М.М.', '', 3),
	(4, 'testapp', '4828140403f6eaee3b5af62a0b09ae61', 1, '2020-10-27 20:55:48', '+7 342 296-27-56 (доб. 559)', 'kuzaevel@mail.ru', '', 'Тестовый М.Р.', '', 3);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

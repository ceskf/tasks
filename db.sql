-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.67-community-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-11-12 19:43:07
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for tasks
CREATE DATABASE IF NOT EXISTS `tasks` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tasks`;


-- Dumping structure for table tasks.calls
CREATE TABLE IF NOT EXISTS `calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dt_call` datetime NOT NULL,
  `dt_enter` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `comments` text,
  `user` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table tasks.calls: ~3 rows (approximately)
/*!40000 ALTER TABLE `calls` DISABLE KEYS */;
INSERT INTO `calls` (`id`, `phone`, `name`, `dt_call`, `dt_enter`, `comments`, `user`) VALUES
	(1, '0997454704', 'Zhneya', '2013-11-06 09:27:30', '2013-11-06 09:27:49', 'First order', 'koval'),
	(2, '0661704672', 'Sveta', '2013-11-06 10:27:28', '2013-11-06 10:27:24', 'Second order', 'koval'),
	(4, '38098961053311111', 'userphotogallery11111', '2013-11-05 17:55:00', '2013-11-06 13:05:57', 'dsfsdf11111', 'admin'),
	(5, '111', '111', '2013-11-12 12:33:57', '2013-11-12 11:34:01', '111', 'mtest'),
	(6, '222', '222', '2013-11-12 12:35:48', '2013-11-12 11:35:51', '222', 'test2');
/*!40000 ALTER TABLE `calls` ENABLE KEYS */;


-- Dumping structure for table tasks.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_code` varchar(255) DEFAULT NULL,
  `phones` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dt_call` datetime NOT NULL,
  `dt_enter` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comments` text,
  `price_delivery` float DEFAULT NULL,
  `prepayment` float DEFAULT NULL,
  `delivery` enum('Yes','No') NOT NULL DEFAULT 'No',
  `del_city` text,
  `del_street` text,
  `del_house` text,
  `del_apartment` text,
  `time` varchar(50) DEFAULT NULL,
  `deliveryman` text,
  `deliveryman_name` varchar(500) DEFAULT NULL,
  `deliveryman_werehouse` varchar(500) DEFAULT NULL,
  `redelivery` text,
  `redelivery_werehouse_from` varchar(500) DEFAULT NULL,
  `redelivery_dt` date DEFAULT NULL,
  `status` enum('Не обработан','Собирается','Доставляется','Отправлен в другой город','Выполнен') NOT NULL DEFAULT 'Не обработан',
  `user` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table tasks.orders: ~15 rows (approximately)
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` (`id`, `client_code`, `phones`, `name`, `dt_call`, `dt_enter`, `comments`, `price_delivery`, `prepayment`, `delivery`, `del_city`, `del_street`, `del_house`, `del_apartment`, `time`, `deliveryman`, `deliveryman_name`, `deliveryman_werehouse`, `redelivery`, `redelivery_werehouse_from`, `redelivery_dt`, `status`, `user`) VALUES
	(1, '1111', '0997454704', 'Eugene', '2013-11-07 09:59:52', '2013-11-07 11:00:01', 'First oder', 50, 10, 'Yes', 'ZP', 'Rjza', '21', '20', '12:30', 'df', 'sdfg', 'sdgf', 'sdg', 'sdfg', '2013-11-08', 'Не обработан', 'mtest'),
	(2, '1234', '67567', '457', '2013-11-07 12:55:00', '2013-11-07 14:44:07', '574', 50, 10, 'No', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Не обработан', 'test2'),
	(3, '5017', '099', '055', '2013-11-07 23:11:00', '2013-11-07 14:44:44', '202020', 100, 90, 'No', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Не обработан', 'admin'),
	(4, '0', '5555', '6666', '2013-11-07 12:55:00', '2013-11-07 14:49:42', '', 0, 0, 'No', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Собирается', 'admin'),
	(5, '4610', '675411111', '4674722222', '2013-11-07 12:55:00', '2013-11-07 14:51:06', '4574533333', 1000, 1000, 'Yes', '11', '22', '33', '44', '12:55', '55', '66', '77', '88', '99', '2013-11-07', 'Не обработан', 'admin'),
	(6, '3557', '0555', '5050', '2013-11-07 14:56:00', '2013-11-07 14:52:15', 'jhk', 0, 0, 'Yes', 'ZP', 'Koz', '21', '20', '15:25', 'Rehmth', 'New post', '55', 'OD', '40', '2013-11-09', 'Не обработан', 'admin'),
	(7, '6194', '111', '222', '2013-11-07 12:00:00', '2013-11-07 15:21:30', '333', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Не обработан', 'admin'),
	(8, '6194', '11', '22', '2013-11-07 12:44:00', '2013-11-07 15:22:36', '33', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Собирается', 'admin'),
	(9, '5017', '11', '22', '2013-11-07 12:55:00', '2013-11-07 15:23:22', '0202', 0, 0, '', '', '', '', '', '', '', '', '', '', '', NULL, 'Собирается', 'admin'),
	(10, '6194', '000', '444', '2013-11-07 12:45:00', '2013-11-07 15:23:56', '444ddddddddddddd\nddddddddddddddd\ndddddddddddddgggggggg\nggggggggggggggg\ngggggggggggg\nggggggggggggg', 0, 100, 'No', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Не обработан', 'admin'),
	(11, '5017', '45', '45', '2013-11-08 12:55:00', '2013-11-08 11:43:54', '45', 110, 10, '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', 'Не обработан', 'admin');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;


-- Dumping structure for table tasks.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `product` varchar(100) NOT NULL,
  `manufacturer` varchar(255) NOT NULL,
  `collection` varchar(255) NOT NULL,
  `articul` varchar(500) NOT NULL,
  `count` int(11) NOT NULL,
  `werehouse` varchar(255) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `type` enum('call','order','return') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- Dumping data for table tasks.products: ~35 rows (approximately)
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `type_id`, `product`, `manufacturer`, `collection`, `articul`, `count`, `werehouse`, `price`, `type`) VALUES
	(1, 6, 'Ковролин', 'Rasch', 'Rasch coll', '12345', 5, NULL, 0, 'call'),
	(2, 1, 'wallpapers', 'AS Creation', 'Contzen', '55', 10, '175', 0, 'call'),
	(3, 1, 'laminat', 'Tarkett', 'Lamin \' Art', '111', 45, '150.6', NULL, 'call'),
	(5, 1, 'kovrolin', 'fdsg', '', '12', 5, '44.2', NULL, 'call'),
	(6, 1, 'laminat', 'Tarkett', 'Lamin \' Art', 'rtert', 444, '5.6', NULL, 'call'),
	(7, 1, 'laminat', 'Krono Original', 'Castello Stoneline', '45345', 6, '4', NULL, 'call'),
	(8, 1, 'laminat', 'Balterio', 'Tradition Exotic', '2323', 5, '25.6', NULL, 'call'),
	(9, 1, 'laminat', 'Tarkett', 'Woodstock', '436', 6, '5', NULL, 'call'),
	(10, 1, 'laminat', 'Tarkett', 'Vintage', '56', 10, '5', NULL, 'call'),
	(11, 1, 'laminat', 'Balterio', 'Tradition Quattro', '5454', 5, '6', NULL, 'call'),
	(12, 1, 'laminat', 'Balterio', 'Tradition Quattro', '67567', 6, '7', NULL, 'call'),
	(13, 1, 'laminat', 'Balterio', 'Magnitude', '67', 7, '8', NULL, 'call'),
	(14, 1, 'laminat', 'Elesgo', 'Wellness 32', '356', 5, '99', NULL, 'call'),
	(15, 1, 'wallpapers', 'Marburg', 'Patio', '6767', 7, '7', NULL, 'call'),
	(16, 1, 'laminat', 'Krono Original', 'Castello Stoneline', '4545', 10, NULL, 150.6, 'call'),
	(17, 1, 'Обои', 'Marburg', 'La Veneziana', '111', 10, NULL, 159.5, 'call'),
	(18, 1, 'Обои', 'Zambaiti', 'Fiorenza', '2345235', 0, NULL, 0, 'call'),
	(20, 2, 'Ламинат', 'Balterio', 'Tradition Quattro', '324', 4, NULL, 5, 'call'),
	(21, 2, 'Обои', 'Marburg', 'Ravenna', '55555', 10, 'Киев4', 170, 'order'),
	(23, 10, 'Обои', 'AS Creation', 'Appassionata', '6666', 50, 'Киев1', 10.6, 'order'),
	(24, 10, 'Ламинат', 'Balterio', 'Tradition Exotic', '2222', 10, 'W', 20.3, 'order'),
	(25, 10, 'Ламинат', 'Balterio', 'Tradition Quattro', '5656', 10, 'W', 666, 'order'),
	(26, 10, 'Ковролин', 'fdsg', '11111', '555', 10, 'KK', 5, 'order'),
	(27, 10, 'Ламинат', 'Krono Original', 'Castello Classic', '555', 55, 'W', 10, 'order'),
	(28, 10, 'Ламинат', 'Balterio', 'Optimum', '111', 10, 'W', 1, 'order'),
	(29, 10, 'Ламинат', 'Balterio', 'Magnitude', '555', 10, 'W', 100, 'order'),
	(30, 7, 'Ковролин', 'fdsg', '\'\'', '55', 10, '\'\'', 10, 'order'),
	(31, 2, 'Обои', 'AS Creation', 'Flock 2', '556', 5, NULL, 10, 'call'),
	(45, 7, 'Ковролин', 'fdsg', '11111', '555', 10, 'KK', 5, 'return'),
	(46, 13, 'Ламинат', 'Krono Original', 'Castello Classic', '555', 55, 'W', 10, 'return'),
	(47, 13, 'Ламинат', 'Balterio', 'Optimum', '111', 10, 'W', 1, 'return'),
	(48, 13, 'Ламинат', 'Balterio', 'Magnitude', '555', 10, 'W', 100, 'return'),
	(49, 13, 'Обои', 'Marburg', 'Ravenna', '55', 50, 'Киев2', 251, 'return'),
	(50, 3, 'Обои', 'AS Creation', 'Flock 2', '11', 10, 'Киев2', 5, 'order'),
	(51, 9, 'Обои', 'Marburg', 'Opulence', '111', 1, 'Одесса1', 1, 'order');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;


-- Dumping structure for table tasks.returns
CREATE TABLE IF NOT EXISTS `returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `dt_return` datetime NOT NULL,
  `dt_enter` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cause` text,
  `user` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table tasks.returns: ~4 rows (approximately)
/*!40000 ALTER TABLE `returns` DISABLE KEYS */;
INSERT INTO `returns` (`id`, `order_id`, `dt_return`, `dt_enter`, `cause`, `user`) VALUES
	(4, 11, '2013-11-08 23:11:00', '2013-11-08 13:18:57', '3', 'test2'),
	(6, 3, '2013-11-08 23:11:00', '2013-11-08 13:18:57', '3', 'mtest'),
	(7, 2, '2013-11-08 23:11:00', '2013-11-08 13:18:57', '3', 'test2'),
	(13, 10, '2013-11-08 12:55:00', '2013-11-08 14:00:56', '111', 'admin');
/*!40000 ALTER TABLE `returns` ENABLE KEYS */;


-- Dumping structure for table tasks.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `role` enum('admin','manager','seller') NOT NULL DEFAULT 'seller',
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table tasks.users: ~8 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `login`, `pwd`, `role`, `name`, `phone`, `parent_id`) VALUES
	(1, 'admin', 'svetlana', 'admin', 'Administrator', NULL, NULL),
	(2, 'test', 'test', 'seller', 'test', NULL, NULL),
	(3, 'mtest', 'mtest', 'manager', 'mtest', NULL, NULL),
	(5, 'test2', 'test2', 'seller', NULL, NULL, NULL),
	(6, 'test3', 'test3', 'seller', NULL, NULL, NULL),
	(7, 'test2', 'test2', 'seller', NULL, NULL, 3),
	(12, '777', '777', 'admin', '', '', NULL),
	(13, '555', '555', 'seller', '', '', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

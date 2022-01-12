/*
SQLyog Community v12.04 (64 bit)
MySQL - 10.4.18-MariaDB : Database - basic
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`basic` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `basic`;

/*Table structure for table `hrm_users` */

DROP TABLE IF EXISTS `hrm_users`;

CREATE TABLE `hrm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(300) DEFAULT NULL,
  `display_name` varchar(300) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `path` text DEFAULT NULL,
  `company_id` int(7) DEFAULT NULL,
  `emp_id` int(10) DEFAULT NULL,
  `branchid` int(11) DEFAULT NULL,
  `themecolour` varchar(60) DEFAULT NULL,
  `status` char(3) DEFAULT NULL,
  `asalta_user_application` varchar(150) DEFAULT NULL,
  `access_token` varchar(135) DEFAULT NULL,
  `default_screen` varchar(90) DEFAULT NULL,
  `default_superadmin` char(9) DEFAULT NULL,
  `forgotten_password_code` varchar(120) DEFAULT NULL,
  `forgotten_password_time` int(11) DEFAULT NULL,
  `language` varchar(300) DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL,
  `createddate` timestamp NULL DEFAULT NULL,
  `updateddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `hrm_users` */

insert  into `hrm_users`(`id`,`fullname`,`display_name`,`email`,`password`,`image`,`path`,`company_id`,`emp_id`,`branchid`,`themecolour`,`status`,`asalta_user_application`,`access_token`,`default_screen`,`default_superadmin`,`forgotten_password_code`,`forgotten_password_time`,`language`,`createdby`,`updatedby`,`createddate`,`updateddate`) values (1,'Super Admin','INOS','demo@asaltatechnologies.com','$2y$13$RecBbUFEBIJBHPnVMAKhm.bdW82BVZ1x.W0yE.AkKSoXYfVhLwfpC','1504690513.png','uploads/user_lmage/c4ca4238a0b923820dcc509a6f75849b/',1,NULL,NULL,NULL,'1','hrm,crm','9cf355139b26fc17a347fb455c1d2c',NULL,'Yes',NULL,NULL,'en',NULL,1,'2016-03-28 00:00:00','2017-12-08 10:42:41');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

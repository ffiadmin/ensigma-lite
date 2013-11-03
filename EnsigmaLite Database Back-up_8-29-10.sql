-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 29, 2010 at 08:02 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ensigmalite`
--

-- --------------------------------------------------------

--
-- Table structure for table `collaboration`
--

CREATE TABLE IF NOT EXISTS `collaboration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `type` text NOT NULL,
  `fromDate` longtext NOT NULL,
  `fromTime` longtext NOT NULL,
  `toDate` longtext NOT NULL,
  `toTime` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `assignee` longtext NOT NULL,
  `task` longtext NOT NULL,
  `dueDate` longtext NOT NULL,
  `priority` longtext NOT NULL,
  `completed` longtext NOT NULL,
  `directories` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `collaboration`
--


-- --------------------------------------------------------

--
-- Table structure for table `dailyhits`
--

CREATE TABLE IF NOT EXISTS `dailyhits` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `date` varchar(255) NOT NULL,
  `hits` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `dailyhits`
--

INSERT INTO `dailyhits` (`id`, `date`, `hits`) VALUES
(1, 'Jul-30-2010', 15),
(2, 'Jul-31-2010', 136),
(3, 'Aug-01-2010', 24),
(4, 'Aug-27-2010', 91),
(5, 'Aug-29-2010', 51);

-- --------------------------------------------------------

--
-- Table structure for table `external`
--

CREATE TABLE IF NOT EXISTS `external` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `published` int(1) NOT NULL,
  `message` int(1) NOT NULL,
  `display` int(1) NOT NULL,
  `content1` longtext NOT NULL,
  `content2` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `external`
--


-- --------------------------------------------------------

--
-- Table structure for table `pagehits`
--

CREATE TABLE IF NOT EXISTS `pagehits` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `hits` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pagehits`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `published` int(1) NOT NULL,
  `message` int(1) NOT NULL,
  `display` int(1) NOT NULL,
  `content1` longtext NOT NULL,
  `content2` longtext NOT NULL,
  `comments1` int(1) NOT NULL,
  `comments2` int(1) NOT NULL,
  `name` longtext NOT NULL,
  `date` longtext NOT NULL,
  `comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE IF NOT EXISTS `privileges` (
  `id` int(1) NOT NULL,
  `deleteFile` int(1) NOT NULL,
  `uploadFile` int(1) NOT NULL,
  `sendEmail` int(1) NOT NULL,
  `viewStaffPage` int(1) NOT NULL,
  `createStaffPage` int(1) NOT NULL,
  `editStaffPage` int(1) NOT NULL,
  `deleteStaffPage` int(1) NOT NULL,
  `publishStaffPage` int(1) NOT NULL,
  `autoPublishStaffPage` int(1) NOT NULL,
  `addStaffComments` int(1) NOT NULL,
  `deleteStaffComments` int(1) NOT NULL,
  `createPage` int(1) NOT NULL,
  `editPage` int(1) NOT NULL,
  `deletePage` int(1) NOT NULL,
  `publishPage` int(1) NOT NULL,
  `autoPublishPage` int(1) NOT NULL,
  `createSideBar` int(1) NOT NULL,
  `editSideBar` int(1) NOT NULL,
  `deleteSideBar` int(1) NOT NULL,
  `publishSideBar` int(1) NOT NULL,
  `autoPublishSideBar` int(1) NOT NULL,
  `siteSettings` int(1) NOT NULL,
  `sideBarSettings` int(1) NOT NULL,
  `deleteComments` int(1) NOT NULL,
  `createExternal` int(1) NOT NULL,
  `editExternal` int(1) NOT NULL,
  `deleteExternal` int(1) NOT NULL,
  `publishExternal` int(1) NOT NULL,
  `autoPublishExternal` int(1) NOT NULL,
  `viewStatistics` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `deleteFile`, `uploadFile`, `sendEmail`, `viewStaffPage`, `createStaffPage`, `editStaffPage`, `deleteStaffPage`, `publishStaffPage`, `autoPublishStaffPage`, `addStaffComments`, `deleteStaffComments`, `createPage`, `editPage`, `deletePage`, `publishPage`, `autoPublishPage`, `createSideBar`, `editSideBar`, `deleteSideBar`, `publishSideBar`, `autoPublishSideBar`, `siteSettings`, `sideBarSettings`, `deleteComments`, `createExternal`, `editExternal`, `deleteExternal`, `publishExternal`, `autoPublishExternal`, `viewStatistics`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sidebar`
--

CREATE TABLE IF NOT EXISTS `sidebar` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `visible` text NOT NULL,
  `published` int(1) NOT NULL,
  `message` int(1) NOT NULL,
  `display` int(1) NOT NULL,
  `type` text NOT NULL,
  `title` longtext NOT NULL,
  `content1` longtext NOT NULL,
  `content2` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `sidebar`
--


-- --------------------------------------------------------

--
-- Table structure for table `siteprofiles`
--

CREATE TABLE IF NOT EXISTS `siteprofiles` (
  `id` int(11) NOT NULL,
  `siteName` varchar(200) NOT NULL,
  `paddingTop` tinyint(4) NOT NULL,
  `paddingLeft` tinyint(4) NOT NULL,
  `paddingRight` tinyint(4) NOT NULL,
  `paddingBottom` tinyint(4) NOT NULL,
  `width` int(3) NOT NULL,
  `height` int(3) NOT NULL,
  `sideBar` text NOT NULL,
  `auto` text NOT NULL,
  `siteFooter` text NOT NULL,
  `author` varchar(200) NOT NULL,
  `language` varchar(15) NOT NULL,
  `copyright` varchar(200) NOT NULL,
  `description` varchar(20000) NOT NULL,
  `meta` text NOT NULL,
  `timeZone` varchar(20) NOT NULL,
  `welcome` text NOT NULL,
  `style` varchar(200) NOT NULL,
  `iconType` text NOT NULL,
  `spellCheckerAPI` varchar(50) NOT NULL,
  PRIMARY KEY (`siteName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `siteprofiles`
--

INSERT INTO `siteprofiles` (`id`, `siteName`, `paddingTop`, `paddingLeft`, `paddingRight`, `paddingBottom`, `width`, `height`, `sideBar`, `auto`, `siteFooter`, `author`, `language`, `copyright`, `description`, `meta`, `timeZone`, `welcome`, `style`, `iconType`, `spellCheckerAPI`) VALUES
(1, 'Ensigma Suite', 20, 0, 0, 0, 203, 60, 'Left', 'on', '', 'Apex Development', 'en-US', 'Apex Development, All Rights Reserved', 'An interactive, flexible content management system for individuals, organizations, or companies, which is built to suit a variety of needs', 'Apex Development, Ensigma Suite', 'America/New_York', 'Ads', 'backToSchool.css', 'jpg', 'jmyppg6c5k5ajtqcra7u4eql4l864mps48auuqliy3cccqrb6b');

-- --------------------------------------------------------

--
-- Table structure for table `staffpages`
--

CREATE TABLE IF NOT EXISTS `staffpages` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `position` int(11) NOT NULL,
  `published` int(1) NOT NULL,
  `message` int(1) NOT NULL,
  `display` int(1) NOT NULL,
  `content1` longtext NOT NULL,
  `content2` longtext NOT NULL,
  `comments1` int(1) NOT NULL,
  `comments2` int(1) NOT NULL,
  `name` longtext NOT NULL,
  `date` longtext NOT NULL,
  `comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `staffpages`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `active` varchar(20) NOT NULL,
  `firstName` longtext NOT NULL,
  `lastName` longtext NOT NULL,
  `userName` longtext NOT NULL,
  `passWord` longtext NOT NULL,
  `changePassword` text NOT NULL,
  `emailAddress1` longtext NOT NULL,
  `emailAddress2` longtext NOT NULL,
  `emailAddress3` longtext NOT NULL,
  `role` longtext NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `firstName` (`firstName`,`lastName`,`userName`,`emailAddress1`,`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=195 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `active`, `firstName`, `lastName`, `userName`, `passWord`, `changePassword`, `emailAddress1`, `emailAddress2`, `emailAddress3`, `role`) VALUES
(35, '1283111276', 'Oliver', 'Spryn', 'spryno724', 'Oliver99', '', 'wot200@zoominternet.net', 'oliverspryn@zoominternet.net', 'wot200@gmail.com', 'Administrator'),
(194, '1283111111', 'john', 'doe', 'johndoe', 'Oliver99', '', '-@-.net', '', '', 'User');

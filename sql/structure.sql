SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bptarguments`
--

-- --------------------------------------------------------

--
-- Table structure for table `arguments`
--

CREATE TABLE IF NOT EXISTS `arguments` (
  `argumentId` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL,
  `parentId` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `userId` int(11) NOT NULL,
  `url` varchar(256) NOT NULL,
  `headline` varchar(100) NOT NULL,
  `abstract` varchar(256) NOT NULL,
  `details` text NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`argumentId`),
  UNIQUE KEY `questionId` (`questionId`,`parentId`,`url`),
  KEY `userId` (`userId`,`questionId`),
  KEY `questionId_2` (`questionId`,`argumentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `badwords`
--

CREATE TABLE IF NOT EXISTS `badwords` (
  `badwordId` int(11) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `word` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `confirmation_codes`
--

CREATE TABLE IF NOT EXISTS `confirmation_codes` (
  `confirmationId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `code` varchar(128) NOT NULL,
  `dateAdded` int(13) NOT NULL,
  PRIMARY KEY (`confirmationId`),
  UNIQUE KEY `userId` (`userId`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `localization`
--

CREATE TABLE IF NOT EXISTS `localization` (
  `loc_key` varchar(255) NOT NULL,
  `loc_language` varchar(4) NOT NULL,
  `loc_val` text NOT NULL,
  PRIMARY KEY (`loc_language`,`loc_key`),
  KEY `loc_language` (`loc_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notificationId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `flags` int(11) NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  PRIMARY KEY (`notificationId`),
  UNIQUE KEY `questionId` (`questionId`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `pageId` int(11) NOT NULL AUTO_INCREMENT,
  `pageTitle` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `className` varchar(100) NOT NULL,
  `templateFile` varchar(100) NOT NULL,
  PRIMARY KEY (`pageId`),
  UNIQUE KEY `pageTitle` (`pageTitle`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `permissionId` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY (`permissionId`),
  UNIQUE KEY `groupId` (`groupId`,`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `questionId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `url` varchar(256) NOT NULL,
  `details` text NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  `userId` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `scoreTrending` int(11) NOT NULL,
  `scoreTop` int(11) NOT NULL,
  `additionalData` text NOT NULL,
  `groupId` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `flags` tinyint(4) NOT NULL,
  PRIMARY KEY (`questionId`),
  UNIQUE KEY `url` (`url`),
  KEY `score` (`score`),
  KEY `scoreTrending` (`scoreTrending`),
  KEY `scoreTop` (`scoreTop`),
  KEY `type` (`type`,`groupId`,`questionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sessionId` varchar(32) NOT NULL,
  `sessionData` text NOT NULL,
  `sessionDate` int(11) NOT NULL,
  PRIMARY KEY (`sessionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `signup_tokens`
--

CREATE TABLE IF NOT EXISTS `signup_tokens` (
  `tokenId` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  PRIMARY KEY (`tokenId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tagId` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `groupId` int(11) NOT NULL,
  PRIMARY KEY (`tagId`),
  KEY `tag` (`tag`,`questionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `group` int(11) NOT NULL,
  `password` varchar(256) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  `user_last_action` bigint(20) NOT NULL,
  `scoreQuestions` int(11) NOT NULL,
  `scoreArguments` int(11) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userName` (`userName`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `user_votes`
--

CREATE TABLE IF NOT EXISTS `user_votes` (
  `voteId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `argumentId` int(11) NOT NULL,
  `vote` int(4) NOT NULL,
  `dateAdded` bigint(20) NOT NULL,
  PRIMARY KEY (`voteId`),
  KEY `userId` (`userId`,`questionId`,`argumentId`),
  KEY `questionIdArgumentId` (`questionId`,`argumentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


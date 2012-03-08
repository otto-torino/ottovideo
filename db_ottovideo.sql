SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `db_ottovideo`
--

-- --------------------------------------------------------

--
-- Table structure for table `ov_categories`
--

CREATE TABLE IF NOT EXISTS `ov_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) character set latin1 default NULL,
  `parent` int(11) NOT NULL,
  `description` text character set latin1,
  `public` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `ov_config`
--

CREATE TABLE IF NOT EXISTS `ov_config` (
  `id` int(11) NOT NULL auto_increment,
  `parent_url` varchar(100) NOT NULL,
  `streamingAddress` varchar(200) NOT NULL,
  `httpAddress` varchar(128) NOT NULL,
  `onDemandSwf` varchar(200) NOT NULL,
  `onAirSwf` varchar(200) NOT NULL,
  `splashLive` varchar(128) NOT NULL,
  `imgWidthDemand` int(11) NOT NULL,
  `imgHeightDemand` int(11) NOT NULL,
  `imgWidthAir` int(11) NOT NULL,
  `imgHeightAir` int(11) NOT NULL,
  `live_stream_url` varchar(128) NOT NULL,
  `stream_name` varchar(64) NOT NULL,
  `live_stream_mobile_url` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ov_config`
--

INSERT INTO `ov_config` (`id`, `parent_url`, `streamingAddress`, `httpAddress`, `onDemandSwf`, `onAirSwf`, `splashLive`, `imgWidthDemand`, `imgHeightDemand`, `imgWidthAir`, `imgHeightAir`, `live_stream_url`, `stream_name`, `live_stream_mobile_url`) VALUES
(1, 'http://www.example.it', 'example.it/stream', 'example.it:8080/html5', 'img/splash_ondemand.jpg', 'img/splash_onair.jpg', 'img/splash_live.png', 120, 80, 120, 80, 'rtmp://example.it:1935/stream', 'rtmp.stream', 'http://example.it:1935/live/rtmp.stream/playlist.m3u8');

-- --------------------------------------------------------

--
-- Table structure for table `ov_languages`
--

CREATE TABLE IF NOT EXISTS `ov_languages` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(127) NOT NULL,
  `code` varchar(5) NOT NULL,
  `main` int(1) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ov_languages`
--

INSERT INTO `ov_languages` (`id`, `label`, `code`, `main`, `active`) VALUES
(1, 'Italiano', 'it_IT', 1, 1),
(2, 'Inglese', 'en_US', 0, 0),
(3, 'Francese', 'fr_FR', 0, 1),
(4, 'Spagnolo', 'es_ES', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ov_layout`
--

CREATE TABLE IF NOT EXISTS `ov_layout` (
  `id` enum('ondemand','onair','onairbox','live') NOT NULL,
  `layout` varchar(10) NOT NULL,
  `pWidth` int(4) NOT NULL,
  `pHeight` int(4) NOT NULL,
  `lWidth` int(4) NOT NULL,
  `lHeight` int(4) NOT NULL,
  `lSpeed` int(4) default NULL,
  `lChars` int(6) NOT NULL,
  `bWidth` int(4) NOT NULL,
  `bHeight` int(3) NOT NULL,
  `bSpeed` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ov_layout`
--

INSERT INTO `ov_layout` (`id`, `layout`, `pWidth`, `pHeight`, `lWidth`, `lHeight`, `lSpeed`, `lChars`, `bWidth`, `bHeight`, `bSpeed`) VALUES
('onair', 'pl_vlr', 600, 474, 380, 474, 1000, 0, 0, 0, ''),
('ondemand', 'pl_vlr', 600, 474, 380, 474, 800, 90, 0, 0, ''),
('onairbox', '', 0, 0, 0, 0, NULL, 0, 160, 310, '14000'),
('live', '', 600, 450, 0, 0, NULL, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `ov_schedule`
--

CREATE TABLE IF NOT EXISTS `ov_schedule` (
  `id` int(11) NOT NULL auto_increment,
  `date` date default NULL,
  `initTime` int(11) default NULL,
  `endTime` int(11) default NULL,
  `itemId` int(11) default NULL,
  `block` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ov_spot_categories`
--

CREATE TABLE IF NOT EXISTS `ov_spot_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ov_translations`
--

CREATE TABLE IF NOT EXISTS `ov_translations` (
  `tbl` varchar(127) NOT NULL,
  `id` int(11) NOT NULL,
  `field` varchar(127) NOT NULL,
  `language` varchar(5) NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ov_users`
--

CREATE TABLE IF NOT EXISTS `ov_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ov_users`
--

INSERT INTO `ov_users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ov_video_comments`
--

CREATE TABLE IF NOT EXISTS `ov_video_comments` (
  `id` int(11) NOT NULL auto_increment,
  `video` int(11) NOT NULL,
  `author` varchar(128) collate utf8_unicode_ci NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `email` varchar(128) collate utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ov_video_items`
--

CREATE TABLE IF NOT EXISTS `ov_video_items` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(2) NOT NULL default '1',
  `category` int(11) default NULL,
  `spot_category` int(3) NOT NULL,
  `name` varchar(200) default NULL,
  `name_html5` varchar(64) NOT NULL,
  `duration` varchar(10) default NULL,
  `ratio` varchar(10) NOT NULL,
  `title` varchar(200) default NULL,
  `description` text,
  `image` varchar(200) default NULL,
  `new` enum('yes','no') NOT NULL default 'no',
  `ondemand` enum('yes','no') NOT NULL default 'no',
  `date` datetime default NULL,
  `notes` text NOT NULL,
  `bind_spot` int(1) NOT NULL,
  `spot_active` int(1) NOT NULL,
  `spot_max_view` int(8) NOT NULL,
  `spot_url` varchar(128) NOT NULL,
  `views` int(8) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

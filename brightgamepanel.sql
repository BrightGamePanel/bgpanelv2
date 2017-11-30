-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 31, 2016 at 05:35 PM
-- Server version: 5.5.36
-- PHP Version: 5.5.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brightgamepanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `bgp_box`
--

CREATE TABLE IF NOT EXISTS `bgp_box` (
`box_id` int(10) unsigned NOT NULL,
  `box_credential_id` int(10) unsigned NOT NULL,
  `os_id` int(10) unsigned NOT NULL,
  `steam_lib_path` text COLLATE utf8_unicode_ci,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_box_cache`
--

CREATE TABLE IF NOT EXISTS `bgp_box_cache` (
`box_cache_id` int(10) unsigned NOT NULL,
  `box_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cache` blob NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_box_credential`
--

CREATE TABLE IF NOT EXISTS `bgp_box_credential` (
`box_credential_id` int(10) unsigned NOT NULL,
  `login` blob NOT NULL,
  `password` blob NOT NULL,
  `remote_user_home` text COLLATE utf8_unicode_ci NOT NULL,
  `com_protocol` text COLLATE utf8_unicode_ci NOT NULL,
  `com_port` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_box_ip`
--

CREATE TABLE IF NOT EXISTS `bgp_box_ip` (
`box_ip_id` int(10) unsigned NOT NULL,
  `box_id` int(10) unsigned NOT NULL,
  `ip` text COLLATE utf8_unicode_ci NOT NULL,
  `is_default` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_config`
--

CREATE TABLE IF NOT EXISTS `bgp_config` (
`config_id` int(10) unsigned NOT NULL,
  `setting` text COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `bgp_config`
--

INSERT INTO `bgp_config` (`config_id`, `setting`, `value`) VALUES
(1, 'panel_name', 'BrightGamePanel V2'),
(2, 'system_url', 'http://localhost/'),
(3, 'panel_version', '0.1.0'),
(4, 'api_version', '100'),
(5, 'maintenance_mode', '0'),
(6, 'last_cron_run', 'Never'),
(7, 'user_template', 'bootstrap.min.css');

-- --------------------------------------------------------

--
-- Table structure for table `bgp_game`
--

CREATE TABLE IF NOT EXISTS `bgp_game` (
`game_id` int(10) unsigned NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `max_slots` int(10) unsigned NOT NULL,
  `default_port` int(10) unsigned NOT NULL,
  `query_port` int(10) unsigned NOT NULL,
  `query_type` text COLLATE utf8_unicode_ci NOT NULL,
  `config` blob,
  `exe` text COLLATE utf8_unicode_ci NOT NULL,
  `launcher` text COLLATE utf8_unicode_ci NOT NULL,
  `cache_dir` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Dumping data for table `bgp_game`
--

INSERT INTO `bgp_game` (`game_id`, `name`, `status`, `max_slots`, `default_port`, `query_port`, `query_type`, `config`, `exe`, `launcher`, `cache_dir`) VALUES
(1, 'Counter-Strike: Source', 'Active', 16, 27015, 27015, 'source', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a31303a2263735f61737361756c74223b7d7d, 'srcds_run', './srcds_run -game cstrike -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', '~/game-repositories/css/'),
(2, 'Day of Defeat: Source', 'Active', 16, 27015, 27015, 'source', 0x613a323a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a393a22646f645f616e7a696f223b7d693a313b613a323a7b733a343a226e616d65223b733a383a225469636b72617465223b733a353a2276616c7565223b733a333a22313030223b7d7d, 'srcds_run', './srcds_run -game dod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate', '~/game-repositories/dods/'),
(3, 'Half-Life 2: Deathmatch', 'Active', 16, 27015, 27015, 'source', 0x613a323a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a31313a22646d5f6c6f636b646f776e223b7d693a313b613a323a7b733a343a226e616d65223b733a383a225469636b72617465223b733a353a2276616c7565223b733a333a22313030223b7d7d, 'srcds_run', './srcds_run -game hl2mp -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate', '~/game-repositories/hl2dm/'),
(4, 'Team Fortress 2', 'Active', 24, 27015, 27015, 'source', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a393a226374665f32666f7274223b7d7d, 'srcds_run', './srcds_run -game tf -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', '~/game-repositories/tf2/'),
(5, 'Left 4 Dead', 'Active', 8, 27015, 27015, 'source', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a32343a226c34645f686f73706974616c30315f61706172746d656e74223b7d7d, 'srcds_run', './srcds_run -game left4dead -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', '~/game-repositories/left4dead/'),
(6, 'Left 4 Dead 2', 'Active', 8, 27015, 27015, 'source', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a31303a2263316d315f686f74656c223b7d7d, 'srcds_run', './srcds_run -game left4dead2 -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', '~/game-repositories/left4dead2/'),
(7, 'Counter-Strike', 'Active', 16, 27015, 27015, 'halflife', 0x613a323a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a383a2264655f6475737432223b7d693a313b613a323a7b733a343a226e616d65223b733a393a2250696e67626f6f7374223b733a353a2276616c7565223b733a313a2232223b7d7d, 'hlds_run', './hlds_run -game cstrike +ip {ip} +port {port} +maxplayers {slots} +map {cfg1} -pingboost {cfg2} -autoupdate', '~/game-repositories/cstrike/'),
(8, 'Killing Floor', 'Inactive', 6, 7707, 7708, 'killingfloor', 0x613a353a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a31333a224b462d4265646c616d2e726f6d223b7d693a313b613a323a7b733a343a226e616d65223b733a393a22564143536563757265223b733a353a2276616c7565223b733a343a2254727565223b7d693a323b613a323a7b733a343a226e616d65223b733a393a2241646d696e4e616d65223b733a353a2276616c7565223b733a353a2261646d696e223b7d693a333b613a323a7b733a343a226e616d65223b733a31333a2241646d696e50617373776f7264223b733a353a2276616c7565223b733a363a22706173737764223b7d693a343b613a323a7b733a343a226e616d65223b733a383a22494e492046696c65223b733a353a2276616c7565223b733a31363a224b696c6c696e67466c6f6f722e696e69223b7d7d, 'ucc_bin', './ucc-bin server {cfg1}?game=KFmod.KFGameType?VACSecure={cfg2}?MaxPlayers={slots}?AdminName={cfg3}?AdminPassword={cfg4} -nohomedir ini={cfg5}', '~/game-repositories/kfserver/'),
(9, 'Call of Duty 4: Modern Warfare', 'Inactive', 18, 28960, 28960, 'callofduty4', 0x613a333a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d693a313b613a323a7b733a343a226e616d65223b733a31313a2266735f686f6d6570617468223b733a353a2276616c7565223b733a31353a222f686f6d652f757365722f636f6434223b7d693a323b613a323a7b733a343a226e616d65223b733a31313a2266735f6261736570617468223b733a353a2276616c7565223b733a31353a222f686f6d652f757365722f636f6434223b7d7d, 'cod4_lnxded', './cod4_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', '~/game-repositories/cod4/'),
(10, 'Minecraft', 'Active', 24, 25565, 25565, 'minecraft', 0x613a303a7b7d, 'minecraft_server', 'java -Xms1024M -Xmx1024M -jar minecraft_server.jar nogui', '~/game-repositories/minecraft/'),
(11, 'Call of Duty: Modern Warfare 3', 'Active', 18, 27015, 27016, 'callofdutymw3', 0x613a343a7b693a303b613a323a7b733a343a226e616d65223b733a31333a226e65745f7175657279506f7274223b733a353a2276616c7565223b733a353a223237303134223b7d693a313b613a323a7b733a343a226e616d65223b733a31323a226e65745f61757468506f7274223b733a353a2276616c7565223b733a343a2238373636223b7d693a323b613a323a7b733a343a226e616d65223b733a32303a226e65745f6d6173746572536572766572506f7274223b733a353a2276616c7565223b733a353a223237303136223b7d693a333b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d7d, 'iw5mp_server.exe', 'xvfb-run -a wine iw5mp_server.exe +set sv_config {cfg4} +set sv_maxclients {slots} +start_map_rotate +set net_ip {ip} +set net_port {port} +set net_queryPort {cfg1} +set net_authPort {cfg2} +set net_masterServerPort {cfg3} +set dedicated 2', '~/game-repositories/codmw3/'),
(12, 'Call of Duty 2', 'Inactive', 32, 28960, 28960, 'callofduty2', 0x613a333a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d693a313b613a323a7b733a343a226e616d65223b733a31313a2266735f686f6d6570617468223b733a353a2276616c7565223b733a31353a222f686f6d652f757365722f636f6432223b7d693a323b613a323a7b733a343a226e616d65223b733a31313a2266735f6261736570617468223b733a353a2276616c7565223b733a31353a222f686f6d652f757365722f636f6432223b7d7d, 'cod2_lnxded', './cod2_lnxded +exec {cfg1} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', '~/game-repositories/cod2/'),
(13, 'Call of Duty: World at War', 'Inactive', 32, 28960, 28960, 'callofdutywaw', 0x613a333a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d693a313b613a323a7b733a343a226e616d65223b733a31313a2266735f686f6d6570617468223b733a353a2276616c7565223b733a31373a222f686f6d652f757365722f636f64776177223b7d693a323b613a323a7b733a343a226e616d65223b733a31313a2266735f6261736570617468223b733a353a2276616c7565223b733a31373a222f686f6d652f757365722f636f64776177223b7d7d, 'codwaw_lnxded', './codwaw_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', '~/game-repositories/codwaw/'),
(14, 'Wolfenstein: Enemy Territory', 'Inactive', 32, 27960, 27960, 'wolfet', 0x613a333a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d693a313b613a323a7b733a343a226e616d65223b733a31313a2266735f686f6d6570617468223b733a353a2276616c7565223b733a31373a222f686f6d652f757365722f776f6c666574223b7d693a323b613a323a7b733a343a226e616d65223b733a31313a2266735f6261736570617468223b733a353a2276616c7565223b733a31373a222f686f6d652f757365722f776f6c666574223b7d7d, 'etded', './etded +exec {cfg1} +sv_maxclients {slots} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set net_port {port}', '~/game-repositories/wolfet/'),
(15, 'ArmA: 2', 'Active', 64, 2302, 2302, 'arma2', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d7d, 'server', './server -config={cfg1} -netlog -port={port}', '~/game-repositories/arma2/'),
(16, 'Garrysmod', 'Active', 16, 27015, 27015, 'source', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a31323a22676d5f636f6e737472756374223b7d7d, 'srcds_run', './srcds_run -game garrysmod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', '~/game-repositories/garrysmod/'),
(17, 'Counter-Strike: Global Offensive', 'Active', 24, 27015, 27015, 'source', 0x613a353a7b693a303b613a323a7b733a343a226e616d65223b733a31313a2244656661756c74204d6170223b733a353a2276616c7565223b733a383a2263735f6974616c79223b7d693a313b613a323a7b733a343a226e616d65223b733a393a224d61702047726f7570223b733a353a2276616c7565223b733a31303a226d675f686f7374616765223b7d693a323b613a323a7b733a343a226e616d65223b733a393a2247616d652054797065223b733a353a2276616c7565223b733a313a2230223b7d693a333b613a323a7b733a343a226e616d65223b733a393a2247616d65204d6f6465223b733a353a2276616c7565223b733a313a2230223b7d693a343b613a323a7b733a343a226e616d65223b733a383a225469636b72617465223b733a353a2276616c7565223b733a323a223634223b7d7d, 'srcds_run', './srcds_run -game csgo -console -usercon -secure -nohltv -tickrate {cfg5} +ip {ip} +hostport {port} -maxplayers_override {slots} +map {cfg1} +mapgroup {cfg2} +game_type {cfg3} +game_mode {cfg4}', '~/game-repositories/csgo/'),
(18, 'ArmA: Armed Assault', 'Active', 64, 2302, 2302, 'arma', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d7d, 'server', './server -config={cfg1} -netlog -port={port}', '~/game-repositories/arma/'),
(19, 'Battlefield 2', 'Active', 64, 16567, 29900, 'bf2', 0x613a303a7b7d, 'start.sh', './start.sh', '~/game-repositories/bf2/'),
(20, 'Battlefield 1942', 'Active', 64, 14567, 23000, 'bf1942', 0x613a303a7b7d, 'start.sh', './start.sh +statusMonitor 1', '~/game-repositories/bf1942/'),
(21, 'Multi Theft Auto', 'Active', 128, 22003, 22126, 'mta', 0x613a303a7b7d, 'mta-server', './mta-server -t --ip {ip} --port {port} --httpport {port} --maxplayers {slots}', '~/game-repositories/mta/'),
(22, 'San Andreas: Multiplayer (SA-MP)', 'Active', 128, 7777, 7777, 'samp', 0x613a303a7b7d, 'samp03svr', './samp03svr', '~/game-repositories/samp/'),
(23, 'Urban Terror', 'Active', 32, 27960, 27960, 'urbanterror', 0x613a313a7b693a303b613a323a7b733a343a226e616d65223b733a31353a22536572766572204346472046696c65223b733a353a2276616c7565223b733a31303a227365727665722e636667223b7d7d, 'ioUrTded.i386', './ioUrTded.i386 +set fs_game q3ut4 +set net_port {port} +set com_hunkmegs 128 +exec {cfg1} +set dedicated 2', '~/game-repositories/urbanterror/');

-- --------------------------------------------------------

--
-- Table structure for table `bgp_group`
--

CREATE TABLE IF NOT EXISTS `bgp_group` (
`group_id` int(10) unsigned NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_group_member`
--

CREATE TABLE IF NOT EXISTS `bgp_group_member` (
`group_member_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `client_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_lgsl`
--

CREATE TABLE IF NOT EXISTS `bgp_lgsl` (
`id` int(10) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `c_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `q_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `s_port` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `zone` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `disabled` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `cache` text COLLATE utf8_unicode_ci NOT NULL,
  `cache_time` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_os`
--

CREATE TABLE IF NOT EXISTS `bgp_os` (
`os_id` int(10) unsigned NOT NULL,
  `operating_system` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_permissions`
--

CREATE TABLE IF NOT EXISTS `bgp_permissions` (
`ID` int(10) unsigned NOT NULL,
  `Lft` int(10) unsigned NOT NULL,
  `Rght` int(10) unsigned NOT NULL,
  `Title` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `bgp_permissions`
--

INSERT INTO `bgp_permissions` (`ID`, `Lft`, `Rght`, `Title`, `Description`) VALUES
(1, 0, 23, 'root', 'root'),
(2, 1, 2, 'Box/', 'Box Module'),
(3, 3, 4, 'Box/add/', 'New Box Form'),
(4, 5, 6, 'Config/', 'Config Module'),
(5, 7, 8, 'Config/apikey/', 'Api Key'),
(6, 9, 10, 'Config/cron/', 'Cron Settings'),
(7, 11, 12, 'Config/license/', 'System License'),
(8, 13, 14, 'Dashboard/', 'Dashboard Module'),
(9, 15, 16, 'Myaccount/', 'Myaccount Module'),
(10, 17, 18, 'Tools/', 'Tools Module'),
(11, 19, 20, 'Tools/opdb/', 'Optimize Database'),
(12, 21, 22, 'Tools/phpinfo/', 'Php Info');

-- --------------------------------------------------------

--
-- Table structure for table `bgp_rolepermissions`
--

CREATE TABLE IF NOT EXISTS `bgp_rolepermissions` (
  `RoleID` int(10) unsigned NOT NULL,
  `PermissionID` int(10) unsigned NOT NULL,
  `AssignmentDate` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bgp_rolepermissions`
--

INSERT INTO `bgp_rolepermissions` (`RoleID`, `PermissionID`, `AssignmentDate`) VALUES
(1, 1, 1438680689),
(3, 2, 1438680689),
(3, 3, 1438680689),
(3, 4, 1438680689),
(3, 5, 1438680689),
(3, 6, 1438680689),
(3, 7, 1438680689),
(3, 8, 1438680689),
(4, 8, 1438680689),
(3, 9, 1438680689),
(4, 9, 1438680689),
(3, 10, 1438680689),
(3, 11, 1438680689),
(3, 12, 1438680689);

-- --------------------------------------------------------

--
-- Table structure for table `bgp_roles`
--

CREATE TABLE IF NOT EXISTS `bgp_roles` (
`ID` int(10) unsigned NOT NULL,
  `Lft` int(10) unsigned NOT NULL,
  `Rght` int(10) unsigned NOT NULL,
  `Title` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `bgp_roles`
--

INSERT INTO `bgp_roles` (`ID`, `Lft`, `Rght`, `Title`, `Description`) VALUES
(1, 0, 7, 'root', 'root'),
(2, 1, 2, 'api', 'API User'),
(3, 3, 4, 'admin', 'System Administrator'),
(4, 5, 6, 'user', 'Regular System User');

-- --------------------------------------------------------

--
-- Table structure for table `bgp_script`
--

CREATE TABLE IF NOT EXISTS `bgp_script` (
`script_id` int(10) unsigned NOT NULL,
  `script_category_id` int(10) unsigned NOT NULL,
  `box_id` int(10) unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `owner_type` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `process_state` text COLLATE utf8_unicode_ci,
  `path` text COLLATE utf8_unicode_ci NOT NULL,
  `launcher` text COLLATE utf8_unicode_ci NOT NULL,
  `screen_name` text COLLATE utf8_unicode_ci,
  `type` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_script_category`
--

CREATE TABLE IF NOT EXISTS `bgp_script_category` (
`script_category_id` int(10) unsigned NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_server`
--

CREATE TABLE IF NOT EXISTS `bgp_server` (
`server_id` int(10) unsigned NOT NULL,
  `box_id` int(10) unsigned NOT NULL,
  `ip_id` int(10) unsigned NOT NULL,
  `game_id` int(10) unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `owner_type` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `process_state` text COLLATE utf8_unicode_ci NOT NULL,
  `slots` int(10) unsigned NOT NULL,
  `port` int(10) unsigned NOT NULL,
  `query_port` int(10) unsigned NOT NULL,
  `config` blob,
  `path` text COLLATE utf8_unicode_ci NOT NULL,
  `launcher` text COLLATE utf8_unicode_ci NOT NULL,
  `screen_name` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bgp_session`
--

CREATE TABLE IF NOT EXISTS `bgp_session` (
  `session_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `session_data` blob NOT NULL,
  `expires` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bgp_session`
--

INSERT INTO `bgp_session` (`session_id`, `session_data`, `expires`) VALUES
('raa4ludk7hm5jot6rubqtgepl4', 0x54494d455354414d507c693a313433383638303833383b, 1438691638),
('jlpuv4lbdie2sjrqfk0osmual5', 0x54494d455354414d507c693a313433383638313437333b, 1438692273),
('qokp4gnmson7ubmpcmsbtoqdr4', 0x54494d455354414d507c693a313435313931343931303b, 1451925710),
('u8uivr54bliss6pl453h9p2j83', 0x54494d455354414d507c693a313435343235373834373b, 1454268647);

-- --------------------------------------------------------

--
-- Table structure for table `bgp_user`
--

CREATE TABLE IF NOT EXISTS `bgp_user` (
`user_id` int(10) unsigned NOT NULL,
  `username` text COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `firstname` text COLLATE utf8_unicode_ci,
  `lastname` text COLLATE utf8_unicode_ci,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `status` text COLLATE utf8_unicode_ci NOT NULL,
  `lang` text COLLATE utf8_unicode_ci NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_ip` text COLLATE utf8_unicode_ci,
  `last_host` text COLLATE utf8_unicode_ci,
  `token` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bgp_user`
--

INSERT INTO `bgp_user` (`user_id`, `username`, `password`, `firstname`, `lastname`, `email`, `notes`, `status`, `lang`, `last_login`, `last_activity`, `last_ip`, `last_host`, `token`) VALUES
(1, 'root', '50fca63154fa73a9c567750e000ab81813a8d0de5a221a532119dc0b66fbe674d1fb5cb8865f84951256f1d3548eb3b79096a8dc315066c4426342ca2b7e7130', 'root', 'root', 'root@toor.com', '', 'Active', 'en_EN', '2015-08-04 08:31:29', '2015-08-04 08:31:29', '', '', NULL),
(2, 'api', '24667e521421c158fe465e1f17c254d0b659706533e888d6222f771654513d7ddb3bf305fc5fe369d2080cb8d4b50fcfdedbf84468cb546d49827ce887a65aa9', '', '', 'root@toor.com', '', 'Inactive', 'en_EN', '2015-08-04 08:31:29', '2015-08-04 08:31:29', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bgp_userroles`
--

CREATE TABLE IF NOT EXISTS `bgp_userroles` (
  `UserID` int(10) unsigned NOT NULL,
  `RoleID` int(10) unsigned NOT NULL,
  `AssignmentDate` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bgp_userroles`
--

INSERT INTO `bgp_userroles` (`UserID`, `RoleID`, `AssignmentDate`) VALUES
(1, 1, 1438680689),
(2, 2, 1438680689);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bgp_box`
--
ALTER TABLE `bgp_box`
 ADD PRIMARY KEY (`box_id`);

--
-- Indexes for table `bgp_box_cache`
--
ALTER TABLE `bgp_box_cache`
 ADD PRIMARY KEY (`box_cache_id`);

--
-- Indexes for table `bgp_box_credential`
--
ALTER TABLE `bgp_box_credential`
 ADD PRIMARY KEY (`box_credential_id`);

--
-- Indexes for table `bgp_box_ip`
--
ALTER TABLE `bgp_box_ip`
 ADD PRIMARY KEY (`box_ip_id`);

--
-- Indexes for table `bgp_config`
--
ALTER TABLE `bgp_config`
 ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `bgp_game`
--
ALTER TABLE `bgp_game`
 ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `bgp_group`
--
ALTER TABLE `bgp_group`
 ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `bgp_group_member`
--
ALTER TABLE `bgp_group_member`
 ADD PRIMARY KEY (`group_member_id`);

--
-- Indexes for table `bgp_lgsl`
--
ALTER TABLE `bgp_lgsl`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bgp_os`
--
ALTER TABLE `bgp_os`
 ADD PRIMARY KEY (`os_id`);

--
-- Indexes for table `bgp_permissions`
--
ALTER TABLE `bgp_permissions`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `bgp_rolepermissions`
--
ALTER TABLE `bgp_rolepermissions`
 ADD PRIMARY KEY (`RoleID`,`PermissionID`);

--
-- Indexes for table `bgp_roles`
--
ALTER TABLE `bgp_roles`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `bgp_script`
--
ALTER TABLE `bgp_script`
 ADD PRIMARY KEY (`script_id`);

--
-- Indexes for table `bgp_script_category`
--
ALTER TABLE `bgp_script_category`
 ADD PRIMARY KEY (`script_category_id`);

--
-- Indexes for table `bgp_server`
--
ALTER TABLE `bgp_server`
 ADD PRIMARY KEY (`server_id`);

--
-- Indexes for table `bgp_session`
--
ALTER TABLE `bgp_session`
 ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `bgp_user`
--
ALTER TABLE `bgp_user`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `bgp_userroles`
--
ALTER TABLE `bgp_userroles`
 ADD PRIMARY KEY (`UserID`,`RoleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bgp_box`
--
ALTER TABLE `bgp_box`
MODIFY `box_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_box_cache`
--
ALTER TABLE `bgp_box_cache`
MODIFY `box_cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_box_credential`
--
ALTER TABLE `bgp_box_credential`
MODIFY `box_credential_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_box_ip`
--
ALTER TABLE `bgp_box_ip`
MODIFY `box_ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_config`
--
ALTER TABLE `bgp_config`
MODIFY `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `bgp_game`
--
ALTER TABLE `bgp_game`
MODIFY `game_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `bgp_group`
--
ALTER TABLE `bgp_group`
MODIFY `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_group_member`
--
ALTER TABLE `bgp_group_member`
MODIFY `group_member_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_lgsl`
--
ALTER TABLE `bgp_lgsl`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_os`
--
ALTER TABLE `bgp_os`
MODIFY `os_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_permissions`
--
ALTER TABLE `bgp_permissions`
MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `bgp_roles`
--
ALTER TABLE `bgp_roles`
MODIFY `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bgp_script`
--
ALTER TABLE `bgp_script`
MODIFY `script_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_script_category`
--
ALTER TABLE `bgp_script_category`
MODIFY `script_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_server`
--
ALTER TABLE `bgp_server`
MODIFY `server_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bgp_user`
--
ALTER TABLE `bgp_user`
MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

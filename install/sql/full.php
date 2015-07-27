<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyright	Copyleft 2014, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



// Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
// PDO

try {
	// Connect to MySQL
	if (DB_DRIVER == 'sqlite') {
		$dbh = new PDO( DB_DRIVER.':'.DB_FILE );
	}
	else {
		$dbh = new PDO( DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD );
	}

	// Set ERRORMODE to exceptions
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
    die();
}

try {
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


	//---------------------------------------------------------+

	/*
	-- BrightGamePanel V2 Database
	-- Version 1.0.0
	-- 25/07/2015
	*/

	//---------------------------------------------------------+

	// Table structure for table "box"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."box  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."box (
	  box_id 			INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_credential_id	INTEGER UNSIGNED NOT NULL,
	  os_id				INTEGER UNSIGNED NOT NULL,
	  steam_lib_path	TEXT,
	  name				TEXT NOT NULL,
	  notes				TEXT,
	  PRIMARY KEY  (box_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "box_cache"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."box_cache  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."box_cache (
	  box_cache_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id		INTEGER UNSIGNED NOT NULL,
	  timestamp		TIMESTAMP NOT NULL,
	  cache			BLOB NOT NULL,
	  PRIMARY KEY  (box_cache_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "box_credential"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."box_credential  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."box_credential (
	  box_credential_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  login				BLOB NOT NULL,
	  password			BLOB NOT NULL,
	  remote_user_home	TEXT NOT NULL,
	  com_protocol		TEXT NOT NULL,
	  com_port			TEXT NOT NULL,
	  PRIMARY KEY (box_credential_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "box_ip"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."box_ip  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."box_ip (
	  box_ip_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id		INTEGER UNSIGNED NOT NULL,
	  ip			TEXT NOT NULL,
	  is_default	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY (box_ip_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "config"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."config  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."config (
	  config_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  setting		TEXT NOT NULL,
	  value			TEXT NOT NULL,
	  PRIMARY KEY (config_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "config"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."config (setting, value)
	VALUES
	  ('panel_name',		'BrightGamePanel V2'),
	  ('system_url',		'http://localhost/'),
	  ('panel_version',		'".LASTBGPVERSION."'),
	  ('maintenance_mode',	'0'),
	  ('last_cron_run',		'Never'),
	  ('user_template',		'bootstrap.min.css')  ; " );

	//---------------------------------------------------------+

	// Table structure for table "game"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."game  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."game (
	  game_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  name			TEXT NOT NULL,
	  status		TEXT NOT NULL,
	  max_slots		INTEGER UNSIGNED NOT NULL,
	  default_port	INTEGER UNSIGNED NOT NULL,
	  query_port	INTEGER UNSIGNED NOT NULL,
	  query_type	TEXT NOT NULL,
	  config		BLOB,
	  exe			TEXT NOT NULL,
	  launcher		TEXT NOT NULL,
	  cache_dir		TEXT NULL,
	  PRIMARY KEY  (game_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "game"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."game (
	  game_id, name,								status,		max_slots, default_port, query_port, query_type, config,						exe,			launcher,
	  			cache_dir
	)
	VALUES
	  ('1', 'Counter-Strike: Source',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[1])."',	'srcds_run',	'./srcds_run -game cstrike -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/css/'),
	  ('2', 'Day of Defeat: Source',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[2])."',	'srcds_run',	'./srcds_run -game dod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate',
	  			'~/game-repositories/dods/'),
	  ('3', 'Half-Life 2: Deathmatch',				'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[3])."',	'srcds_run',	'./srcds_run -game hl2mp -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate',
	  			'~/game-repositories/hl2dm/'),
	  ('4', 'Team Fortress 2',						'Active',	'24',  '27015', '27015',	'source',			'".serialize($GAME_DB[4])."',	'srcds_run',	'./srcds_run -game tf -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/tf2/'),
	  ('5', 'Left 4 Dead',							'Active',	'8',   '27015', '27015',	'source',			'".serialize($GAME_DB[5])."',	'srcds_run',	'./srcds_run -game left4dead -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/left4dead/'),
	  ('6', 'Left 4 Dead 2',						'Active',	'8',   '27015', '27015',	'source',			'".serialize($GAME_DB[6])."',	'srcds_run',	'./srcds_run -game left4dead2 -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/left4dead2/'),
	  ('7', 'Counter-Strike',						'Active',	'16',  '27015', '27015',	'halflife', 		'".serialize($GAME_DB[7])."',	'hlds_run',		'./hlds_run -game cstrike +ip {ip} +port {port} +maxplayers {slots} +map {cfg1} -pingboost {cfg2} -autoupdate',
	  			'~/game-repositories/cstrike/'),
	  ('8', 'Killing Floor',						'Inactive', '6',   '7707', 	'7708',		'killingfloor',		'".serialize($GAME_DB[8])."',	'ucc_bin',		'./ucc-bin server {cfg1}?game=KFmod.KFGameType?VACSecure={cfg2}?MaxPlayers={slots}?AdminName={cfg3}?AdminPassword={cfg4} -nohomedir ini={cfg5}',
	  			'~/game-repositories/kfserver/'),
	  ('9', 'Call of Duty 4: Modern Warfare',		'Inactive', '18',  '28960', '28960',	'callofduty4', 		'".serialize($GAME_DB[9])."',	'cod4_lnxded',	'./cod4_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/cod4/'),
	  ('10', 'Minecraft',							'Active',	'24',  '25565', '25565',	'minecraft',		'".serialize($GAME_DB[10])."',	'minecraft_server',	'java -Xms1024M -Xmx1024M -jar minecraft_server.jar nogui',
	  			'~/game-repositories/minecraft/'),
	  ('11', 'Call of Duty: Modern Warfare 3',		'Active',	'18',  '27015', '27016',	'callofdutymw3',	'".serialize($GAME_DB[11])."',	'iw5mp_server.exe',	'xvfb-run -a wine iw5mp_server.exe +set sv_config {cfg4} +set sv_maxclients {slots} +start_map_rotate +set net_ip {ip} +set net_port {port} +set net_queryPort {cfg1} +set net_authPort {cfg2} +set net_masterServerPort {cfg3} +set dedicated 2',
	  			'~/game-repositories/codmw3/'),
	  ('12', 'Call of Duty 2',						'Inactive', '32',  '28960', '28960',	'callofduty2',		'".serialize($GAME_DB[12])."',	'cod2_lnxded',	'./cod2_lnxded +exec {cfg1} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/cod2/'),
	  ('13', 'Call of Duty: World at War',			'Inactive', '32',  '28960', '28960',	'callofdutywaw', 	'".serialize($GAME_DB[13])."',	'codwaw_lnxded', './codwaw_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2',
	  			'~/game-repositories/codwaw/'),
	  ('14', 'Wolfenstein: Enemy Territory',		'Inactive', '32',  '27960', '27960',	'wolfet', 			'".serialize($GAME_DB[14])."',	'etded',		'./etded +exec {cfg1} +sv_maxclients {slots} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set net_port {port}',
	  			'~/game-repositories/wolfet/'),
	  ('15', 'ArmA: 2',								'Active',	'64',  '2302', 	'2302',		'arma2',			'".serialize($GAME_DB[15])."',	'server',		'./server -config={cfg1} -netlog -port={port}',
	  			'~/game-repositories/arma2/'),
	  ('16', 'Garrysmod',							'Active',	'16',  '27015', '27015',	'source',			'".serialize($GAME_DB[16])."',	'srcds_run',	'./srcds_run -game garrysmod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate',
	  			'~/game-repositories/garrysmod/'),
	  ('17', 'Counter-Strike: Global Offensive',	'Active',	'24',  '27015', '27015',	'source',			'".serialize($GAME_DB[17])."',	'srcds_run',	'./srcds_run -game csgo -console -usercon -secure -nohltv -tickrate {cfg5} +ip {ip} +hostport {port} -maxplayers_override {slots} +map {cfg1} +mapgroup {cfg2} +game_type {cfg3} +game_mode {cfg4}',
	  			'~/game-repositories/csgo/'),
	  ('18', 'ArmA: Armed Assault',					'Active',	'64',  '2302',  '2302',		'arma',				'".serialize($GAME_DB[18])."',	'server',		'./server -config={cfg1} -netlog -port={port}',
	  			'~/game-repositories/arma/'),
	  ('19', 'Battlefield 2',						'Active',	'64',  '16567', '29900',	'bf2',				'".serialize($GAME_DB[19])."',	'start.sh',		'./start.sh',
	  			'~/game-repositories/bf2/'),
	  ('20', 'Battlefield 1942',					'Active',	'64',  '14567', '23000',	'bf1942', 			'".serialize($GAME_DB[20])."',	'start.sh',		'./start.sh +statusMonitor 1',
	  			'~/game-repositories/bf1942/'),
	  ('21', 'Multi Theft Auto',					'Active',	'128', '22003', '22126',	'mta',				'".serialize($GAME_DB[21])."',	'mta-server',	'./mta-server -t --ip {ip} --port {port} --httpport {port} --maxplayers {slots}',
	  			'~/game-repositories/mta/'),
	  ('22', 'San Andreas: Multiplayer (SA-MP)',	'Active',	'128', '7777',  '7777',		'samp',				'".serialize($GAME_DB[22])."',	'samp03svr',	'./samp03svr',
	  			'~/game-repositories/samp/'),
	  ('23', 'Urban Terror',						'Active',	'32',  '27960', '27960',	'urbanterror',		'".serialize($GAME_DB[23])."',	'ioUrTded.i386', './ioUrTded.i386 +set fs_game q3ut4 +set net_port {port} +set com_hunkmegs 128 +exec {cfg1} +set dedicated 2',
	  			'~/game-repositories/urbanterror/') 
	; " );

	//---------------------------------------------------------+

	// Table structure for table "group"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."group  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."group (
	  group_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  name			TEXT NOT NULL,
	  description	TEXT,
	  PRIMARY KEY  (group_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "group_member"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."group_member  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."group_member (
	  group_member_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  group_id			INTEGER UNSIGNED NOT NULL,
	  client_id			INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (group_member_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "lgsl"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."lgsl  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."lgsl (
	  id 		 INTEGER UNSIGNED 	NOT NULL AUTO_INCREMENT,
	  type       VARCHAR(255)     	NOT NULL DEFAULT '',
	  ip         VARCHAR(255)    	NOT NULL DEFAULT '',
	  c_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  q_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  s_port     VARCHAR(255)      	NOT NULL DEFAULT '0',
	  zone       VARCHAR(255)    	NOT NULL DEFAULT '',
	  disabled   INTEGER UNSIGNED  	NOT NULL DEFAULT '0',
	  comment    VARCHAR(255) 		NOT NULL DEFAULT '',
	  status     INTEGER UNSIGNED  	NOT NULL DEFAULT '0',
	  cache      TEXT             	NOT NULL,
	  cache_time TEXT             	NOT NULL,
	  PRIMARY KEY  (id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "os"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."os  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."os (
	  os_id				INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  operating_system	TEXT NOT NULL,
	  PRIMARY KEY (os_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "permissions"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."permissions  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."permissions (
	  ID 			INTEGER UNSIGNED NOT NULL auto_increment,
	  Lft 			INTEGER UNSIGNED NOT NULL,
	  Rght 			INTEGER UNSIGNED NOT NULL,
	  Title 		TEXT NOT NULL,
	  Description 	TEXT NOT NULL,
	  PRIMARY KEY  (ID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "permissions"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."permissions (ID, Lft, Rght, Title, Description)
	VALUES (1, 0, 1, 'root', 'root');
		" );

	//---------------------------------------------------------+

	// Table structure for table "rolepermissions"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."rolepermissions  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."rolepermissions (
	  RoleID 			INTEGER UNSIGNED NOT NULL,
	  PermissionID 		INTEGER UNSIGNED NOT NULL,
	  AssignmentDate 	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (RoleID, PermissionID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "rolepermissions"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."rolepermissions (RoleID, PermissionID, AssignmentDate)
	VALUES (1, 1, " . time() . ");
		" );

	//---------------------------------------------------------+

	// Table structure for table "roles"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."roles  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."roles (
	  ID 			INTEGER UNSIGNED NOT NULL auto_increment,
	  Lft 			INTEGER UNSIGNED NOT NULL,
	  Rght 			INTEGER UNSIGNED NOT NULL,
	  Title 		TEXT NOT NULL,
	  Description 	TEXT NOT NULL,
	  PRIMARY KEY  (ID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "roles"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."roles (ID, Lft, Rght, Title, Description)
	VALUES (1, 0, 1, 'root', 'root');
		" );

	//---------------------------------------------------------+

	// Table structure for table "script"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."script  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."script (
	  script_id				INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  script_category_id	INTEGER UNSIGNED NOT NULL,
	  box_id				INTEGER UNSIGNED NOT NULL,
	  owner_id				INTEGER UNSIGNED NOT NULL,
	  owner_type			TEXT NOT NULL,
	  name					TEXT NOT NULL,
	  description			TEXT,
	  status				TEXT NOT NULL,
	  process_state			TEXT,
	  path 					TEXT NOT NULL,
	  launcher				TEXT NOT NULL,
	  screen_name			TEXT,
	  type					TEXT NOT NULL,
	  PRIMARY KEY  (script_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "script_category"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."script_category  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."script_category (
	  script_category_id	INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  name					TEXT NOT NULL,
	  description			TEXT,
	  PRIMARY KEY  (script_category_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "server"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."server  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."server (
	  server_id			INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  box_id			INTEGER UNSIGNED NOT NULL,
	  ip_id				INTEGER UNSIGNED NOT NULL,
	  game_id			INTEGER UNSIGNED NOT NULL,
	  owner_id			INTEGER UNSIGNED NOT NULL,
	  owner_type		TEXT NOT NULL,
	  name				TEXT NOT NULL,
	  description 		TEXT,
	  status			TEXT NOT NULL,
	  process_state		TEXT NOT NULL,
	  slots				INTEGER UNSIGNED NOT NULL,
	  port				INTEGER UNSIGNED NOT NULL,
	  query_port		INTEGER UNSIGNED NOT NULL,
	  config 			BLOB,
	  path				TEXT NOT NULL,
	  launcher			TEXT NOT NULL,
	  screen_name		TEXT NOT NULL,
	  PRIMARY KEY  (server_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "session"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."session  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."session (
	  session_id		VARCHAR(255) NOT NULL,
	  session_data		BLOB NOT NULL,
	  expires			INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (session_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	//---------------------------------------------------------+

	// Table structure for table "user"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."user  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."user (
	  user_id		INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  username		TEXT NOT NULL,
	  password		TEXT NOT NULL,
	  firstname		TEXT,
	  lastname		TEXT,
	  email			TEXT NOT NULL,
	  notes			TEXT,
	  status		TEXT NOT NULL,
	  lang			TEXT NOT NULL,
	  last_login	TIMESTAMP,
	  last_activity	TIMESTAMP,
	  last_ip		TEXT,
	  last_host		TEXT,
	  token			TEXT,
	  PRIMARY KEY  (user_id)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "user"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."user (
	  user_id,
	  username,
	  password,
	  firstname,
	  lastname,
	  email,
	  notes,
	  status,
	  lang,
	  last_login,
	  last_activity,
	  last_ip,
	  last_host,
	  token
	)
	VALUES (
	  1,
	  'root',
	  '".getHash('password', $APP_AUTH_SALT)."',
	  'root',
	  'root',
	  'root@toor.com',
	  '',
	  'Active',
	  '".CONF_DEFAULT_LOCALE."',
	  '".date('Y-m-d H:i:s', time())."',
	  '".date('Y-m-d H:i:s', time())."',
	  '',
	  '',
	  NULL
	)  ; " );

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."user (
	  user_id,
	  username,
	  password,
	  firstname,
	  lastname,
	  email,
	  notes,
	  status,
	  lang,
	  last_login,
	  last_activity,
	  last_ip,
	  last_host,
	  token
	)
	VALUES (
	  2,
	  'api',
	  '".getHash(str_shuffle( 'abcdefghijkmnpqrstuvwxyz23456789-#@*!_?ABCDEFGHJKLMNPQRSTUVWXYZ' ), $APP_AUTH_SALT)."',
	  '',
	  '',
	  'root@toor.com',
	  '',
	  'Inactive',
	  '".CONF_DEFAULT_LOCALE."',
	  '".date('Y-m-d H:i:s', time())."',
	  '".date('Y-m-d H:i:s', time())."',
	  '',
	  '',
	  NULL
	)  ; " );

	//---------------------------------------------------------+

	// Table structure for table "userroles"

		$dbh->exec( "DROP TABLE IF EXISTS ".DB_PREFIX."userroles  ; " );
		$dbh->exec( "
	CREATE TABLE ".DB_PREFIX."userroles (
	  UserID 			INTEGER UNSIGNED NOT NULL,
	  RoleID 			INTEGER UNSIGNED NOT NULL,
	  AssignmentDate 	INTEGER UNSIGNED NOT NULL,
	  PRIMARY KEY  (UserID, RoleID)
	)
	ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

	// Data for table "userroles"

		$dbh->exec( "
	INSERT INTO ".DB_PREFIX."userroles (UserID, RoleID, AssignmentDate)
	VALUES (1, 1, " . time() . ");
		" );


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
}
catch (PDOException $e) {
    echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
    die();
}

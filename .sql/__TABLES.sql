/**
	Profile.
*/
DROP TABLE IF EXISTS profile;
CREATE TABLE profile (
	id	int UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`group_name`	varchar(20),
	`invites_no`	int,
	
	`sex`		varchar(100),
	`age_min`	int,
	`age_max`	int,
	`region`	varchar(200),
	
	`csv_row`	text,
	`csv_file`	int UNSIGNED,
	
	PRIMARY KEY (id),
	KEY (csv_file)
);

/**
	Osoby.
*/
DROP TABLE IF EXISTS personal;
CREATE TABLE personal (
	id int UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`sex`		varchar(100),
	`age`		int,
	`region`	varchar(200),
	
	`csv_row`	text,
	`csv_file`	int UNSIGNED,
	
	PRIMARY KEY (id),
	KEY (csv_file)
);

/**
	Pliki źródłowe (CSV).
*/
DROP TABLE IF EXISTS file;
CREATE TABLE file (
	id int UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`type`		varchar(20),	-- 'profile' / 'personal' (?)
	`name`		varchar(200),
	`dt_upload` DATETIME,

	`contents`	LONGTEXT,
	
	PRIMARY KEY (id),
);

/**
	Historia działań/zdarzeń.
*/
DROP TABLE IF EXISTS event_history;
CREATE TABLE event_history (
	id int UNSIGNED NOT NULL AUTO_INCREMENT,

	`uuid`			varchar(50),
	`dt_create`		DATETIME,
	`dt_change`		DATETIME,
	`history_data`	LONGTEXT,

	PRIMARY KEY (id),
	KEY (uuid)
);

/**
    Profile.
*/
DROP TABLE IF EXISTS profile;
CREATE TABLE profile (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    
    `dt_create`   DATETIME,
    `dt_change`   DATETIME,

    `group_name`  varchar(20),
    `invites_no`  int,
    
    `sex`         varchar(100),
    `age_min`     int,
    `age_max`     int,
    `region`      varchar(200),
    
	`row_state`   int,
    `csv_row`     text,
    `csv_file`    int UNSIGNED,
    
    PRIMARY KEY (id),
    KEY (csv_file)
);

/**
    Osoby.
*/
DROP TABLE IF EXISTS personal;
CREATE TABLE personal (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,

    `dt_create`     DATETIME,
    `dt_change`     DATETIME,
    
    `sex`           varchar(20),
    `age`           int,
    `region`        varchar(200),

    `pesel`         varchar(12),
    
    `name`          varchar(255),
    `surname`       varchar(255),
    `city`          varchar(255),
    `street`        varchar(255),
    `building_no`   varchar(10),
    `flat_no`       varchar(10),
    `zip_code`      varchar(10),
    
	`row_state`     int,
    `csv_row`       text,
    `csv_file`      int UNSIGNED,
    
    PRIMARY KEY (id),
    KEY (csv_file)
);

/**
    Pliki źródłowe (CSV).
*/
DROP TABLE IF EXISTS file;
CREATE TABLE file (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    
    `dt_create`   DATETIME,
    `dt_change`   DATETIME,

    `type`        varchar(20),    -- 'profile' / 'personal' (?)
    `column_map`  text,            -- column mapping e.g. 'sex:2,\nage:3,\nregion:1,\n...'
    `name`        varchar(200),

    `contents`    LONGTEXT,
    
    PRIMARY KEY (id)
);

/**
    Historia działań/zdarzeń.
*/
DROP TABLE IF EXISTS event_history;
CREATE TABLE event_history (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,

    `uuid`          varchar(50),
    `dt_create`     DATETIME,
    `dt_change`     DATETIME,
    `history_data`  LONGTEXT,

    PRIMARY KEY (id),
    KEY (uuid)
);

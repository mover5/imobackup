<?php

$TABLE[] = "ALTER TABLE members ADD chat_banned TINYINT(1) NOT NULL DEFAULT '0';";

$TABLE[] = "CREATE TABLE chat_log_archive (
 log_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 log_room_id INT NOT NULL DEFAULT '0',
 log_time INT NOT NULL DEFAULT '0',
 log_code SMALLINT NOT NULL DEFAULT '0',
 log_user VARCHAR( 255 ) NULL DEFAULT NULL ,
 log_message TEXT NULL DEFAULT NULL ,
 log_extra VARCHAR( 255 ) NULL DEFAULT NULL
) ENGINE = MYISAM;";
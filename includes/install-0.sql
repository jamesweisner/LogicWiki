CREATE TABLE `Prop` (
	`propID`   INT(10) UNSIGNED NOT NULL,
	`contents` TEXT             NOT NULL
	`source`   TEXT             NOT NULL,
	`validity` INT(10)          NOT NULL,
);

CREATE TABLE `Argument` (
	`propID`   INT(10) UNSIGNED NOT NULL,
	`version`  INT(10) UNSIGNED NOT NULL,
	`userID`   INT(10) UNSIGNED NOT NULL,
	`validity` INT(10)          NOT NULL,
	`premises` TEXT             NOT NULL,
	`time`     INT(10) UNSIGNED NOT NULL,
);

CREATE TABLE `Vote` (
	`userID`   INT(10) UNSIGNED NOT NULL,
	`propID`   INT(10) UNSIGNED NOT NULL,
	`validity` INT(10)          NOT NULL,
	`time`     INT(10) UNSIGNED NOT NULL,
);

CREATE TABLE `user` (
	`user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
	`email`   VARCHAR(255) NOT NULL UNIQUE KEY,
	`name`    VARCHAR(255) NOT NULL
);

CREATE TABLE `friend` (
	`user_id`   INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`friend_id` INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`notify`    BOOL         NOT NULL,
	PRIMARY KEY (user_id, friend_id)
);

CREATE TABLE `proposition` (
	`prop_id`      INT   UNSIGNED NOT NULL PRIMARY KEY,
	`contents`     TEXT           NOT NULL,
	`popularity`   INT   UNSIGNED NOT NULL, /* Calculated number of arguments in which this proposition is used.  */
	`endorsements` INT   UNSIGNED NOT NULL, /* Calculated number of endorsements whether supporting or negative.  */
	`soundness`    FLOAT UNSIGNED NOT NULL  /* Calculated ratio of those endorsements that support its soundness. */
);

CREATE TABLE `argument` (
	`arg_id`       INT   UNSIGNED NOT NULL PRIMARY KEY,
	`conclusion`   INT   UNSIGNED NOT NULL REFERENCES Proposition(prop_id),
	`author`       INT   UNSIGNED NOT NULL REFERENCES User(user_id),
	`version`      INT   UNSIGNED NOT NULL,
	`published`    TIMESTAMP  DEFAULT NULL,
	`popularity`   INT   UNSIGNED NOT NULL, /* Calculated number of arguments using its conclusion as a premise. */
	`endorsements` INT   UNSIGNED NOT NULL, /* Calculated number of endorsements whether supporting or negative. */
	`validity`     FLOAT UNSIGNED NOT NULL  /* Calculated ratio of those endorsements that support its validity. */
);

CREATE TABLE `premise` (
	`arg_id`  INT UNSIGNED NOT NULL REFERENCES Argument(arg_id),
	`prop_id` INT UNSIGNED NOT NULL REFERENCES Proposition(prop_id),
	`num`     INT UNSIGNED NOT NULL,
	PRIMARY KEY (arg_id, num)
);

CREATE TABLE `validity` (
	`user_id`  INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`arg_id`   INT UNSIGNED NOT NULL REFERENCES Argument(arg_id),
	`support`  BOOL         NOT NULL,
	`time`     TIMESTAMP    NOT NULL,
	PRIMARY KEY (user_id, arg_id)
);

CREATE TABLE `soundness` (
	`user_id`  INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`prop_id`  INT UNSIGNED NOT NULL REFERENCES Proposition(prop_id),
	`support`  BOOL         NOT NULL,
	`time`     TIMESTAMP    NOT NULL,
	PRIMARY KEY (user_id, prop_id)
);

CREATE TABLE `comment` (
	`comment_id` INT UNSIGNED NOT NULL PRIMARY KEY,
	`arg_id`     INT UNSIGNED NOT NULL REFERENCES Argument(arg_id),
	`user_id`    INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`contents`   TEXT         NOT NULL,
	`created`    TIMESTAMP    NOT NULL,
	`edited`     TIMESTAMP    NOT NULL
);

CREATE TABLE `notice` (
	`notice_id` INT UNSIGNED NOT NULL PRIMARY KEY,
	`user_id`   INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`arg_id`    INT UNSIGNED NOT NULL REFERENCES Argument(arg_id),
	`actor`     INT UNSIGNED NOT NULL REFERENCES User(user_id),
	`contents`  TEXT         NOT NULL,
	`time`      TIMESTAMP    NOT NULL,
	INDEX `check_new` (user_id, notice_id DESC)
);

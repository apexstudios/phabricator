CREATE TABLE {$NAMESPACE}_packager.packager_transaction (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  phid VARCHAR(64) NOT NULL COLLATE utf8_bin,
  authorPHID VARCHAR(64) NOT NULL COLLATE utf8_bin,
  objectPHID VARCHAR(64) NOT NULL COLLATE utf8_bin,
  viewPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin,
  editPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin,
  commentPHID VARCHAR(64) COLLATE utf8_bin,
  commentVersion INT UNSIGNED NOT NULL,
  transactionType VARCHAR(32) NOT NULL COLLATE utf8_bin,
  oldValue LONGTEXT NOT NULL COLLATE utf8_bin,
  newValue LONGTEXT NOT NULL COLLATE utf8_bin,
  contentSource LONGTEXT NOT NULL COLLATE utf8_bin,
  dateCreated INT UNSIGNED NOT NULL,
  dateModified INT UNSIGNED NOT NULL,

  UNIQUE KEY `key_phid` (phid),
  KEY `key_object` (objectPHID)

) ENGINE=InnoDB, COLLATE utf8_general_ci;

CREATE TABLE {$NAMESPACE}_packager.packager_transaction_comment (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  phid VARCHAR(64) NOT NULL COLLATE utf8_bin,
  transactionPHID VARCHAR(64) COLLATE utf8_bin,
  authorPHID VARCHAR(64) NOT NULL COLLATE utf8_bin,
  viewPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin,
  editPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin,
  commentVersion INT UNSIGNED NOT NULL,
  content LONGTEXT NOT NULL COLLATE utf8_bin,
  contentSource LONGTEXT NOT NULL COLLATE utf8_bin,
  isDeleted BOOL NOT NULL,
  dateCreated INT UNSIGNED NOT NULL,
  dateModified INT UNSIGNED NOT NULL,

  UNIQUE KEY `key_phid` (phid),
  UNIQUE KEY `key_version` (transactionPHID, commentVersion)

) ENGINE=InnoDB, COLLATE utf8_general_ci;

CREATE TABLE {$NAMESPACE}_packager.`edge` (
  `src` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `dst` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `seq` int(10) unsigned NOT NULL,
  `dataID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`src`,`type`,`dst`),
  KEY `src` (`src`,`type`,`dateCreated`,`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE {$NAMESPACE}_packager.`edgedata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

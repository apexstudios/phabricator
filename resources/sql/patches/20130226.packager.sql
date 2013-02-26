CREATE TABLE {$NAMESPACE}_packager.packager_filepackage (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  phid VARCHAR(64) NOT NULL COLLATE utf8_bin,
  authorPHID VARCHAR(64) NOT NULL COLLATE utf8_bin,
  packageUrl VARCHAR(255) NOT NULL COLLATE utf8_general_ci,
  downloads INT UNSIGNED NOT NULL DEFAULT 0,
  dateCreated INT UNSIGNED NOT NULL,
  dateModified INT UNSIGNED NOT NULL,
  KEY `key_author` (authorPHID),
  UNIQUE KEY `key_package` (packagePHID),
  UNIQUE KEY `key_package_url` (packageUrl)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

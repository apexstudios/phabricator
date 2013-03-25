ALTER TABLE {$NAMESPACE}_packager.packager_filepackage
  ADD packageRequestID INT UNSIGNED DEFAULT 0,
  ADD viewPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin,
  ADD editPolicy VARCHAR(64) NOT NULL COLLATE utf8_bin;

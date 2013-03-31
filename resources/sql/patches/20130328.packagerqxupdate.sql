ALTER TABLE `{$NAMESPACE}_packager`.`package_request_transaction`
  ADD metadata LONGTEXT NOT NULL COLLATE utf8_bin;
UPDATE `{$NAMESPACE}_packager`.package_request_transaction SET metadata = '{}'
  WHERE metadata = '';

ALTER TABLE `{$NAMESPACE}_packager`.`packager_transaction`
  ADD metadata LONGTEXT NOT NULL COLLATE utf8_bin;
UPDATE `{$NAMESPACE}_packager`.packager_transaction SET metadata = '{}'
  WHERE metadata = '';

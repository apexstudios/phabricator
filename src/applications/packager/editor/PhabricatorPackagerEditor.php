<?php

final class PhabricatorPackagerEditor
  extends PhabricatorApplicationTransactionEditor {

  public function getTransactionTypes() {
    $types = parent::getTransactionTypes();

    $types[] = PhabricatorTransactions::TYPE_COMMENT;
    $types[] = PhabricatorPackagerTransactionType::TYPE_DOWNLOAD;

    return $types;
  }

  protected function getCustomTransactionOldValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return $object->getDownloads();
    }
  }

  protected function getCustomTransactionNewValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return $xaction->getNewValue() + 1;
    }
  }

  protected function applyCustomInternalTransaction(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        $object->setDownloads($xaction->getNewValue());
        break;
    }
  }

  protected function applyCustomExternalTransaction(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {
    return;
  }

  protected function mergeTransactions(
    PhabricatorApplicationTransaction $u,
    PhabricatorApplicationTransaction $v) {

    $type = $u->getTransactionType();
    switch ($type) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return $v;
    }

    return parent::mergeTransactions($u, $v);
  }

  protected function supportsMail() {
    return false;
  }

  protected function supportsFeed() {
    return true;
  }
}

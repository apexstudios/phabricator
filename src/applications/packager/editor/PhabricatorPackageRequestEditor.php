<?php

final class PhabricatorPackageRequestEditor
  extends PhabricatorApplicationTransactionEditor {

  public function getTransactionTypes() {
    $types = parent::getTransactionTypes();

    $types[] = PhabricatorTransactions::TYPE_COMMENT;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_ISSUE;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_REGISTER;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_DESC;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_FILE;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_REV;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_URL;

    return $types;
  }

  protected function getCustomTransactionOldValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return $object->getStatus();
      case PhabricatorPackageRequestTransactionType::TYPE_FILE:
        return $object->getFileName();
      case PhabricatorPackageRequestTransactionType::TYPE_DESC:
        return $object->getDescription();
      case PhabricatorPackageRequestTransactionType::TYPE_REV:
        return $object->getRevision();
      case PhabricatorPackageRequestTransactionType::TYPE_URL:
        return $object->getUrl();
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return $object->getPackageID();
    }
  }

  protected function getCustomTransactionNewValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_DESC:
      case PhabricatorPackageRequestTransactionType::TYPE_FILE:
      case PhabricatorPackageRequestTransactionType::TYPE_REV:
      case PhabricatorPackageRequestTransactionType::TYPE_URL:
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return $xaction->getNewValue();
    }
  }

  protected function applyCustomInternalTransaction(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        $object->setStatus(PhabricatorPackageRequestConstants::STATUS_ISSUED);
        break;
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        $object->setStatus(PhabricatorPackageRequestConstants::STATUS_PACKAGED);
        $object->setPackageID($xaction->getNewValue());
        break;
      case PhabricatorPackageRequestTransactionType::TYPE_DESC:
        return $object->setDescription($xaction->getNewValue());
      case PhabricatorPackageRequestTransactionType::TYPE_FILE:
        return $object->setFileName($xaction->getNewValue());
      case PhabricatorPackageRequestTransactionType::TYPE_REV:
        return $object->setRevision($xaction->getNewValue());
      case PhabricatorPackageRequestTransactionType::TYPE_URL:
        return $object->setUrl($xaction->getNewValue());
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
      case PhabricatorPackageRequestTransactionType::TYPE_DESC:
      case PhabricatorPackageRequestTransactionType::TYPE_FILE:
      case PhabricatorPackageRequestTransactionType::TYPE_REV:
      case PhabricatorPackageRequestTransactionType::TYPE_URL:
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return $v;
    }

    return parent::mergeTransactions($u, $v);
  }

}

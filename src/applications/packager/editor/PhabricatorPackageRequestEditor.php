<?php

final class PhabricatorPackageRequestEditor
  extends PhabricatorApplicationTransactionEditor {

  public function getTransactionTypes() {
    $types = parent::getTransactionTypes();

    $types[] = PhabricatorTransactions::TYPE_COMMENT;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_ISSUE;
    $types[] = PhabricatorPackageRequestTransactionType::TYPE_REGISTER;

    return $types;
  }

  protected function getCustomTransactionOldValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return $object->getStatus();
    }
  }

  protected function getCustomTransactionNewValue(
    PhabricatorLiskDAO $object,
    PhabricatorApplicationTransaction $xaction) {

    switch ($xaction->getTransactionType()) {
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
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        $object->setStatus($xaction->getNewValue());
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
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return $v;
    }

    return parent::mergeTransactions($u, $v);
  }

}

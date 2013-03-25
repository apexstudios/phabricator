<?php

final class PhabricatorPackageRequestTransaction
  extends PhabricatorApplicationTransaction {

  public function getApplicationName() {
    return 'packager';
  }

  public function getTableName() {
    return 'package_request_transaction';
  }

  public function getApplicationTransactionType() {
    return PhabricatorPHIDConstants::PHID_TYPE_PKRQ;
  }

  public function getApplicationTransactionCommentObject() {
    return new PhabricatorPackageRequestTransactionComment();
  }

  public function getApplicationObjectTypeName() {
    return pht('packager');
  }

  public function getTitle() {
    $author_phid = $this->getAuthorPHID();

    switch ($this->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return pht(
          '%s issued the packaging request to the servers.',
          $this->renderHandleLink($author_phid));
        break;
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return pht(
          '%s registered a package with this PRQ.',
          $this->renderHandleLink($author_phid));
        break;
    }

    return parent::getTitle();
  }

  public function getTitleForFeed() {
    $author_phid = $this->getAuthorPHID();
    $object_phid = $this->getObjectPHID();

    switch ($this->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return pht(
          '%s issued a request for %s',
          $this->renderHandleLink($author_phid),
          $this->renderHandleLink($object_phid));
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return pht(
          '%s packaged %s.',
          $this->renderHandleLink($author_phid),
          $this->renderHandleLink($object_phid));
    }

    return parent::getTitleForFeed();
  }

  public function getActionName() {
    switch ($this->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return pht('Issued');
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return pht('Packaged');
    }

    return parent::getActionName();
  }

  public function getIcon() {
    switch ($this->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return 'edit';
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return 'create';
    }

    return parent::getIcon();
  }

  public function getColor() {
    switch ($this->getTransactionType()) {
      case PhabricatorPackageRequestTransactionType::TYPE_REGISTER:
        return PhabricatorTransactions::COLOR_SKY;
      case PhabricatorPackageRequestTransactionType::TYPE_ISSUE:
        return PhabricatorTransactions::COLOR_BLUE;
    }

    return parent::getColor();
  }

}


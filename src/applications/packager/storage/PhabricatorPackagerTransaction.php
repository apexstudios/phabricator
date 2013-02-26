<?php

final class PhabricatorPackagerTransaction
  extends PhabricatorApplicationTransaction {

  public function getApplicationName() {
    return 'packager';
  }

  public function getApplicationTransactionType() {
    return PhabricatorPHIDConstants::PHID_TYPE_PCKG;
  }

  public function getApplicationTransactionCommentObject() {
    return new PhabricatorPackagerTransactionComment();
  }

  public function getApplicationObjectTypeName() {
    return pht('packager');
  }

  public function getTitle() {
    $author_phid = $this->getAuthorPHID();

    switch ($this->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return pht(
          '%s downloaded this package.',
          $this->renderHandleLink($author_phid));
        break;
    }

    return parent::getTitle();
  }

  public function getTitleForFeed() {
    if ($this->getTransactionType() ==
      PhabricatorPackagerTransactionType::TYPE_DOWNLOAD) {
      return $this->getTitle();
    } else {
      return parent::getTitleForFeed();
    }
  }

  public function getActionName() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return pht('Downloaded');
        break;
    }

    return parent::getActionName();
  }

  public function getActionStrength() {
    switch ($this->getTransactionType()) {
      case PhabricatorPackagerTransactionType::TYPE_DOWNLOAD:
        return 1.5;
    }
    return parent::getActionStrength();
  }

  public function getIcon() {
    if ($this->getTransactionType() ==
      PhabricatorPackagerTransactionType::TYPE_DOWNLOAD) {
      return 'view';
    } else {
      return parent::getIcon();
    }
  }

  public function getColor() {
    if ($this->getTransactionType() ==
      PhabricatorPackagerTransactionType::TYPE_DOWNLOAD) {
      return PhabricatorTransactions::COLOR_SKY;
    } else {
      return parent::getColor();
    }
  }

}


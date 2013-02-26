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


}


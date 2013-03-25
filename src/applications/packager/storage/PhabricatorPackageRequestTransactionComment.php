<?php

final class PhabricatorPackageRequestTransactionComment
  extends PhabricatorApplicationTransactionComment {

  public function getApplicationTransactionObject() {
    return new PhabricatorPackageRequestTransaction();
  }

}


<?php

final class PhabricatorPackagerTransactionComment
  extends PhabricatorApplicationTransactionComment {

  public function getApplicationTransactionObject() {
    return new PhabricatorPackagerTransaction();
  }

}


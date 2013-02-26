<?php

final class PhabricatorPackagerTransactionQuery
  extends PhabricatorApplicationTransactionQuery {

  protected function getTemplateApplicationTransaction() {
    return new PhabricatorPackagerTransaction();
  }

}

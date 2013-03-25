<?php

final class PhabricatorPackageRequestTransactionQuery
  extends PhabricatorApplicationTransactionQuery {

  protected function getTemplateApplicationTransaction() {
    return new PhabricatorPackageRequestTransaction();
  }

}

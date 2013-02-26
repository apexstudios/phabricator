<?php

final class PhabricatorPackagerTransactionQuery
  extends PhabricatorApplicationTransactionQuery {

  protected function getTemplateApplicationTransaction() {
    return new PhabricatorPackagerTransaction();
  }

  public function executeWithOffsetPager(AphrontPagerView $pager) {
    $this->setLimit($pager->getPageSize() + 1);
    $this->setOffset($pager->getOffset());

    $results = $this->execute();

    return $pager->sliceResults($results);
  }

}

<?php

abstract class PhabricatorPackagerRequestController
  extends PhabricatorPackagerController {

  protected function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setName(pht('Package Requests'))
        ->setHref($this->getApplicationURI('/request/')));

    return $crumbs;
  }

}

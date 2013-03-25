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

  protected function buildRQName(PhabricatorPackageRequest $package_request) {
    return pht('Package RQ %s (%s)',
      $package_request->getID(), $package_request->getFileName());
  }

}

<?php

abstract class PhabricatorPackagerController
  extends PhabricatorController {

  protected function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addAction(
      id(new PhabricatorMenuItemView())
        ->setName(pht('Register Package'))
        ->setHref($this->getApplicationURI('/create/'))
        ->setIcon('create'));

    return $crumbs;
  }

}

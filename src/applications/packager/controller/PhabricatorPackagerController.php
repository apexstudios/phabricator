<?php

abstract class PhabricatorPackagerController
  extends PhabricatorController {

  protected function buildSideNavView($for_app = false) {
    $nav = new AphrontSideNavFilterView();
    $nav->setBaseURI(new PhutilURI($this->getApplicationURI()));

    if ($for_app) {
      $nav->addLabel(pht('Register Package'));
      $nav->addFilter('',
        pht('Register Package'),
        $this->getApplicationURI('/register/'));
    }

    $nav->addLabel(pht('Packages'));
    $nav->addFilter('/', pht('All Packages'));

    return $nav;
  }

  public function buildApplicationMenu() {
    return $this->buildSideNavView($for_app = true)->getMenu();
  }

  protected function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addAction(
      id(new PhabricatorMenuItemView())
        ->setName(pht('Register new package'))
        ->setHref($this->getApplicationURI('/register/'))
        ->setWorkflow(true)
        ->setIcon('create'));

    return $crumbs;
  }

}

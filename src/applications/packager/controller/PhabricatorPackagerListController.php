<?php

final class PhabricatorPackagerListController
  extends PhabricatorPackagerController {

  public function processRequest() {

    $request = $this->getRequest();

    $package_table = new PhabricatorFilePackage();
    $conn = $package_table->establishConnection('r');

    $pager = new AphrontPagerView();
    $pager->setOffset($request->getInt('page'));

    $package_entries = $package_table->loadAllWhere(
      '1 = 1 ORDER BY id DESC LIMIT %d, %d',
      $pager->getOffset(),
      $pager->getPageSize());

    // Get an exact count since the size here is reasonably going to be a few
    // thousand at most in any reasonable case.
    $countQuery = queryfx_one(
      $conn,
      'SELECT COUNT(*) N FROM %T',
      $package_table->getTableName());
    $count = $countQuery['N'];

    $pager->setCount($count);
    $pager->setURI($request->getRequestURI(), 'page');

    $nodata = pht('There are no registered packages yet.');

    $list = new PhabricatorObjectItemListView();
    // $list->setStackable();
    $list->setNoDataString($nodata);

    foreach ($package_entries as $package) {
    $this->loadHandles(array($package->getAuthorPHID()));
      $url = $package->getPackageUrl();
      $fileName = basename($url);

      $item = id(new PhabricatorObjectItemView())
        ->setHeader($fileName)
        ->setHref($this->getApplicationURI("view/" . $package->getID()))
        ->addAttribute($this->getHandle($package->getAuthorPHID())
          ->renderLink())
        ->addAttribute($url)
        ->addIcon(
          'view',
          pht('Download'),
          "download/" . $package->getID());

      $list->addItem($item);
    }

    $header = id(new PhabricatorHeaderView())
      ->setHeader(pht("Registered Packages"));

    $nav = $this->buildSideNavView($for_app = false);

    $nav->appendChild(array(
        $header,
        $list,
        $pager,
    ));

    $name = pht('All Packages');

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setName($name)
        ->setHref($request->getRequestURI()));
    $nav->setCrumbs($crumbs);

    $nav->selectFilter('/');

    return $this->buildApplicationPage(
      $nav,
      array(
        'device' => true,
        'title' => pht('File Packages'),
      ));
  }
}

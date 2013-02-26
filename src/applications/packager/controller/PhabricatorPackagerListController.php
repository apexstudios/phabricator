<?php

final class PhabricatorPackagerListController
  extends PhabricatorPackagerController {

  public function processRequest() {

    $request = $this->getRequest();

    $packageTable = new PhabricatorFilePackage();
    $conn = $packageTable->establishConnection('r');

    $pager = new AphrontPagerView();
    $pager->setOffset($request->getInt('page'));

    $packageEntries = $packageTable->loadAllWhere(
      '1 = 1 ORDER BY id DESC LIMIT %d, %d',
      $pager->getOffset(),
      $pager->getPageSize());

    // Get an exact count since the size here is reasonably going to be a few
    // thousand at most in any reasonable case.
    $countQuery = queryfx_one(
      $conn,
      'SELECT COUNT(*) N FROM %T',
      $packageTable->getTableName());
    $count = $countQuery['N'];

    $pager->setCount($count);
    $pager->setURI($request->getRequestURI(), 'page');

    $nodata = pht('There are no registered packages yet.');

    $list = new PhabricatorObjectItemListView();
    // $list->setStackable();
    $list->setNoDataString($nodata);

    foreach ($packageEntries as $package) {
      $url = $package->getPackageUrl();
      $fileName = basename($url);

      $item = id(new PhabricatorObjectItemView())
        ->setHeader($fileName)
        ->setHref($this->getApplicationURI("view/" . $package->getID()))
        ->addAttribute(pht("Downloads: %d", $package->getDownloads()))
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

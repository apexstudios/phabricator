<?php

class PhabricatorPackagerRequestListController
  extends PhabricatorPackagerRequestController {

  public function processRequest() {
    $request = $this->getRequest();
    $user = $request->getUser();

    $pager = new AphrontCursorPagerView();
    $pager->readFromRequest($request);

    $packages = id(new PhabricatorPackageRequest())
      ->loadAllWhere('1 = 1 ORDER BY id DESC');

    $phids = mpull($packages, 'getAuthorPHID');
    $this->loadHandles($phids);

    $header = new PhabricatorHeaderView();
    $header->setHeader(pht('Package Requests'));
    $list = new PhabricatorObjectItemListView();
    $list->setCards(true);

    foreach ($packages as $package) {
      $item = new PhabricatorObjectItemView();
      $item->setHeader($this->buildRQName($package));
      $item->setHref('/PRQ'.$package->getID().'/');

      $item->addAttribute(
        $this->getHandle($package->getAuthorPHID())->renderLink());
      $item->addAttribute($package->getUrl());
      $item->addAttribute($package->getRevision());

      $status = $package->getStatus();
      switch ($status) {
        case PhabricatorPackageRequestConstants::STATUS_ISSUED:
          $color = 'green';
          break;
        case PhabricatorPackageRequestConstants::STATUS_OPEN:
          $color = 'orange';
          break;
        case PhabricatorPackageRequestConstants::STATUS_PACKAGED:
          $color = 'blue';
          break;
        default:
          throw new Exception("Unknown status $status");
          break;
      }
      $item->setBarColor($color);

      $list->addItem($item);
    }

    $crumbs = $this->buildApplicationCrumbs();
    $nav = $this->buildSideNavView($for_app = false);
    $nav->setCrumbs($crumbs);
    $nav->selectFilter('request');
    $nav->appendChild(array(
        $header,
        $list,
        $pager,
    ));

    return $this->buildStandardPageResponse(
      $nav,
      array(
        'device' => true,
        'title' => pht('Package Requests'),
        'dust' => true,
      ));
  }
}

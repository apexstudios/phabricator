<?php

class PhabricatorPackagerRequestViewController
  extends PhabricatorPackagerRequestController {

  private $id;

  public function willProcessRequest(array $uri_data) {
    $this->id = idx($uri_data, 'id');
  }

  public function processRequest() {
    $request = $this->getRequest();
    $user = $request->getUser();

    $package_request = id(new PhabricatorPackageRequest())->load($this->id);
    if (!$package_request) {
      return new Aphront404Response();
    }

    $subscribers = PhabricatorSubscribersQuery::loadSubscribersForPHID(
      $package_request->getPHID());

    $phids = array(
      $package_request->getAuthorPHID(),
    ) + $subscribers;
    $this->loadHandles($phids);

    $header = $this->buildHeader($package_request);
    $action = $this->buildActionList($package_request);
    $property = $this->buildPropertyList($package_request, $subscribers);

    $xaction = null;

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(id(new PhabricatorCrumbView())
      ->setName(pht('Package RQ %s', $package_request->getID()))
      ->setHref($request->getRequestURI()));

    return $this->buildStandardPageResponse(
      array(
        $crumbs,
        $header,
        $action,
        $property,
        $xaction,
      ),
      array(
        'device' => true,
        'title' => pht('Package RQ %s', $package_request->getID()),
        'dust' => true,
      ));
  }

  private function buildHeader(PhabricatorPackageRequest $request) {
    $header = id(new PhabricatorHeaderView())
      ->setHeader(pht('Package RQ %s', $request->getID()));

    $status = $request->getStatus();
    switch ($status) {
      case PhabricatorPackageRequestConstants::STATUS_ISSUED:
        $label = pht('Issued');
        $color = 'green';
        break;
      case PhabricatorPackageRequestConstants::STATUS_OPEN:
        $label = pht('Open');
        $color = 'orange';
        break;
      case PhabricatorPackageRequestConstants::STATUS_PACKAGED:
        $label = pht('Packaged');
        $color = 'blue';
        break;
      default:
        throw new Exception("Unknown status $status");
        break;
    }

    $header->addTag(id(new PhabricatorTagView())
      ->setType(PhabricatorTagView::TYPE_STATE)
      ->setBackgroundColor($color)
      ->setName($label));

    return $header;
  }

  protected function buildActionList(PhabricatorPackageRequest $request) {
    $view = new PhabricatorActionListView();
    $view->setObject($request);
    $view->setUser($this->getRequest()->getUser());

    return $view;
  }

  protected function buildPropertyList(PhabricatorPackageRequest $request,
    array $subscribers) {
    $view = new PhabricatorPropertyListView();

    $view->addProperty(pht('Requester'),
      $this->getHandle($request->getAuthorPHID())->renderLink());
    $view->addProperty(pht('Repo URL'), $request->getUrl());

    $subscriber_list = pht('None');
    if ($subscribers) {
      $subscriber_list = array();
      foreach ($subscribers as $subscriber) {
        $subscriber_list[] = $this->getHandle($subscriber)->renderLink();
      }
      $subscriber_list = phutil_implode_html(', ', $subscriber_list);
    }
    $view->addProperty(pht('Subscribers'), $subscriber_list);

    return $view;
  }

  protected function buildXActionView(PhabricatorPackageRequest $request) {
    // Stub
    return null;
  }

}

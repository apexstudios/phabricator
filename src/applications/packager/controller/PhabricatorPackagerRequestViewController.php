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
    );
    if ($subscribers) {
      $phids = array_merge($phids, $subscribers);
    }
    $this->loadHandles($phids);

    $header = $this->buildHeader($package_request);
    $action = $this->buildActionList($package_request);
    $property = $this->buildPropertyList($package_request, $subscribers);

    $xaction = $this->buildXActionView($package_request);
    $comment_form = $this->buildCommentForm($package_request);

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(id(new PhabricatorCrumbView())
      ->setName($this->buildRQName($package_request))
      ->setHref($request->getRequestURI()));

    return $this->buildStandardPageResponse(
      array(
        $crumbs,
        $header,
        $action,
        $property,
        $xaction,
        $comment_form,
      ),
      array(
        'device' => true,
        'title'  => $this->buildRQName($package_request),
        'dust'   => true,
      ));
  }

  private function buildHeader(PhabricatorPackageRequest $package_request) {
    $header = id(new PhabricatorHeaderView())
      ->setHeader($this->buildRQName($package_request));

    $status = $package_request->getStatus();
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

  protected function buildActionList(
    PhabricatorPackageRequest $package_request) {

    $view = new PhabricatorActionListView();
    $view->setObject($package_request);
    $view->setUser($this->getRequest()->getUser());

    switch ($package_request->getStatus()) {
      case PhabricatorPackageRequestConstants::STATUS_OPEN:
        $view->addAction(id(new PhabricatorActionView())
          ->setName(pht('Edit Request (this is your last chance!)'))
          ->setIcon('edit')
          ->setHref(
            $this->getApplicationURI(
              '/request/edit/'.$package_request->getID().'/')));

        // Fallthrough
      case PhabricatorPackageRequestConstants::STATUS_ISSUED:
        $is_open = $package_request->getStatus() ==
            PhabricatorPackageRequestConstants::STATUS_OPEN;
        $name = $is_open ?
          pht('Issue Pack Request') : pht('Re-issue Pack Request');
        $icon = $is_open ? 'start-sandcastle' : 'refresh';

        $view->addAction(id(new PhabricatorActionView())
          ->setName($name)
          ->setIcon($icon)
          ->setWorkflow(true)
          ->setHref(
            $this->getApplicationURI(
              '/request/issue/'.$package_request->getID().'/')));

        $view->addAction(id(new PhabricatorActionView())
          ->setName(pht('Register Package'))
          ->setIcon('link')
          ->setWorkflow(true)
          ->setHref(
            $this->getApplicationURI(
              '/request/register/'.$package_request->getID().'/')));
        break;
    }

    return $view;
  }

  protected function buildPropertyList(
    PhabricatorPackageRequest $package_request,
    array $subscribers) {
    $view = new PhabricatorPropertyListView();

    $view->addProperty(pht('Requester'),
      $this->getHandle($package_request->getAuthorPHID())->renderLink());
    $view->addProperty(pht('Requested on'),
      phabricator_datetime(
        $package_request->getDateCreated(), $this->getRequest()->getUser()));
    $view->addProperty(pht('Repo URL'),
      sprintf('%s @ %s',
        $package_request->getUrl(), $package_request->getRevision()));

    $subscriber_list = pht('None');
    if ($subscribers) {
      $subscriber_list = array();
      foreach ($subscribers as $subscriber) {
        $subscriber_list[] = $this->getHandle($subscriber)->renderLink();
      }
      $subscriber_list = phutil_implode_html(', ', $subscriber_list);
    }
    $view->addProperty(pht('Subscribers'), $subscriber_list);

    $pid = $package_request->getPackageID();
    if ($pid) {
      $package = id(new PhabricatorFilePackage())->load($pid);
      $view->addSectionHeader(pht('Package'));
      $view->addProperty(pht('Package'), phutil_tag(
        'a',
        array(
          'href' => $package->getDownloadURI(),
        ),
        pht('PCKG%s', $pid)));
    }

    if ($package_request->getDescription()) {
      $view->addSectionHeader(pht('Description'));
      $view->addTextContent($package_request->getDescription());
    }

    return $view;
  }

  protected function buildXActionView(
    PhabricatorPackageRequest $package_request) {
    $xactions = id(new PhabricatorPackageRequestTransactionQuery())
      ->setViewer($this->getRequest()->getUser())
      ->withObjectPHIDs(array($package_request->getPHID()))
      ->execute();

    $engine = id(new PhabricatorMarkupEngine())
      ->setViewer($this->getRequest()->getUser());
    foreach ($xactions as $xaction) {
      if ($xaction->getComment()) {
        $engine->addObject(
          $xaction->getComment(),
          PhabricatorApplicationTransactionComment::MARKUP_FIELD_COMMENT);
      }
    }
    $engine->process();

    $timeline = id(new PhabricatorApplicationTransactionView())
      ->setUser($this->getRequest()->getUser())
      ->setTransactions($xactions)
      ->setMarkupEngine($engine);
    return $timeline;
  }

  protected function buildCommentForm(
    PhabricatorPackageRequest $package_request) {
    $is_serious = PhabricatorEnv::getEnvConfig('phabricator.serious-business');

    $add_comment_header = id(new PhabricatorHeaderView())
      ->setHeader(
        $is_serious
          ? pht('Add Comment')
          : pht('Do something silly'));

    $submit_button_name = $is_serious
      ? pht('Add Comment')
      : pht('Waste your coins');

    $draft = PhabricatorDraft::newFromUserAndKey($this->getRequest()->getUser(),
      $package_request->getPHID());

    $add_comment_form = id(new PhabricatorApplicationTransactionCommentView())
      ->setUser($this->getRequest()->getUser())
      ->setDraft($draft)
      ->setAction(
        $this->getApplicationURI(
          '/request/comment/'.$package_request->getID().'/'))
      ->setSubmitButtonName($submit_button_name);

    return array(
      $add_comment_header,
      $add_comment_form,
    );
  }

}

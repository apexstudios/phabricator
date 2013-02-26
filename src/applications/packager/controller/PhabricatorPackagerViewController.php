<?php

final class PhabricatorPackagerViewController
  extends PhabricatorPackagerController {

  private $id;

  public function willProcessRequest(array $data) {
    $this->id = $data['id'];
  }

  public function processRequest() {
    $request = $this->getRequest();
    $user = $request->getUser();

    $packageObject = id(new PhabricatorFilePackage())->load($this->id);
    if (!$packageObject) {
      return new Aphront404Response();
    }

    $title = pht('Package "%s"', basename($packageObject->getPackageUrl()));

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setHref($this->getApplicationURI('/view/'.$packageObject->getID().'/'))
        ->setName($title));

    $actions = $this->buildActionView($packageObject);
    $properties = $this->buildPropertyView($packageObject);

    $xactions = id(new PhabricatorPackagerTransactionQuery())
      ->setViewer($request->getUser())
      ->withObjectPHIDs(array($packageObject->getPHID()))
      ->execute();

    $engine = id(new PhabricatorMarkupEngine())
      ->setViewer($user);
    foreach ($xactions as $xaction) {
      if ($xaction->getComment()) {
        $engine->addObject(
          $xaction->getComment(),
          PhabricatorApplicationTransactionComment::MARKUP_FIELD_COMMENT);
      }
    }
    $engine->process();

    $timeline = id(new PhabricatorApplicationTransactionView())
      ->setUser($user)
      ->setTransactions($xactions)
      ->setMarkupEngine($engine);

    $header = id(new PhabricatorHeaderView())
      ->setHeader($title);

    $is_serious = PhabricatorEnv::getEnvConfig('phabricator.serious-business');

    $add_comment_header = id(new PhabricatorHeaderView())
      ->setHeader(
        $is_serious
          ? pht('Add Comment')
          : pht('Reportagery'));

    $submit_button_name = $is_serious
      ? pht('Add Comment')
      : pht('Add something... please...');

    $draft = PhabricatorDraft::newFromUserAndKey($user, $packageObject->getPHID());

    $add_comment_form = id(new PhabricatorApplicationTransactionCommentView())
      ->setUser($user)
      ->setDraft($draft)
      ->setAction($this->getApplicationURI('/comment/'.$packageObject->getID().'/'))
      ->setSubmitButtonName($submit_button_name);

    return $this->buildApplicationPage(
      array(
        $crumbs,
        $header,
        $actions,
        $properties->render(),
        $timeline,
        $add_comment_header,
        $add_comment_form,
      ),
      array(
        'title' => $title,
      ));
  }

  private function buildActionView(PhabricatorFilePackage $macro) {
    $view = new PhabricatorActionListView();
    $view->setUser($this->getRequest()->getUser());
    $view->setObject($macro);
    $view->addAction(
      id(new PhabricatorActionView())
        ->setName(pht('Download'))
        ->setHref($this->getApplicationURI('/download/'.$macro->getID().'/'))
        ->setIcon('view'));
    $view->addAction(
      id(new PhabricatorActionView())
        ->setName(pht('Edit Package'))
        ->setHref($this->getApplicationURI('/edit/'.$macro->getID().'/'))
        ->setIcon('edit'));

    return $view;
  }

  private function buildPropertyView(PhabricatorFilePackage $packageObject) {

    $view = new PhabricatorPropertyListView();

    $view->addSectionHeader(pht("Statistics"));
    $view->addProperty(pht("Downloads"), $packageObject->getDownloads());
    $view->addProperty(pht("Location"), phutil_tag("em", array(), $packageObject->getPackageUrl()));

    $view->addSectionHeader(pht("Description"));
    $view->addTextContent(pht("Let's hope the download links on the right " .
      "actually work this time. Else it would be embarrasing."));

    return $view;
  }

}

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

    $title = pht('Package %s', basename($packageObject->getPackageUrl()));

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setHref($this->getApplicationURI('/view/'.$packageObject->getID().'/'))
        ->setName($title));

    $actions = $this->buildActionView($packageObject);
    $properties = $this->buildPropertyView($packageObject);

    $header = id(new PhabricatorHeaderView())
      ->setHeader($title);


    return $this->buildApplicationPage(
      array(
        $crumbs,
        $header,
        $actions,
        $properties->render(),
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

    $view->addSectionHeader(pht("Info"));
    $view->addProperty(pht("Location"), phutil_tag("em", array(), $packageObject->getPackageUrl()));
    $view->addTextContent(pht("Let's hope the download links on the right " .
      "actually work this time. Else it would be embarrasing."));

    return $view;
  }

}

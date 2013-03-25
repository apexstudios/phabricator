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

    $package_object = id(new PhabricatorFilePackage())->load($this->id);
    if (!$package_object) {
      return new Aphront404Response();
    }

    $this->loadHandles(array($package_object->getAuthorPHID()));

    $title = pht('Package %s', basename($package_object->getPackageUrl()));

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setHref(
          $this->getApplicationURI('/view/'.$package_object->getID().'/'))
        ->setName($title));

    $actions = $this->buildActionView($package_object);
    $properties = $this->buildPropertyView($package_object);

    $header = id(new PhabricatorHeaderView())
      ->setHeader($title);


    return $this->buildApplicationPage(
      array(
        $crumbs,
        $header,
        $actions,
        $properties,
      ),
      array(
        'title' => $title,
      ));
  }

  private function buildActionView(PhabricatorFilePackage $package) {
    $view = new PhabricatorActionListView();
    $view->setUser($this->getRequest()->getUser());
    $view->setObject($package);
    $view->addAction(
      id(new PhabricatorActionView())
        ->setName(pht('Download'))
        ->setHref($this->getApplicationURI('/download/'.$package->getID().'/'))
        ->setIcon('view'));
    $view->addAction(
      id(new PhabricatorActionView())
        ->setName(pht('Edit Package'))
        ->setHref($this->getApplicationURI('/edit/'.$package->getID().'/'))
        ->setIcon('edit'));

    return $view;
  }

  private function buildPropertyView(PhabricatorFilePackage $package_object) {

    $view = new PhabricatorPropertyListView();
    $package_request = $package_object->loadPackageRequest();
    $package_request_link = null;
    if ($package_request->getID()) {
      $package_request_uri = $this->getApplicationURI(
        '/request/view/'.$package_request->getID().'/');
      $package_request_link = phutil_tag(
        'a',
        array(
          'href' => $package_request_uri,
        ),
        $package_request->getPHID());
    }

    $view->addSectionHeader(pht("Info"));
    $view->addProperty(pht("Author"), $this->getHandle(
      $package_object->getAuthorPHID())->renderLink());
    $view->addProperty(pht("Location"), phutil_tag("em", array(),
      $package_object->getPackageUrl()));
    if ($package_request_link) {
      $view->addProperty(pht("Package Request"), $package_request_link);
    }
    $view->addTextContent(pht("Let's hope the download links on the right " .
      "actually work this time. Else it would be embarrasing."));

    return $view;
  }

}

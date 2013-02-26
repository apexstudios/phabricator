<?php


final class PhabricatorPackagerCreateController
  extends PhabricatorPackagerController {

  public function processRequest() {

    $request = $this->getRequest();
    $user = $request->getUser();

    $packageObject = new PhabricatorFilePackage();

    $packageObject->setAuthorPHID($user->getPHID());

    $e_url = null;
    $errors = array();

    if ($request->isFormPost()) {
      $url = $request->getStr('url');

      if (empty($url)) {
        $e_url = pht("Required");
        $errors[] = pht("Package URL must not be empty!");
      } else {
        $packageObject->setPackageUrl($url);
        $packageObject->setDownloads(0);
        $packageObject->save();

        return id(new AphrontRedirectResponse())
          ->setURI($this->getApplicationURI('view/' . $packageObject->getID()));
      }
    }

    $error_view = null;
    if ($errors) {
      $error_view = new AphrontErrorView();
      $error_view->setTitle(pht('Form Errors'));
      $error_view->setErrors($errors);
    }

    $instructions =
      phutil_tag(
        'p',
        array(
          'class' => 'aphront-form-instructions',
        ),
        pht('Just paste a clean URL to the file in Amazon S3 here, '.
          'and everything will be fine.'));

    $form = id(new AphrontFormView())
      ->setUser($user)
      ->appendChild($instructions)
      ->appendChild(
        id(new AphrontFormTextControl())
        ->setLabel(pht("Package Url"))
        ->setName('url')
        ->setError($e_url)
        ->setValue("")
        ->setCaption("The clean url to the file on S3"))
      ->appendChild(
        id(new AphrontFormSubmitControl())
          ->setValue(pht('Register this package'))
          ->addCancelButton($this->getApplicationURI()));

    $panel = new AphrontPanelView();
    $panel->setWidth(AphrontPanelView::WIDTH_FORM);
    $panel->setHeader(pht('Register Package'));
    $panel->setNoBackground();
    $panel->appendChild($form);

    $crumbs = $this->buildApplicationCrumbs($this->buildSideNavView());
    $crumbs->addCrumb(
      id(new PhabricatorCrumbView())
        ->setName(pht('Register Package'))
        ->setHref($this->getApplicationURI().'register/'));

    return $this->buildApplicationPage(
      array(
        $crumbs,
        $error_view,
        $panel,
      ),
      array(
        'title' => pht('Register Package'),
        'device' => true,
      ));
  }

}

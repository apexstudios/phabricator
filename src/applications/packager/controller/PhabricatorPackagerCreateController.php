<?php


final class PhabricatorPackagerCreateController
  extends PhabricatorPackagerController {

  public function processRequest() {

    $request = $this->getRequest();
    $user = $request->getUser();

    $e_url = null;
    $errors = array();

    if ($request->isFormPost()) {
      $url = $request->getStr('url');

      if (empty($url)) {
        $e_url = pht("Required");
        $errors[] = pht("Package URL must not be empty!");
      } else {
        $packageObject = new PhabricatorFilePackage();
        $packageObject->setAuthorPHID($user->getPHID());
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

    $form = id(new AphrontFormLayoutView())
      ->appendChild($instructions)
      ->appendChild(
        id(new AphrontFormTextControl())
        ->setLabel(pht("Package Url"))
        ->setName("url")
        ->setError($e_url)
        ->setValue("")
        ->setCaption("The clean url to the file on S3"));

    $dialog = new AphrontDialogView();
    $dialog->setUser($user);
    $dialog->setTitle(pht("Register Package"));
    $dialog->appendChild($form);
    $dialog->addCancelButton($this->getApplicationURI());
    $dialog->addSubmitButton(pht("Register this package"));

    $resp = new AphrontDialogResponse();
    return $resp->setDialog($dialog);
  }

}

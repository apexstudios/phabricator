<?php

class PhabricatorPackagerRequestRegisterController
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

    $package_id = $request->getInt('pid');

    $errors = array();
    $e_pid = null;

    // TODO: Present package selection to use
    // Let him select package, and register it with the package request
    if ($request->isFormPost()) {
      $package = id(new PhabricatorFilePackage())->load($package_id);
      if (!$package) {
        $errors[] = pht('Package not found');
        $e_pid = pht('Invalid');
      }

      if (!$errors && $package->getPackageRequestID()) {
        $errors[] = pht('Can not associate a package with a package request '.
          'twice!');
        $e_pid = pht('Already registered');
      }

      if (!$errors) {
        $xactions = array();
        $xactions[] = id(new PhabricatorPackageRequestTransaction())
          ->setTransactionType(
            PhabricatorPackageRequestTransactionType::TYPE_REGISTER)
          ->setNewValue($package_id);

        $editor = id(new PhabricatorPackageRequestEditor())
          ->setActor($user)
          ->setContentSource(
            PhabricatorContentSource::newForSource(
              PhabricatorContentSource::SOURCE_WEB,
              array(
                'ip' => $request->getRemoteAddr(),
              )));

        $package->setPackageRequestID($package_request->getID())->update();

        $view_uri = '/PRQ'.$package_request->getID().'/';

        $editor->applyTransactions($package_request, $xactions);

        return id(new AphrontRedirectResponse())
          ->setURI($view_uri);
      }
    }

    $error_view = null;
    if ($errors) {
      $error_view = new AphrontErrorView();
      $error_view->setTitle(pht('Form Errors!'));
      $error_view->setSeverity(AphrontErrorView::SEVERITY_ERROR);
      $error_view->setErrors($errors);
    }

    $form = new AphrontFormLayoutView();
    $form->appendChild(id(new AphrontFormTextControl())
      ->setName('pid')
      ->setLabel(pht('Package ID'))
      ->setValue($package_id)
      ->setError($e_pid)
      ->setCaption(pht('Go away if you don\'t know what to put here.')));

    $dialog = new AphrontDialogView();
    $dialog->setUser($user)
      ->setTitle(pht('Register a package'))
      ->appendChild($error_view)
      ->appendChild($form)
      ->addCancelButton($this->getApplicationURI('/PRQ'.$this->id.'/'))
      ->addSubmitButton(pht('Kyun!'));

    return id(new AphrontDialogResponse())->setDialog($dialog);
  }


}

<?php

class PhabricatorPackagerRequestIssueController
  extends PhabricatorPackagerRequestController {

  private $id;

  public function willProcessRequest(array $uri_data) {
    $this->id = idx($uri_data, 'id');
  }

  public function processRequest() {
    $request = $this->getRequest();
    $user = $request->getUser();

    if ($request->isFormPost()) {
      $package_request = id(new PhabricatorPackageRequest())->load($this->id);
      if (!$package_request) {
        return new Aphront404Response();
      }

      $xactions = array();
      $xactions[] = id(new PhabricatorPackageRequestTransaction())
        ->setTransactionType(
          PhabricatorPackageRequestTransactionType::TYPE_ISSUE);

      $editor = id(new PhabricatorPackageRequestEditor())
        ->setActor($user)
        ->setContentSource(
          PhabricatorContentSource::newForSource(
            PhabricatorContentSource::SOURCE_WEB,
            array(
              'ip' => $request->getRemoteAddr(),
            )));

      $view_uri = '/PRQ'.$package_request->getID().'/';

      $editor->applyTransactions($package_request, $xactions);

      return id(new AphrontRedirectResponse())
        ->setURI($view_uri);
    } else {
      $dialog = new AphrontDialogView();
      $dialog->setUser($user)
        ->setTitle(pht('Issue Pack Request?'))
        ->appendChild(pht('This will spin up a new server instance, which '.
          'will process the pack request and shut down. Note that servers '.
          'cost money, so please refrain from re-issueing pack requests, '.
          'as well as requesting duplicate pack requests.'))
        ->addCancelButton(pht('Save my wallet!'))
        ->addSubmitButton(pht('Once more unto the breah!'));
      return id(new AphrontDialogResponse())->setDialog($dialog);
    }
  }

}

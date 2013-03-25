<?php

class PhabricatorPackagerRequestNewController
  extends PhabricatorPackagerRequestController {

  public function processRequest() {
    $request = $this->getRequest();
    $user = $request->getUser();

    $errors = array();
    $e_url = null;
    $e_rev = null;
    $e_name = null;

    $file_name = $request->getStr('filename');
    $file_name = PhabricatorFile::normalizeFileName($file_name);
    $repo_url = $request->getStr('repo-url');
    $rev = $request->getStr('rev');
    $description = $request->getStr('description');

    if ($request->isFormPost()) {
      // Validate and save package request

      if (!(id(new PhutilURI($repo_url))->getProtocol())) {
        $errors[] = pht('Invalid URL given');
        $e_url = pht('Invalid');
      }

      if (!PhabricatorPackageRequest::validateRevision($rev)) {
        $errors[] = pht('Invalid Revision Number given');
        $e_rev = pht('Invalid');
      }

      if (!$file_name) {
        $errors[] = pht('Empty File Name given');
        $e_name = pht('Required');
      }

      if (!$errors) {
        // Save
        $pack_request = new PhabricatorPackageRequest();
        $pack_request->setFileName($file_name);
        $pack_request->setURL($repo_url);
        $pack_request->setType(strpos($rev, ':') ?
          PhabricatorPackageRequestConstants::SIZE_DIFF :
          PhabricatorPackageRequestConstants::SIZE_FULL);
        $pack_request->setStatus(
          PhabricatorPackageRequestConstants::STATUS_OPEN);
        $pack_request->setRevision($rev);
        $pack_request->setDescription($description);
        $pack_request->setAuthorPHID($user->getPHID());
        $pack_request->save();

        $uri = sprintf('PRQ%d/', $pack_request->getID());
        return id(new AphrontRedirectResponse())->setURI($uri);
      }
    }

    $error_view = null;
    if ($errors) {
      $error_view = new AphrontErrorView();
      $error_view->setSeverity(AphrontErrorView::SEVERITY_ERROR)
        ->setTitle(pht('Form Errors'))
        ->setErrors($errors);
    }

    $repo_instructions = hsprintf('%s<ul><li><tt>https://subversion.assembla'.
      '.com/svn/ap_hcaw/trunk/</tt></li><li><tt>https://subversion.assembla'.
      '.com/svn/ap_hcaw/branches/release/</tt></li></ul>',
      pht('Specify the url to the repository, '.
      'including trunk or branch. Examples:'));

    $rev_instructions = hsprintf('Specify a revision or a range of revision '.
      'here. When selecting a single revision (<tt>x</tt>), a full package '.
      'will be created. When selecting a range of revisions (<tt>x:y</tt>), '.
      'only the files included between the two revisions will be included.');

    $form = new AphrontFormLayoutView();
    $form->appendChild($repo_instructions)
      ->appendChild(id(new AphrontFormTextControl())
        ->setName('repo-url')
        ->setLabel(pht('Repo URL'))
        ->setCaption('Currently supports only SVN. Should pose no trouble')
        ->setValue($repo_url)
        ->setError($e_url))
      ->appendChild($rev_instructions)
      ->appendChild(id(new AphrontFormTextControl())
        ->setName('rev')
        ->setLabel(pht('Revision'))
        ->setCaption(pht('Something like "726" or "701:730"'))
        ->setValue($rev)
        ->setError($e_rev))
      ->appendChild(id(new AphrontFormTextControl())
        ->setName('filename')
        ->setLabel(pht('File name'))
        ->setCaption(pht('The file name the final file should have. Note that '.
          'it should end in ".zip" and may be stripped of unwanted letters'))
        ->setValue($file_name)
        ->setError($e_name))
      ->appendChild(id(new AphrontFormTextAreaControl())
        ->setName('description')
        ->setLabel(pht('Description'))
        ->setValue($description)
        ->setCaption(pht('Describe the purpose of this package')));

    $dialog = new AphrontDialogView();
    $dialog->setWidth(AphrontDialogView::WIDTH_FORM);
    $dialog->setUser($user)
      ->setTitle(pht('Request a new package build'))
      ->appendChild($error_view)
      ->appendChild($form)
      ->addSubmitButton(pht('Send Request'))
      ->addCancelButton($this->getApplicationURI());

    return id(new AphrontDialogResponse())->setDialog($dialog);
  }

}

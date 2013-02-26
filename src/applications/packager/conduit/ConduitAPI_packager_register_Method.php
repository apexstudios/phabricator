<?php

/**
 * @group conduit
 */
final class ConduitAPI_packager_register_Method extends
  ConduitAPI_packager_Method {

  public function getMethodDescription() {
    return "Registers a new package.";
  }

  public function defineParamTypes() {
    return array(
      'url' => 'required nonempty string',
    );
  }

  public function defineReturnType() {
    return 'nonempty phid';
  }

  public function defineErrorTypes() {
    return array(
    );
  }

  protected function execute(ConduitAPIRequest $request) {
    $user = $request->getUser();
    $url = $request->getValue('url');

    $package = new PhabricatorFilePackage();
    $package->setAuthorPHID($user->getPHID());
    $package->setPackageUrl($url);
    $package->setDownloads(0);

    $package->save();

    return $package->getPHID();
  }

}

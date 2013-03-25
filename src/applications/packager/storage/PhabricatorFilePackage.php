<?php

final class PhabricatorFilePackage extends PhabricatorLiskDAO
  implements PhabricatorTokenReceiverInterface {

  protected $authorPHID;
  protected $phid;
  protected $packageUrl;

  protected $downloads;

  protected $packageRequestID = null;
  private $packageRequest;

  protected $dateCreated;
  protected $dateModified;

  public function attachPackageRequest(PhabricatorPackageRequest $request) {
    $this->packageRequest = $request;
    return $this;
  }

  public function loadPackageRequest() {
    if ($this->packageRequest) {
      return $this->packageRequest;
    }

    $package_request_dao = new PhabricatorPackageRequest();
    $this->packageRequest = $package_request_dao->load($this->packageRequestID);
    if (!$this->packageRequest) {
      $this->packageRequest = $package_request_dao;
      $package_request_dao->setPackageID($this->getID());
    }

    return $this->packageRequest;
  }

  public function getDownloadURI()
  {
    return '/packager/download/'.$this->getID().'/';
  }

  public function getApplicationName() {
    return 'packager';
  }

  public function getConfiguration() {
    return array(
      self::CONFIG_AUX_PHID  => true,
    ) + parent::getConfiguration();
  }

  public function generatePHID() {
    return PhabricatorPHID::generateNewPHID(
      PhabricatorPHIDConstants::PHID_TYPE_PCKG);
  }

  public function getUsersToNotifyOfTokenGiven() {
    return array(
      $this->getAuthorPHID(),
    );
  }

}


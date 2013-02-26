<?php

final class PhabricatorFilePackage extends PhabricatorLiskDAO
  implements PhabricatorTokenReceiverInterface {

  protected $authorPHID;
  protected $phid;
  protected $packageUrl;

  protected $downloads;

  protected $dateCreated;
  protected $dateModified;

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


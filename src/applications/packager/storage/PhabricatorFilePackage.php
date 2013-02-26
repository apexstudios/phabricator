<?php

final class PhabricatorFilePackage extends PhabricatorLiskDAO
  implements
    PhabricatorApplicationTransactionInterface,
    PhabricatorSubscribableInterface,
    PhabricatorTokenReceiverInterface {

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

  public function getApplicationTransactionEditor() {
    return new PhabricatorPackagerEditor();
  }

  public function getApplicationTransactionObject() {
    return new PhabricatorPackagerTransaction();
  }

  public function isAutomaticallySubscribed($phid) {
    return false;
  }

  public function getUsersToNotifyOfTokenGiven() {
    return array(
      $this->getAuthorPHID(),
    );
  }

}


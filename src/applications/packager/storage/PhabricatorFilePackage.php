<?php

final class PhabricatorFilePackage extends PhabricatorLiskDAO
  implements
    PhabricatorSubscribableInterface,
    PhabricatorApplicationTransactionInterface,
    PhabricatorTokenReceiverInterface {

  protected $authorPHID;
  protected $packagePHID;
  protected $packageUrl;

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

  public function isAutomaticallySubscribed($phid) {
    return false;
  }

  public function getApplicationTransactionEditor() {
    return new PhabricatorPackagerEditor();
  }

  public function getApplicationTransactionObject() {
    return new PhabricatorPackagerTransaction();
  }

  public function getUsersToNotifyOfTokenGiven() {
    return array(
      $this->getAuthorPHID(),
    );
  }

}


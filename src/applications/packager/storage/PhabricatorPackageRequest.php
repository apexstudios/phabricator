<?php

class PhabricatorPackageRequest extends PhabricatorLiskDAO
  implements
    PhabricatorSubscribableInterface,
    PhabricatorPolicyInterface,
    PhabricatorApplicationTransactionInterface {

  protected $phid;
  protected $authorPHID;

  protected $type;
  protected $status;
  protected $url;
  protected $revision;
  protected $fileName;
  protected $description;

  protected $packageID = 0;

  protected $dateCreated;
  protected $dateModified;

  public static function validateRevision($rev) {
    if (!is_scalar($rev)) {
      return false;
    }

    if (!strlen($rev)) {
      return true;
    }

    if (is_int($rev) || ctype_digit($rev)) {
      return true;
    } else {
      if (!strpos($rev, ':')) {
        return false;
      }

      $rev_parts = explode(':', $rev);
      if (count($rev_parts) != 2) {
        return false;
      }

      if (ctype_digit($rev_parts[0]) && ctype_digit($rev_parts[1]) &&
        strlen($rev_parts[0]) && strlen($rev_parts[1])) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function getApplicationName() {
    return 'packager';
  }

  public function getTableName() {
    return 'package_request';
  }

  public function getConfiguration() {
    return array(
      self::CONFIG_AUX_PHID  => true,
    ) + parent::getConfiguration();
  }

  public function generatePHID() {
    return PhabricatorPHID::generateNewPHID(
      PhabricatorPHIDConstants::PHID_TYPE_PKRQ);
  }

  public function getCapabilities() {
    return array(
      PhabricatorPolicyCapability::CAN_VIEW,
      PhabricatorPolicyCapability::CAN_EDIT,
    );
  }

  public function getPolicy($capability) {
    return PhabricatorPolicies::POLICY_USER;
  }

  public function hasAutomaticCapability(
    $capability, \PhabricatorUser $viewer) {

    return false;
  }

  public function isAutomaticallySubscribed($phid) {
    return $this->getAuthorPHID() == $phid;
  }

  public function getApplicationTransactionEditor() {
    return new PhabricatorPackageRequestEditor();
  }

  public function getApplicationTransactionObject() {
    return new PhabricatorPackageRequestTransaction();
  }

}

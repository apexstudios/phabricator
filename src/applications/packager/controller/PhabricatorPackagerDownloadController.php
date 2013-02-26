<?php

use Aws\Common\Aws;

final class PhabricatorPackagerDownloadController
  extends PhabricatorPackagerController {

  private $id;

  public function willProcessRequest(array $data) {
    $this->id = $data['id'];
  }

  public function processRequest() {

    $request = $this->getRequest();

    $packageObject = new PhabricatorFilePackage();
    $packageObject->load($this->id);

    if (!$packageObject) {
      return new Aphront404Response();
    }

    $fullUrl = $packageObject->getPackageUrl();
    $cappedUrl = substr($fullUrl, strlen("https://"));

    $urlParts = explode("/", $cappedUrl);
    $bucket = $urlParts[1];
    $fileName = implode("/", array_slice($urlParts, 2));

    $awsKey = PhabricatorEnv::getEnvConfig('amazon-s3.access-key');
    $awsSecret = PhabricatorEnv::getEnvConfig('amazon-s3.secret-key');

    $configArray = array(
        'key'    => $awsKey,
        'secret' => $awsSecret,
        'region' => 'eu-west-1',
    );
    $s3 = Aws::factory($configArray)->get('s3');
    $s3Request = $s3->get("{$bucket}/{$fileName}");
    $downloadLink = $s3->createPresignedUrl($s3Request, '+10 minutes');

    return id(new AphrontRedirectResponse())
      ->setURI($downloadLink);
  }

}

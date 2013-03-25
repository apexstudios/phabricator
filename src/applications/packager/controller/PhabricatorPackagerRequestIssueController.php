<?php

use Aws\Common\Aws;

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

      try {
        $awsKey = PhabricatorEnv::getEnvConfig('amazon-s3.access-key');
        $awsSecret = PhabricatorEnv::getEnvConfig('amazon-s3.secret-key');

        $configArray = array(
            'key'    => $awsKey,
            'secret' => $awsSecret,
            'region' => 'us-east-1',
        );

        $msgBody = array(
          'url' => $package_request->getUrl(),
          'revision' => $package_request->getRevision(),
          'filename' => $package_request->getFileName(),
        );

        $sqs = Aws::factory($configArray)->get('sqs');
        $sqs->sendMessage(array(
          'QueueUrl' => 'https://sqs.us-east-1.amazonaws.com/830649155612/PackagingQueue',
          'MessageBody' => json_encode($msgBody),
        ));

        $ec2 = Aws::factory($configArray)->get('ec2');
        $ec2->requestSpotInstances(array(
          'SpotPrice' => '0.5',
          'InstanceCount' => 1,
          'Type' => 'one-time',
          'LaunchSpecification' => array(
            'ImageId' => 'ami-9cfd66f5',
            'KeyName' => 'use-key1',
            'InstanceType' => 'm1.medium',
            'SecurityGroupIds' => array('sg-78bc9510'),
          ),
        ));

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
      } catch (Exception $exc) {
        throw $exc;

        $dialog = new AphrontDialogView();
        $dialog->setUser($user)
          ->setTitle(pht('Error?'))
          ->appendChild(pht('Something went wrong. I\'m sorry.'))
          ->addCancelButton($this->getApplicationURI('/request/'));
        return id(new AphrontDialogResponse())->setDialog($dialog);
      }
    } else {
      $dialog = new AphrontDialogView();
      $dialog->setUser($user)
        ->setTitle(pht('Issue Pack Request?'))
        ->appendChild(pht('This will spin up a new server instance, which '.
          'will process the pack request and shut down. Note that servers '.
          'cost money, so please refrain from re-issueing pack requests, '.
          'as well as requesting duplicate pack requests.'))
        ->addCancelButton($this->getApplicationURI('/request/'),
          pht('Save my wallet!'))
        ->addSubmitButton(pht('Once more unto the breach!'));
      return id(new AphrontDialogResponse())->setDialog($dialog);
    }
  }

}

<?php

final class PhabricatorApplicationPackager extends PhabricatorApplication {

  public function getBaseURI() {
    return '/packager/';
  }

  public function getShortDescription() {
    return pht('Managing AWS S3 downloads');
  }

  public function getIconName() {
    return 'packager';
  }

  public function getTitleGlyph() {
    return "\xE1\x8C\x98";
  }

  public function getApplicationGroup() {
    return self::GROUP_CORE;
  }

  public function getRoutes() {
    return array(
      '/packager/' => array(
        '' => 'PhabricatorPackagerListController',
        'register/' => 'PhabricatorPackagerCreateController',
        'view/(?P<id>[1-9]\d*)/' => 'PhabricatorPackagerViewController',
        'download/(?P<id>[1-9]\d*)/' => 'PhabricatorPackagerDownloadController',
        'edit/(?P<id>[1-9]\d*)/' => 'PhabricatorPackagerEditController',
        'request/' => array(
          '' => 'PhabricatorPackagerRequestListController',
          'new/' => 'PhabricatorPackagerRequestNewController',
          'comment/(?P<id>[1-9]\d*)/' =>
            'PhabricatorPackagerRequestCommentController',
          'edit/(?P<id>[1-9]\d*)/' =>
            'PhabricatorPackagerRequestEditController',
          'issue/(?P<id>[1-9]\d*)/' =>
            'PhabricatorPackagerRequestIssueController',
          'register/(?P<id>[1-9]\d*)/' =>
            'PhabricatorPackagerRequestRegisterController',
        ),
      ),
      '/PRQ(?P<id>[1-9]\d*)/' => 'PhabricatorPackagerRequestViewController',
    );
  }

}

<?php

final class PhabricatorApplicationPackager extends PhabricatorApplication {

  public function getBaseURI() {
    return '/packager/';
  }

  public function getShortDescription() {
    return pht('Managing build package packaging management');
  }

  public function getIconName() {
    return 'packager';
  }

  public function getTitleGlyph() {
    return "\xE2\x9A\x98";
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function getRoutes() {
    return array(
      '/packager/' => array(
        '' => 'PhabricatorPackagerListController',
        'create/' => 'PhabricatorPackagerEditController',
        'view/(?P<id>[1-9]\d*)/' => 'PhabricatorPackagerViewController',
      ),
    );
  }

}

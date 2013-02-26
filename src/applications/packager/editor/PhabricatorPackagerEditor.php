<?php

final class PhabricatorPackagerEditor
  extends PhabricatorApplicationTransactionEditor {

  protected function supportsMail() {
    return false;
  }

  protected function supportsFeed() {
    return true;
  }
}

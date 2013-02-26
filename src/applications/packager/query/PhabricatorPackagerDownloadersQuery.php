<?php

class PhabricatorPackagerDownloadersQuery extends PhabricatorQuery {

  // Just for copying
  private $objectPHIDs;
  private $subscriberPHIDs;

  // Just for rename
  public static function loadDownloadersForPHID($phid) {
    if (!$phid) {
      return array();
    }

    $subscribers = id(new PhabricatorPackagerDownloadersQuery())
      ->withObjectPHIDs(array($phid))
      ->execute();
    return $subscribers[$phid];
  }

  // Just for copying
  public function withObjectPHIDs(array $object_phids) {
    $this->objectPHIDs = $object_phids;
    return $this;
  }

  public function execute() {
    $query = new PhabricatorEdgeQuery();

    // Only changed line of this method
    $edge_type = PhabricatorEdgeConfig::TYPE_OBJECT_HAS_DOWNLOADER;

    $query->withSourcePHIDs($this->objectPHIDs);
    $query->withEdgeTypes(array($edge_type));

    if ($this->subscriberPHIDs) {
      $query->withDestinationPHIDs($this->subscriberPHIDs);
    }

    $edges = $query->execute();

    $results = array_fill_keys($this->objectPHIDs, array());
    foreach ($edges as $src => $edge_types) {
      foreach ($edge_types[$edge_type] as $dst => $data) {
        $results[$src][] = $dst;
      }
    }

    return $results;
  }

}

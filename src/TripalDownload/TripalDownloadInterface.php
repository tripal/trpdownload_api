<?php

namespace Drupal\trpdownload_api\TripalDownload;

/**
 * Interface for tripal_download plugins.
 */
interface TripalDownloadInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

}

<?php

namespace Drupal\trpdownload_api\TripalDownload;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for tripal_download plugins.
 */
abstract class TripalDownloadPluginBase extends PluginBase implements TripalDownloadInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

}

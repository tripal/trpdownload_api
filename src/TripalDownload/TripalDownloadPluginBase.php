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
  public function getFilename(array $options = []) {

    $site_name = \Drupal::config('system.site')->get('name');
    $safe_site_name = preg_replace('/\W+/','',ucwords(strip_tags($site_name)));

    $filename = $safe_site_name . '.' . $this->id() . '.' . date('YMj-his') . '.' . $this->file_suffix();

    return $filename;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat() {
    return $this->format_label();
  }

    /**
   * {@inheritdoc}
   */
  public function id() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function format_label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['format_label'];
  }

  /**
   * {@inheritdoc}
   */
  public function file_suffix() {
    return $this->pluginDefinition['file_suffix'];
  }

  /**
   * {@inheritdoc}
   */
  public function description() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['description'];
  }
}

<?php

namespace Drupal\trpdownload_api\Plugin\TripalDownload;

use Drupal\trpdownload_api\TripalDownloadPluginBase;

/**
 * EXAMPLE Plugin implementation of the tripal_download plugin.
 *
 * @TripalDownload(
 *   id = "example",
 *   label = @Translation("Example Download"),
 *   format_label = @Translation("Tab-Separated Values (TSV)"),
 *   file_suffix = "tsv",
 *   description = @Translation("This plugin implementation provides an example of how the Tripal Download plugin can be used.")
 * )
 */
class Example extends TripalDownloadPluginBase {

}

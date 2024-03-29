<?php

namespace Drupal\trpdownload_example\Plugin\TripalDownload;

use Drupal\trpdownload_api\TripalDownload\TripalDownloadPluginBase;
use Drupal\trpdownload_api\TripalDownload\TripalDownloadInterface;

/**
 * EXAMPLE Plugin implementation of the tripal_download plugin.
 * Specifically, this example will download organisms from the default
 * chado schema into a tsv file.
 *
 * @TripalDownload(
 *   id = "example_organism_tsv",
 *   label = @Translation("Example Organism TSV Download"),
 *   format_label = @Translation("Tab-Separated Values (TSV)"),
 *   file_suffix = "tsv",
 *   description = @Translation("This plugin implementation provides an example of how the Tripal Download plugin can be used to download data from chado. It focuses on implementing as few methods as possible.")
 * )
 */
class ExampleOrganismTsv extends TripalDownloadPluginBase implements TripalDownloadInterface {

  /**
   * {@inheritdoc}
   */
  public function generateFile(array $variables, int $job_id = NULL) {}

  /**
   * {@inheritdoc}
   */
  public function summarizeDownload() {}
}

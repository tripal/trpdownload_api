<?php

namespace Drupal\Tests\trpdownload_api\Functional;

use Drupal\Core\Url;
use Drupal\Tests\tripal_chado\Functional\ChadoTestBrowserBase;
use Drupal\trpdownload_api\TripalDownload\TripalDownloadInterface;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group Tripal Download API
 */
class BasicAnnotationTest extends ChadoTestBrowserBase {

  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['trpdownload_api', 'trpdownload_example'];

  /**
   * Tests that we can retrieve the annotation details for our
   * example plugin implementations.
   */
  public function testDownloadAPIGetters() {

    // @todo switch this to be supplied by a data provider.
    $plugin_id = 'example_organism_tsv';
    $expected_annotation = [
      'label' => "Example Organism TSV Download",
      'format_label' => "Tab-Separated Values (TSV)",
      'file_suffix' => "tsv",
      'description' => "This plugin implementation provides an example of how the Tripal Download plugin can be used to download data from chado. It focuses on implementing as few methods as possible.",
    ];

    // Instanciate the plugin object.
    $plugin = \Drupal::service('plugin.manager.tripal_download')->createInstance($plugin_id, []);
    $this->assertIsObject($plugin, "Unable to create an instance of $plugin_id");
    $this->assertInstanceOf(TripalDownloadInterface::class, $plugin,
      "Returned object for $plugin_id is not an instance of TripalDownloadPluginBase.");

    // Check each annotation using the getter method by the same name.
    foreach($expected_annotation as $key => $expected) {
      $retrieved = $plugin->$key();
      $this->assertIsString($retrieved,
        "Retrieved $key was not a string.");
      $this->assertEquals($expected, $retrieved,
        "Unable to retrieve the expected $key set in the plugin annotation.");
    }

  }
}

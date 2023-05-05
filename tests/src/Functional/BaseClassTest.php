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
class BaseClassTest extends ChadoTestBrowserBase {

  protected $defaultTheme = 'stable';

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
  public function testBaseClass() {

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

    // Check that dependency injection worked properly.
    // Since the database connection and logger are protected properties, we cannot test them directly.
    // As such, we will use PHP closures to access these properties for testing.
    //  -- Create a variable to store a copy of this test object for use within the closure.
    $that = $this;
    //  -- Create a closure (i.e. a function tied to a variable) that does not need any parameters.
    //     Within this function we will want all of the assertions we will use to test the private methods.
    //     Also, $this within the function will actually be the plugin object that you bind later (mind blown).
    $assertDependencyInjectionClosure = function ()  use ($that){
      $that->assertIsObject($this->connection,
        "The connection object in our plugin was not set properly.");
      $that->assertIsObject($this->logger,
        "The connection object in our plugin was not set properly.");
    };
    //  -- Now, bind our assertion closure to the $plugin object. This is what makes the plugin available
    //     inside the function.
    $doAssertDependencyInjectionClosure = $assertDependencyInjectionClosure->bindTo($plugin, get_class($plugin));
    //  -- Finally, call our bound closure function to run the assertions on our plugin.
    $doAssertDependencyInjectionClosure();

    // Check getFormat()
    $retrieved = $plugin->getFormat();
    $expected = $expected_annotation['format_label'];
    $this->assertIsString($retrieved,
      "Retrieved format was not a string.");
    $this->assertEquals($expected, $retrieved,
      "Unable to retrieve the expected value for getFormat().");

    // Check getFilename()
    $retrieved = $plugin->getFilename();
    $expected = 'Fred';
    $this->assertIsString($retrieved,
      "Retrieved filename was not a string.");
    $this->assertStringContainsString($plugin_id, $retrieved,
      "The filename is expected to have the plugin_id in it.");
    $this->assertStringContainsString(date('YMj-his'), $retrieved,
      "The filename is expected to have the current date in it.");
    $this->assertTrue(str_ends_with($retrieved, $expected_annotation['file_suffix']),
      "The filename is expected to end with the file suffix.");
  }
}

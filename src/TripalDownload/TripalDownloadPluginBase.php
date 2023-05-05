<?php

namespace Drupal\trpdownload_api\TripalDownload;

use Drupal\Component\Plugin\PluginBase;
use Drupal\tripal\Services\TripalLogger;
use Drupal\tripal_chado\Database\ChadoConnection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for tripal_download plugins.
 */
abstract class TripalDownloadPluginBase extends PluginBase implements TripalDownloadInterface, ContainerFactoryPluginInterface {

  /**
   * The logger for reporting progress, warnings and errors to admin.
   *
   * @var Drupal\tripal\Services\TripalLogger
   */
  protected $logger;

  /**
   * The database connection for querying Chado.
   *
   * @var Drupal\tripal_chado\Database\ChadoConnection
   */
  protected $connection;

  /**
   * Implements ContainerFactoryPluginInterface->create().
   *
   * Since we have implemented the ContainerFactoryPluginInterface this static function
   * will be called behind the scenes when a Plugin Manager uses createInstance(). Specifically
   * this method is used to determine the parameters to pass to the contructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('tripal.logger'),
      $container->get('tripal_chado.database')
    );
  }

  /**
   * Implements __contruct().
   *
   * Since we have implemented the ContainerFactoryPluginInterface, the constructor
   * will be passed additional parameters added by the create() function. This allows
   * our plugin to use dependency injection without our plugin manager service needing
   * to worry about it.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param Drupal\tripal\Services\TripalLogger $logger
   * @param Drupal\tripal_chado\Database\ChadoConnection $connection
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TripalLogger $logger, ChadoConnection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->logger = $logger;
    $this->connection = $connection;
  }

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

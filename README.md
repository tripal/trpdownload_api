# Tripal Download API
This module provides an API for downloading Tripal/Chado data. Since download functionality is often sought after for Tripal sites and Views Data Export is not currently meeting our needs, this module aims to provide an API to aid module and site developers in making efficient, user friendly downloads available.

## How does it work?
1.Module developers specify a download type using hook_register_trpdownload_type().
````php
/**
 * Implements hook_register_trpdownload_type().
 */
function trpdownload_example_register_trpdownload_type() {
  $types = array();

  // The key is the machine name of my download type.
  $types['feature_csv'] = array(
    // A human readable name to show in an administrative interface one day.
    'type_name' => 'Feature CSV',
    // A human readable description of the format.
    'format' => 'Comma-separated Values',
    // An array of functions that the API will use to customize your experience.
    'functions' => array(
      // The function that tripal jobs will call to generate the file.
      'generate_file' => 'trpdownload_feature_csv_generate_file',
      // OPTIONAL: provide a summary to the user on the download page.
      'summarize' => 'trpdownload_feature_csv_summarize_download',
      // OPTIONAL: determine your own filename.
      'get_filename' => 'trpdownload_feature_csv_get_filename',
      // OPTIONAL: Change the file suffix (defaults to .txt)
      'get_file_suffix' => 'trpdownload_feature_csv_get_suffix',
      // OPTIONAL: determine the human-readable format based on a function.
      'get_format' => 'trpdownload_feature_csv_get_readable_format',
    ),
  );

  return $types;
}
````
2.Next they create a download page by implementing hook_menu(). This can be as simple as specifying to use the generic download page provided by the API.
  * The download page can be linked to from an existing listing or you can create a custom download page with a form to retrieve information on what to download.
  * When using the generic download page, it is expected all information needed to generate the file is in the URL as query parameters.
````php
/**
 * Implements hook_menu().
 */
function trpdownload_example_menu() {
  $items = array();

  $items['chado/feature/csv'] = array(
    'title' => 'Download Features: CSV',
    'page callback' => 'trpdownload_download_page',
    'page arguments' => array('feature_csv', 3),
    'access arguments' => array('access content'),
    'type' => MENU_CALLBACK,
  );

  return $items;
}
````
3.When a user is redirected to the download page, the generic download page will create a tripal job which calls the "generate_file" function specified in hook_register_trpdownload_type() and passes it the query parameters as well as helpful information such as the filename of the file to generate and where to put it. ;-)
````php
/**
 * Generates a file listing feature in CSV.
 *
 * @param $variables
 *   An associative array of parameters including:
 *     - q: all the query paramters.
 *     - site_safe_name: a sanitized version of your site name for use in variables & filenames.
 *     - type_info: an array of info for the download type.
 *     - suffix: the file format suffix.
 *     - filename: the filename of the file to generate not including path.
 *     - fullpath: the full path and filename of the file to generate.
 *     - format_name: a human-readable description of the format.
 * @param $job_id
 *   The ID of the tripal job executing this function ;-).
 */
function trpdownload_feature_csv_generate_file($variables, $job_id = NULL) {
   *All your code for generating the file */
}
````
4.The URL of the page changes to hide the query parameters and instead show an obsfuscated job_id allowing users to bookmark this page if needed. There is an ajax progress bar that shows the progress of the Tripal job and once complete, provides a link to the user to download the file.

Thus module developers can provide a simple interface showing progress and providing access to the file by (1) specify a download type and path, (2) Implementing a generate_file function, and (3) Linking to the download page and passing information in the form of query parameters.

## Future Work
Development on this module has just begun and as such it still doesn't meet all the needs of the Tripal community. The following list of features are needs that we know exist and intend to address. If you have any additional needs in reference to downloads please open an issue and tell us about it!
* Views Support
* Administrative Interface?

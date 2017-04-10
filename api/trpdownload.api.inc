<?php
/**
 * @file
 * Provide API functions of use to many tripal downloads.
 */

/**
 * Estimate the number of rows being exported for use in updating the progress bar.
 *
 * USE ONLY FOR REPORTING DOWNLOAD PROGRESS. THIS ESTIMATE MAY BE SUBSTANTIALLY LOWER
 * OR HIGHER THEN THE ACTUAL NUMBER OF ROWS.
 *
 * This function uses PostgreSQL EXPLAIN to estimate the number of rows. It returns an
 * ESTIMATE whose accuracy depends in part on how many changes have been made since the
 * last time ANALYZE was run.
 *
 * Our implementation is based on a plpgsql function by Michael Fuhr
 * @see https://wiki.postgresql.org/wiki/Count_estimate
 *
 * @param $query
 *    The chado query to estimate the number of rows returned by.
 * @param $args
 *    The arguements for the query.
 * @return
 *    An integer representing the estimated number of rows.
 */
function trpdownload_estimate_rows($query, $args = array()) {

  // Currently only supports local chado installs.
  $is_local = isset($GLOBALS["chado_is_local"]) && $GLOBALS["chado_is_local"];
  if (!$is_local) {
    // @todo report warning.
    return FALSE;
  }

  // First, since $query is a chado query, we need to re-write the tables such that
  // they will actually be found by explain.
  // Chado tables should be enclosed in curly brackets (ie: {feature} )
  // and Drupal tables should be enclosed in square brackets (ie: [tripal_jobs] ).
  $chado_schema_name = tripal_get_schema_name('chado');
  $drupal_schema_name = tripal_get_schema_name('drupal');
  $query = preg_replace('/\{(.*?)\}/', $chado_schema_name.'.$1', $query);
  $query = preg_replace('/\[(\w+)\]/', $drupal_schema_name.'.$1', $query);

  // Ensure the same hook-based alteration that are going to occur once chado_query is
  // run are done to this query as well.
  drupal_alter('chado_query', $query, $args);


  // Now we want to run EXPLAIN on the query in order to get the query planner's row
  // estimate. For example, EXPLAIN SELECT * FROM chado.feature.
  $sql = 'EXPLAIN '.$query;

  $result = db_query($sql, $args);
  foreach ($result as $r) {

    // Find the first line with rows=### and return that estimated number of rows.
    if (preg_match('/rows=(\d+)/',$r->{'QUERY PLAN'}, $matches)) {
      $rows = $matches[1];
      return $rows;
    }
  }
}

/**
 * Retrieve the path to save downloadable files at.
 *
 * @param $type
 *   Whether you want the full path or relative path ('full' and 'relative' respectively.)
 * @return
 *   The directory path to save downloadable files in.
 */
function trpdownload_api_get_filedir($type = 'full') {

  // Add for backwards compatibility :-(.
  variable_set('trpdownload_fullpath', drupal_realpath('public://') . '/tripal/tripal_downloads/');
  variable_set('trpdownload_relpath', 'public://tripal/tripal_downloads/');

  if ($type == 'relative') {
    return 'public://tripal/tripal_downloads/';
  }
  // Assume they want the full path.
  else {
    return drupal_realpath('public://') . '/tripal/tripal_downloads/';
  }
}

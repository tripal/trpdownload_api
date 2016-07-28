<?php
/**
 * @file
 * Contains all functions pertaining to Tripal Job Integration.
 */

/**
 * Return the progress of the specified job
 * formatted for updating the drupal progress bar.
 *
 * @param $type
 *   The machine name of the tripal download type.
 * @param $job_id
 *   The tripal job_id of the job you would like to check progrtess for.
 * @return
 *   JSON describing the progress of the specified Tripal job.
 */
function tripal_job_progress_json($type, $job_id) {
  $r = db_query('SELECT * FROM {tripal_jobs} WHERE job_id=:id',
    array(':id' => $job_id))->fetchObject();

  $progress = array(
    'percentage' => $r->progress,
    'message' => $r->status,
    'file_message' => $r->status,
    'file_class' => 'file-'.str_replace(' ','-',strtolower($r->status)),
  );

  // Make the Tripal job messages more specific to the generating of a file.
  $info = trpdownload_get_download_type_info($type);
  $function = (isset($info['function']['progress_msg'])) ? $info['function']['progress_msg'] : 'trpdownload_api_get_status_msg';
  $progress = call_user_func($function, $progress, $r);

  print drupal_json_output($progress);
  exit();
}

/**
 * Determine the status to show to the user.
 *
 * @param $job
 *   An object describing the current state of the job.
 * @return
 *   An array describing the status of the job with at least the following two keys:
 *    - percentage: a whole number percentage (ie: 50 for 50%).
 *    - message: a string message to the user.
 *   Other important keys include:
 *    - file_message: the message displayed on the api-provided download page in the file box.
 *    - file_class: a class to add to the file box in the api-provided download page.
 */
function trpdownload_api_get_status_msg($progress, $job) {

  switch ($job->status) {
    case 'Waiting':
      $progress['message'] = 'In Job Queue...';
      $progress['file_message'] = 'Waiting to generate file.';
      $progress['file_class'] = 'file-not-ready';
      break;
    case 'Error':
      $progress['message'] = 'Encountered an Error.';
      $progress['file_message'] = 'Unable to generate file.';
      $progress['file_class'] = 'file-error';
      break;
    case 'Cancelled':
      $progress['message'] = 'Cancelled by an Administrator.';
      $progress['file_message'] = 'Unable to generate file.';
      $progress['file_class'] = 'file-error';
      break;
    case 'Completed':
      $progress['message'] = 'Completed Successfully.';
      $progress['file_message'] = 'File Generated.';
      $progress['file_class'] = 'file-ready';
      break;
    case 'Running':
      $progress['message'] = 'Generating file...';
      $progress['file_message'] = 'Generating file.';
      $progress['file_class'] = 'file-not-ready';
      break;
  }

  return $progress;
}

/**
 * Makes the tripal job_id unrecognizable.
 *
 * @param $job_id
 *   The tripal job_id of the blast you want to make secret.
 *
 * @return
 *   A short string representing the job_id.
 */
function trpdownload_api_make_secret($job_id) {
  $mapping = trpdownload_api_secret_mapping();
  $secret = str_replace(array_keys($mapping), $mapping, $job_id);

  return $secret;
}
/**
 * Reveals the true job_id for your secret blast result.
 *
 * @param $secret
 *    The job_id previously made secret by trpdownload_api_make_secret().
 *
 * @return
 *    The revealed tripal job_id.
 */
function trpdownload_api_reveal_secret($secret) {
  $mapping = trpdownload_api_secret_mapping(TRUE);
  $job_id = str_replace(array_keys($mapping), $mapping, $secret);

  // Check that the job_id exists if it is an integer.
  if (is_numeric($job_id)) {
    $exists = db_query('SELECT job_id FROM {tripal_jobs} WHERE job_id=:id', array(':id' => $job_id))->fetchField();
    if (!$exists) {
      tripal_report_error(
        'trpdownload_api',
        TRIPAL_ERROR,
        'Unable to decode the blast job_id from :id.',
        array(':id' => $secret)
      );
    }
    else {
      return $job_id;
    }
  }
  // Last ditch effort: maybe this job was encoded before the upgrade?
  else {
    $job_id = base64_decode($secret);
    if (is_numeric($job_id)) {
      $exists = db_query('SELECT job_id FROM {tripal_jobs} WHERE job_id=:id', array(':id' => $job_id))->fetchField();
      if (!$exists) {
        tripal_report_error(
          'trpdownload_api',
          TRIPAL_ERROR,
          'Unable to decode the blast job_id from :id.',
          array(':id' => $secret)
        );
      }
      else {
        return $job_id;
      }
    }
    else {
      tripal_report_error(
        'trpdownload_api',
        TRIPAL_ERROR,
        'Unable to decode the blast job_id from :id.',
        array(':id' => $secret)
      );
    }
  }

  return FALSE;
}
/**
 * A single location for keeping track of the mapping used in our secrets.
 */
function trpdownload_api_secret_mapping($reveal = FALSE) {
  $mapping = array(
    1 => 'P',
    2 => 'sA',
    3 => 'b',
    4 => 'Q',
    5 => 'Hi',
    6 => 'yt',
    7 => 'f',
    8 => 'zE',
    9 => 'Km',
    0 => 'jVo',
  );

  // Since this is an open-source module with all the code publically available,
  // our secret is not very secret... We are ok with this since the liklihood of
  // profiting by stealing random blast results is pretty low. That said, if this bothers
  // you, feel free to implement the following function in a private module to change
  // this mapping to something that cannot easily be looked up on github. ;-).
  // NOTE: Ensure that the mapping you come up with is unique to ensure that the
  // job_id can be consistently revealed or your users might end up unable to find
  // their own blast results...
  if (function_exists('private_make_mapping_ultra_secret')) {
    $mapping = private_make_mapping_ultra_secret($mapping);
  }

  if ($reveal) {
    return array_flip($mapping);
  }
  else {
    return $mapping;
  }
}
/**
 * Tests your secret mapping over a set of random integers
 * to ensure the job_id can be recovered.
 *
 * @param $num_iterations
 *    An integer representing the number of times you wish to test your mapping.
 */
function trpdownload_api_test_secret_mapping($num_iterations = 10000) {
  $all_work = TRUE;

  for($i = 0; $i <= $num_iterations; $i++) {
    $job_id = mt_rand(0,100000);

    $secret = trpdownload_api_make_secret($job_id);
    $recovered_job_id = trpdownload_api_reveal_secret($secret);

    if ($job_id != $recovered_job_id) {
      drupal_set_message("Unable to recover job_id: $job_id; Secret: $secret.",'error');
      $all_work = FALSE;
    }
  }

  if ($all_work) {
    drupal_set_message("Secret Mapping works over $num_iterations iterations with random integers.");
  }

}

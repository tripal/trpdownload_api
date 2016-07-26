<?php
/**
 * @file
 * Provides a generic download page with progress bar and file download link.
 *
 * It is expected that the name of a tripal download type has been provided as the first
 * arguement (trpdownload_key) to this template. This template will then use information
 * registered with this API to:
 *  1. Create a tripal job which calls the generate_file function specified in
 *       hook_register_trpdownload_type() for this tripal download type. It passes along
 *       any unused url parameters and the query paramters to the generate_file function.
 *  2. Store the job_id in the URL allowing the user to bookmark their download and/or
 *       come back to it later (perhaps just to give it time to generate the file).
 *  3. Reports the progress of the Tripal job to the user so they can determine how long
 *       it will take before their file is ready.
 *  4. Once ready, this page provides a link that downloads the generated file to the
 *       users computer.
 */

// Save the query parameters.
$variables['q'] = drupal_get_query_parameters();
$variables['format'] = $variables['arg1']; //@TODO not generic.
$variables['job_code'] = (isset($variables['arg2'])) ? $variables['arg2'] : NULL;

// If we have the job_id encoded in the URL then we should show the user progress
// on the generation of their download file. All of the variables needed to show them
// this progress should have been saved in the job so pull them out now :-).
if ($variables['job_code']) {
  $variables['job_id'] = $variables['job_code'];
  $variables['job'] = tripal_get_job($variables['job_id']);
  $variables['job']->arguments = unserialize($variables['job']->arguments);
  $variables['download_args'] = $variables['job']->arguments['variables'];
  $info = $variables['download_args']['type_info'];
}
// Otherwise we should parse the URL and create a tripal job to generate the file
// and then save that job_id in the URL.
else {
  $variables['download_args']['q'] = $variables['q'];
  $variables['download_args']['safe_site_name'] = preg_replace('/\W+/','',ucwords(strip_tags(variable_get('site_name', 'Drupal'))));

  // First get information for the specified download type.
  $info = trpdownload_get_download_type_info($trpdownload_key);
  $variables['download_args']['type_info'] = $info;

  // Determine the suffix the file should be given.
  $variables['download_args']['suffix'] = (isset($info['file_suffix'])) ? $info['file_suffix'] : 'txt';
  if (isset($info['functions']['get_file_suffix']) AND function_exists($info['functions']['get_file_suffix'])) {
    $variables['download_args']['suffix'] = call_user_func($info['functions']['get_file_suffix'], $variables);
  }

  // Deteremine the file name to give the generated file.
  $variables['download_args']['filename'] = NULL;
  if (isset($info['functions']['get_filename']) AND function_exists($info['functions']['get_filename'])) {
    $variables['download_args']['filename'] = call_user_func($info['functions']['get_filename'], $variables);
  }
  if (!$variables['download_args']['filename']) {
    $variables['download_args']['filename'] = $variables['download_args']['safe_site_name'] .'.'. $trpdownload_key .'.'. date('YMj-his');
  }
  $variables['download_args']['filename'] .= '.' . $variables['download_args']['suffix'];

  // Determine the human-readable name of the file format.
  $variables['download_args']['format_name'] = 'Text';
  if (isset($info['type_name'])) {
    $variables['download_args']['format_name'] = $info['type_name'];
  }
  if (isset($info['functions']['get_format']) AND function_exists($info['functions']['get_format'])) {
    $variables['download_args']['format_name'] = call_user_func($info['functions']['get_format'], $variables);
  }

  // Now create the tripal job to generate the file :-D.
  global $user;
  $variables['job_id'] = tripal_add_job(
    'Download '.$info['type_name'],
    'trpdownload_api',
    $info['functions']['generate_file'],
    array('variables' => $variables['download_args']),
    $user->uid
  );

  // Encode the URL.
  $variables['job_code'] = $variables['job_id'];

  // Determine the path to redirect to.
  global $base_url;
  $variables['path'] = $base_url . '/' . current_path() . '/' . $variables['job_code'];

  // Load the job so my progress bar knows what to display ;-).
  $variables['job'] = tripal_get_job($variables['job_id']);
}

// Used by javascript to determine the status of the job and update this page via ajax.
$variables['progress_path'] = url('/tripal/progress/job/'.$trpdownload_key.'/'.$variables['job_id']);

$variables['file_download_url'] = file_create_url(variable_get('trpdownload_relpath', NULL) . $variables['download_args']['filename']);
?>

<!-- Change the URL to include the job code and remove the query parameters.
      This is done via javascript/HTML5 in order to avoid the page refresh. -->
<?php if (isset($variables['path'])) : ?>
  <script>
    window.history.pushState('', 'Download: Job Submitted', "<?php print $variables['path'];?>");
  </script>
<?php endif; ?>

<!-- Add the progress bar and ensure it is being updated -->
<script src="<?php print url('/misc/progress.js');?>"></script>
<script>
(function ($) {
  Drupal.behaviors.trpdownloadApiProgressBar = {
    attach: function (context, settings) {

      // Add a drupal progress bar to monitor the current job.
      pb = new Drupal.progressBar('trpdownloadProgressBar');
      pb.setProgress(0,''); //Start the progress bar at 0 instead of 1%
      $('.progress-pane').append(pb.element);
      pb.startMonitoring('<?php print $variables["progress_path"];?>',5);
    }
  };
}(jQuery));
</script>

<!-- Update the file box to ensure users see the feedback -->
<script>
(function ($) {
  Drupal.behaviors.trpdownloadApiFileBoxFeedback = {
    attach: function (context, settings) {

      setTimeout(trpdownloadapiUpdateStatus, 500);

      function trpdownloadapiUpdateStatus() {
        var pane = $('.download-pane');
        $.ajax({
          type: 'GET',
          url: '<?php print $variables["progress_path"];?>',
          data: '',
          dataType: 'json',
          success: function (progress) {
            // Display errors.
            if (progress.status == 0) {
              pb.displayError(progress.data);
              return;
            }
            // Update display.
            pane.removeClass('file-not-ready');
            pane.removeClass('file-ready');
            pane.removeClass('file-error');
            pane.addClass(progress.file_class);
            // Schedule next timer.
            pb.timer = setTimeout(function () { pb.sendPing(); }, pb.delay);
          },
          error: function (xmlhttp) {
            pb.displayError(Drupal.ajaxError(xmlhttp, pb.uri));
          }
        });
        setTimeout(trpdownloadapiUpdateStatus, 500);
      };
    }
  };
}(jQuery));
</script>

<style>
/* Layout */
.download-page {
  margin-top: 60px;
}
.progress-pane {
  clear: both;
  margin: 20px 12px 20px 13px;
}
.download-pane {
  display: flex;
  margin: 0px 15px;
  padding: 20px 10px;
  height: 128px;
}
.inner-pane {
  margin: 23px;
  margin-bottom: 0;
  flex-grow: 1;
  width: 125px;
}
.download-pane h2 {
  margin-top: 0;
}
.download-pane ul {
  margin: 0;
}

/* Style */
.download-pane {
  border: 1px solid #D0D0D0;
  font-size: 12px;
}
.download-pane .file-format {
  font-style: italic;
  font-size: 0.9em;
}

.messages.info {
  background-color: #ECF6FE;
  border: 1px solid #65A6E9;
  background-image: url(<?php print url(drupal_get_path('module','trpdownload_api').'/theme/icons/info.24.png');?>);
}

/* Color Que's for status */
.download-pane.file-ready {
  color: black;
  border-color: #be7;
  background-color: #f8fff0;
}
.download-pane.file-error, .download-pane.file-error a {
  color: black;
  border-color: #8c2e0b;
  background-color: #fef5f1;
}
.download-pane.file-not-ready, .download-pane.file-not-ready a {
  color: #999999;
}
.download-pane.file-not-ready img {
  -webkit-filter: opacity(40%);
  filter: opacity(40%);
}
</style>
<div class="download-page">
  <div class="messages info">We are working on generating the file you requested. Refer to the progress bar below for status.</div>
  <div class="progress-pane"></div>
  <div class="download-pane">
    <?php print theme_image(array(
      'path' => drupal_get_path('module','trpdownload_api').'/theme/icons/file_generic.128.png',
      'alt' => 'file download icon',
      'attributes' => array()
    ));?>
    <div class="inner-pane file">
      <h2>File:</h2>
      <div class="file-link"><?php print l($variables['download_args']['filename'], $variables['file_download_url'] );?></div>
      <div class="file-format">Format: <?php print $variables['download_args']['format_name']?></div>
    </div>
    <div class="inner-pane summary">
      <h2>Summary:</h2>
      <?php print call_user_func($info['functions']['summarize'], $variables, drupal_get_query_parameters()); ?>
    </div>
  </div>
</div>

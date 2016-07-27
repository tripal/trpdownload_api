/**
 * Adds a Drupal progress bar to an element with class progress-pane
 * and then sets up monitoring of a JSON callback specifying progress.
 * To set the URL of the JSON callback add the following code to the
 * preprocess hook for your download template. This is already taken
 * care of for you if you use the generic_download_page.tpl.php
 * @code
     drupal_add_js(
       array(
         'trpdownloadApiProgressBar' => array(
           'progressPath' => url('/tripal/progress/job/'.$trpdownload_key.'/'.$variables['job_id'])
         )
       ),
       'setting'
     );
 * @endcode
 */
(function ($) {
  Drupal.behaviors.trpdownloadApiProgressBar = {
    attach: function (context, settings) {

      // Add a drupal progress bar to monitor the current job.
      pb = new Drupal.progressBar('trpdownloadProgressBar', function(percentage, message) {
          // Also ensure that we stop when the progress bar is complete ;-).
          if (percentage == "100") pb.stopMonitoring();
        });
      pb.setProgress(0,''); //Start the progress bar at 0 instead of 1%
      $('.progress-pane').append(pb.element);

      // Start monitoring the JSN callback for status.
      pb.startMonitoring(Drupal.settings.trpdownloadApiProgressBar.progressPath,2000);

    }
  };
}(jQuery));

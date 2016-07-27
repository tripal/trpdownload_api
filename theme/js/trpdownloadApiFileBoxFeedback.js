/**
 * Updates the file status box based on the same JSON used to
 * update the progress bar.
 *
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
  Drupal.behaviors.trpdownloadApiFileBoxFeedback = {
    attach: function (context, settings) {

      setTimeout(trpdownloadapiUpdateStatus, 500);

      function trpdownloadapiUpdateStatus() {
        var pane = $('.download-pane');
        $.ajax({
          type: 'GET',
          url: Drupal.settings.trpdownloadApiProgressBar.progressPath,
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

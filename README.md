# Tripal Download API
This module provides an API for downloading Tripal/Chado data. Since download functionality is often sought after for Tripal sites and Views Data Export is not currently meeting our needs, this module aims to provide an API to aid module and site developers in making efficient, user friendly downloads available.

# Under active development -not ready for use.
This module is currently being developed and is not yet ready for testing. If you are interested in this functionality please watch this module to be updated when a stable alpha version is released. If you have any feedback, design ideas, questions or feature requests please file an issue -I would love to hear from you :-).

## How will it work?
1. Module developers will specify a download type using hook_register_trpdownload_type().
2. Next they create a download page by implementing hook_menu(). This can be as simply as specifying to use the generic download page provided by the API.
  * The download page can be linked to from an existing listing or you can create a custom download page with a form to retrieve information on what to download.
  * When using the generic download page, it is expected all information needed to generate the file is in the URL as query parameters.
  
3. When a user is redirected to the download page, the generic download page will create a tripal job which calls the "generate_file" function specified in hook_register_trpdownload_type() and passes it the query parameters as well as helpful information such as the filename of the file to generate and where to put it ;-)
4. The URL of the page changes to hide the query parameters and instead show an obsfuscated job_id allowing users to bookmark this page if needed. There is an ajax progress bar that shows the progress of the Tripal job and once complete, provides a link to the user to download the file.

Thus module developers can provide a simple interface showing progress and providing access to the file by (1) specify a download type and path, (2) Implementing a generate_file function, and (3) Linking to the download page and passing information in the form of query parameters.

## Future Work
Development on this module has just begun and as such it still doesn't meet all the needs of the Tripal community. The following list of features are needs that we know exist and intend to address. If you have any additional needs in reference to downloads please open an issue and tell us about it!
* Views Support
* Administrative Interface?

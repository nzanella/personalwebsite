<?php
/**
 * Outputs the HTML for the page served to the visitor's web browser.
 *
 * @package NeilGlenZanellaWebSite
 */

  // run before outputting HTML since cookies must be sent to browser in HTTP headers before HTML

  require_once("settings.php");

  include("site-head.php");

  include("site-header.php");

  include("site-mainContent.php");

  include("site-footer.php");

?>

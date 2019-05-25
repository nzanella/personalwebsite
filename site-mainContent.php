<?php
/**
 * Outputs the HTML code used to display the main content section of our website.
 *
 * @package NeilGlenZanellaWebSite
 */
?>
<?php

  $currentPathToPageArray = $pageSettings->menuTreeToPagesData->getCurrentPathToPageArray();

  //$pageSettings->menuTreeToPagesData->showCurrentPathToPageNames();

  $currentPage = $currentPathToPageArray[count($currentPathToPageArray)];

  $pageName = $currentPage->getPageName();

?>
      <div id="mainContent">
        <div id="innerMainContent">
<?php

  include("pages/$pageName");

?>
        </div>
      </div>

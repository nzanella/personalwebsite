<?php
/**
 * Outputs the HTML code used to display the header section of our website.
 *
 * @package NeilGlenZanellaWebSite
 */

require_once("settings.php");

?>
  <body>
    <div id="wrapper">
      <header>
        <div id="siteLogo">
          <a href="<?php echo $pageSettings->siteURLCanonical; ?>"><img alt="Neil Glen Zanella" src="<?php echo $pageSettings->getFile("images/NeilZanellaBusinessSiteLogo.png"); ?>" /></a>
        </div>
        <div id="headerRight">
          <div id="headerRightTop">
            <h1><a href="<?php echo $pageSettings->siteURLCanonical; ?>">Neil Glen Zanella</a></h1>
            <nav id="languageChoice">
              <ul>
<?php

  foreach (ConfigSettings::$supportedLangsArray as $lang) {

    $uppercaseLang = strtoupper($lang);

    $flagImageURL = $pageSettings->getFile("images/FlatFlagCollection" . $uppercaseLang . "-45x30.png");

?>
                <li class="languageItem"><a href="<?php echo $pageSettings->currentPageURLArray[$lang]; ?>"><img alt="<?php echo $pageContents->getContents("pageContentLanguage", $lang); ?>" src="<?php echo $flagImageURL; ?>" /></a></li>
<?php

  }

?>
              </ul>
            </nav>
          </div>
<?php

  $menuLevelToNavDivIdsMap = array(1 => "headerRightMiddle", 2 => "headerRightBottom");

  $menuDepth = $pageSettings->menuTreeToPagesData->getMenuDepth();

  $currentMenuTreeNode = $pageSettings->menuTreeToPagesData->getRootMenuTreeNode();

  $currentPathToPageArray = $pageSettings->menuTreeToPagesData->getCurrentPathToPageArray();

  for ($currentMenuLevel = 1; $currentMenuLevel <= $menuDepth; $currentMenuLevel++) {

?>
         <div id="<?php echo $menuLevelToNavDivIdsMap[$currentMenuLevel]; ?>">
           <nav>
             <ul>
<?php

    if (array_key_exists($currentMenuLevel, $currentPathToPageArray)) {

      // recall that a full path of menu tree nodes will be active (clicked) at any given time

      $currentSelectedMenuTreeNode = $currentPathToPageArray[$currentMenuLevel];

      foreach ($currentMenuTreeNode->getChildren() as $childMenuTreeNode) {

        $nextMenuTreeNode = $currentPathToPageArray[$currentMenuLevel];

        $isMenuEntrySelected = (strcmp($childMenuTreeNode, $nextMenuTreeNode) == 0);

?>
                <li lang="<?php echo $pageSettings->displayLang ?>"<?php if ($isMenuEntrySelected) { ?> class="selected"<?php } ?>><a href="<?php echo $childMenuTreeNode->getPageLink(); ?>"><?php echo strtoupper($childMenuTreeNode->getMenuContents()); ?></a></li>
<?php

        $currentMenuTreeNode = $nextMenuTreeNode;

      }

    }

?>
             </ul>
           </nav>
         </div>
<?php

  }

?>
        </div>
      </header>
<!-- End of header section -->

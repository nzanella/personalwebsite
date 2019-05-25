<?php
/**
 * Outputs the XML sitemap for our website.
 *
 * @package NeilGlenZanellaWebSite
 */
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
  <url>
<?php

  require_once("settings.php");

  $rootMenuTreeNode = $pageSettings->menuTreeToPagesData->getRootMenuTreeNode();

  $treeNodeIterator = new TreeNodeIterator($rootMenuTreeNode);

  foreach (ConfigSettings::$supportedLangsArray as $supportedLang) {

    foreach ($treeNodeIterator as $menuTreeNodeKey => $menuTreeNode) {

?>
    <loc><?php echo $menuTreeNode->getPageLink($supportedLang); ?></loc> 
<?php

    }

  }

?>
  </url>
</urlset>

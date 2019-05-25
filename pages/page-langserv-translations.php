<?php
/**
 * @package NeilGlenZanellaWebSite
 */
?>
<div id="translationsSummary">
<?php echo $pageContents->getContents("translationsTopParagraph"); ?>
</div>
<div id="translationsAreas">
  <h3><?php echo $pageContents->getContents("translationsAreasOfExpertese"); ?></h3>
  <ul>
    <li><?php echo $pageContents->getContents("translationsAreasLI1"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI2"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI3"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI4"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI5"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI6"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI7"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI8"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI9"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI10"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI11"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI12"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI13"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI14"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI15"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI16"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI17"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI18"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI19"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI20"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI21"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI22"); ?></li>
    <li><?php echo $pageContents->getContents("translationsAreasLI23"); ?></li>
  </ul>
</div>
<div id="translationsSteps">
  <h3><?php echo $pageContents->getContents("translationsHowToContact"); ?></h3>
  <ul>
    <li><span class="step">Step 1</span>: <?php echo $pageContents->getContents("translationsStepLI1"); ?></li>
    <li><span class="step">Step 2</span>: <?php echo $pageContents->getContents("translationsStepLI2"); ?></li>
    <li><span class="step">Step 3</span>: <?php echo $pageContents->getContents("translationsStepLI3"); ?></li>
    <li><span class="step">Step 4</span>: <?php echo $pageContents->getContents("translationsStepLI4"); ?></li>
  </ul>
<?php

  $contactPageLink = $pageSettings->menuTreeToPagesData->getRootMenuTreeNode()->getChildByName("contact")->getPageLink();

?>
  <a href="<?php echo $contactPageLink ?>"><div class="getInTouchButton"><?php echo $pageContents->getContents("getInTouchButton"); ?></div></a>
</div>

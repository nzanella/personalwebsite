<?php
/**
 * Outputs the HTML code used to display the footer section of our website.
 *
 * @package NeilGlenZanellaWebSite
 */
?>
      <footer>
        <ul class="footerLinks">
          <li><br /><a href="<?php echo $pageSettings->getFile("sitemap.xml"); ?>"><?php echo $pageContents->getContents("siteMap"); ?></a></li>
          <!--
          <li><a href="#"><?php echo $pageContents->getContents("privacyPolicy"); ?></a></li>
          -->
          <li><p>Copyright &copy; 2007 - 2015 Neil Glen Zanella - P.IVA 04767530282</p></li>
        </ul>
        <ul class="socialLinks">
          <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($pageSettings->siteURLCanonicalLocalized); ?>" target="_blank"><img alt="<?php echo $pageContents->getContents("shareOnFacebook"); ?>" src="<?php echo $pageSettings->getFile("images/icon-round-facebook.png"); ?>" /></a></li>
          <li><a href="https://www.twitter.com/share?text=<?php echo urlencode($pageContents->getContents("ogTitle")); ?>&amp;url=<?php echo urlencode($pageSettings->siteURLCanonical); ?>&amp;via=<?php echo urlencode("NeilGlenZanella"); ?>" target="_blank"><img alt="<?php echo $pageContents->getContents("shareOnTwitter"); ?>" src="<?php echo $pageSettings->getFile("images/icon-round-twitter.png"); ?>" /></a></li>
          <li><a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode($pageSettings->siteURLCanonicalLocalized); if (DebugSettings::$debug) { echo "&amp;customDebugQueryParameter=", DebugSettings::$debugLinkedInSharingVersion; } ?>&amp;title=<?php echo urlencode($pageContents->getContents(null)); ?>&amp;source=<?php echo urlencode($pageContents->getContents("linkedInSource")); ?>&amp;summary=<?php echo urlencode($pageContents->getContents("linkedInSummary")); ?>" target="_blank"><img alt="<?php echo $pageContents->getContents("shareOnLinkedIn"); ?>" src="<?php echo $pageSettings->getFile("images/icon-round-linkedin.png"); ?>" /></a></li>
          <li><a href="https://plus.google.com/share?url=<?php echo urlencode($pageSettings->siteURLCanonicalLocalized); ?>&amp;hl=<?php echo urlencode($pageContents->getContents("googlePlusLanguageCode")); ?>" target="_blank"><img alt="<?php echo $pageContents->getContents("shareOnGooglePlus"); ?>" src="<?php echo $pageSettings->getFile("images/icon-round-googlePlus.png"); ?>" /></a></li>
<?php $pinterestImage="images/NeilZanellaBusinessPinterestImage532x1095.png"; ?>
          <li><a href="https://www.pinterest.com/pin/create/button/?url=<?php echo urlencode($pageSettings->siteURLCanonicalLocalized); ?>&amp;media=<?php echo urlencode($pageSettings->siteURLCanonical . $pinterestImage); ?>&amp;description=<?php echo urlencode($pageContents->getContents("ogDescription")); ?>" target="_blank"><img alt="<?php echo $pageContents->getContents("shareOnPinterest"); ?>" src="<?php echo $pageSettings->getFile("images/icon-round-pinterest.png"); ?>" /></a></li>
        </ul>
      </footer>
    </div>
  </body>
</html>

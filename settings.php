<?php
/**
 * Place where the debug settings are defined and where the $pageSettings and $pageContents objects are instantiated.
 *
 * @package NeilGlenZanellaWebSite
 */

  require_once("functions.php");

  require_once("menu/menuTreeNode-v3.php");

  ////////////////////////////////////////////////////////////////////////////////
  // debug settings and configuration variables
  ////////////////////////////////////////////////////////////////////////////////

  class DebugSettings {

    public static $debug = false;

    public static $debugPathAppendedToCanonicalURL = "www.neilzanella.com"; /* development directory under XAMPP htdocs including trailing slash (e.g. "www.neilzanella.com/"); instead of here this can also be set in the C:\xampp\apache\conf\httpd.conf DocumentRoot (Directory just below it will also need updating) */

    public static $debugPageLanguageApacheModRewriteEnabled = false;

    public static $disableClientCaching = true; // use this to prevent LinkedIn and Google+ servers from caching meta tag image content when site is scraped

    // DOESN'T SEEM TO WORK: http://stackoverflow.com/questions/24081373/getting-old-image-while-sharing-page-in-linkedin
    public static $debugLinkedInSharingVersion = 1; /* increment each time you need to dispatch a new image to the LinkedIn cache */

  }

  class ConfigSettings {

    // array of languages supported on the site listed in order of appearence on the UI

    public static $supportedLangsArray = array("it", "en");

    // top-level fallback language for the site

    public static $defaultLang = "it";

    // the $urlScheme is used to describe the scheme used to encode the page language and slugs as part of the URL

    //public static $urlScheme = self::URL_SCHEME_QUERYSTRING;
    public static $urlScheme = self::URL_SCHEME_DIRECTORY;

    const URL_SCHEME_DOMAIN = 0; /* example: http://en.neilzanella.com/.../ */

    const URL_SCHEME_DIRECTORY = 1; /* example: http://www.neilzanella.com/en/.../ */

    const URL_SCHEME_QUERYSTRING = 2; /* example: http://www.neilzanella.com/.../?lang=en */

    function __construct() {

      // modify the URL language scheme if it is not possible to use the one that was specified

      ConfigSettings::$urlScheme = $this->getURLLangScheme(ConfigSettings::$urlScheme);

      if (DebugSettings::$disableClientCaching) {

        header("Cache-Control: no-store");

      }

    }

    /*
     * @return integer The URL language scheme to be used, either the specified value or another value if making use of the specified value is not possible.
     */

    private function getURLLangScheme($requestedURLLangScheme) {

      // check whether it is possible to use the configured URL language scheme, otherwise set it to something else

      if ($requestedURLLangScheme == self::URL_SCHEME_DOMAIN and serverNameIsLocalhostOrNumeric()) {

        return self::URL_SCHEME_DIRECTORY;

      }

      return $requestedURLLangScheme;

    }

    /*
     * Return whether the server name is "localhost" or numeric.
     */

    private function serverNameIsLocalhostOrNumeric() {

      $isDomainNameLocalhost = (strcmp(strtolower($_SERVER['SERVER_NAME']), "localhost") == 0);

      if ($isDomainNameLocalhost) {

        return true;

      }

      $matchIsDomainNameNumeric = preg_match("/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/", $_SERVER['SERVER_NAME']);

      if ($matchIsDomainNameNumeric === false) doDie("Could not perform PCRE pattern match on \$_SERVER['SERVER_NAME'])", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      $isDomainNameNumeric = ($matchIsDomainNameNumeric === 1);

      if ($isDomainNameNumeric) {

        return true;

      }

      return false;

    }

  }

  /**
   * Menu tree node class used to parse the contents of menu/menuTreeNodes.xml and set up the header menu support structure.
   */
/*
  class MenuTreeNode extends TreeNode {

    public $langToMenuItemNameMap = array();

    public $langToMenuItemSlugMap = array();

    public $submenuOrFileName = null;

    public static constructMenuStructureToPageNamesTree() {

      $rootMenu = null;//for now

    }



//TODO: This function should really be part of the PageSettings class so you can access all its stuff.
// NEWUPDATE: if that is the case, why not just use composition to store a $rootMenuTreeNode therein.
//UNIMPLEMENTED: for now store information inside site-header.php
    /*
     * Read menu tree node structure from XML file
     *
     * @return array Associative array of content names mapped to associative arrays of
     * language codes mapped to textual contents.
     */
/*
    const menuTreeNodesXMLDirectory = "menu/";

    const menuTreeNodesXMLFileName = "menuTreeNodes.xml";

    private function parseAndValidateMenuTreeNodesXML() {

      $menuTreeNodesXMLLocation = self::menuTreeNodesXMLDirectory . self::menuTreeNodesXMLFileName;

      // retrieve root XML element handle from XML file where the menu tree nodes structure is defined

      $menuTreeNodesContainer = simplexml_load_file($menuTreeNodesXMLLocation);

      if ($menuTreeNodesContainer === false) doDie("Could not find " . $menuTreeNodesXMLLocation . "!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      // retrieve root XML element children

      $menuTreeNodesContainerChil = $menuTreeNodesContainer->children();

      // 

      foreach ($menuTreeNodesContainerChildren as $menuTreeNodeSlugComponent) {

        if (array_key_exists($menuItem->getName(), $contentNamesMappedToLangContentMap)) {

          // duplicate content name found

          doDie("Duplicate content name " . $contentName->getName() . " found in file " .  $contentXMLFileName . ".", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        }

        $contentNamesMappedToLangContentMap[$contentName->getName()] = array();

        $contentNameLanguageElements = $contentName->children();

        // check whether content name XML element has any language code XML element children

        if ($contentNameLanguageElements->count() != 0) {

          // iterate through language elements for this content

          foreach ($contentNameLanguageElements as $contentNameLanguageElement) {

            // retrieve the name of this language tag

            $lang = $contentNameLanguageElement->getName();

            // retrieve text content found directly inside this language tag

            $content = $this->dohtmlentities(trim($contentNameLanguageElement->__toString()));

            // store the language-content pair

            $contentNamesMappedToLangContentMap[$contentName->getName()][$lang] = $content; // already htmlentities-converted

          }

        } else {

          // content name XML element has no language code XML element children

          // parse optional inner text (excluding missing inner tags and tag contents) for fallback purposes

          $fallbackContent = $this->dohtmlentities(trim($contentName->__toString()));

          foreach (ConfigSettings::$supportedLangsArray as $supportedLang) {

            $contentNamesMappedToLangContentMap[$contentName->getName()][$supportedLang] = $fallbackContent; // already htmlentities-converted

          }

        }

        // store XML attributes associated with content name tag

        $contentNamesMappedToContentNameAttributes[$contentName->getName()] = $contentName->attributes();

      } 

      return $contentNamesMappedToLangContentMap;

    }

  // TODO: REMOVEME: brace above ends function, brace below ends class

  }
*/
  /*
   * Class used to access page settings once instantiated.
   */

  class PageSettings {

    ////////////////////////////////////////////////////////////////////////////////
    // setup other stuff based on the the global configuration variables
    ////////////////////////////////////////////////////////////////////////////////

    public $displayLang;

    public $siteURLCanonical;

    public $siteURLCanonicalLocalized;

    public $siteURLArray;

    public $siteURLDefault;

    public $localizedCanonicalPageURLArray;

    public $currentPageURLArray;

    public $fallbackLanguageArray;

    public $menuTreeToPagesData;

    const menuTreeNodesXMLFileName = "menuTreeNodes.xml";

    function __construct() {

      $pageURLLang = getPageURLLanguage(DebugSettings::$debugPageLanguageApacheModRewriteEnabled, ConfigSettings::$supportedLangsArray);

      $this->displayLang = getDisplayLangAndSetLangCookie(ConfigSettings::$supportedLangsArray, ConfigSettings::$defaultLang, $pageURLLang);

      $this->siteURLCanonical = getCanonicalURL() /* example: "http://www.neilzanella.com/" */;

      $this->siteURLCanonicalLocalized = getLocalizedCanonicalURL($this->displayLang) /* example: "http://www.neilzanella.com/?lang=xx" */;

      $this->siteURLArray = $this->getSiteURLArray(ConfigSettings::$supportedLangsArray);

      $this->siteURLDefault = getDefaultURL() /* example: "http://www.neilzanella.com/?lang=it" */;

      $this->fallbackLanguageArray = getPageURLLangThenSupportedPreferredThenDefaultThenOtherLangArray($pageURLLang, ConfigSettings::$supportedLangsArray, ConfigSettings::$defaultLang);

      $this->menuTreeToPagesData = new MenuTreeToPagesData($this);

      $this->localizedCanonicalPageURLArray = $this->getLocalizedCanonicalPageURLArray(ConfigSettings::$supportedLangsArray);

      $this->currentPageURLArray = $this->getCurrentPageURLArray(ConfigSettings::$supportedLangsArray);

    }

    function getPrimaryLanguage() {

      if (count($this->fallbackLanguageArray) == 0) {

        doDie("Array of fallback languages is empty!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      }

      return $this->fallbackLanguageArray[0];

    }

    /*
     * Construct the array of site URLs to print out inside the HTML head element.
     */

    private function getSiteURLArray($supportedLangsArray) {

      $siteURLArray = array();

      foreach (ConfigSettings::$supportedLangsArray as $lang) {

        $siteURLArray[$lang] = getLocalizedCanonicalURL($lang);

      }

      return $siteURLArray;

    }

    /*
     * Construct the array of full current page URLs with one URL corresponding to each language.
     */

    private function getCurrentPageURLArray($supportedLangsArray) {

      return $this->getPageURLArray($supportedLangsArray, true);

    }

    /*
     * Construct the array of parial localized current page URLs with one URL corresponding to each language.
     */

    private function getLocalizedCanonicalPageURLArray($supportedLangsArray) {

      return $this->getPageURLArray($supportedLangsArray, false);

    }

    /*
     * Construct the array of current page URLs with one URL corresponding to each language.
     */

    private function getPageURLArray($supportedLangsArray, $doIncludeSlugs) {

      $currentPageURLArray = array();

      foreach (ConfigSettings::$supportedLangsArray as $lang) {

        if ($doIncludeSlugs) {

          $currentPathToPageArray = $this->menuTreeToPagesData->getCurrentPathToPageArray();

          $currentPageURLArray[$lang] = getCurrentPageURL($lang, $currentPathToPageArray);

        } else {

          $currentPageURLArray[$lang] = getCurrentPageURL($lang, null);

        }

      }

      return $currentPageURLArray;

    }

    public function getFile($fileName) {

      return $this->siteURLCanonical . $fileName;

    }

    public function getImage($imageFileName) {

      return $this->siteURLCanonical . "images/" . $imageFileName;

    }

    public function getCSS($cssFileName) {

      return $this->siteURLCanonical . "css/" . $cssFileName;
    }

    public function getJS($jsFileName) {

      return $this->siteURLCanonical . "js/" . $jsFileName;
    }

  }

  /*
   * Class used to access page contents in various languages once instantiated.
   */

  class PageContents {

    private $displayLang;

    public $xmlContents; // public because during debugging we may want to be able to var_dump($pageContents->xmlContents);

    function __construct() {

      global $pageSettings;

      $this->displayLang = $pageSettings->displayLang;

      $this->xmlContents = $this->parseAndValidateXMLContents();

    }

    /*
     * Use this function to retrieve the contents corresponding to content name to be displayed.
     *
     * @param string $contentName The content name given to the given content.
     * @param string $optionalContentLanguage Optional content language to use (otherwise the fallbackLanguageArray is traversed).
     * @return string The content or the empty string if no content name is specified.
     */

    public function getContents($contentName = null, $optionalContentLanguage = null) {

      if (is_null($contentName)) return "";

      // Iterate through array of supported fallback languages and display contents
      // as soon as contents for a supported language is found in the given order.
      // Of course if content is available in all languages the first fallback language is used.

      global $pageSettings;

      // modify search algorithm according to whether an optional content language has been specified

      if (is_null($optionalContentLanguage)) {

        $fallbackLanguageArray = $pageSettings->fallbackLanguageArray;

      } else {

        $fallbackLanguageArray = array($optionalContentLanguage);

      }

      // first try retrieving page content from an associative array
      // mapping language codes to content strings defined inside this class

      $notFound = true;

      foreach ($fallbackLanguageArray as $langCode) {

        if (property_exists($this, $contentName) && array_key_exists($langCode, $this->{$contentName})) {

          $notFound = false;

          return htmlentities($this->{$contentName}[$langCode]);

        }

      }

      // next try retrieving page content from content parsed from xml files
      // (we usually want to run this code)

      if ($notFound) {

        foreach ($fallbackLanguageArray as $langCode) {

          if (array_key_exists($contentName, $this->xmlContents) && array_key_exists($langCode, $this->xmlContents[$contentName])) {

            return $this->xmlContents[$contentName][$langCode]; // htmlentities already invoked on xmlContents array elements

          }

        }

      }

      // return correct comment according to whether an optional language was not specified

      if (is_null($optionalContentLanguage)) {

        return "<!-- No supported language found anywhere for \$contentName = \"$contentName\". -->";

      } else {

        return "<!-- No content specified for language $optionalContentLanguage for \$contentName = \"$contentName\". -->";

      }

    }

    /*
     * Use this function to set the lang attribute on the HTML element displaying such contents.
     * In reality we don't use this function as we would need to type a lot more HTML and the added benefits are questionable.
     * 
     * @param string $contentName The content name given to the given content.
     * @return string The content language.
     */

    public function getContentsLanguage($contentName) {

      global $pageSettings;

      // first try retrieving page content from an associative array
      // mapping language codes to content strings defined inside this class

      $notFound = true;

      foreach ($pageSettings->fallbackLanguageArray as $langCode) {

        if (property_exists($this, $contentName) && array_key_exists($langCode, $this->{$contentName})) {

          $notFound = false;

          return $langCode;

        }

      }

      // next try retrieving page content from content parsed from xml files
      // (we usually want to run this code)

      if ($notFound) {

        foreach ($pageSettings->fallbackLanguageArray as $langCode) {

          if (array_key_exists($langCode, $this->xmlContents[$contentName])) {

            return $langCode;

          }
 
        }

      }

      return "<!-- No supported language found anywhere for \$contentName = \"$contentName\". -->";

    }

    /*
     * Use this function to retrieve the language to contents map corresponding to content name.
     *
     * @param string $contentName The content name.
     * @return array The language to contents map for this content name.
     */

    public function getLangToContentsMap($contentName) {

      // Iterate through array of supported fallback languages and add language to content map to results
      // as soon as contents for a supported language is found in the given order.

      global $pageSettings;

      $languageToContentMap = array();

      // first try retrieving page content from an associative array
      // mapping language codes to content strings defined inside this class

      foreach ($pageSettings->fallbackLanguageArray as $langCode) {

        // if content for a given language is found add it to the map to be returned

        if (property_exists($this, $contentName) && array_key_exists($langCode, $this->{$contentName})) {

          $languageToContentMap[$langCode] = htmlentities($this->{$contentName}[$langCode]);

        }

      }

      // next retrieve remaining page content from content parsed from xml files

      foreach ($pageSettings->fallbackLanguageArray as $langCode) {

        // avoid duplicate insertion into map when content for a given language has already been retrieved from class

        if (!array_key_exists($langCode, $languageToContentMap)) {

          // if content for a given language is found add it to the map to be returned

          if (array_key_exists($langCode, $this->xmlContents[$contentName])) {

            $languageToContentMap[$langCode] = $this->xmlContents[$contentName][$langCode]; // htmlentities already invoked on xmlContents array elements
          }
        }

      }

      return $languageToContentMap;

    }

    /*
     * Read contents from XML files (content can be arranged into a bunch of files for convenience).
     *
     * @return array Associative array of content names mapped to associative arrays of
     * language codes mapped to textual contents.
     */

    const contentsParentXMLDirectory = "contents/";

    const contentsParentXMLFileName = "contents.xml";

    private function parseAndValidateXMLContents() {

      $contentsParentXMLLocation = self::contentsParentXMLDirectory . self::contentsParentXMLFileName;

      $contentXMLFileNames = array();

      // parent XML file where all XML files containing all localized content are defined.

      $contentsParentXML = simplexml_load_file($contentsParentXMLLocation);

      if ($contentsParentXML === false) doDie("Could not find " . $contentsParentXMLLocation . "!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      foreach ($contentsParentXML->contentSection as $contentSection) {

        // TODO: Check that the name attribute of the contentSection element exists.

        $contentXMLFile = $contentSection["name"];

        // TODO: Check that the file corresponding to the retrieved filename exists.

        $contentXMLFileNames[] = self::contentsParentXMLDirectory . $contentXMLFile;

      }

      $contentNamesMappedToLangContentMap = array();

      $contentNamesMappedToContentNameAttributes = array();

      foreach ($contentXMLFileNames as $contentXMLFileName) {

        $contentContainer = simplexml_load_file($contentXMLFileName);

        if ($contentContainer === false) doDie("Could not find " . $contentXMLFileName . "!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        // retrieve element XML children

        $contentContainerChildren = $contentContainer->children();

        foreach ($contentContainerChildren as $contentName) {

          if (array_key_exists($contentName->getName(), $contentNamesMappedToLangContentMap)) {

            // duplicate content name found

            doDie("Duplicate content name " . $contentName->getName() . " found in file " .  $contentXMLFileName . ".", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          }

          $contentNamesMappedToLangContentMap[$contentName->getName()] = array();

          $contentNameLanguageElements = $contentName->children();

          // check whether content name XML element has any language code XML element children

          if ($contentNameLanguageElements->count() != 0) {

            // iterate through language elements for this content

            foreach ($contentNameLanguageElements as $contentNameLanguageElement) {

              // retrieve the name of this language tag

              $lang = $contentNameLanguageElement->getName();

              // retrieve text content found directly inside this language tag

              $content = $this->dohtmlentities(trim($contentNameLanguageElement->__toString()));

              // store the language-content pair

              $contentNamesMappedToLangContentMap[$contentName->getName()][$lang] = $content; // already htmlentities-converted

            }

          } else {

            // content name XML element has no language code XML element children

            // parse optional inner text (excluding missing inner tags and tag contents) for fallback purposes

            $fallbackContent = $this->dohtmlentities(trim($contentName->__toString()));

            foreach (ConfigSettings::$supportedLangsArray as $supportedLang) {

              $contentNamesMappedToLangContentMap[$contentName->getName()][$supportedLang] = $fallbackContent; // already htmlentities-converted

            }

          }

          // store XML attributes associated with content name tag

          $contentNamesMappedToContentNameAttributes[$contentName->getName()] = $contentName->attributes();

        } 

      }

      // iterate through content names carrying out all variable substitutions on the corresponding content values (TODO: guard against loops resulting from infinite variable substitution in content values)

      $contentNames = array_keys($contentNamesMappedToLangContentMap);

      $variablesFound = true;

      while ($variablesFound) {

        $variablesFound = false;

        foreach ($contentNamesMappedToLangContentMap as $contentName => $langToContentMap) {

          foreach ($langToContentMap as $lang => $content) {

            $resultNumMatches = preg_match_all('/(\$\{\w+\})/', $content, $matches);

            if ($resultNumMatches === false) {

              doDie("Could not perform PCRE pattern match on \$_SERVER['SERVER_NAME'])", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

            } else if ($resultNumMatches > 0) {

              $variablesFound = true;

            }

            $distinctMatchedVariables = array_unique($matches[0]);

            $distinctMatchedVariableNames = array();

            foreach ($distinctMatchedVariables as $variable) {

              $distinctMatchedVariableName = substr($variable, 2, -1);

              if (!in_array($distinctMatchedVariableName, $contentNames)) {

                doDie("Variable name \$\{$distinctMatchedVariableName\} referenced from content name \"$conentName\" does not match any content name.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

              }

              $distinctMatchedVariableNames[] = $distinctMatchedVariableName;

            }

            for ($i = 0; $i < count($distinctMatchedVariables); $i++) {

              $variableToReplace = $distinctMatchedVariables[$i];

              $variableReplacement = $contentNamesMappedToLangContentMap[$distinctMatchedVariableNames[$i]][$lang];

              $modifiedContent = str_replace($variableToReplace, $variableReplacement, $content);

              $contentNamesMappedToLangContentMap[$contentName][$lang] = $modifiedContent;

            }

          }

        }

        //$variablesFound = false;  // one iteration is enough for now (TODO: DELETEME)

      }

      // process content name attributes

      foreach ($contentNamesMappedToContentNameAttributes as $contentName => $contentNameAttributes) {

        // parse optional maximum number of characters

        $contentNameMaxChars = $contentNameAttributes["maxChars"]; /* null when maxChars attribute is missing from content name tag */

        if (!is_null($contentNameMaxChars)) {

          $contentNameMaxChars = trim($contentNameMaxChars);

          if (count(explode(" ", $contentNameMaxChars)) > 1) doDie("Multiple values found for maxChars attribute in element: " . $contentName->getName() . ", file: " . $contentXMLFileName . ".", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          $contentNameMaxChars = intval($contentNameMaxChars);

          foreach ($contentNamesMappedToLangContentMap[$contentName] as $lang => $langContent) {

            if (strlen($langContent) > $contentNameMaxChars) {

              doDie("Content for " . $contentName->getName() . ", language: " . $lang . ", file: " . $contentXMLFileName . " exceeds maxChars: " . $contentNameMaxChars . ".", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

            }

          }

        }

      }

      return $contentNamesMappedToLangContentMap;

    }

    private function dohtmlentities($string) {

      // ensure quotes are also escaped so that you may also embed the text in attribute values

      return htmlentities($string, ENT_QUOTES, "UTF-8");

    }

    // any associative arrays mapping language codes to content strings
    // defined inside this class as opposed to in a separate XML file follow:
    // (using XML files is preferable in all scenarios)

    /*

    private $contentName1 = array(
      "it" => "Content text it for contentName1 HERE",
      "en" => "Content text en for contentName1 HERE",
    );

    private $contentName2 = array(
      "it" => "Content text it for contentName1 HERE",
      "en" => "Content text en for contentName1 HERE",
    );

    // ...

    */

  }

  ////////////////////////////////////////////////////////////////////////////////
  // ensure configuration settings are properly initialized
  ////////////////////////////////////////////////////////////////////////////////

  new ConfigSettings;

  ////////////////////////////////////////////////////////////////////////////////
  // page settings and contents objects
  ////////////////////////////////////////////////////////////////////////////////

  $pageSettings = new PageSettings;

  $pageContents = new PageContents($pageSettings);

?>

<?php
/**
 * This is the include file that defines utility functions used on www.neilzanella.com.
 * Many of these are invoked from settings.php.
 *
 * @package NeilGlenZanellaWebSite
 */

  /*
   * Stop execution and output an error message to the browser (ensure this function is called after HTTP headers have been sent).
   */

  function doDie($message, $paramFile = null, $paramLine = null, $paramClass = null, $paramMethod = null, $paramFunction = null) {

    die("\n<br />Message: " . $message
      . "\n<br />File: "  . $paramFile
      . "\n<br />Line: " . $paramLine
      . "\n<br />Class: " . $paramClass
      . "\n<br />Method: " . $paramMethod
      . "\n<br />Function: " . $paramFunction . "\n\n");

  }

  /*
   * @internal
   * @return array The array of user-preferred languages.
   */

  function getPreferredLangArray() {

    if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {

      $fields = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);

    } else {

      $fields = array();

    }

    for ($i = 0; $i < count($fields); $i++) {

      $fields[$i] = substr($fields[$i], 0, 2);

    }

    return array_unique($fields);

  }

  /*
   * Return those preferred languages which happen to be supported on the site.
   * 
   * @internal
   * @param array $supportedLangsArray The array of languages that are supported on the site.
   * @return array
   */

  function getSupportedPreferredLangArray($supportedLangsArray) {

    $preferredLangArray = getPreferredLangArray();

    $supportedPreferredLangArray = array();

    foreach ($preferredLangArray as $preferredLang) {

      if (in_array($preferredLang, ConfigSettings::$supportedLangsArray)) {

        $supportedPreferredLangArray[] = $preferredLang;

      }

    }

    return $supportedPreferredLangArray;

  }

  /*
   * This function is used inside the PageContents class to set up a fallback mechanism
   * so that the interface tries to display text in the topmost language in which the
   * text is made available inside the PageContents class.
   * 
   * @return array The array of fallback languages supported on the site in the best correct order.
   */

  function getPageURLLangThenSupportedPreferredThenDefaultThenOtherLangArray($pageURLLang, $supportedLangsArray, $defaultLang) {

    $fallbackLanguageArray = getSupportedPreferredThenDefaultThenOtherLangArray(ConfigSettings::$supportedLangsArray, ConfigSettings::$defaultLang);

    if (!is_null($pageURLLang)) {

      // remove $pageURLLang from $fallbackLanguageArray if (and wherever) present

      $fallbackLanguageArray = array_diff($fallbackLanguageArray, array($pageURLLang));

      // append $pageURLLang at the beginning of $fallbackLanguageArray

      array_unshift($fallbackLanguageArray, $pageURLLang);

    }

    return $fallbackLanguageArray;

  }

  /*
   * @internal
   * @return array The array of fallback languages (excluding the language specified in the page URL).
   */

  function getSupportedPreferredThenDefaultThenOtherLangArray($supportedLangsArray, $defaultLang) {

    $fallbackLanguageArray = getSupportedPreferredLangArray($supportedLangsArray);

    if (!in_array($defaultLang, $fallbackLanguageArray)) {

      $fallbackLanguageArray[] = $defaultLang;

    }

    foreach (ConfigSettings::$supportedLangsArray as $supportedLang) {

      if (strcmp($supportedLang, $defaultLang) != 0 && !in_array($supportedLang, $fallbackLanguageArray)) {

        $fallbackLanguageArray[] = $supportedLang;

      }

    }

    return $fallbackLanguageArray;

  }

  /*
   * This function is used to set the HTML lang attribute for the top-level html tag.
   * 
   * @param array $supportedLangsArray The array of languages that are supported on the site.
   * @param string $defaultLang The default language that is supported on the site.
   * @return string The user-preferred language or the default language.
   */

  function getPreferredLangOrDefault($supportedLangsArray, $defaultLang) {

    $langArray = getPreferredLangArray();

    for ($i = 0; $i < count($langArray); $i++) {

      if (in_array($langArray[$i], ConfigSettings::$supportedLangsArray)) {

        return $langArray[$i];

      }

    }

    return $defaultLang;

  }

  /*
   * @return string The canonical URL for the site (which can also be used as the base URL of an image URL).
   */

  function getCanonicalURL() {

    // TODO: deal with ConfigSettings::$urlScheme as the $_SERVER['SERVER_NAME'] part of the canonical URL could have one of the forms: neilzanella.com, www.neilzanella.com, or xx.neilzanella.com where xx is the language code of a language supported by the site

    $canonicalURL = "http://" . $_SERVER['SERVER_NAME'] . "/"; /* ensure slash is last character for concatenation to work properly in og:image and other places */

      if (DebugSettings::$debug) $canonicalURL .= DebugSettings::$debugPathAppendedToCanonicalURL . "/";

      return $canonicalURL;

  }

  /*
   * @return string The canonical URL for the site localized for social sharing according to the specified language.
   */

  function getLocalizedCanonicalURL($langCode) {

    switch (ConfigSettings::$urlScheme) {

      case ConfigSettings::URL_SCHEME_DOMAIN:

        doDie("Untested!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        // $_SERVER['SERVER_NAME'] could have one of the forms: neilzanella.com, www.neilzanella.com, xx.eilzanella.com where xx is the language code of a language supported by the site
        // the forms localhost and n.n.n.n should have already been ruled out by program logic in ConfigSettings::getURLLangScheme().

        $domainNameFields = explode(".", $_SERVER['SERVER_NAME']);

        $screenedBottomLevelDomainNameFields = array_merge(array("www"), ConfigSettings::$supportedLangsArray);

        $doReplaceBottomLevelDomain = in_array($domainNameFields[0], $screenedBottomLevelDomainNameFields);

        if ($doReplaceBottomLevelDomain) {

          $localizedCanonicalURLFields = array_merge(array("$langCode"), array_slice($domainNameFields, 1));

        } else {

          $localizedCanonicalURLFields = array_merge(array($langCode), $domainNameFields);

        }

        return implode(".", $localizedCanonicalURLFields);

        break; /* defensive coding */

      case ConfigSettings::URL_SCHEME_DIRECTORY:

        return getCanonicalURL() . $langCode . "/";

        break; /* defensive coding */

      case ConfigSettings::URL_SCHEME_QUERYSTRING:

        return getCanonicalURL() . "?lang=" . $langCode;

        break; /* defensive coding */

      default:

        doDie("ConfigSettings::\$urlScheme is set to an invalid value.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        break; /* defensive coding */

    }

    doDie("ConfigSettings::\$urlScheme is set to an invalid value.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

  }

  /*
   * @return string The default URL for the site.
   */

  function getDefaultURL() {

    return getLocalizedCanonicalURL(ConfigSettings::$defaultLang);

  }

  /*
   * @param string $langCode The language of the content displayed by the returned URL.
   * @param string $currentPathToPageArray The array of menu tree nodes leading to the current page or null if the menu tree nodes portion of the URL is not to be included.
   * @return string The URL used to serve the current page with its contents displayed in the specified language.
   */

  function getCurrentPageURL($langCode, $currentPathToPageArray = null) {

    // retrieve the canonical URL which may include an appended debug path

    $canonicalURL = getCanonicalURL();

    // retrieve page language and multi-level menu query string components

    $queryStringFieldArray = explode('&', $_SERVER['QUERY_STRING']);

    $queryStringLanguageFields = preg_grep("/^lang=(\w+)/", $queryStringFieldArray);

    // check that a language code was specified as part of the query string

    if (!empty($queryStringLanguageFields)) {

      // check that no more than one language code was specified as part of the query string

      if (count($queryStringLanguageFields) > 1) {

        doDie("Multiple query string language fields encountered!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      }

      // retrieve the page language

      $key = array_keys($queryStringLanguageFields)[0];

      $queryStringLanguageCode = substr($queryStringLanguageFields[$key], strpos($queryStringLanguageFields[$key], '=') + 1);

      $pageLangCode = $queryStringLanguageCode;

    } else {

      // no page language code was specified in the URL so set it to the passed parameter

      $pageLangCode = $langCode;

    }

    // check whether we also need to process the current path to page

    if (!is_null($currentPathToPageArray))  {

      // retrieve multi-level menu components

      $queryStringSlugNames = array();

      for ($i = 1; $i <= count($queryStringFieldArray); $i++) {

        $queryStringSlugFields = preg_grep("/^menu$i=(\w+)/", $queryStringFieldArray);

        if (!empty($queryStringSlugFields)) {

          if (count($queryStringLanguageFields) > 1) {

            doDie("Multiple menu$i query string field values encountered!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          }

          $key = array_keys($queryStringSlugFields)[0];

          $queryStringSlugName = substr($queryStringSlugFields[$key], strpos($queryStringSlugFields[$key], '=') + 1);

          $queryStringSlugNames[$i] = convertCurrentPageLevelSlugForLanguage($pageLangCode, $queryStringSlugName, $key, $currentPathToPageArray);

        }

      }

      // trim multi-level menu components if a gap is found (e.g. menu1=a, menu2=b, menu4=c becomes menu1=a, menu2=b)

      $retrievedQueryStringSlugNames = $queryStringSlugNames;

      $queryStringSlugNames = array();

      for ($i = 1; $i <= count($retrievedQueryStringSlugNames); $i++) {

        if (array_key_exists($i, $retrievedQueryStringSlugNames)) {

          $queryStringSlugNames[$i] = $retrievedQueryStringSlugNames[$i];

        } else {

          break;

        }

      }

      // check whether the language code for the page to be served is different from the current page language code

      if (strcmp($langCode, $pageLangCode) != 0) {

        // convert the query string slug names according to the specified language

        foreach ($queryStringSlugNames as $menuLevel => $queryStringSlugName) {

           $queryStringSlugNames[$menuLevel] = $currentPathToPageArray[$menuLevel]->getSlugLangCodeToSlugNameMap()[$langCode];

        }

      }

    }

    switch (ConfigSettings::$urlScheme) {

      case ConfigSettings::URL_SCHEME_DOMAIN:

        doDie("Unimplemented!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        $pageURL = null; // change me

        break;

      case ConfigSettings::URL_SCHEME_DIRECTORY: // in this case the URL is rewritten 

        $pageURL = $canonicalURL . $langCode;

        if (!is_null($currentPathToPageArray))  {

          foreach ($queryStringSlugNames as $queryStringSlugName) {

            $pageURL .= "/" . $queryStringSlugName;

          }

        }

        break;

      case ConfigSettings::URL_SCHEME_QUERYSTRING:

        $pageURL = $canonicalURL . "?lang=" . $langCode;

        if (!is_null($currentPathToPageArray))  {

          foreach ($queryStringSlugNames as $key => $queryStringSlugName) {

            $pageURL .= "&menu" . $key . "=" . $queryStringSlugName;

          }

        }

        break;

      default:

        doDie("ConfigSettings::\$urlScheme is set to an invalid value.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        break; /* defensive coding */

    }

    return $pageURL;

  }

  function convertCurrentPageLevelSlugForLanguage($languageCode, $slugName, $slugLevel, $currentPathToPageArray) {

    return $currentPathToPageArray[$slugLevel]->getSlugNameByLanguage($languageCode);

  }

  /*
   * Set a cookie and set it to expire at the maximum expiration date: 2038-01-19 04:14:07.
   * @param string $cookieName The cookie name stored alongside the site domain in the browser cache.
   * @param string $cookieValue The cookie value.
   */

  function setPermanentCookie($cookieName, $cookieValue) {

    setcookie($cookieName, $cookieValue, 2147483647 /* maximum expiration time value */);

  }

  /*
   * @param array $supportedLangsArray The array of languages that are supported on the site (corresponding to the country flags in the page header).
   * @param string $defaultLang The default language that is supported on the site (used when URL does not include a language).
   * @param string $pageURLLang The language found in the page URL (which must be set to null if the language is missing from the URL).
   * @return string The language used to display the site content.
   */

  function getDisplayLangAndSetLangCookie($supportedLangsArray, $defaultLang, $pageURLLang = null) {

    // check whether the URL used to access the page does not specify a language

    if (is_null($pageURLLang)) {

      // check whether the user accepted a cookie for remembering the preferred language on a previous visit

      if (isset($_COOKIE["lastVisitLang"])) {

        // retrieve cookie

        $displayLang = $_COOKIE["lastVisitLang"];

      } else {

        // retrieve preferred language based on browser preferences

        $displayLang = getPreferredLangOrDefault($supportedLangsArray, $defaultLang);

        // set cookie

        setPermanentCookie("lastVisitLang", $displayLang);

      }

      return $displayLang;

    } else {

      // the site URL includes a language

      // check whether the "lastVisitLang" cookie is present (as user may have deleted the cookie from the browser options)

      if (isset($_COOKIE["lastVisitLang"])) {

        // check whether the cookie language is different from the language specified in the URL else no need to send the cookie in the HTTP response

        if (strcmp($_COOKIE["lastVisitLang"], $pageURLLang) != 0) {

          // no cookie found in the browser so set cookie

          setPermanentCookie("lastVisitLang", $pageURLLang);

        }

      } else {

        // no cookie found in the browser so set cookie

        setPermanentCookie("lastVisitLang", $pageURLLang);

      }

      return $pageURLLang;

    }

  }

  /*
   * @param string $debugPageLanguageApacheModRewriteEnabled Whether Apache ModRewrite is being used in the site URLs.
   * @param array $supportedLangsArray The array of languages that are supported on the site.
   * @return string The language included in the current page URL or null.
   */

  function getPageURLLanguage($debugPageLanguageApacheModRewriteEnabled, $supportedLangsArray) {

    $pageLangCode = null;

    // since everything is rewritten as a query string we do not need to deal with ConfigSettings:$urlScheme.

    $queryStringFieldArray = explode('&', $_SERVER['QUERY_STRING']);

    $queryStringLanguageFields = preg_grep("/^lang=(\w+)/", $queryStringFieldArray);

    // check that $queryStringLanguageFields is not the empty array (i.e. a language code was specified in the URL)

    if (!empty($queryStringLanguageFields)) {

      $key = array_keys($queryStringLanguageFields)[0];

      $queryStringLanguageCode = substr($queryStringLanguageFields[$key], strpos($queryStringLanguageFields[$key], '=') + 1);

      $pageLangCode = $queryStringLanguageCode;

    }

    // check that the language code from the URL is valid and supported at the same time

    if (!in_array($pageLangCode, $supportedLangsArray)) {

      return null;

    }

    return $pageLangCode;

  }

  /*
   * 
   */

  function getPageURLMenuSlugs() {

    switch (ConfigSettings::$urlScheme) {

      case ConfigSettings::URL_SCHEME_DOMAIN:

        doDie("Untested!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        break; /* defensive coding */

      case ConfigSettings::URL_SCHEME_DIRECTORY:

        // since in this case Apache rewrites the URL using query strings, fall through to the following case

      case ConfigSettings::URL_SCHEME_QUERYSTRING:

        $queryStringFieldArray = explode('&', $_SERVER['QUERY_STRING']);

        $queryStringMenuEntryRegExp = "/^menu(\d+)=((\w|-)*)/";

        $queryStringMenuEntryFields = preg_grep($queryStringMenuEntryRegExp, $queryStringFieldArray);

        $localizedMenuSlugs = array();

        foreach ($queryStringMenuEntryFields as $queryStringMenuEntryField) {

          $menuEntryMatches = array();

          $matchResult = preg_match($queryStringMenuEntryRegExp, $queryStringMenuEntryField, $menuEntryMatches);

          if ($matchResult === false) {

            doDie("Error encountered while matching menu entry query string field!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          }

          $localizedMenuSlugDepth = $menuEntryMatches[1];

          $localizedMenuSlugName = $menuEntryMatches[2];

          $localizedMenuSlugs[$localizedMenuSlugDepth] = $localizedMenuSlugName;

        }

        return $localizedMenuSlugs;

        break; /* defensive coding */

      default:

        doDie("ConfigSettings::\$urlScheme is set to an invalid value.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

        break; /* defensive coding */

    }

  }

  class Utility {

    public static function arrayHasDuplicateValues($array) {

      $valueTrackingArray = array();

      foreach ($array as $value) {

        if (in_array($value, $valueTrackingArray, true)) {

          return true;

        }

        $valueTrackingArray[] = $value;

      }

      return false;

    }

  }

?>

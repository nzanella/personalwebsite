<?php

// This file is included in ../settings.php.

require_once("examplecode/includes/treeNode-v6.php");

require_once("settings.php");

class MenuTreeToPagesData {

  private $rootMenuTreeNode;

  private $currentPathToPageArray;

  const MENU_TREE_NODES_XML_FILE = "menu/menuTreeNodes.xml";

  public function __construct($pageSettings) {

    $menuTreeNodeReader = new MenuTreeNodeReader(self::MENU_TREE_NODES_XML_FILE);

    $this->rootMenuTreeNode = $menuTreeNodeReader->getRootNode();

    $this->setCurrentPathToPageArray($pageSettings);

  }

  public function setCurrentPathToPageArray($pageSettings) {

    $this->currentPathToPageArray = array();

    $currentMenuTreeNode = $this->rootMenuTreeNode;

    // retrieve array of menu levels to localized menu slugs

    $pageURLMenuSlugs = getPageURLMenuSlugs();

    // retrieve the language for the menu entries to be displayed

    $pagePrimaryLanguage = $pageSettings->getPrimaryLanguage();

    // check whether no page URL menu slugs appear as part of the URL

    if (empty($pageURLMenuSlugs)) {

      // amend the page URL menu slugs with default values

      for ($i = 0; $currentMenuTreeNode->hasChildren(); $i++) {

        $currentMenuTreeNodeChild = $currentMenuTreeNode->getChildByIndex(0);

        $pageURLMenuSlugs[$i + 1] = $currentMenuTreeNodeChild->getSlugLangCodeToSlugNameMap()[$pagePrimaryLanguage];

        $currentMenuTreeNode = $currentMenuTreeNodeChild;

      }

    }

    // reset current menu tree node to root menu tree node

    $currentMenuTreeNode = $this->rootMenuTreeNode;

    // set the path of menu entries to current page

    for ($menuLevel = 1; $currentMenuTreeNode->hasChildren(); $menuLevel++) {

      $currentMenuTreeNodeChildren = $currentMenuTreeNode->getChildren();

      $found = false;

      foreach ($currentMenuTreeNodeChildren as $currentMenuTreeNodeChild) {

        $currentSlugName = $currentMenuTreeNodeChild->getSlugLangCodeToSlugNameMap()[$pagePrimaryLanguage];

        if (strcmp($currentSlugName, $pageURLMenuSlugs[$menuLevel]) == 0) {

          $found = true;

          $this->currentPathToPageArray[$menuLevel] = $currentMenuTreeNodeChild;

          $currentMenuTreeNode = $currentMenuTreeNodeChild;

          break;

        }

      }

      if (!$found) {

        var_dump(getPageURLMenuSlugs());

        doDie("Menu tree node corresponding to slug $currentSlugName not found!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

      }

    }

  }

  public function getRootMenuTreeNode() {

    return $this->rootMenuTreeNode;
 
  }

  public function getCurrentPathToPageArray() {

    return $this->currentPathToPageArray;

  }

  public function showCurrentPathToPageNames() {

    $names = "NAMES: ";

    for ($i = 1; $i < count($this->currentPathToPageArray); $i++) {

      $names .= "Name " . $i . ": " . $this->currentPathToPageArray[$i];

    }

    echo $names;

  }

  public function getMenuDepth() {

    return $this->rootMenuTreeNode->getTreeMaxLevel();

  }

}

class MenuTreeNode extends TreeNode {

  private $parent;

  // create parentless root node

  public static function createRootNode() {

    return new static("menu", array(), array(), null);

  }

  // create node with name and parent

  public static function createNodeWithNameAndParent($name, $parent) {

    return new static($name, array(), array(), $parent);

  }

  public function getRootMenuTreeNode() {

    $current = $this;

    while (!is_null($current->parent)) {

      $current = $current->parent;

    }

    return $current;

  }

  public function setParent($parent) {

    static::checkMenuTreeNode($parent);

    $this->parent = $parent;

  }

  public function getParent() {

    return $this->parent;

  }

  // overload this method to set the parent when adding a child node

  public function addChild($childTreeNode) {

    $childTreeNode->setParent($this);

    parent::addChild($childTreeNode);

  }

  private static function checkMenuTreeNode($treeNode) {

    // check whether menu tree node is not null or is not a class instance

    if (!is_null($treeNode) and !is_a($treeNode, __CLASS__)) {

      die("Menu tree node or null expected.");

    }

  }

  public function __construct($name, $value, $children, $parent) {

    parent::__construct($name, $value, $children);

    $this->setParent($parent);

  }

  // set array of language codes to slug names as value

  public function setValue($value) {

    self::checkTreeNodeValue($value);

    $this->value = $value;

  }

  public function getSlugLangCodeToSlugNameMap() {

    return $this->value;

  }

  public function getSlugNameByLanguage($slugLangCode) {

    return $this->getValue()[$slugLangCode];

  }

  public function addSlug($slugLangCode, $slugName) {

    $this->value[$slugLangCode] = $slugName;

  }

  public static function checkTreeNodeValue($value) {

    if (!is_null($value) and !is_array($value)) {

      die("Was expecting an array value for node value.");

    }

  }

  public function getPath() {

    $pathExcludingRootNode = array();

    $currentMenuTreeNode = $this;

    while (!is_null($currentMenuTreeNode->parent)) {

      array_unshift($pathExcludingRootNode, $currentMenuTreeNode);

      $currentMenuTreeNode = $currentMenuTreeNode->parent;

    }

    return $pathExcludingRootNode;

  }

  public function getPathToPage() {

    $path = $this->getPath();

    if (count($this->getPath()) > 0) {

      $currentMenuTreeNode = $path[count($path) - 1];

    } else {

      $currentMenuTreeNode = $this->getRootMenuTreeNode();

    }

    while ($currentMenuTreeNode->hasChildren()) {

      $nextMenuTreeNode = $currentMenuTreeNode->getChildByIndex(0);

      array_push($path, $nextMenuTreeNode);

      $currentMenuTreeNode = $nextMenuTreeNode;

    }

    return $path;

  }

  public function getPageLink($lang = null) {

    global $pageSettings;

    $path = $this->getPathToPage();

    if (is_null($lang)) {

      $lang = $pageSettings->getPrimaryLanguage();

    }

    $pageURL = $pageSettings->localizedCanonicalPageURLArray[$lang];

    for ($i = 1; $i <= count($path); $i++) {

      $slugName = $path[$i - 1]->getSlugNameByLanguage($lang);

      switch (ConfigSettings::$urlScheme) {

        case ConfigSettings::URL_SCHEME_DOMAIN:

          doDie("Untested!", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          break;

        case ConfigSettings::URL_SCHEME_DIRECTORY:

          $pageURL .= "/" . $slugName;

          break;

        case ConfigSettings::URL_SCHEME_QUERYSTRING:

          $pageURL .= "&menu" . $i . "=" . $slugName;

          break;

        default:

          doDie("ConfigSettings::\$urlScheme is set to an invalid value.", __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__);

          break; /* defensive coding */

      }

    }

    return $pageURL;

  }

  public function getMenuContents() {

    global $pageContents;

    return $pageContents->getContents($this->getContentName());

  }

  public function getContentName() {

    $path = $this->getPath();

    if (count($path) == 0) {

      return null;

    }

    $contentName = "pageMenuItem";

    for ($i = 0; $i < count($path); $i++) {

      $contentName .= "_" . $path[$i]->getName();

    }

    return $contentName;

  }

  public function getPageName() {

    $path = $this->getPathToPage();

    if (count($path) == 0) {

      return null;

    }

    $pageName = "page";

     for ($i = 0; $i < count($path); $i++) {

      $pageName .= "-" . $path[$i]->getName();

    }

    $pageName .= ".php";

    return $pageName;  

  }

  public function __toString() {

    $returnString = "Node Name: " . $this->name . "; Node Value:";

    foreach ($this->value as $slugLangCode => $slugName) {

       $returnString .= " [$slugLangCode, $slugName]";

    }

    $returnString .= "; Children count: " . (is_null($this->children) ? 0 : count($this->children));

    $returnString .= "; Parent name: " . (is_null($this->parent) ? "none" : $this->parent->getName()) . ".\n";

    return $returnString;

  }

}

class MenuTreeNodeReader /* implements TreeReader */ {

  private $xmlFileName;

  private $hasTreeBeenParsed;

  private $rootNode;

  const ROOT_XML_ELEMENT_NAME = "menu";

  const SLUG_ELEMENT_PREFIX = "slug_";

  const SLUG_LANG_CODE_NUM_CHARS = 2;

  public function __construct($xmlFileName) {

    $this->setXMLFileName($xmlFileName);

    $this->hasTreeBeenParsed = false;

    $this->rootNode = null; /* defensive coding */

  }

  /* methods from TreeReader interface (and their helper methods) */

  public function getRootNode() {

    if (!$this->hasTreeBeenParsed) {

      $this->doParseTree();

      $this->hasTreeBeenParsed = true;

    }

    return $this->rootNode;

  }

  private function doParseTree() {

    // parse XML file and throw an exception if the file could not be parsed

    $currentInputSimpleXMLIterator = new SimpleXMLIterator($this->xmlFileName, 0, true);

    // create the root MenuTreeNode instance

    $rootMenuTreeNode = MenuTreeNode::createRootNode();

    // create the output menu tree node iterator to be used to populate our output menu tree structure

    $outputMenuTreeNodeIterator = new TreeNodeIterator($rootMenuTreeNode, TreeNodeIterator::TRAVERSAL_MODE_BFS);

    // iterate in a BFS-wise manner over the XML elements of the input XML file

    $nonTraversedInputSimpleXMLIterators = array($currentInputSimpleXMLIterator);

    while (count($nonTraversedInputSimpleXMLIterators) > 0) {

      $currentInputSimpleXMLIterator = array_shift($nonTraversedInputSimpleXMLIterators);

      for ($currentInputSimpleXMLIterator->rewind(); $currentInputSimpleXMLIterator->valid(); $currentInputSimpleXMLIterator->next()) {

        if ($currentInputSimpleXMLIterator->hasChildren()) {

          $childSimpleXMLIterator = $currentInputSimpleXMLIterator->getChildren();

          array_push($nonTraversedInputSimpleXMLIterators, $childSimpleXMLIterator);

        }

        self::convertChildInputToParentOutput($outputMenuTreeNodeIterator, $currentInputSimpleXMLIterator);

      }

      // advance output iterator to next node

      $outputMenuTreeNodeIterator->next();

    }

    return $this->rootNode = $rootMenuTreeNode;

  }

  private static function convertChildInputToParentOutput($outputMenuTreeNodeIterator, $inputSimpleXMLIterator) {

    // initialize the current menu tree node parent to what the output iterator is pointing to

    $parentOutputMenuTreeNode = $outputMenuTreeNodeIterator->current();

    // retrieve the child XML element name

    $childXMLElementName = $inputSimpleXMLIterator->current()->getName();

    // check whether child XML element is a slug for a parent entry

    if (strncmp($childXMLElementName, self::SLUG_ELEMENT_PREFIX, strlen(self::SLUG_ELEMENT_PREFIX)) == 0) {

      // make sure that root XML element does not have slugs

      if ($outputMenuTreeNodeIterator->key()->getNodeLevel() == 0) {

        die("XML root node does not represent a physical menu entry so cannot have any associated language slugs.");

      }

      // make sure that the encountered slug XML element is the correct number of characters long

      if (strlen($childXMLElementName) != strlen(self::SLUG_ELEMENT_PREFIX) + self::SLUG_LANG_CODE_NUM_CHARS) {

        die("Encountered slug length is different from " . strlen(self::SLUG_ELEMENT_PREFIX) + self::SLUG_LANG_CODE_NUM_CHARS . ".");

      }

      // retrieve slug language code

      $slugLangCode = substr($childXMLElementName, strlen(self::SLUG_ELEMENT_PREFIX), self::SLUG_LANG_CODE_NUM_CHARS);

      // retrieve slug name

      $slugName = $inputSimpleXMLIterator->current()->__toString();

      // check that slug language code does not already exist for current output menu tree node

      if (array_key_exists($slugLangCode, $parentOutputMenuTreeNode->getSlugLangCodeToSlugNameMap())) {

        die("Repeated slug language code encountered for menu tree node.");

      }

      // append slug lanuage to slug name map to current output menu tree node array of slug language to slug name maps

      $parentOutputMenuTreeNode->addSlug($slugLangCode, $slugName);

    } else { // current element is not a slug so add it to the list of nonslug child XML elements

       // create menu tree node from simple XML element

       $childMenuTreeNode = MenuTreeNode::createNodeWithNameAndParent($childXMLElementName, $parentOutputMenuTreeNode);

       // add child (note that since the addChild method is overloaded the parent will also be set here

       $outputMenuTreeNodeIterator->addChild($childMenuTreeNode);

    }

  }

  /* other methods */

  private function setXMLFileName($xmlFileName) {

    self::checkFileName($xmlFileName);

    $this->xmlFileName = $xmlFileName;

  }

  private static function checkFileName($xmlFileName) {

    if (!is_string($xmlFileName)) {

      die("XML file name must be a string.");

    }

    if (!file_exists($xmlFileName)) {

      die("XML file name does not exist.");

    }

  }

}
/*
function test() {

  $menuTreeNodeReader = new MenuTreeNodeReader("menuTreeNodes.xml");

  $rootMenuTreeNode = $menuTreeNodeReader->getRootNode();

  $rootMenuTreeNode->showFullTree();

}

test();
*/

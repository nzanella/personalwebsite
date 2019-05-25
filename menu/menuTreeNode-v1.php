<?php

require_once("../examplecode/includes/treeNode-v6.php");

class MenuTreeNode extends TreeNode {

  public static function createRootNode() {

    return new static(null, array(), array());

  }

  public static function createNodeWithName($name) {

    return new static($name, array(), array());

  }

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

  public function __toString() {

    $returnString = "Node Name: " . $this->name . "; Node Value:";

    foreach($this->value as $slugLangCode => $slugName) {

       $returnString .= " [$slugLangCode, $slugName]";

    }

    $returnString .= "; Children count: " . (is_null($this->children) ? 0 : count($this->children)) . ".\n";

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

       $childMenuTreeNode = MenuTreeNode::createNodeWithName($childXMLElementName);

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

function test() {

  $menuTreeNodeReader = new MenuTreeNodeReader("menuTreeNodes.xml");

  $rootMenuTreeNode = $menuTreeNodeReader->getRootNode();

  $rootMenuTreeNode->showFullTree();

}

test();

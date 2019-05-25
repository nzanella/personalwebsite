<?php
/**
 * Tree nodes including nameless non-leaf node used to construct nonempty nontrivial tree root node,
 * named non-leaf node with children, and leaf node with name and value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v2
 */

class TreeNode {

  private $name;

  private $value;

  private $children;

  public static function createNamelessNonLeafNode($children) {

    return new TreeNode(null, null, $children);

  }

  public static function createNonLeafNode($name, $children) {

    return new TreeNode($name, null, $children);

  }

  public static function createLeafNode($name, $value) {

    return new TreeNode($name, $value, array());

  }

  private function __construct($name, $value, $children) {

    $this->setName($name);

    $this->setValue($value);

    $this->setChildren($children);

  }

  public function __toString() {

    return "Node Name: " . $this->name . "; Node Value: " . $this->value . "; Children count: " . (is_null($this->children) ? 0 : count($this->children)) . ".\n";

  }

  public function getName() {

    return $this->name;

  }

  public function setName($name) {

   if (!is_null($name) and !is_string($name)) {

     die("Was expecting a string value for node name or null for a nameless node.");

   }

   $this->name = $name;

  }

  public function getValue() {

    return $this->value;

  }

  public function setValue($value) {

   if (!is_null($value) and !is_string($value)) {

     die("Was expecting a string value for node value.");

   }

   $this->value = $value;

  }

  public function hasChildren() {

    return count($this->children) > 0;

  }

  public function getChildren() {

    return $this->children;

  }

  public function setChildren($children) {

    $this->children = array();

    $this->addChildren($children);

  }

  public function setChildrenArgs() {

    $this->setChildren(func_get_args());

  }

  public function addChildren($children) {

    if (!is_array($children)) {

      die("Was expecting an array of child nodes.");

    }

    foreach ($children as $child) {

      $this->addChild($child);

    }

  }

  public function addChildrenArgs() {

    foreach (func_get_args() as $arg) {

      $this->addChild($arg);

    }

  }

  public function addChild($child) {

    $this->checkCanInsertChild($child);

    $this->children[] = $child;

  }

  private function checkCanInsertChild($child) {

    if (!is_a($child, __CLASS__)) {

      die("Was expecting an object of class " . __CLASS__ . ".");

    }

    foreach ($this->children as $currentChild) {

      if (strcmp($currentChild->getName(), $child->getName()) == 0) {

        die("Inserting child node would result in duplicate child name among children.");

      }

    }

  }

  public function getChildByName($name) {

    if (!is_string($name)) {

      die("Was expecting a string for child node name.");

    }

    foreach ($this->getChildren() as $child) {

      if (strcmp($child->getName(), $name) == 0) {

        return $child;

      }

    }

    return null;

  }

  public function showFullTree() {

    $rootNodeDFSIterator = new TreeNodeDFSIterator($this);

    foreach ($rootNodeDFSIterator as $nodeIndex => $node) {

      echo $nodeIndex, ". ", $node;

    }

  }

}

/**
 * Iterator enabling DFS tree traversal.
 */

class TreeNodeDFSIterator implements Iterator {

  private $rootNode;

  private $nodesToVisit;

  private $currentNodeIndex;

  function __construct($rootNode) {

    $this->rootNode = $rootNode;

    $this->rewind();

  }

  /* methods from Iterator interface */

  public function current() {

    return $this->nodesToVisit[0];

  }

  public function key() {

    return $this->currentNodeIndex;

  }

  public function next() {

    // forget current node

    $visitedNode = array_shift($this->nodesToVisit);

    // remember to visit children first

    $this->nodesToVisit = array_merge($visitedNode->getChildren(), $this->nodesToVisit);

    // check whether we have a current (first) node to visit and update current node index accordingly

    if (count($this->nodesToVisit) > 0) {

      ++$this->currentNodeIndex;

    } else {

      $this->currentNodeIndex = null;

    }

  }

  public function rewind() {

    $this->nodesToVisit = array();

    $this->currentNodeIndex = null;

    if (!is_null($this->rootNode)) {

      array_push($this->nodesToVisit, $this->rootNode);

      $this->currentNodeIndex = 0;

    }

  }

  public function valid() {

    return !is_null($this->currentNodeIndex);

  }

}

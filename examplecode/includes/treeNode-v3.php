<?php
/**
 * Tree nodes including nameless non-leaf node used to construct nonempty nontrivial tree root node,
 * named non-leaf node with children, and leaf node with name and value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v3
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

    foreach ($rootNodeDFSIterator as $nodeKey => $node) {

      echo "Index: ", $nodeKey->getNodeIndex(), ". Level: ", $nodeKey->getNodeLevel(), ". ";

      for ($i = 0; $i < $nodeKey->getNodeLevel(); $i++) {

        echo "  ";

      }

      echo $node;

    }

  }

}

/**
 * Iterator enabling DFS tree traversal.
 */

/**
 * Iterator enabling DFS tree traversal.
 */

class TreeNodeDFSIterator implements Iterator {

  private $rootNode;

  private $nodesToVisit;

  private $nodesToVisitLevels;

  private $currentNodeIteratorKey;

  function __construct($rootNode) {

    $this->rootNode = $rootNode;

    $this->rewind();

  }

  /* methods from Iterator interface */

  public function current() {

    return $this->nodesToVisit[0];

  }

  public function key() {

    return $this->currentNodeIteratorKey;

  }

  public function next() {

    // pop current node

    $visitedNode = array_shift($this->nodesToVisit);

    // pop current level

    $visitedNodeLevel = array_shift($this->nodesToVisitLevels);

    // retrieve visited node children

    $visitedNodeChildren = $visitedNode->getChildren();

    // remember to visit children first (DFS)

    $this->nodesToVisit = array_merge($visitedNodeChildren, $this->nodesToVisit);

    // keep track of level of nodes to visit

    if (count($visitedNodeChildren) > 0) {

      $visitedNodeChildrenLevel = $visitedNodeLevel + 1;

      $visitedNodeChildrenLevels = array_fill(0, count($visitedNodeChildren), $visitedNodeChildrenLevel);

      // remember to insert levelts to visit at the front since we are visiting children first (DFS)

      $this->nodesToVisitLevels = array_merge($visitedNodeChildrenLevels, $this->nodesToVisitLevels);

    }

    // check whether we have a current (first in nodes to visit array) node to visit and update current node index accordingly

    if (count($this->nodesToVisit) > 0) {

      // increment current node index

      $this->currentNodeIteratorKey->incrementNodeIndex();

      // increment or decrement current node level according to nodes to visit levels

      $this->currentNodeIteratorKey->setNodeLevel($this->nodesToVisitLevels[0]);

    } else {

      $this->currentNodeIteratorKey = null;

    }

  }

  public function rewind() {

    $this->nodesToVisit = array();

    $this->nodesToVisitLevels = array();

    $this->currentNodeIteratorKey = null;

    if (!is_null($this->rootNode)) {

      array_push($this->nodesToVisit, $this->rootNode);

      array_push($this->nodesToVisitLevels, 0);

      $this->currentNodeIteratorKey = new TreeNodeIteratorKey();

      $this->currentNodeIteratorKey->setNodeIndex(0);

      $this->currentNodeIteratorKey->setNodeLevel($this->nodesToVisitLevels[0]);

    }

  }

  public function valid() {

    return !is_null($this->currentNodeIteratorKey);

  }

}

class TreeNodeIteratorKey {

  private $nodeIndex;

  private $nodeLevel;

  function __construct() {

    $this->nodeIndex = null;

    $this->nodeLevel = null;

  }

  public function getNodeIndex() {

    return $this->nodeIndex;

  }

  public function setNodeIndex($nodeIndex) {

    $this->nodeIndex = $nodeIndex;

  }

  public function incrementNodeIndex() {

    $this->checkNodeIndex();

    ++$this->nodeIndex;

  }

  private function checkNodeIndex() {

    if (is_null($this->nodeIndex)) {

      die("Cannot increment null node index.");

    }

  }

  public function getNodeLevel() {

    return $this->nodeLevel;

  }

  public function setNodeLevel($nodeLevel) {

    $this->nodeLevel = $nodeLevel;

  }

  public function incrementNodeLevel() {

    $this->checkNodeLevel();

    ++$this->nodeLevel;

  }

  public function decrementNodeLevel() {

    $this->checkNodeLevel();

    --$this->nodeLevel;

  }

  private function checkNodeLevel() {

    if (is_null($this->nodeLevel)) {

      die("Cannot increment or decrement null node level.");

    }

  } 

}

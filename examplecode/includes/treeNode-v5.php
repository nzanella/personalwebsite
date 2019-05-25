<?php
/**
 * Tree nodes including nameless non-leaf node used to construct nonempty nontrivial tree root node,
 * named non-leaf node with children, and leaf node with name and value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v5
 */

class TreeNode {

  private $name;

  private $value;

  private $children;

  public static function createStubNode() {

    return new TreeNode(null, null, array());

  }

  public static function createNamelessNonleafNode($children) {

    $treeNode = new TreeNode(null, null, $children);

    self::checkTreeNodeHasChildren($treeNode);

    return $treeNode;

  }

  public static function createNonLeafNode($name, $children) {

    $treeNode = new TreeNode($name, null, $children);

    self::checkTreeNodeHasChildren($treeNode);

    return $treeNode;

  }

  public static function createLeafNode($name, $value) {

    $treeNode = new TreeNode($name, $value, array());

    self::checkTreeNodeHasNoChildren($treeNode);

    return $treeNode;

  }

  private function __construct($name, $value, $children) {

    $this->setName($name);

    $this->setValue($value);

    $this->setChildren($children);

  }

  public static function checkTreeNode($treeNode) {

    // check whether tree node is not null and is a class instance

    if (!is_a($treeNode, __CLASS__)) {

      die("Tree node expected.");

    }

  }

  public static function checkTreeNodeHasChildren($treeNode) {

    if (!$treeNode->hasChildren()) {

      die("Was expecting a tree node with children.");

    }

  }

  public static function checkTreeNodeHasNoChildren($treeNode) {

    if ($treeNode->hasChildren()) {

      die("Was expecting a tree node with no children.");

    }

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

  public function __toString() {

    return "Node Name: " . $this->name . "; Node Value: " . $this->value . "; Children count: " . (is_null($this->children) ? 0 : count($this->children)) . ".\n";

  }

  public function showFullTree($traversalMode = TreeNodeIterator::TRAVERSAL_MODE_DFS) {

    $rootNodeIterator = new TreeNodeIterator($this, $traversalMode);

    foreach ($rootNodeIterator as $nodeKey => $node) {

      echo "Index: ", $nodeKey->getNodeIndex(), ". Level: ", $nodeKey->getNodeLevel(), ". ";

      for ($i = 0; $i < $nodeKey->getNodeLevel(); $i++) {

        echo "\t";

      }

      echo $node;

    }

  }

}

/**
 * Iterator enabling BFS or DFS tree traversal (assumes validity of tree nodes has already been checked).
 */

class TreeNodeIterator implements Iterator {

  private $traversalMode;

  private $rootNode;

  private $nodesToVisit;

  private $nodesToVisitLevels;

  private $currentNodeIteratorKey;

  const TRAVERSAL_MODE_BFS = 0;

  const TRAVERSAL_MODE_DFS = 1;

  const NUM_TRAVERSAL_MODES = 2;

  function __construct($rootNode, $traversalMode = self::TRAVERSAL_MODE_DFS) {

    $this->setTraversalMode($traversalMode);

    $this->setRootNode($rootNode);

    $this->rewind();

  }

  private function setTraversalMode($traversalMode) {

    if (!is_integer($traversalMode) or $traversalMode < 0 or $traversalMode > self::NUM_TRAVERSAL_MODES) {

      die("Unsupported traversal mode specified.");

    }

    $this->traversalMode = $traversalMode;

  }

  private function setRootNode($rootNode) {

    $this->rootNode = $rootNode;

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

    // remember to visit visited node children

    switch ($this->traversalMode) {

      case self::TRAVERSAL_MODE_BFS:

        // remember children to be visited last (BFS)

        $this->nodesToVisit = array_merge($this->nodesToVisit, $visitedNodeChildren);

        break;

      case self::TRAVERSAL_MODE_DFS:

        // remember children to be visited first (DFS)

        $this->nodesToVisit = array_merge($visitedNodeChildren, $this->nodesToVisit);

        break;

    }

    // keep track of level of nodes to visit

    if (count($visitedNodeChildren) > 0) {

      $visitedNodeChildrenLevel = $visitedNodeLevel + 1;

      $visitedNodeChildrenLevels = array_fill(0, count($visitedNodeChildren), $visitedNodeChildrenLevel);

      switch ($this->traversalMode) {

        case self::TRAVERSAL_MODE_BFS:

          // remember levels of children to be visited last (BFS)

          $this->nodesToVisitLevels = array_merge($this->nodesToVisitLevels, $visitedNodeChildrenLevels);

          break;

        case self::TRAVERSAL_MODE_DFS:

          // remember levels of children to be visited first (DFS)

          $this->nodesToVisitLevels = array_merge($visitedNodeChildrenLevels, $this->nodesToVisitLevels);

          break;

      }

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

  private $customData;

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

  private function setCustomData($customData) {

    $this->customData = $customData;

  }

  private function getCustomData() {

    return $this->customData;

  }

}

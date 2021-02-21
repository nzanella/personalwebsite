<?php
/**
 * Tree nodes including nameless non-leaf node used to construct nonempty nontrivial tree root node,
 * named non-leaf node with children, and leaf node with name and value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v8 (added factory method for labeled tree)
 */

class TreeNodeFactory {

  public static function createStubNode() {

    return new TreeNode(null, null, array());

  }

  public static function createNamelessNonleafNode($children) {

    $treeNode = new TreeNode(null, null, $children);

    TreeNode::checkTreeNodeHasChildren($treeNode);

    return $treeNode;

  }

  public static function createNonLeafNode($name, $children) {

    $treeNode = new TreeNode($name, null, $children);

    TreeNode::checkTreeNodeHasChildren($treeNode);

    return $treeNode;

  }

  public static function createLeafNode($name, $value) {

    $treeNode = new TreeNode($name, $value, array());

    TreeNode::checkTreeNodeHasNoChildren($treeNode);

    return $treeNode;

  }

}

class TreeNode {

  protected $name;

  protected $value;

  protected $children;

  protected $customData;

  public function __construct($name, $value, $children) {

    $this->setName($name);

    $this->setValue($value);

    $this->setChildren($children);

    $this->setCustomData(null);

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

   self::checkTreeNodeName($name);

   $this->name = $name;

  }

  public static function checkTreeNodeName($name) {

    if (!is_null($name) and !is_string($name)) {

      die("Was expecting a string or null for node name.");

    }

  }

  public static function checkTreeNodeNameNotNull($name) {

    if (!is_string($name)) {

      die("Was expecting a string for node name.");

    }

  }

  public function getValue() {

    return $this->value;

  }

  public function setValue($value) {

   self::checkTreeNodeValue($value);

   $this->value = $value;

  }

  public static function checkTreeNodeValue($value) {

    if (!is_null($value) and !is_string($value)) {

      die("Was expecting a string value for node value.");

    }

  }

  public function hasChildren() {

    return count($this->children) > 0;

  }

  public function getChildren() {

    return $this->children;

  }

  public function getChildCount() {

    return count($this->children);

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

    self::checkTreeNode($child);

    $this->addTreeNodeChild($child);

  }

  protected function addTreeNodeChild($child) {

    $this->checkCanInsertChild($child);

    $this->children[] = $child;

  }

  private function checkCanInsertChild($child) {

    foreach ($this->children as $currentChild) {

      if (strcmp($currentChild->getName(), $child->getName()) == 0) {

        die("Inserting child node would result in duplicate child name among children.");

      }

    }

  }

  public function removeAllChildren() {

    $this->setChildren(array());

  }

  public function hasMatchingChildNode($childNode) {

    foreach ($this->getChildren() as $child) {

      if ($childNode === $child) {

        return true;

      }

    }

    return false;
 
  }

  public function getChildByIndex($childIndex) {

    $children = $this->getChildren();

    $numChildren = count($children);

    if (!is_int($childIndex) or $childIndex < 0 or $childIndex > $numChildren - 1) {

      die("Invalid child index.");

    }

    for ($index = 0; $index < $numChildren; $index++) {

      if ($index == $childIndex) {

        return $children[$index];

      }

    }

  }

  public function getChildByName($name) {

    self::checkTreeNodeNameNotNull($name);

    foreach ($this->getChildren() as $child) {

      if (strcmp($child->getName(), $name) == 0) {

        return $child;

      }

    }

    return null;

  }

  public function getTreeNodeByName($name, $traversalMode = TreeNodeIterator::TRAVERSAL_MODE_BFS) {

    self::checkTreeNodeNameNotNull($name);

    $treeNodeIterator = new TreeNodeIterator($this, $traversalMode);

    foreach ($treeNodeIterator as $nodeKey => $node) {

      if (strcmp($node->getName(), $name) == 0) {

        return $node;

      }

    }

    return null;

  }

  public function getTreeNodePath($treeNode, $traversalMode = TreeNodeIterator::TRAVERSAL_MODE_BFS) {

    self::checkTreeNode($treeNode);

    $treeNodePath = array();

    $previousLevel = -1;

    $treeNodeIterator = new TreeNodeIterator($this, $traversalMode);

    foreach ($treeNodeIterator as $nodeKey => $node) {

      // update partial path to target tree node

      $currentLevel = $treeNodeIterator->key()->getNodeLevel();

      if ($currentLevel > $previousLevel) {

        array_push($treeNodePath, $treeNodeIterator->current());

      } else if ($currentLevel < $previousLevel) {

        array_pop($treeNodePath);

      }

      $previousLevel = $currentLevel;

      if ($treeNodeIterator->current() == $treeNode) {

        return $treeNodePath;

      }

    }

    return null;

  }

  public function setCustomData($customData) {

    $this->customData = $customData;

  }

  public function getCustomData() {

    return $this->customData;

  }

  public function __toString() {

    return "Node Name: " . $this->name . "; Node Value: " . $this->value . "; Children count: " . (is_null($this->children) ? 0 : count($this->children)) . ".\n";

  }

  public function showFullTree($traversalMode = TreeNodeIterator::TRAVERSAL_MODE_DFS) {

    $condition = function($treeNodeIterator) {

      return true;

    };

    $this->showSubTreeWhileNodeIteratorCondition($condition, $traversalMode);

  }

  public function showSubTreeByMaxNodeCount($maxNodeCount, $traversalMode = TreeNodeIterator::TRAVERSAL_MODE_DFS) {

    if (!is_int($maxNodeCount) or $maxNodeCount < 0) {

      die("Non-negative subtree node count expected.");

    }

    $condition = function($treeNodeIterator) use ($maxNodeCount) {

      static $count = 0;

      $countReached = ($count < $maxNodeCount);

      ++$count;

      return $countReached;

    };

    $this->showSubTreeWhileNodeIteratorCondition($condition, $traversalMode);

  }

  public function showSubTreeWhileNodeIteratorCondition($condition, $traversalMode = TreeNodeIterator::TRAVERSAL_MODE_DFS) {

    $treeNodeIterator = new TreeNodeIterator($this, $traversalMode);

    foreach ($treeNodeIterator as $nodeKey => $node) {

      if (!call_user_func($condition, $treeNodeIterator)) {

        break;

      }

      echo "Index: ", $nodeKey->getNodeIndex(), ". Level: ", $nodeKey->getNodeLevel(), ". ";

      for ($i = 0; $i < $nodeKey->getNodeLevel(); $i++) {

        echo "\t";

      }

      echo $node;

    }

  }

}

class ParentAwareTreeNodeFactory {

  public static function createStubNode() {

    return new ParentAwareTreeNode(null, null, array(), null);

  }

  public static function createNamelessNonleafNode($children, $parent) {

    $parentAwareTreeNode = new ParentAwareTreeNode(null, null, $children, $parent);

    TreeNode::checkTreeNodeHasChildren($parentAwareTreeNode);

    ParentAwareTreeNode::checkParentAwareTreeNodeHasNoParent($parentAwareTreeNode);

    return $parentAwareTreeNode;

  }

  public static function createNonLeafNode($name, $children, $parent) {

    $parentAwareTreeNode = new ParentAwareTreeNode($name, null, $children, $parent);

    TreeNode::checkTreeNodeHasChildren($parentAwareTreeNode);

    ParentAwareTreeNode::checkParentAwareTreeNodeHasParent($parentAwareTreeNode);

    return $parentAwareTreeNode;

  }

  public static function createLeafNode($name, $value, $parent) {

    $parentAwareTreeNode = new ParentAwareTreeNode($name, $value, array(), $parent);

    TreeNode::checkTreeNodeHasNoChildren($parentAwareTreeNode);

    ParentAwareTreeNode::checkParentAwareTreeNodeHasParent($parentAwareTreeNode);

    return $parentAwareTreeNode;

  }

  public static function createChildlessParentAwareTreeNodeFromTreeNode($treeNode, $parent) {

    $parentAwareTreeNode = new ParentAwareTreeNode($treeNode->getName(), $treeNode->getValue(), array(), $parent);

    if (!is_null($parent)) {

      $parentAwareTreeNode->setCustomData($parent->getCustomData());

    }

    return $parentAwareTreeNode;

  }

}

class ParentAwareTreeNode extends TreeNode {

  private $parent;

  public function __construct($name, $value, $children, $parent) {

    parent::__construct($name, $value, $children);

    $this->setParent($parent);

  }

  public static function checkParentAwareTreeNode($parentAwareTreeNode) {

    // check whether tree node is not null and is a class instance

    if (!is_a($parentAwareTreeNode, __CLASS__)) {

      die("Parent aware tree node expected.");

    }

  }

  public static function checkParentAwareTreeNodeOrNull($parentAwareTreeNode) {

    if (!is_null($parentAwareTreeNode) and !is_a($parentAwareTreeNode, __CLASS__)) {

      die("Parent aware tree node or null expected.");

    }

  }

  public static function checkParentAwareTreeNodeHasNoParent($parentAwareTreeNode) {

    if ($parentAwareTreeNode->hasParent()) {

      die("Was expecting a parent aware tree node with a parent node.");

    }

  }

  public static function checkParentAwareTreeNodeHasParent($parentAwareTreeNode) {

    if (!$treeNode->hasChildren()) {

      die("Was expecting a parent aware tree node with no children.");

    }

  }

  public function hasParent($parent) {

    return $this->parent != null;

  }

  public function setParent($parent) {

    self::checkParentAwareTreeNodeOrNull($parent);

    $this->parent = $parent;

  }

  public function getParent() {

    return $this->parent;

  }

  public function addChild($child) {

    self::checkParentAwareTreeNode($child);

    $this->addTreeNodeChild($child);

    $child->setParent($this);

  }

  public function __toString() {

    if (is_null($this->parent)) {

      $parentString = "No parent";

    } else {

      $parentName = $this->parent->getName();

      $parentString = "Parent name: " . (is_null($parentName) ? "nameless" : $parentName);

    }

    return $parentString . "; " . parent::__toString();

  }

}

class LabeledTreeNodeFactory {

  public static function createLabeledTreeNode($label, $children) {

    $treeNode = new TreeNode($label, null, $children);

    return $treeNode;

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

  private $customData;

  const TRAVERSAL_MODE_BFS = 0;

  const TRAVERSAL_MODE_DFS = 1;

  const NUM_TRAVERSAL_MODES = 2;

  function __construct($rootNode, $traversalMode = self::TRAVERSAL_MODE_BFS) {

    $this->setTraversalMode($traversalMode);

    $this->setRootNode($rootNode);

    $this->rewind();

    $this->setCustomData(null);

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

  public function getRootNode() {

    return $this->rootNode;

  }

  /* methods from Iterator interface (and their helper methods) */

  public function current() {

    return $this->nodesToVisit[0];

  }

  public function key() {

    return $this->currentNodeIteratorKey;

  }

  public function next() {

   // update tracking data structures (must be done first)

    $this->planToVisitChildren();

    // pop current node from data structure tracking nodes to be visited

    array_shift($this->nodesToVisit);

    // pop current level from data structure tracking levels of nodes to be visited

    array_shift($this->nodesToVisitLevels);

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

  /**
   * Call this method to plan to visit (further) children of current node.
   * If children are being added to the current node this method must be called before adding such children.
   */

  private function planToVisitChildren() {

    // retrieve visited node children

    $childrenToVisit = $this->nodesToVisit[0]->getChildren();
 
    if (count($childrenToVisit) > 0) {

      // remember (further) children to be visited

      switch ($this->traversalMode) {

        case self::TRAVERSAL_MODE_BFS:

          // remember (further) children to be visited last (BFS)

          $this->nodesToVisit = array_merge($this->nodesToVisit, $childrenToVisit);

          break;

        case self::TRAVERSAL_MODE_DFS:

          // remember (further) children to be visited first (DFS)

          array_splice($this->nodesToVisit, 1, 0, $childrenToVisit);

          break;

      }

      // retrieve node level at current tree node position

      $visitedNodeLevel = $this->nodesToVisitLevels[0];

      $visitedNodeChildrenLevel = $visitedNodeLevel + 1;

      $visitedNodeChildrenLevels = array_fill(0, count($childrenToVisit), $visitedNodeChildrenLevel);

      // remember level of (further) children to be visited

      switch ($this->traversalMode) {

        case self::TRAVERSAL_MODE_BFS:

          // remember levels of children to be visited last (BFS)

          $this->nodesToVisitLevels = array_merge($this->nodesToVisitLevels, $visitedNodeChildrenLevels);

          break;

        case self::TRAVERSAL_MODE_DFS:

          // remember levels of children to be visited first (DFS)

          array_splice($this->nodesToVisitLevels, 1, 0, $visitedNodeChildrenLevels);

          break;

      }

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

  /* other methods */

  public function addChild($childTreeNode) {

    // (no tree traversal data structures related to extra children to be visited to update)

    // (having updated tree traversal data structures) add child tree node to current tree node children

    $this->current()->addChild($childTreeNode);

  }

  public function getNextNonLeafStepCount() {

    return $this->getStepCountToNextNodeIteratorCondition(self::getIteratorConditionIsNonLeafNode());

  }

  public function getNextLeafStepCount() {

    return $this->getStepCountToNextNodeIteratorCondition(self::getIteratorConditionIsLeafNode());

  }

  public function getStepCountToNextNodeIteratorCondition($condition) {

    $searchIterator = new TreeNodeIterator($this->current(), $this->traversalMode);

    if ($searchIterator->valid()) {

      $searchIterator->next();

      for ($stepCount = 1; $searchIterator->valid(); $stepCount++) {

        if (call_user_func($condition, $searchIterator)) {

          return $stepCount;

        }

        $searchIterator->next();

      }

    }

    return null;

  }

  public function nextCount($count) {

    for ($i = 0; $i < $count; $i++) {

      $this->checkValid();

      $this->next();

    }

  }

  public function nextNonLeaf() {

    return $this->nextCondition(self::getIteratorConditionIsNonLeafNode());

  }

  public function nextLeaf() {

    return $this->nextCondition(self::getIteratorConditionIsLeafNode());

  }

  public function nextMatch($treeNode) {

    return $this->nextCondition(self::getIteratorConditionNodeMatches($treeNode));

  }

  public function nextChildMatch($childNode) {

    return $this->nextCondition(self::getIteratorConditionHasMatchingChildNode($childNode));

  }

  public function nextCondition($condition) {

    $stepCount = 0;

    if ($this->valid()) {

      $this->next();

      ++$stepCount;

      while ($this->valid()) {

        if (call_user_func($condition, $this)) {

          break;

        }

        $this->next();

        ++$stepCount;

      }

    }

    return $stepCount;

  }

  private function checkValid() {

    if (!$this->valid()) {

      die("Tree node iterator already past last node in the tree.");

    }

  }

  private static function getIteratorConditionIsNonLeafNode() {

    return function($treeNodeIterator) {

      return $treeNodeIterator->current()->hasChildren();

    };

  }

  private static function getIteratorConditionIsLeafNode() {

    return function($treeNodeIterator) {

      return !$treeNodeIterator->current()->hasChildren();

    };

  }

  private static function getIteratorConditionNodeMatches($treeNode) {

    return function($treeNodeIterator) use ($treeNode) {

      return $treeNodeIterator->current() === $treeNode;

    };

  }

  private static function getIteratorConditionHasMatchingChildNode($childNode) {

    return function($treeNodeIterator) use ($childNode) {

      return $treeNodeIterator->current()->hasMatchingChildNode($childNode);

    };

  }

  public function setCustomData($customData) {

    $this->customData = $customData;

  }

  public function getCustomData() {

    return $this->customData;

  }

}

class TreeNodeIteratorKey {

  private $nodeIndex;

  private $nodeLevel;

  private $customData;

  function __construct() {

    $this->nodeIndex = null;

    $this->nodeLevel = null;

    $this->customData = null;

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

  public function setCustomData($customData) {

    $this->customData = $customData;

  }

  public function getCustomData() {

    return $this->customData;

  }

}

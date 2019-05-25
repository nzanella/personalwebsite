<?php
/**
 * Example: non-recursive BFS (breadth-first search) tree traversal and deep-copying with separate modification of non-leaves and leaves.
 * Also works with trees having variable length paths from the root node to leaf nodes.
 * All nodes except for the root node are named and the leaves also have values associated with such names.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v1
 */

  require_once("includes/treeNode-v7.php");

  function createTestTreeInstance() {

    return TreeNode::createNamelessNonLeafNode(array(
      TreeNode::createNonLeafNode("node1", array(
        TreeNode::createNonLeafNode("node11", array(
          TreeNode::createLeafNode("node111", "The"),
        )),
        TreeNode::createNonLeafNode("node12", array(
          TreeNode::createLeafNode("node121", "fishermen"),
          TreeNode::createLeafNode("node122", "know"),
        )),
        TreeNode::createNonLeafNode("node13", array(
          TreeNode::createLeafNode("node131", "that"),
          TreeNode::createNonLeafNode("node132", array(
            TreeNode::createLeafNode("node1321", "the"),
            TreeNode::createLeafNode("node1322", "sea"),
            TreeNode::createLeafNode("node1323", "is"),
            TreeNode::createLeafNode("node1324", "dangerous"),
          )),
        )),
      )),
      TreeNode::createNonLeafNode("node2", array(
        TreeNode::createNonLeafNode("node21", array(
          TreeNode::createNonLeafNode("node211", array(
            TreeNode::createNonLeafNode("node2111", array(
              TreeNode::createNonLeafNode("node21111", array(
                TreeNode::createLeafNode("node211111", "and"),
                TreeNode::createNonLeafNode("node211112", array(
                  TreeNode::createNonLeafNode("node2111121", array(
                    TreeNode::createLeafNode("node21111211", "the"),
                  )),
                )),
                TreeNode::createLeafNode("node211113", "storm"),
                TreeNode::createNonLeafNode("node211114", array(
                  TreeNode::createLeafNode("node2111141", "terrible,"),
                  TreeNode::createLeafNode("node2111142", "but"),
                )),
                TreeNode::createLeafNode("node211115", "they"),
              )),
            )),
          )),
          TreeNode::createNonLeafNode("node212", array(
            TreeNode::createLeafNode("node2121", "have"),
            TreeNode::createLeafNode("node2122", "never"),
            TreeNode::createLeafNode("node2123", "found"),
            TreeNode::createLeafNode("node2124", "these"),
          )),
        )),
      )),
      TreeNode::createNonLeafNode("node3", array(
        TreeNode::createNonLeafNode("node31", array(
          TreeNode::createLeafNode("node311", "dangers"),
        )),
        TreeNode::createLeafNode("node32", "sufficient"),
        TreeNode::createLeafNode("node33", "reason"),
        TreeNode::createLeafNode("node34", "for"),
        TreeNode::createNonLeafNode("node35", array(
          TreeNode::createLeafNode("node351", "remaining"),
        )),
        TreeNode::createLeafNode("node36", "ashore..."),
      )),
      TreeNode::createNonLeafNode("node4", array(
        TreeNode::createLeafNode("node41", "or"),
        TreeNode::createLeafNode("node42", "so"),
        TreeNode::createLeafNode("node43", "goes"),
      )),
      TreeNode::createLeafNode("node5", "the"),
      TreeNode::createLeafNode("node6", "saying."),
    ));

  }

  function createTrivialTestTreeInstance() {

    return TreeNode::createLeafNode("node1", "value1");

  }

  function createEmptyTestTreeInstance() {

    return null;

  }

  function parentAwareTreeNodeTransformation($inputTreeNodeIterator) {

    $currentNode = $inputTreeNodeIterator->current();

    if ($inputTreeNodeIterator->key()->getNodeLevel() > 0) {

      $outputParentTreeNodeIterator = $inputTreeNodeIterator->getCustomData();

      $parentNode = $outputParentTreeNodeIterator->current();
      
      $parentAwareTreeNode = ParentAwareTreeNode::createChildlessParentAwareTreeNodeFromTreeNode($currentNode, $parentNode);

    } else {

      $parentAwareTreeNode = ParentAwareTreeNode::createChildlessParentAwareTreeNodeFromTreeNode($currentNode, null);

    }

    $parentAwareTreeNode->removeAllChildren();

    return $parentAwareTreeNode;

  }

  function makeParentAwareTree($rootNode) {

    return TreeTransformer::getTreeTransform($rootNode, 'parentAwareTreeNodeTransformation');

  }

  function showTreeTraversals($treeInstance, $treeInstanceName = null) {

    $treeInstanceString = is_null($treeInstanceName) ? "" : $treeInstanceName . ": ";

    if (!is_null($treeInstance)) {

      echo $treeInstanceString, "DFS Traversal:\n";

      $treeInstance->showFullTree(TreeNodeIterator::TRAVERSAL_MODE_DFS);

      echo "\n", $treeInstanceString, "BFS Traversal:\n";

      $treeInstance->showFullTree(TreeNodeIterator::TRAVERSAL_MODE_BFS);

    } else {

      echo $treeInstanceString, "Tree is null.\n";

    }

    echo "\n";

  }

  function testRun($myTreeInstance, $myTreeInstanceName) {

    showTreeTraversals($myTreeInstance, $myTreeInstanceName . ": Original Tree Instance");

    $myTreeNodeTransformation = 'myTreeNodeTransformation';

    $myTreeTransform = makeParentAwareTree($myTreeInstance);

    showTreeTraversals($myTreeTransform, $myTreeInstanceName . ": Transformed Tree Instance");

  }

  class TreeTransformer {

    // function transforming a single tree node instance including its name and value but excluding its children

    private $treeNodeTransformation;

    // maximum number of nodes to be retrieved as part of the transformed tree (all if null)

    private $treeNodeMaxCount;

    // retrieve transformed tree

    public static function getTreeTransform($rootNode, $treeNodeTransformation = null, $treeNodeMaxCount = null) {

      $treeTransformer = new TreeTransformer($treeNodeTransformation, $treeNodeMaxCount);

      return $treeTransformer->constructTreeTransform($rootNode);

    }

    private function __construct($treeNodeTransformation, $treeNodeMaxCount) {

      $this->setTreeNodeTransformation($treeNodeTransformation, $treeNodeMaxCount);

      if (!is_null($treeNodeMaxCount) and (!is_int($treeNodeMaxCount) or $treeNodeMaxCount < 0)) {

        die("Maximum transformed tree node count must be a non-negative integer or null.");

      }

      $this->treeNodeMaxCount = $treeNodeMaxCount;

    }

    private function treeNodeMaxCountReached() {

      if (is_null($this->treeNodeMaxCount)) {

        return false;

      }

      return --$this->treeNodeMaxCount < 0;

    }

    private function constructTreeTransform($rootNode) {

      // check whether tree instance is the empty tree instance

      if (is_null($rootNode) or $this->treeNodeMaxCountReached()) {

        return null;

      }

      // check whether root node is a valid tree node

      TreeNode::checkTreeNode($rootNode);

      // set up input parent tree node iterator

      $inputParentTreeNodeIterator = new TreeNodeIterator($rootNode, TreeNodeIterator::TRAVERSAL_MODE_BFS);

       // initialize output root node

      $outputRootNode = call_user_func($this->treeNodeTransformation, $inputParentTreeNodeIterator);

      // check whether tree instance is the trivial tree instance

      if (!$rootNode->hasChildren() or $this->treeNodeMaxCountReached()) {

        return $outputRootNode;

      }

      // set up output parent tree node iterator and input child tree node iterator

      $outputParentTreeNodeIterator = new TreeNodeIterator($outputRootNode, TreeNodeIterator::TRAVERSAL_MODE_BFS);

      $inputChildTreeNodeIterator = new TreeNodeIterator($rootNode, TreeNodeIterator::TRAVERSAL_MODE_BFS);

      // store output tree node iterator as input child node iterator custom data (to make it available to transformation function)

      $inputChildTreeNodeIterator->setCustomData($outputParentTreeNodeIterator);

      // iterate through input non-leaf nodes

      while ($inputParentTreeNodeIterator->valid()) {

        // retrieve number of children to add to current output non-leaf node

        $numChildrenToAdd = $inputParentTreeNodeIterator->current()->getChildCount();

        // add the retrieved number of current input non-leaf node children to the current output non-leaf node

        for ($childNodeIndex = 0; $childNodeIndex < $numChildrenToAdd; $childNodeIndex++) {

          // step forward to next (BFS) node for current parent node

          $inputChildTreeNodeIterator->next();

           // retrieve current input child node for current parent node

          $inputChildTreeNode = $inputChildTreeNodeIterator->current();

          // carry out safety check

          TreeNode::checkTreeNode($inputChildTreeNode);

          // transform input child node into output child node and add it to output parent node

          $outputChildTreeNode = call_user_func($this->treeNodeTransformation, $inputChildTreeNodeIterator);

          $outputParentTreeNodeIterator->addChild($outputChildTreeNode);

          if ($this->treeNodeMaxCountReached()) {

            return $outputRootNode;

          }

        }

        // advance input and output parent tree node iterators to next non-leaf node

        $stepCount = $inputParentTreeNodeIterator->nextNonLeaf();

        $outputParentTreeNodeIterator->nextCount($stepCount);

      }

      return $outputRootNode;

    }

    private function setTreeNodeTransformation($treeNodeTransformation) {

      if (is_null($treeNodeTransformation)) {

        $treeNodeTransformation = function($treeNodeIterator) { 

          $treeNode = $treeNodeIterator->current();

          $outputTreeNode = TreeNode::createStubNode();

          $outputTreeNode->setName($treeNode->getName());

          if (!$treeNode->hasChildren()) {

            $outputTreeNode->setValue($treeNode->getValue());

          }

          return $outputTreeNode;

        };

      } else {

        self::checkFunction($treeNodeTransformation);

      }

      $this->treeNodeTransformation = $treeNodeTransformation;

    }

    private static function checkFunction($function) {

      return is_callable($function);

    }

  }

  testRun(createTestTreeInstance(), "Full Tree Instance");
  testRun(createTrivialTestTreeInstance(), "Trivial Tree Instance");
  testRun(createEmptyTestTreeInstance(), "Empty Tree Instance");

?>

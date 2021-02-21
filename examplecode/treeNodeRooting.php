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

  require_once("includes/treeNode-v8.php");

  function createTestTreeInstance() {

    return
      LabeledTreeNodeFactory::createLabeledTreeNode("A", array(
        LabeledTreeNodeFactory::createLabeledTreeNode("B", array(
          LabeledTreeNodeFactory::createLabeledTreeNode("F", array(
          )),
          LabeledTreeNodeFactory::createLabeledTreeNode("G", array(
          )),
        )),
        LabeledTreeNodeFactory::createLabeledTreeNode("C", array(
          LabeledTreeNodeFactory::createLabeledTreeNode("H", array(
          )),
          LabeledTreeNodeFactory::createLabeledTreeNode("I", array(
            LabeledTreeNodeFactory::createLabeledTreeNode("M", array(
              LabeledTreeNodeFactory::createLabeledTreeNode("P", array(
              )),
              LabeledTreeNodeFactory::createLabeledTreeNode("Q", array(
              )),
            )),
            LabeledTreeNodeFactory::createLabeledTreeNode("N", array(
            )),
          )),
          LabeledTreeNodeFactory::createLabeledTreeNode("J", array(
          )),
        )),
        LabeledTreeNodeFactory::createLabeledTreeNode("D", array(
          LabeledTreeNodeFactory::createLabeledTreeNode("K", array(
          )),
          LabeledTreeNodeFactory::createLabeledTreeNode("L", array(
            LabeledTreeNodeFactory::createLabeledTreeNode("O", array(
              LabeledTreeNodeFactory::createLabeledTreeNode("R", array(
                LabeledTreeNodeFactory::createLabeledTreeNode("S", array(
                )),
              )),
            )),
          )),
        )),
      ));

  }

  function createTrivialTestTreeInstance() {

    return LabeledTreeNodeFactory::createLabeledTreeNode("A", array());

  }

  function createEmptyTestTreeInstance() {

    return null;

  }

  function parentAwareTreeNodeTransformation($inputTreeNodeIterator) {

    $currentNode = $inputTreeNodeIterator->current();

    if ($inputTreeNodeIterator->key()->getNodeLevel() > 0) {

      $outputParentTreeNodeIterator = $inputTreeNodeIterator->getCustomData();

      $parentNode = $outputParentTreeNodeIterator->current();
      
      $parentAwareTreeNode = ParentAwareTreeNodeFactory::createChildlessParentAwareTreeNodeFromTreeNode($currentNode, $parentNode);

    } else {

      $parentAwareTreeNode = ParentAwareTreeNodeFactory::createChildlessParentAwareTreeNodeFromTreeNode($currentNode, null);

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

  function transformInPlaceRerootTree($oldRootNode, $newRootNode) {



  }

  function transformInPlaceRerootParentAwareTree($newRootTreeNode) {

    // ensure parent aware tree node to become new root is not null
    // and is a parent aware tree node

    ParentAwareTreeNode::checkTreeNode($newRootNode);

    $currentNode = $newRootTreeNode;

    $currentParent = $currentNode->getParent();

    if ($currentParent != null) {

      do {

        $nextParent = $currentParent->getParent();

        $currentNode->addChild($currentParent);

        $currentParent->setParent($currentNode);

        $currentNode = $currentParent;

        $currentParent = $nextParent;

      } while ($currentParent != null);

      $newRootTreeNode->setParent(null);

    }

    return $newRootNode;

  }

  function testRun($myTreeInstance, $myTreeInstanceName, $newRootNodeLabel, $modePrependAppendChildren) {

    showTreeTraversals($myTreeInstance, $myTreeInstanceName . ": Original Tree Instance");

// insert here code to root the tree at new root node without using parent links

    $myParentAwareNodesTreeTransform = makeParentAwareTree($myTreeInstance);

    $newRootNode = $myParentAwareNodesTreeTransform.getTreeNodeByName($newRootNodeLabel);

    // check whether node with given name to become new root node was found

    if ($newRootNode != null) {

      $treeNode = $newRootNode;

      $treeNodeParent = $treeNode->getParent();

      if ($treeNodeParent->hasParent()) {

        // store the parent's old parent in temporary variable

        $nextParent = $treeNodeParent->getParent();

      }

      $treeNode->addChild($treeNodeParent);

      foreach ($treeNodeParent->getChildren() as $treeNodeParentChild) {

        if ($treeNodeParentChild !== $treeNode) {

          $treeNode->addChild($treeNodeParentChild);

        }

        $treeNode = $treeNodeParent;

	$treeNodeParent = $nextParent;

      }

      return $newRootNode;

    } else {

      echo "Tree node with given label \"$newRootNodeLabel\" not found.\n");

      return;

    }

// insert here code to root the tree at new root node using parent links

    /*
    showTreeTraversals($myParentAwareNodesTreeTransform, $myTreeInstanceName . ": Transformed Tree Instance");
     */

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

  testRun(createTestTreeInstance(), "Full Tree Instance", "H");
  testRun(createTrivialTestTreeInstance(), "Trivial Tree Instance", "A");
  testRun(createTrivialTestTreeInstance(), "Trivial Tree Instance", "H");
  testRun(createEmptyTestTreeInstance(), "Empty Tree Instance", "H");

?>

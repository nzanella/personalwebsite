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

  require_once("includes/treeNode-v5.php");

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

  function testRun($myTreeInstance) {

    $myNonleafNodeChildlessTransformation = 'myNonleafNodeChildlessTransformation';

    $myLeafNodeTransformation = 'myLeafNodeTransformation';

    $myModifiedDeepCopiedTreeInstance = getModifiedDeepCopy($myTreeInstance, $myNonleafNodeChildlessTransformation, $myLeafNodeTransformation);

    if (!is_null($myModifiedDeepCopiedTreeInstance)) {

      $myModifiedDeepCopiedTreeInstance->showFullTree();

    } else {

      echo "Tree is null.";

    }

    echo "\n";

  }

  function myNonleafNodeChildlessTransformation($treeNode) {

    $outputTreeNode = TreeNode::createStubNode();

    $outputTreeNode->setName("NON-LEAF-NAME-" . $treeNode->getName());

    return $outputTreeNode;

  }

  function myLeafNodeTransformation($treeNode) {

    $outputTreeNode = TreeNode::createStubNode();

    $outputTreeNode->setName("LEAF-NAME-" . $treeNode->getName());

    $outputTreeNode->setValue("LEAF-VALUE-" . $treeNode->getValue());

    return $outputTreeNode;

  }

  function getModifiedDeepCopy($rootNode, $nonleafNodeChildlessTransformation = null, $leafNodeTransformation = null) {

    // properly initialize node transformations

    if (is_null($nonleafNodeChildlessTransformation)) {

      $nonleafNodeChildlessTransformation = function($treeNode) { 

        $outputTreeNode = TreeNode::createStubNode();

        $outputTreeNode->setName($treeNode->getName());

        return $outputTreeNode;

      };

    }

    if (is_null($leafNodeTransformation)) {

      $leafNodeTransformation = function($treeNode) {

        $outputTreeNode = TreeNode::createStubNode();

        $outputTreeNode->setName($treeNode->getName());
        
        $outputTreeNode->setValue($treeNode->getValue());

        return $outputTreeNode;

      };

    }

    // check whether tree instance is the empty tree instance

    if (is_null($rootNode)) {

      return null;

    }

    // check whether root node is a valid tree node

    TreeNode::checkTreeNode($rootNode);

    // check whether tree instance is the trivial tree instance

    if (!$rootNode->hasChildren()) {

      return $leafNodeTransformation($rootNode);

    }

    // initialize output tree

    $outputRootNode = $nonleafNodeChildlessTransformation($rootNode);

    // initialize previous level input and previous level output non-leaf node tracking data structures

    $previousLevelNonLeafNodes = array($rootNode);

    $outputPreviousLevelNonLeafNodes = array($outputRootNode);

    // traverse tree levels one level at a time starting at the current level just below the root node

    while (count($previousLevelNonLeafNodes) > 0) {

      // set up input and output non-leaf node tracking data structures

      $currentLevelNonLeafNodes = array();

      $outputCurrentLevelNonLeafNodes = array();

      // simultaneously iterate through previous level input and previous level output non-leaf nodes

      for ($previousLevelNonLeafNodeIndex = 0; $previousLevelNonLeafNodeIndex < count($previousLevelNonLeafNodes); $previousLevelNonLeafNodeIndex++) {

        $previousLevelNonLeafNode = $previousLevelNonLeafNodes[$previousLevelNonLeafNodeIndex];

        $outputPreviousLevelNonLeafNode = $outputPreviousLevelNonLeafNodes[$previousLevelNonLeafNodeIndex];

        // set up data structure to collect transformations of all children of input current level non-leaf node

        $outputPreviousLevelNonLeafNodeChildren = array();

        // iterate through children of current previous level input non-leaf node

        foreach ($previousLevelNonLeafNode->getChildren() as $currentLevelNode) {

          // carry out safety check

          TreeNode::checkTreeNode($currentLevelNode);

          // check whether current level input node is a non-leaf node

          if ($currentLevelNode->hasChildren()) {

            // set up output (at the moment childless) transformed non-leaf node

            $outputCurrentLevelNode = $nonleafNodeChildlessTransformation($currentLevelNode);

            // update input and output non-leaf node tracking data structures

            array_push($currentLevelNonLeafNodes, $currentLevelNode);

            array_push($outputCurrentLevelNonLeafNodes, $outputCurrentLevelNode);

          } else {

            // set up output transformed leaf node

            $outputCurrentLevelNode = $leafNodeTransformation($currentLevelNode);

          }

          // keep track of output current level child node for previous level non-leaf node

          array_push($outputPreviousLevelNonLeafNodeChildren, $outputCurrentLevelNode);

        }

        // set children for output previous level non-leaf node

        $outputPreviousLevelNonLeafNode->setChildren($outputPreviousLevelNonLeafNodeChildren);

      }

      // update input and output non-leaf node tracking data structures

      $previousLevelNonLeafNodes = $currentLevelNonLeafNodes;

      $outputPreviousLevelNonLeafNodes = $outputCurrentLevelNonLeafNodes;

    }

    return $outputRootNode;

  }

  testRun(createTestTreeInstance());
  testRun(createTrivialTestTreeInstance());
  testRun(createEmptyTestTreeInstance());

?>

<?php
/**
 * Example: non-recursive BFS (breadth-first search) tree traversal and deep-copying with separate modification of non-leaves and leaves.
 * Also works with trees having variable length paths from the root node to leaf nodes.
 * All nodes except for the root node are named and the leaves also have values associated with such names.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
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

  function getTreeLeavesMessage($treeNode) {

    $treeLeavesMessage = "";

    if (is_null($treeNode)) {

      return $treeLeavesMessage;

    }

    TreeNode::checkTreeNode($treeNode);

    $treeNodeIterator = new TreeNodeIterator($treeNode);

    $notFirstWord = false;

    foreach ($treeNodeIterator as $nodeKey => $node) {

      if (!$node->hasChildren()) {

        if ($notFirstWord) {

          $treeLeavesMessage .= " ";

        }

        $treeLeavesMessage .= $node->getValue();

        $notFirstWord = true;

      }

    }

    return $treeLeavesMessage;

  }

  function testRun($myTreeInstance) {

    echo getTreeLeavesMessage($myTreeInstance);

  }

  testRun(createTestTreeInstance());

?>

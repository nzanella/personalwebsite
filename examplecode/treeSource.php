<?php
/**
 * Tree node value updates starting from source node, following path terminal node names, and setting the final node value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 */

require_once("includes/treeNode-v3.php");

function test() {

  $treeInstances = array(
    getTestTreeInstance1(),
    getTestTreeInstance2(),
    getTestTreeInstance3(),
  );

  $rootNodeToDestinationNodeNames = array(
    array("node2"),
    array("node1", "node13"),
    array("node3", "node31", "node312"),
  );

  for ($i = 0; $i < count($treeInstances); $i++) {

    setDestinationNodeValueByPath($treeInstances[$i], $rootNodeToDestinationNodeNames[$i], "_x_");

    $treeInstances[$i]->showFullTree();

    echo "\n";

  }

}

function setDestinationNodeValueByPath($sourceNode, $pathTerminalNodeNames, $destinationNodeValue) {

  if (!is_a($sourceNode, 'TreeNode')) {

    die("Was expecting a tree node instance.");

  }

  if (!is_array($pathTerminalNodeNames)) {

    die("Was expecting an array of node names.");

  }

  $currentNode = $sourceNode;

  foreach ($pathTerminalNodeNames as $pathTerminalNodeName) {

    $currentNode = $currentNode->getChildByName($pathTerminalNodeName);

    if (is_null($currentNode)) {

      die("Invalid path terminal node name encountered.");

    }

  }

  $currentNode->setValue($destinationNodeValue);

}

function getTestTreeInstance1() {

  return TreeNode::createNamelessNonLeafNode(array(
    TreeNode::createLeafNode("node1", "leaf1"),
    TreeNode::createLeafNode("node2", "leaf2"),
    TreeNode::createLeafNode("node3", "leaf3"),
  ));

}

function getTestTreeInstance2() {

  return TreeNode::createNamelessNonLeafNode(array(

    TreeNode::createNonLeafNode("node1", array(
      TreeNode::createLeafNode("node11", "leaf11"),
      TreeNode::createLeafNode("node12", "leaf12"),
      TreeNode::createLeafNode("node13", "leaf13"),
    )),
    TreeNode::createNonLeafNode("node2", array(
      TreeNode::createLeafNode("node21", "leaf21"),
      TreeNode::createLeafNode("node22", "leaf22"),
      TreeNode::createLeafNode("node23", "leaf23"),
    )),
    TreeNode::createNonLeafNode("node3", array(
      TreeNode::createLeafNode("node31", "leaf31"),
      TreeNode::createLeafNode("node32", "leaf32"),
      TreeNode::createLeafNode("node33", "leaf33"),
    )),

  ));

}

function getTestTreeInstance3() {

  return TreeNode::createNamelessNonLeafNode(array(
    TreeNode::createNonLeafNode("node1", array(
      TreeNode::createNonLeafNode("node11", array(
        TreeNode::createLeafNode("node111", "leaf111"),
        TreeNode::createLeafNode("node112", "leaf112"),
        TreeNode::createLeafNode("node113", "leaf113"),
      )),
      TreeNode::createNonLeafNode("node12", array(
        TreeNode::createLeafNode("node121", "leaf121"),
        TreeNode::createLeafNode("node122", "leaf122"),
        TreeNode::createLeafNode("node123", "leaf123"),
      )),
      TreeNode::createNonLeafNode("node13", array(
        TreeNode::createLeafNode("node131", "leaf131"),
        TreeNode::createLeafNode("node132", "leaf132"),
        TreeNode::createLeafNode("node133", "leaf133"),
      )),
    )),
    TreeNode::createNonLeafNode("node2", array(
      TreeNode::createNonLeafNode("node21", array(
        TreeNode::createLeafNode("node211", "leaf211"),
        TreeNode::createLeafNode("node212", "leaf212"),
        TreeNode::createLeafNode("node213", "leaf213"),
      )),
      TreeNode::createNonLeafNode("node22", array(
        TreeNode::createLeafNode("node221", "leaf221"),
        TreeNode::createLeafNode("node222", "leaf222"),
        TreeNode::createLeafNode("node223", "leaf223"),
      )),
      TreeNode::createNonLeafNode("node23", array(
        TreeNode::createLeafNode("node231", "leaf231"),
        TreeNode::createLeafNode("node232", "leaf232"),
        TreeNode::createLeafNode("node233", "leaf233"),
      )),
    )),
    TreeNode::createNonLeafNode("node3", array(
      TreeNode::createNonLeafNode("node31", array(
        TreeNode::createLeafNode("node311", "leaf311"),
        TreeNode::createLeafNode("node312", "leaf312"),
        TreeNode::createLeafNode("node313", "leaf313"),
      )),
      TreeNode::createNonLeafNode("node32", array(
        TreeNode::createLeafNode("node321", "leaf321"),
        TreeNode::createLeafNode("node322", "leaf322"),
        TreeNode::createLeafNode("node323", "leaf323"),
      )),
      TreeNode::createNonLeafNode("node33", array(
        TreeNode::createLeafNode("node331", "leaf331"),
        TreeNode::createLeafNode("node332", "leaf332"),
        TreeNode::createLeafNode("node333", "leaf333"),
      )),
    )),
  ));

}

test();

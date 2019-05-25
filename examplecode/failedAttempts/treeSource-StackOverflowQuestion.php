<?php

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

  for ($i = 0; $i < 3; $i++) {

    setPathNodeValue($treeInstances[$i], $rootNodeToDestinationNodeNames[$i], "_x_");

    var_dump($treeInstances[$i]);

  }

}

function setPathNodeValue(&$treeInstance, $rootNodeToDestinationNodeKeys, $destinationNodeValue) {

  $numNonTerminalAssignments = count($rootNodeToDestinationNodeKeys);

  for ($edgeIndex = 0; $edgeIndex < $numNonTerminalAssignments; $edgeIndex++) {

    $nodeKey = $rootNodeToDestinationNodeKeys[$edgeIndex];

    if (!array_key_exists($nodeKey, $treeInstance)) {

      die("Invalid path key encountered.");

    }

    $treeInstance = &$treeInstance[$nodeKey];

  }

  $treeInstance[$numNonTerminalAssignments] = $destinationNodeValue;

}

function getTestTreeInstance1() {

  return array(
    "node1" => "leaf1",
    "node2" => "leaf2",
    "node3" => "leaf3",
  );

}

function getTestTreeInstance2() {

  return array(
    "node1" => array(
      "node11" => "leaf11",
      "node12" => "leaf12",
      "node13" => "leaf13",
    ),
    "node2" => array(
      "node21" => "leaf21",
      "node22" => "leaf22",
      "node23" => "leaf23",
    ),
    "node3" => array(
      "node31" => "leaf31",
      "node32" => "leaf32",
      "node33" => "leaf33",
    ),
  );

}

function getTestTreeInstance3() {

  return array(
    "node1" => array(
      "node11" => array(
        "node111" => "leaf111",
        "node112" => "leaf112",
        "node113" => "leaf113",
      ),
          "node12" => array(
        "node121" => "leaf121",
        "node122" => "leaf122",
        "node123" => "leaf123",
      ),
      "node13" => array(
        "node131" => "leaf131",
        "node132" => "leaf132",
        "node133" => "leaf133",
      ),
    ),
    "node2" => array(
      "node21" => array(
        "node211" => "leaf211",
        "node212" => "leaf212",
        "node213" => "leaf213",
      ),
          "node22" => array(
        "node221" => "leaf221",
        "node222" => "leaf222",
        "node223" => "leaf223",
      ),
      "node23" => array(
        "node231" => "leaf231",
        "node232" => "leaf232",
        "node233" => "leaf233",
      ),
    ),
    "node3" => array(
      "node31" => array(
        "node311" => "leaf311",
        "node312" => "leaf312",
        "node313" => "leaf313",
      ),
          "node32" => array(
        "node321" => "leaf321",
        "node322" => "leaf322",
        "node323" => "leaf323",
      ),
      "node33" => array(
        "node331" => "leaf331",
        "node332" => "leaf332",
        "node333" => "leaf333",
      ),
    ),
  );

}

test();

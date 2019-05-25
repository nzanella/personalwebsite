<?php
/**
 * Example: non-recursive BFS (breadth-first search) tree traversal and deep-copying with separate modification of non-leaves and leaves.
 * Also works with trees having variable length paths from the root node to leaf nodes.
 * All nodes except for the root node are named and the leaves also have values associated with such names.
 */

  function createTestTreeInstance() {

    return array(
      "node1" => array(
        "node11" => array(
          "node111" => "The",
        ),
        "node12" => array(
          "node121" => "fishermen",
          "node122" => "know",
        ),
        "node13" => array(
          "node131" => "that",
          "node132" => array(
            "node1321" => "the",
            "node1322" => "sea",
            "node1323" => "is",
            "node1324" => "dangerous",
          ),
        ),
      ),
      "node2" => array(
        "node21" => array(
          "node211" => array(
            "node2111" => array(
              "node21111" => array(
                "node211111" => "and",
                "node211112" => array(
                  "node2111121" => array(
                    "node21111211" => "the",
                  ),
                ),
                "node211113" => "storm",
                "node211114" => array(
                  "node2111141" => "terrible,",
                  "node2111142" => "but",
                ),
                "node211115" => "they",
              ),
            ),
          ),
          "node212" => array(
            "node2121" => "have",
            "node2122" => "never",
            "node2123" => "found",
            "node2124" => "these",
          ),
        ),
      ),
      "node3" => array(
        "node31" => array(
          "node311" => "dangers",
        ),
        "node32" => "sufficient",
        "node33" => "reason",
        "node34" => "for",
        "node35" => array(
          "node351" => "remaining",
        ),
        "node36" => "ashore...",
      ),
      "node4" => array(
        "node41" => "or",
        "node42" => "so",
        "node43" => "goes",
      ),
      "node5" => "the",
      "node6" => "saying.",
    );

  }

  function createTrivialTestTreeInstance() {

    return array();

  }

  function createEmptyTestTreeInstance() {

    return null;

  }

 
  function myReturnNodeNameWithVisitIndex($nodeName, $visitedNodeIndex) {

    return $nodeName . " (visit index: " . ($visitedNodeIndex + 1) . ")";

  }
 
  function myOutputAndReturnModifiedLeafNodeValue($leafNodeName, $leafNodeValue) {

    if (preg_match('/(\d+)$/', $leafNodeName, $matches) === 1) {

      $modifiedLeafNodeString = $matches[1] . ". " . $leafNodeValue;

    } else {

      $modifiedLeafNodeString = "LEAF NODE NAME NUMERIC MATCH ERROR: " . $leafNodeName . " => " . $leafNodeValue;

    }

    echo "<br />" . $modifiedLeafNodeString . "\n";

    return $modifiedLeafNodeString;

  }

  function testRun(&$myTreeInstance) {

    $leafNodeNameTransformationFunction = 'myReturnNodeNameWithVisitIndex';

    $leafNodeValueTransformationFunction = 'myOutputAndReturnModifiedLeafNodeValue';

    $copiedTree = deepModifiedCopy($myTreeInstance, $leafNodeNameTransformationFunction, $leafNodeValueTransformationFunction);

    var_dump($copiedTree);

    echo "<br /><br />\n";

  }

  function isTrivialTree(&$treeInstance) {

    return is_array($treeInstance) and count($treeInstance) == 0;

  }

  function isNonLeafNodeValue(&$treeNodeValue) {

    // check whether tree node value corresponds to a non-leaf node by design

    return is_array($treeNodeValue) and count($treeNodeValue) > 0;

  }

  function isLeafNodeValue(&$treeNodeValue) {

    // check whether tree node value corresponds to a leaf node by design

    return is_string($treeNodeValue);

  }

  function dieNonNonLeafRootNodeEncountered() {

    // found a node whose type is unexepcted by design

    die("Was expecting a nonempty array value for root node.");

  }

  function dieNeitherLeafNorNonLeafEncountered() {

    // found a node whose type is unexepcted by design

    die("Was expecting a nonempty array value or a string value.");

  }

  function deepModifiedCopy(&$treeInstance, $leafNodeNameTransformationFunction, $leafNodeValueTransformationFunction) {

    // check whether tree instance is the empty tree instance

    if ($treeInstance === null) {

      return null;

    }

    // check whether tree instance is the trivial tree instance

    if (isTrivialTree($treeInstance)) {

      return array();

    }

    // tree is neither empty nor trivial so check whether root node is valid

    if (!isNonLeafNodeValue($treeInstance)) {

      dieNonNonLeafRootNodeEncountered();

    }

    // initialize output and output creation data structures

    $outputTreeInstance = array();

    $visitedNodeIndex = 0;

    // initialize previous level array of non-leaf nodes to array containing root node array

    $previousLevelNonLeafNodeValues = array(&$treeInstance);

    // initialize previous level output array of non-leaf nodes to array containing childless output array

    $outputPreviousLevelNonLeafNodeValues = array(&$outputTreeInstance);

    // traverse tree levels one level at a time starting at the current level just below the root node

    while (count($previousLevelNonLeafNodeValues) > 0) {

      // set up input and output non-leaf node tracking data structures

      $currentLevelNonLeafNodeValues = array();

      $outputCurrentLevelNonLeafNodeValues = array();

      // simultaneously iterate through previous level input and output non-leaf nodes
//var_dump(count($previousLevelNonLeafNodeValues));

      for ($previousLevelNonLeafNodeIndex = 0; $previousLevelNonLeafNodeIndex < count($previousLevelNonLeafNodeValues); $previousLevelNonLeafNodeIndex++) {

        $previousLevelNonLeafNodeValue = &$previousLevelNonLeafNodeValues[$previousLevelNonLeafNodeIndex];
//var_dump($previousLevelNonLeafNodeValue);

        $outputPreviousLevelNonLeafNodeValue = &$outputPreviousLevelNonLeafNodeValues[$previousLevelNonLeafNodeIndex];
var_dump($outputPreviousLevelNonLeafNodeValue);
die();

        // iterate through current level children of previous level non-leaf node

        foreach ($previousLevelNonLeafNodeValue as $currentLevelNodeName => $currentLevelNodeValue) {

          if (isNonLeafNodeValue($currentLevelNodeValue)) {

            // set up empty array value for (at the moment childless) non-leaf node

            $outputCurrentLevelNodeValue = array();

            // update input and output non-leaf node tracking data structures

            $currentLevelNonLeafNodeValues[] = &$currentLevelNodeValue;

            $outputCurrentLevelNonLeafNodeValues[] = &$outputCurrentLevelNodeValue;


          } else if (isLeafNodeValue($currentLevelNodeValue)) {

            // set up transformed value for leaf node

            $outputCurrentLevelNodeValue = $leafNodeValueTransformationFunction($currentLevelNodeName, $currentLevelNodeValue);

          } else {

            dieNeitherLeafNorNonLeafEncountered();

          }

          // set up transformed node name

          $outputCurrentLevelNodeName = $leafNodeNameTransformationFunction($currentLevelNodeName, $visitedNodeIndex++);

          // copy transformed childless non-leaf or leaf node to output data structure (assigning a reference here is a must for non-leaf node values)

          $outputPreviousLevelNonLeafNodeValue[$outputCurrentLevelNodeName] = &$outputCurrentLevelNodeValue;

        }

      }

      // update input and output non-leaf node tracking data structures

      $previousLevelNonLeafNodeValues = &$currentLevelNonLeafNodeValues;

      $previousCurrentLevelNonLeafNodeValues = &$outputCurrentLevelNonLeafNodeValues;

    }

    return $outputTreeInstance;

  }

  testRun(createTestTreeInstance());
  testRun(createTrivialTestTreeInstance());
  testRun(createEmptyTestTreeInstance());

?>

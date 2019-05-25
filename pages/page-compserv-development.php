<?php
/**
 * @package NeilGlenZanellaWebSite
 */
?>
<p>The following code samples deal with data structures commonly used in computer science. Feel free to study or reuse the provided lines of code in your own projects as needed.</p>
<ul>
  <li>
    <h3>PHP Code</h3>
    <p>BFS (breadth-first-search) and DFS (depth-first-search) tree traversals in PHP using iterators.</p>
    <ul>
      <li>
        <a onclick="$(pre).hide();">treeNode.php</a>: A generic implementation of trees and iterators used to traverse tree nodes.
        <pre>
<?php

  echo '<code>' . file_get_contents('samplecode/treeNode.php') . '</code>';

?>
        </pre>
      </li>
      <li>
        <pre>
<?php include("sampleCode/treeNode.php"); ?>
        </pre>
      </li>
      <li><a href="">treeDescent.php</a>: Some example code showing how the included classes can be used to traverse and modify a tree.</li>
    </ul>
  </li>
  <li>
    <h3>Java Code</h3>
    <ul>
      <li><a href="<?php echo $pageSettings->getFile("samplecode/NodeList.java"); ?>">NodeList.java</a>: A generic linked list implementation in Java with supporting various kinds of operations.</li>
    </ul>
  </li>
  <li>
    <h3>C Code</h3>
    <ul>
      <li><a href="<?php echo $pageSettings->getFile("samplecode/lexpermlist.c"); ?>">lexpermlist.c</a>: A C implementation of an algorithm I developed for generating lexicographic permutations on n-tuples of any finite length.</li>
    </ul>
  </li>
</ul>

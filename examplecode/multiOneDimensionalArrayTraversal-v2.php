<?php
/**
 * Array traversal of multiple arrays (slightly different version).
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v2
 */

function testRun() {

  $foo = array("aa" => "11", "ab" => "12", "ac" => "13", "ad" => "14");
  $bar = array("ba" => "21", "bb" => "22", "bc" => "23", "bd" => "24");
  $baz = array("ca" => "31", "cb" => "32", "cc" => "33", "cd" => "34");

  $arrays = array($foo, $bar, $baz);

  echo "\nHorizontal followed by vertical (serial) traversal:\n\n";

  horizontalThenVerticalTraversal($arrays);

  echo "\nVertical followed by horizontal (parallel) traversal:\n\n";

  verticalThenHorizontalTraversal($arrays);

  echo "\n";

}

function horizontalThenVerticalTraversal($arrays) {

  arraySizesMatch($arrays, $commonSize);

  foreach ($arrays as $array) {

    reset($array);

  }

  foreach ($arrays as $array) {

    for ($i = 0; $i < $commonSize; $i++) {

      $keyValuePair = each($array);

      processKeyValuePair($keyValuePair[0], $keyValuePair[1]);

    }

  }

}

function verticalThenHorizontalTraversal($arrays) {

  arraySizesMatch($arrays, $commonSize);

  foreach ($arrays as $array) {

    reset($array);

  }

  for ($i = 0; $i < $commonSize; $i++) {

    foreach ($arrays as $array) {

      $keyValuePair = each($array);

      processKeyValuePair($keyValuePair[0], $keyValuePair[1]);

    }

  }

}

function arraySizesMatch($arrays, &$size = null) {

  // check whether all elements are arrays

  foreach ($arrays as $array) {

    if (!is_array($array)) {

      die("Non-array element found.");

    }

  }

  // check that all array elements are of the same size

  if (count($arrays) > 0) {

    $size = count($arrays[0]);

    for ($i = 1; $i < count($arrays); $i++) {

      if (count($arrays[$i]) != $size) {

        return false;
	  
      }

    }

  }
 
  return true;

}

function processKeyValuePair($key, $value) {

  echo "Key: " . $key . "\tValue: " . $value . "\n";

}

testRun();

<?php
/**
 * This example shows how the PHP '__call()' magic method can be used.
 * Tree nodes including nameless non-leaf node used to construct nonempty nontrivial tree root node,
 * named non-leaf node with children, and leaf node with name and value.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 * @version v1
 */

abstract class TreeNode {

  private $name;

  private $value;

  private $methods;

  protected $methodsForName = array('getName', 'setName');

  protected $methodsForValue = array('getValue', 'setValue');

  protected $methodsForChildren = array(
    'getChildren', 'setChildren', 'setChildrenArgs', 'addChildren', 'addChildrenArgs', 'addChild',
  );

  protected function getName() {

    return $this->name;

  }

  protected function setName($name) {

   $this->name = $name;

  }

  protected function getValue() {

    return $this->value;

  }

  protected function setValue($value) {

   $this->value = $value;

  }

  protected function getChildren() {

    return $this->value;

  }

  protected function setChildren($children) {

    $this->children = array();

    $this->addChildren($children);

  }

  protected function setChildrenArgs() {

    $this->setChildren(func_get_args());

  }

  protected function addChildren($children) {

    if (!is_array($children)) {

      die("Was expecting an array of child nodes.");

    }

    foreach ($children as $child) {

      $this->addChild($child);

    }

  }

  protected function addChildrenArgs() {

    foreach (func_get_args() as $arg) {

      $this->addChild($arg);

    }

  }

  protected function addChild($child) {

    $this->checkChild($child);

    $this->value[] = $child;

  }

  private function checkChild($child) {

    if (!is_a($child, __CLASS__)) {

      die("Was expecting an object of class " . __CLASS__ . ".");

    }

  }

  protected function setMethods($methods) {

   $this->methods = array();

   foreach ($methods as $method) {

     if (!method_exists($this, $method)) {

       die("Method corresponding to method name not available.");

     }

     $this->methods[] = $method;

   }

  }

  public function __call($name, $arguments) {

    if (in_array($name, $this->methods)) {

      return call_user_func_array(array($this, $name), $arguments);

    } else {

      die("Unexpected method.");

    }

  }

}

class NamelessNonLeafNode extends TreeNode {

  function __construct($children) {

    $this->setChildren($children);

    $this->setMethods($this->methodsForChildren);

  }

}

class NonLeafNode extends TreeNode {

  function __construct($name, $children) {

    $this->setName($name);

    $this->setChildren($children);

    $this->setMethods(array_merge($this->methodsForName, $this->methodsForChildren));

  }

}

class LeafNode extends TreeNode {

  function __construct($name, $value) {

    $this->setName($name);

    $this->setValue($value);

    $this->setMethods(array_merge($this->methodsForName, $this->methodsForValue));

  }

}

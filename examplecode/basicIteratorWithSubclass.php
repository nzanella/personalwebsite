<?php
/**
 * Basic iterator example with subclass.
 *
 * @author Neil Glen Zanella <coding@neilzanella.com>
 * @copyright 2004 Neil Glen Zanella
 */

class Foo {

  private $value;

  private $next;

  function __construct($value, $next = null) {

    $this->value = $value;

    $this->next = $next;

  }

  public function getValue() {

    return $this->value;

  }

  public function getNext() {

    return $this->next;

  }

}

class SubFoo extends Foo {

  public function getValueSquared() {

    echo "Value squared: " . $this->getValue() * $this->getValue() . ".\n";

  }

}

class FooIterator implements Iterator {

  private $first;

  private $current;

  private $key;

  function __construct($first) {

    $this->first = $first;

    $this->rewind();

  }

  public function current() {

    return $this->current;

  }

  public function key() {

    return $this->key;

  }

  public function next() {

    $this->current = $this->current->getNext();

    ++$this->key;

  }

  public function rewind() {

    $this->current = $this->first;

    $this->key = 0;

  }

  public function valid() {

    return !is_null($this->current);

  }

}

$fooChain = new SubFoo(1, new SubFoo(2, new SubFoo(3)));

echo $fooChain->getValue(), $fooChain->getNext()->getValue(), $fooChain->getNext()->getNext()->getValue(), "\n";

$fooIterator = new FooIterator($fooChain);

foreach ($fooIterator as $key => $foo) {

  echo "Key: ", $key, ", Value: ", $foo->getValue(). ".\n";
  echo $foo->getValueSquared();

}

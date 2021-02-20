<?php

class Foo {

  public static function greet($name) {

    echo "Hi $name. This is Foo.\n";

  }

}

class Bar {

  public static function greet($name){

    echo "Hi $name. This is Bar.\n";

  }

} 

class Baz extends Bar {

  // changing number of arguments in static function of same name in subclass

  // generates: Fatal error: Declaration of Baz::greet($name, $name2) must be compatible with Bar::greet($name)
  public static function greet($name, $name2){

    echo "Hi $name and $name2. This is Baz.\n";

  }

}


Foo::greet("David");

Bar::Greet("Anne");

Baz::Greet("Bob", "Sue");

?>

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

  public static function greet2($name, $name2){

    echo "Hi $name and $name2. This is Baz.\n";

  }

}


Foo::greet("David");

Bar::greet("Anne");

Baz::greet("Bob");

Baz::greet2("Bob", "Sue");

?>

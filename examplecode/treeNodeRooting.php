construct tree
construct tree with parent aware tree nodes from it

input the node name
search for the input node name
from there, input whether to append or prepend other nodes
thus prepend or append the parent
  then append or prepend the [other] children of that parent, with that parent's parent as well, prepended or appended

so, to resume
make the parent a child, appending or prepending
from the child that was a parent, appended or prepended, take its old parent
  and do again
    set it the child of that child, appending or prepending

continue

note, you will need variables

  $oldParent

  $newParent // the current node

  but you need to keeep track of

    $oldParent

  because it will have

    getChildren()

    $oldParentParent

then in a loop, continue


function makeRoot() {



}





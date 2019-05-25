/**
 * NodeList.java
 * 
 * Low-level Java implementation of a linked list,
 * including support for a bunch of operations which
 * can be carried out on such a data srtucture.
 * 
 * Copyright (C) Neil Zanella <coding@neilzanella.com>. All rights reserved.
 */

package com.neilzanella.nodelistutils;

import java.util.Vector;

/********************************************************************
 * NodeList:                                                        *
 ********************************************************************/

class NodeList {

  /********************************************************************
   * Node:                                                            *
   ********************************************************************/

  protected static class Node {

    private Object data;

    private Node next;

    /********************************************************************
     * Construct new node.                                              *
     ********************************************************************/

    Node(Object data) {

      this.data = data;

    }

    /********************************************************************
     * Set node data.                                                   *
     ********************************************************************/

    synchronized void setData(Object data) {

      this.data = data;

    }

    /********************************************************************
     * Retrieve node data.                                              *
     ********************************************************************/

    synchronized Object data() {

      return data;

    }

    /********************************************************************
     * Set next node.                                                   *
     ********************************************************************/

    synchronized void setNext(Node next) {

      this.next = next;

    }

    /********************************************************************
     * Retrieve next node.                                              *
     ********************************************************************/

    synchronized Node next() {

      return next;

    }

  }

  /********************************************************************
   * NodeSelector:                                                    *
   ********************************************************************/

  protected static interface NodeSelector {

    abstract Node selFirst(Node node);

    abstract Node selNext(Node node);

  }

  /********************************************************************
   * NodeCriterion:                                                   *
   ********************************************************************/

  protected static interface NodeCriterion {

    abstract boolean crt(Node node);

  }

  /********************************************************************
   * Criterion:                                                       *
   ********************************************************************/

  protected static interface Criterion {

    abstract boolean crt(Object data);

  }

  /********************************************************************
   * NodeToDataTransformer:                                           *
   ********************************************************************/

  protected static interface NodeToDataTransformer {

    abstract Object trans(Node node);

  }

  /********************************************************************
   * NodeFunctor:                                                     *
   ********************************************************************/

  protected static interface NodeFunctor {

    abstract void func(Node node);

  }

  /********************************************************************
   * Functor:                                                         *
   ********************************************************************/

  protected static interface Functor {

    abstract void func(Object data);

  }

  /********************************************************************
   * Comparator:                                                      *
   ********************************************************************/

  protected static interface Comparator {

    abstract int cmp(Object oa, Object ob);

  }

  private Node first;

  /********************************************************************
   * Retrieve first node list node.                                   *
   ********************************************************************/

  protected Node first() {

    return first;

  }

  /********************************************************************
   * Construct empty node list.                                       *
   ********************************************************************/

  protected NodeList() {

  }

  /********************************************************************
   * Construct node list based on specified array of data elements.   *
   ********************************************************************/

  protected NodeList(Object[] data) {

    if (data != null) {

      for (int i = 0; i < data.length; i++) {

        dataPushBack(data[i]);

      }

    }

  }

  /********************************************************************
   * Construct node list based on specified vector of data elements.  *
   ********************************************************************/

  protected NodeList(Vector vec) {

    if (vec != null) {

      for (int i = 0; i < vec.size(); i++) {

        dataPushBack(vec.elementAt(i));

      }

    }

  }

  /********************************************************************
   * Attach new node to current node representing beginning of list.  *
   ********************************************************************/

  synchronized protected void dataPushFront(Object data) {

    Node next = first;

    first = new Node(data);

    first.setNext(next);

  }

  /********************************************************************
   * Attach new node to end of node list starting with current node.  *
   ********************************************************************/

  protected void dataPushBack(Object data) {

    Node last = new Node(data);

    if (first != null) {

      Node node = first;

      while (node.next() != null) {

        node = node.next();

      }

      node.setNext(last);

    } else {

      first = last;

    }

  }

  /********************************************************************
   * Pop node and retrieve data from node representing list start.    *
   ********************************************************************/

  protected Object dataPopFront() {

    if (first != null) {

      Object data = first.data();

      first = first.next();

      return data;

    }

    return null;

  }

  /********************************************************************
   * Retrieve node from end of nodes list starting with current node. *
   ********************************************************************/

  protected Object dataPopBack() {

    if (first != null) {

      Node node = first;

      if (node.next() != null) {

        while (node.next().next() != null) {

          node = node.next();

        }

        Node last = node.next();

        node.setNext(null);

        return last.data();

      } else {

        Node last = first;

        first = null;

        return last.data();

      }

    }

    return null;

  }

  /********************************************************************
   * Retrieve number of generic nodes traversed using given selector. *
   ********************************************************************/

  protected int countByNodeSelector(NodeSelector sel) {

    // zero initialize node count

    int count = 0;

    // iterate through selected nodes

    for (Node node = sel.selFirst(first); node != null; node = sel.selNext(node)) {

      // increment node count

      ++count;

    }

    // return node count

    return count;

  }

  /********************************************************************
   * Retrieve number of generic nodes satisfying node criterion.      *
   ********************************************************************/

  protected int countByNodeCriterion(NodeCriterion crt) {

    // zero initialize node count

    int count = 0;

    // iterate through given nodes

    for (Node node = first; node != null; node = node.next()) {

      if (crt.crt(node)) {

        // increment node count

        ++count;

      }

    }

    // return node count

    return count;

  }

  /********************************************************************
   * Retrieve number of generic nodes satisfying given criterion.     *
   ********************************************************************/

  protected int countByCriterion(Criterion crt) {

    // zero initialize node count

    int count = 0;

    // iterate through given nodes

    for (Node node = first; node != null; node = node.next()) {

      if (crt.crt(node.data())) {

        // increment node count

        ++count;

      }

    }

    // return node count

    return count;

  }

  /********************************************************************
   * Retrieve number of generic nodes in generic node list.           *
   ********************************************************************/

  public int count() {

    int count = 0;

    for (Node node = first; node != null; node = node.next()) {

      ++count;

    }

    return count;

  }

  /********************************************************************
   * Retrieve index of generic node in generic node list.             *
   ********************************************************************/

  protected int dataIndexOf(Object data) {

    // zero initialize node index

    int index = 0;

    // iterate through given nodes

    for (Node node = first; node != null; node = node.next()) {

      // check whether current node matches specified data

      if (node.data() == data) {

        // return node index

        return index;

      }

      // increment node index

      ++index;

    }

    // return no node index

    return -1;

  }

  /********************************************************************
   * Retrieve node data by index into generic nodes list.             *
   ********************************************************************/

  protected Object dataPeekAt(int index) {

    // zero initialize node count

    int count = 0;

    // iterate through given nodes

    for (Node node = first; node != null; node = node.next()) {

      // check whether we have reached specified node index

      if (count++ == index) {

        // return node data

        return node.data();

      }

    }

    // return no node data

    return null;

  }

  /********************************************************************
   * Retrieve next node which satisfies given criterion.              *
   ********************************************************************/

  protected Node nodeByCriterion(Criterion crt) {

    // iterate through given nodes

    for (Node node = first; node != null; node = node.next()) {

      // check whether criterion applies to current node

      if (crt.crt(node.data())) {

        // return current node

        return node;

      }

    }

    // return no node

    return null;

  }

  /********************************************************************
   * Retrieve first match for node data using specified criterion.    *
   ********************************************************************/

  protected Object dataByCriterion(Criterion crt) {

    // retrieve node by specified criterion

    Node node = nodeByCriterion(crt);

    // ensure node is not null

    if (node != null) {

      // retrieve node data

      return node.data();

    }

    // retrieve no data

    return null;

  }

  /********************************************************************
   * Retrieve array of data pointers using specified selector.        *
   ********************************************************************/

  protected Object[] dataPeekArrayByNodeSelector(NodeSelector sel) {

    // retrieve number of nodes

    int count = countByNodeSelector(sel);

    // ensure number of nodes is nonzero

    if (count > 0) {

      // allocate memory for node array

      Object[] dataArray = new Object[count];

      // initialize array index

      int i = 0;

      // iterate through selected nodes

      for (Node node = sel.selFirst(first); node != null; node = sel.selNext(node)) {

        // set current data array element to current node data

        dataArray[i++] = node.data();

      }

      // return data array

      return dataArray;      

    }

    // return no data array

    return null;

  }

  /********************************************************************
   * Retrieve array of data pointers specifying given node criterion. *
   ********************************************************************/

  protected Object[] dataPeekArrayByNodeCriterion(NodeCriterion crt) {

    // retrieve number of nodes

    int count = countByNodeCriterion(crt);

    // ensure number of nodes is nonzero

    if (count > 0) {

      // allocate memory for node array

      Object[] dataArray = new Object[count];

      // initialize array index

      int i = 0;

      // iterate through nodes

      for (Node node = first; node != null; node = node.next()) {

        // check whether current node satisfies criterion

        if (crt.crt(node)) {

          // set current data array element to current node data

          dataArray[i++] = node.data();

        }

      }

      // return data array

      return dataArray;      

    }

    // return no data array

    return null;

  }

  /********************************************************************
   * Retrieve array of nodes specifying given node criterion.         *
   ********************************************************************/

  protected Node[] nodePeekArrayByCriterion(Criterion crt) {

    // retrieve number of nodes

    int count = countByCriterion(crt);

    // ensure number of nodes is nonzero

    if (count > 0) {

      // allocate memory for node array

      Node[] nodeArray = new Node[count];

      // initialize array index

      int i = 0;

      // iterate through nodes

      for (Node node = first; node != null; node = node.next()) {

        // check whether current node satisfies criterion

        if (crt.crt(node.data())) {

          // set current node array element to current node

          nodeArray[i++] = node;

        }

      }

      // return node array

      return nodeArray;

    }

    // return no node array

    return null;

  }

  /********************************************************************
   * Retrieve array of data pointers specifying given criterion.      *
   ********************************************************************/

  protected Object[] dataPeekArrayByCriterion(Criterion crt) {

    // retrieve number of nodes

    int count = countByCriterion(crt);

    // ensure number of nodes is nonzero

    if (count > 0) {

      // allocate memory for node array

      Object[] dataArray = new Object[count];

      // initialize array index

      int i = 0;

      // iterate through nodes

      for (Node node = first; node != null; node = node.next()) {

        // check whether current node satisfies criterion

        if (crt.crt(node.data())) {

          // set current data array element to current node data

          dataArray[i++] = node.data();

        }

      }

      // return data array

      return dataArray;      

    }

    // return no data array

    return null;

  }

  /********************************************************************
   * Retrieve array of data pointers from generic pointer list.       *
   ********************************************************************/

  protected Object[] dataPeekArray() {

    // retrieve number of nodes

    int count = count();

    // ensure number of nodes is nonzero

    if (count > 0) {

      // allocate memory for node array

      Object[] dataArray = new Object[count];

      // initialize array index

      int i = 0;

      // iterate through nodes

      for (Node node = first; node != null; node = node.next()) {

        // set current data array element to current node data

        dataArray[i++] = node.data();

      }

      // return data array

      return dataArray;

    }

    // return no data array

    return null;

  }

  /********************************************************************
   * Retrieve peeked nodes traversed using given selector.            *
   ********************************************************************/

  protected NodeList peekAllByNodeSelector(NodeSelector sel) {

    NodeList nodeList = new NodeList();

    // iterate through nodes

    for (Node node = sel.selFirst(first); node != null; node = sel.selNext(node)) {

      // shallow copy data elements

      nodeList.dataPushBack(node.data());

    }

    // return matching nodes

    return nodeList;

  }

  /********************************************************************
   * Retrieve peeked nodes traversed using given selector.            *
   ********************************************************************/

  protected NodeList peekNodeAllByNodeSelector(NodeSelector sel) {

    NodeList nodeList = new NodeList();

    // iterate through nodes

    for (Node node = sel.selFirst(first); node != null; node = sel.selNext(node)) {

      // shallow copy data elements

      nodeList.dataPushBack(node);

    }

    // return matching nodes

    return nodeList;

  }

  /********************************************************************
   * Retrieve peeked nodes matching satisfying criterion in new list. *
   ********************************************************************/

  protected NodeList peekAllByNodeCriterion(NodeCriterion crt) {

    // initialize matching nodes

    NodeList nodeList = new NodeList();

    // iterate through nodes

    for (Node node = first; node != null; node = node.next()) {

      // check whether given criterion is satisfied

      if (crt.crt(node)) {

        // shallow copy data elements

        nodeList.dataPushBack(node.data());

      }

    }

    // return matching nodes

    return nodeList;

  }

  /********************************************************************
   * Retrieve peeked nodes matching satisfying criterion in new list. *
   ********************************************************************/

  protected NodeList peekAllByCriterion(Criterion crt) {

    // initialize matching nodes

    NodeList nodeList = new NodeList();

    // iterate through nodes

    for (Node node = first; node != null; node = node.next()) {

      // check whether given criterion is satisfied

      if (crt.crt(node.data())) {

        // shallow copy data elements

        nodeList.dataPushBack(node.data());

      }

    }

    // return matching nodes

    return nodeList;

  }

  /********************************************************************
   * Retrieve peeked nodes with given node functor applied to them.   *
   ********************************************************************/

  protected NodeList peekAllByNodeToDataTransformer(NodeToDataTransformer trans) {

    // initialize matching nodes

    NodeList nodeList = new NodeList();

    // iterate through nodes

    for (Node node = first; node != null; node = node.next()) {

      // shallow copy data elements

      nodeList.dataPushBack(trans.trans(node));

    }

    // return matching nodes

    return nodeList;

  }

  /********************************************************************
   * Detatch first matching node satisfying criterion.                *
   ********************************************************************/

  protected Object detatchByCriterion(Criterion crt) {

    // check whether first node exists

    if (first != null) {

      // check node at beginning of node list

      if (crt.crt(first.data())) {

        // store matching node data

        Object data = first.data();

        // reset beginning of list

        first = first.next();

        // return detatched data

        return data;

      }

      // initialize node to first nonmatching node

      Node node = first;

      // iterate through following nodes

      while (node.next() != null) {

        // check whether following node satisfies criterion

        if (crt.crt(node.next().data())) {          

          // store matching node data

          Object data = node.next().data();

          // advance next node to node following detatched node

          node.setNext(node.next().next());

          // return detatched data

          return data;

        }

      }

    }

    // return no data

    return null;

  }


  /********************************************************************
   * Detatch all matching nodes satisfying criterion onto new list.   *
   ********************************************************************/

  protected NodeList detatchAllByCriterion(Criterion crt) {

    // initialize matching nodes

    NodeList nodeList = new NodeList();

    // iterate through nodes matching at beginning of node list

    while (first != null && crt.crt(first.data())) {

      // append matching node to node list

      nodeList.dataPushBack(first.data());

      // reset beginning of list

      first = first.next();

    }

    // initialize node to first nonmatching node

    Node node = first;

    // ensure that first nonmatching node exists

    if (node != null) {

      // iterate through following nodes

      while (node.next() != null) {

        // check whether following node satisfies criterion

        if (crt.crt(node.next().data())) {          

          // append matching node to node list

          nodeList.dataPushBack(node.next().data());

          // advance next node to node following detatched node

          node.setNext(node.next().next());

        }

        else { 

          // advance to next nonmatching node

          node = node.next();

        }

      }

    }

    // return node list of detatched nodes

    return nodeList;

  }

  /********************************************************************
   * Apply given node functor to all generic node list nodes.         *
   ********************************************************************/

  protected void applyNodeFunctor(NodeFunctor func) {

    for (Node node = first; node != null; node = node.next()) {

      func.func(node);

    }

  }

  /********************************************************************
   * Apply given node functor to list nodes satisfying criterion.     *
   ********************************************************************/

  protected void applyNodeFunctorByCriterion(NodeFunctor func, Criterion crt) {

    for (Node node = first; node != null; node = node.next()) {

      if (crt.crt(node.data())) {

        func.func(node);

      }

    }

  }

  /********************************************************************
   * Apply given functor to all generic nodes in generic node list.   *
   ********************************************************************/

  protected void applyFunctor(Functor func) {

    for (Node node = first; node != null; node = node.next()) {

      func.func(node.data);

    }

  }

  /********************************************************************
   * Apply given functor to all generic nodes satisfying criterion.   *
   ********************************************************************/

  protected void applyFunctorByCriterion(Functor func, Criterion crt) {

    for (Node node = first; node != null; node = node.next()) {

      if (crt.crt(node.data)) {

        func.func(node.data);

      }

    }

  }

  /******************************************************************************
   * Return a sorted list based on current list.                                *
   ******************************************************************************/

  protected NodeList sortedList(Comparator cmp) {

    Object a[] = dataPeekArray();

    dataHeapSort(a, cmp);

    return new NodeList(a);

  }

  /******************************************************************************
   * dataHeapParentNodePos: Array index of parent node.                         *
   ******************************************************************************/

  private static int heapParentPos(int pos) {

    return pos != 0 ? (pos - 1) / 2 : 0;

  }

  /******************************************************************************
   * dataHeapLeftNodePos: Array index of node to the left of current node.      *
   ******************************************************************************/

  private static int heapLeftPos(int pos) {

    return 2 * pos + 1;

  }

  /******************************************************************************
   * dataHeapRightNodePos: Array index of node to the right of current node.    *
   ******************************************************************************/

  private static int heapRightPos(int pos) {

    return 2 * pos + 2;

  }

  /******************************************************************************
   * Use heapsort to sort a contiguous array of data objects                    *
   * comparing contact and folder nodes with comparator cmp.                    *
   *                                                                            *
   *   Naming scheme details:                                                   *
   *     number of array elements: n                                            *
   *     root node value: rnv                                                   *
   *     current node value: nv                                                 *
   *     current node position: np                                              *
   ******************************************************************************/

  protected static void dataHeapSort(Object a[], Comparator cmp) {

    if (a != null) {

      int n = a.length;

      Object rnv, nv;

      int np;

      if (n > 1) {

        for (np = heapParentPos(n - 1); np > 0; np--) {

          dataHeapSortSink(a, n, np, cmp);

        }

        dataHeapSortSink(a, n, np, cmp);

        for (np = n - 1; np > 0; np--) {

          rnv = a[0];

          nv = a[np];

          if (rnv != nv) {

            a[0] = nv;

            a[np] = rnv;

            dataHeapSortSink(a, --n, 0, cmp);

          }

        }

      }

    }

  }

  /******************************************************************************
   * Heap sort sink subfunction on generic data pointers.                       *
   * Recursively swap node with largest of larger subchilds                     *
   * if any exist comparing generic data pointers with comparator cmp.          *
   *                                                                            *
   *  Naming scheme details:                                                    *
   *     number of array elements: n                                            *
   *     top node true value: tnv                                               *
   *     parent node true position and true value: pnp and pnv                  *
   *     left node true position and true value: lnp and lnv                    *
   *     right node true position and true value: rnp and rnv                   *
   *     current node true position and virtual value: np and nv                *
   *                                                                            *
   ******************************************************************************/

  private static void dataHeapSortSink(Object a[], int n, int np, Comparator cmp) {

    int pnp, lnp, rnp;

    Object tnv, pnv, lnv, rnv, nv;

    tnv = a[np];

    nv = tnv;

    do {

      pnp = np;

      pnv = nv;

      nv = tnv;

      if ((lnp = heapLeftPos(pnp)) <= n - 1 && cmp.cmp(nv, (lnv = a[lnp])) < 0) {

        np = lnp;

        nv = lnv;

      }

      if ((rnp = heapRightPos(pnp)) <= n - 1 && cmp.cmp(nv, (rnv = a[rnp])) < 0) {

        np = rnp;

        nv = rnv;

      }

      if (pnv != nv) {

        a[pnp] = nv;

      }

    } while (pnp != np);

    if (nv != tnv) {

      a[np] = tnv;

    }

  }

}

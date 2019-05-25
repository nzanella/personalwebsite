<?php

  // Walrus Translation Software
  // 
  // Copyright (c) Neil Zanella. All rigts reserved.

  require_once('include/connect.php');

  $query = "SELECT lang FROM Languages WHERE lcode = '" . $_GET['lcode'] . "'";

  if (DB::isError($lang = $db->getOne($query))) die($lang->getMessage());

?>
          <h1><?= $lang ?> Application Text Translation</h1>
          <table>
            <tr>
              <td colspan="2"><hr /></td>
            </tr>
<?php

  $query = "SELECT text, tran FROM Source NATURAL JOIN SubmittedTranslations WHERE "
         . "lcode = '" . $_GET['lcode'] . "' AND "
         . "ccode = '" . $_GET['ccode'] . "' AND "
         . "username = '" . $_GET['username'] . "' "
         . "ORDER BY text ";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());

?>
            <tr>
              <td><?= $row['text'] ?></td>
              <td><?= $row['tran'] ?></td>
            </tr>
            <tr>
              <td colspan="2"><hr /></td>
            </tr>
<?php } ?>
          </table>
          <p><a href="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translators">Back to Walrus Translation Translators Page</a></a>

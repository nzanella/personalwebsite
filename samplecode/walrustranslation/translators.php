<?php

  // Walrus Translation Software
  // 
  // Copyright (c) Neil Zanella. All rigts reserved.

  require_once("include/connect.php");

  ////////////////////////////////////////////////////////////
  // Display list of contributing translators.              //
  ////////////////////////////////////////////////////////////
?>
          <p><a name="contributors">
            The following translations have been contributed to the Walrus Translation project:
          </a></p>
            <table align="center" width="80%" cellspacing="10" cellpadding="10">
              <tr>
                <th align="left">
                  Application Text
                </th>
                <th align="left">
                  Contributors
                </th>
                <th align="left">
                  Contributor E-mails
                </th>
              </tr>
<?php

  $query = " SELECT T.username AS username, T.lcode AS lcode, T.ccode AS ccode, "
         . "        T.mentionid AS mentionid, L.lang AS lang, C.country AS country, "
         . "        U.realname AS realname, U.email AS email "
         . " FROM Translations T, Translators U, Languages L, Countries C "
         . " WHERE T.username = U.username "
         . " AND T.lcode = L.lcode "
         . " AND T.ccode = C.ccode "
         . " ORDER BY lang, realname ";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());

?>
              <tr>
                <td>
                  <a href="index.php?page=translations&amp;lcode=<?= $row['lcode'] ?>&amp;ccode=<?= $row['ccode'] ?>&amp;username=<?= $row['username'] ?>"><?= $row['lang'] ?><?= $row['ccode'] == '--' ? '' : ', ' . $row['country'] ?></a>
                </td>
                <td>
<?php if ($row['mentionid'] == 1 or $row['mentionid'] == 2 or $row['mentionid'] == 3) { ?>
                  <?= $row['realname'] ?>
<?php } else { ?>
                  (anonymous)
<?php } ?>
                </td>
                <td>
<?php if ($row['mentionid'] == 1) { ?>
                  <a href="mailto:<?= $row['email'] ?>">&lt;<?= $row['email'] ?>&gt;</a>
<?php } else { ?>
                  (undisclosed)
<?php } ?>
                </td>
              </tr>
<?php } ?>
           </table>
           <p>Many thanks to our Walrus Translation translation team for their excellent contributions!!!</p>

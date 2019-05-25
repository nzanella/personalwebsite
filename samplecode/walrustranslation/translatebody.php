<!--

  Walrus Translation Software

  Copyright (c) Neil Zanella. All rigts reserved.

-->
          <h2><a name="step1">Step 1. Register as a Translator</a></h2>
          <p align="justify">In order to contribute as a translator you must first register. You will then be able to use your user name and password to access your translations. Your e-mail address will be used to contact you should there be any questions or concerns regarding your translations. In case you have already registered please proceed directly to Step 2.</p>
          <form method="post" action="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translate#step1">
            <table>
              <tr>
                <td>Full Name:</td>
                <td><input type="text" name="walrusrealname" value="<?php if ($_POST['walrusstep'] == 1) echo $_POST['walrusrealname'] ?>" /></td>
              </tr>
              <tr>
                <td>E-mail Address:</td>
                <td><input type="text" name="walrusemail" value="<?php if ($_POST['walrusstep'] == 1) echo  $_POST['walrusemail'] ?>" /></td>
              </tr>
              <tr>
                <td>User Name:</td>
                <td><input type="text" name="walrususername" value="<?php if ($_POST['walrusstep'] == 1) echo $_POST['walrususername'] ?>" /></td>
              </tr>
              <tr>
                <td>Password:</td>
                <td><input type="password" name="walruspassword" /></td>
              </tr>
              <tr>
                <td>Confirm Password:</td>
                <td><input type="password" name="walrusconfpassword" /></td>
              </tr>
            </table>
            <br />
<?php if ($_POST['walrusstep'] == 1 and $error) { ?>
            <font color="red">Error: <?= $error ?>.</font>
            <br /><br />
<?php } else if ($_POST['walrusstep'] == 1 and $message) { ?>
            <font color="blue">Success: <?= $message ?>!</font>
            <br /><br />
<?php } ?>
            <input type="hidden" name="walrusstep" value="1" />
            <input type="submit" value="Register Me as a Translator" />
          </form>
          <h2><a name="step2">Step 2. Login</a></h2>
          <p align="justify">To carry out your translations you must login to the system using the username and password you specified when you registered as a target Walrus Translation Software application translator for the software being localized.</p>
          <form method="post" action="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translate#step2">
            <table>
              <tr>
                <td>User Name:</td>
                <td><input type="text" name="walrususername" value="<?php if ($_POST['walrusstep'] >= 2) if ($_POST['walrususername']) echo $_POST['walrususername']; else echo $_COOKIE['walrususername'] ?>" /></td>
              </tr>
              <tr>
                <td>Password:</td>
                <td><input type="password" name="walruspassword" /></td>
              </tr>
            </table>
            <br />
<?php if ($_POST['walrusstep'] == 2 and $error) { ?>
            <font color="red">Error: <?= $error ?>.</font>
            <br /><br />
<?php } else if ($_POST['walrusstep'] == 2 and $message) { ?>
            <font color="blue">Success: <?= $message ?>!</font>
            <br /><br />
<?php } ?>
            <input type="hidden" name="walrusstep" value="2" />
            <input type="submit" value="Log Me In" />
          </form>
          <h2><a name="step3">Step 3: Select Target Locale</a></h2>
          <p align="justify">Congratulations! Now that you have logged in you must choose the target locale. A target locale consists of a target language and an optional target variant. The target language is the language into which you will translate the application text. In some cases language content may vary more or less significantly according to location. If you feel that this applies to you then specify the target variant otherwise omit the target variant altogether. You may translate application text into more than one locale, but you can only translate into one locale at a time.</p>
          <form method="post" action="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translate#step3">
            <table>
              <tr>
                <td>Target Language:</td>
                <td>
                  <select name="walruslcode">
<?php

  $query = "SELECT lcode, lang FROM Languages WHERE lcode <> 'en' ORDER BY lang";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());

?>
                    <option<?php if ($_POST['walrusstep'] >= 3 and ($row['lcode'] == $_POST['walruslcode'] or (!isset($_POST['walruslcode']) and $row['lcode'] == $_COOKIE['walruslcode']))) echo " selected=\"selected\"" ?> value="<?= $row['lcode'] ?>"><?= $row['lang'] ?></option>
<?php } ?>
                  </select>
                </td>
              <tr>
              </tr>
                <td>Target Variant:</td>
                <td>
                  <select name="walrusccode">
<?php

  $query = "SELECT ccode, country FROM Countries ORDER BY country";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());

?>
                    <option<?php if ($_POST['walrusstep'] >= 3 and ($row['ccode'] == $_POST['walrusccode'] or (!isset($_POST['walrusccode']) and $row['ccode'] == $_COOKIE['walrusccode']))) echo " selected=\"selected\"" ?> value="<?= $row['ccode'] ?>"><?= $row['country'] ?></option>
<?php } ?>
                  </select>
                </td>
              </tr>
            </table>
<?php if ($_POST['walrusstep'] == 3 and $error) { ?>
            <br /><br />
            <font color="red">Error: <?= $error ?>.</font>
<?php } else if ($_POST['walrusstep'] == 3 and $message) { ?>
            <br /><br />
            <font color="blue">Success: <?= $message ?>!</font>
<?php } ?>
            <br /><br />
            <input type="hidden" name="walrusstep" value="3" />
            <input type="submit" value="Target Locale Selected" />
          </form>
          <h2><a name="step4">Step 4. Translate Text</a></h2>
          <p align="justify">The following table displays application text for you to translate. For each line of text on the left hand side please enter the corresponding text in the language you are translating into on the right hand side. In order to prevent your additions and modifications from being lost, please ensure you save your changes as often as you like, <i>as well as immediately prior to proceeding to Step 5,</i> by clicking on the <i><a href="#save">Save Translation</a></i> button at the bottom of the lines to be translated. It is important that you save all of your edits prior to proceeding to Step 5 or your edits will be lost and as a consequence will not submitted. The actions of saving and submitting do not affect contributions by other translators. All translations are subject to revision by their corresponding maintainers. In case you do not have the target Walrus Translation Software application to be localized installed on your computer the <a target="_blank" href="<?= $_SERVER['PHP_SELF']; ?>?page=screenshots">screenshots page</a> should provide you with some guidance on how the translated text will appear in context. Thank you for your contribution!</p>
          <form method="post" action="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translate#step4">
<?php if ($_POST['walrusstep'] == 4 and $error) { ?>
            <font color="red">Error: <?= $error ?>.</font>
            <br /><br />
<?php } else if ($_POST['walrusstep'] == 4 and $message) { ?>
            <font color="blue">Success: <?= $message ?>!</font>
            <br /><br />
<?php } ?>
            <table>
              <tr>
                <th>Source Text<br /></th>
                <th>Translated Text <?= $lang ? '(' . $lang . ($country ? " as used in $country" : '') . ')' : '' ?></th>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
<?php

  $username = $_POST['walrususername'] ? $_POST['walrususername'] : $_COOKIE['walrususername'];

  $lcode = $_POST['walruslcode'] ? $_POST['walruslcode'] : $_COOKIE['walruslcode'];
  $ccode = $_POST['walrusccode'] ? $_POST['walrusccode'] : $_COOKIE['walrusccode'];

  $query = "SELECT textid, text FROM Source";

  if (DB::isError($resultA = $db->query($query))) die($resultA->getMessage());

  $query = "SELECT textid, tran FROM WorkingTranslations "
         . "WHERE username = '" . $username . "' "
         . "AND lcode = '" . $lcode . "' "
         . "AND ccode = '" . $ccode . "'";

  if (DB::isError($rowsB = $db->getAll($query, DB_FETCHMODE_ASSOC))) die($rowsB->getMessage());

  while ($rowA = $resultA->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($rowA)) die($rowA->getMessage());

    $tran = "";

    foreach ($rowsB as $rowB)

      if ($rowB['textid'] == $rowA['textid'])

        $tran = $rowB['tran'];

?>
              <tr>
                <td>
                  <?= $rowA['text'] ?>
                </td>
                <td>
                  <input size="60" name="<?php echo 'walrustext' . $rowA['textid'] ?>" value="<?= $tran ?>" />
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;
                </td>
                <td>
                  <font color="blue"><?php if ($_POST['walrusstep'] == 4) echo $tran ?></font>
                </td>
              </tr>
<?php } ?>
            </table>
            <br />
<?php if ($_POST['walrusstep'] == 4 and $error) { ?>
            <font color="red">Error: <?= $error ?>.</font>
            <br /><br />
<?php } else if ($_POST['walrusstep'] == 4 and $message) { ?>
            <font color="blue">Success: <?= $message ?>!</font>
            <br /><br />
<?php } ?>
            <input type="hidden" name="walrusstep" value="4" />
            <table width="100%">
              <tr>
                <td>
                  <a name="save"><input type="submit" value="Save Translaton" /></a>
                </td>
                <td align="right">
                  <!-- not to confuse, could display these only if
                       there is more than one translation for the
                       specified locale
                  <input type="submit" value="Load Private" />
                  <input type="submit" value="Load Public" />
                  -->
                </td>
              </tr>
            </table>
          </form>
          <h2><a name="step5">Step 5. Finish</a></h2>
          <p align="justify">Please ensure you have saved your translation before proceeding and you&#39;re done! Choose from the following options concerning your translation and click to submit your contribution for inclusion!</p>
          <form method="post" action="http://<?= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; ?>?page=translate#step5">
            <select name="walrusmention">
<?php

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  $query = "SELECT mentionid, mention FROM Mentions";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());
?>
              <option value="<?= $row['mentionid'] ?>"<?php if ((!isset($mentionid) and $row['mentionid'] == 1) or $mentionid == $row['mentionid']) echo ' selected="selected"' ?>><?= $row['mention'] ?></option>
<?php } ?>
            </select>
            <br /><br />
            <input type="checkbox" name="walrusmaintain"<?php if (isset($_POST['walrusmaintain'])) echo ' checked="checked"' ?> />&nbsp;&nbsp;I would like to be the maintainer of contributed translations into this particular target locale.
            <br /><br />
<?php if ($_POST['walrusstep'] == 5 and $error) { ?>
            <font color="red">Error: <?= $error ?>.</font>
            <br /><br />
<?php } else if ($_POST['walrusstep'] == 5 and $message) { ?>
            <font color="blue">Success: <?= $message ?>!</font>
            <br /><br />
            <table>
              <tr>
                <th>Source Text<br /></th>
                <th>Translated Text <?= $lang ? '(' . $lang . ($country ? " as used in $country" : '') . ')' : '' ?></th>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
<?php

  $query = "SELECT text, tran FROM Source NATURAL JOIN SubmittedTranslations "
         . "WHERE username = '" . $savedusername . "' "
         . "AND lcode = '" . $savedlcode . "' "
         . "AND ccode = '" . $savedccode . "'";

  if (DB::isError($result = $db->query($query))) die($result->getMessage());

  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

    if (DB::isError($row)) die($row->getMessage());

?>
              <tr>
                <td>
                  <?= $row['text'] ?>
                </td>
                <td>
                  <font color="blue"><?= $row['tran'] ?></font>
                </td>
              </tr>
<?php } ?>
            </table>
            <br /><br />
<?php } ?>
            <input type="hidden" name="walrusstep" value="5" />
            <input type="submit" value="Submit Translation and Logout" />
          </form>

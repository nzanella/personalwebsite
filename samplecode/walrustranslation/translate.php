<?php

  // Walrus Translation Software
  // 
  // Copyright (c) Neil Zanella. All rigts reserved.

  require_once("include/connect.php");

  ////////////////////////////////////////////////////////////
  // Process post data: validate data, manipulate database, //
  //                    set cookies and response messages.  //
  ////////////////////////////////////////////////////////////

  function ensureUnsetTrans() {

    global $db;

    $query = "SELECT COUNT(textid) FROM Source";

    if (DB::isError($count = $db->getOne($query))) die($count->getMessage());

    for ($i = 1; $i <= $count; $i++)

      if (isset($_COOKIE["walrustext$count"])) unset($_COOKIE["walrustext$count"]);

  }

  // unset placeholders for cookies to be removed upon logging out

  unset($savedusername);
  unset($savedlcode);
  unset($savedccode);

  // trim all post variables except for passwords

  foreach ($_POST as $key => $value)
    if ($key != 'walruspassword' and $key != 'walrusconfpassword')
      $_POST["$key"] = trim($_POST["$key"]);

  if (!isset($_POST['walrusstep'])) {

    // ensure cookies to be used in following steps are unset

    if (isset($_COOKIE['walrususername'])) unset($_COOKIE['walrususername']);
    if (isset($_COOKIE['walruslcode'])) unset($_COOKIE['walruslcode']);
    if (isset($_COOKIE['walrusccode'])) unset($_COOKIE['walrusccode']);
    if (isset($_COOKIE['walrusmention'])) unset($_COOKIE['walrusmention']);
    if (isset($_COOKIE['walrusmaintainer'])) unset($_COOKIE['walrusmaintainer']);
    ensureUnsetTrans();

  }

  // step 1 actions

  else if ($_POST['walrusstep'] == 1) {

    // check that fields have been correctly specified

    if (!$_POST['walrusrealname'])

      $error = "full name not specified";

    else if (!$_POST['walrusemail'])

      $error = "email not specified";

    else if (!$_POST['walrususername'])

      $error = "user name not specified";

    else if (!$_POST['walruspassword'])

      $error = "password not specified";

    else if ($_POST['walruspassword'] != $_POST['walrusconfpassword'])

      $error = "passwords do not match";

    else {

      // check whether user with specified username already exists

      $query = "SELECT password FROM Translators "
             . "WHERE username = '" . $_POST['walrususername'] . "'";

      if (DB::isError($password = $db->getOne($query))) die($password->getMessage());

      if ($password) {

        // check that new user pasword matches old user password

        if ($_POST['walruspassword'] == $password) {

          // since old password and new password match we may update other fields

          $message = "contact information for translator " . $_POST['walrususername']
                   . " updated successfully";

        } else {

          $error = "contact information for translator " . $_POST['walrususername']
                 . " could not be updated because old password does not match new password";

        }

      } else {

        // add translator to database

        $query = "INSERT INTO Translators (username, realname, email, password) VALUES ('"
               . $_POST['walrususername'] . "', '" . $_POST['walrusrealname'] . "', '"
               . $_POST['walrusemail'] .  "', '" . $_POST['walruspassword'] . "')";

        if (DB::isError($result = $db->query($query))) die($result->getMessage());

        $message = "new translator " . $_POST['walrususername'] . " registered successfully";

      }

    }

    // conditionally unset cookie values

    if (isset($_COOKIE['walruslcode'])) unset($_COOKIE['walruslcode']);
    if (isset($_COOKIE['walrusccode'])) unset($_COOKIE['walrusccode']);
    if (isset($_COOKIE['walrusmention'])) unset($_COOKIE['walrusmention']);
    if (isset($_COOKIE['walrusmaintainer'])) unset($_COOKIE['walrusmaintainer']);
    ensureUnsetTrans();

  }

  // step 2 actions

  else if ($_POST['walrusstep'] == 2) {

    // check that fields have been correctly specified

    if (!$_POST['walrususername'])

      $error = "user name not specified";

    else if (!$_POST['walruspassword'])

      $error = "password not specified";

    else {

      // check whether specified username exists

      $query = "SELECT password FROM Translators "
             . "WHERE username = '" . $_POST['walrususername'] . "'";

      if (DB::isError($password = $db->getOne($query))) die($password->getMessage());

      if ($password) {

        // check whether specified password matches registered password

        if ($_POST['walruspassword'] == $password) {

          $message = "user " . $_POST['walrususername'] . " authenticated successfully";

          setcookie('walrususername', $_POST['walrususername']);

        } else {

          $error = "invalid password";

        }

      } else {

        $error = "user name not found";

      }

    }
 
    // conditionally unset cookie values

    if (isset($_COOKIE['walruslcode'])) unset($_COOKIE['walruslcode']);
    if (isset($_COOKIE['walrusccode'])) unset($_COOKIE['walrusccode']);
    if (isset($_COOKIE['walrusmention'])) unset($_COOKIE['walrusmention']);
    if (isset($_COOKIE['walrusmaintainer'])) unset($_COOKIE['walrusmaintainer']);
    ensureUnsetTrans();

  }

  // step 3 actions

  else if ($_POST['walrusstep'] == 3) {

    if (!$_COOKIE['walrususername']) {

      $error = "you have not authenticated";

    } else if ($_POST['walruslcode'] == "--") {

      $error = "target language not specified";

    } else {

      $query = "SELECT lang FROM Languages WHERE lcode = '" . $_POST['walruslcode'] . "'";

      if (DB::isError($lang = $db->getOne($query))) die($lang->getMessage());

      $query = "SELECT country FROM Countries WHERE ccode = '" . $_POST['walrusccode'] . "'";

      if (DB::isError($country = $db->getOne($query))) die($lang->getMessage());

      if ($lang and $country) {

        $message = "language '" . $lang . "' ";

        if ($_POST['walrusccode'] != '--')

          $message .= "(variant '" . $country . "') ";

        else

          unset($country);

        $message .= "selected successfully for user " . $_COOKIE['walrususername'];

        setcookie('walruslcode', $_POST['walruslcode']);
        setcookie('walrusccode', $_POST['walrusccode']);

        $query = "SELECT mentionid FROM Translations WHERE "
               . "username = '" . $_COOKIE['walrususername'] . "' AND "
               . "lcode = '" . $_POST['walruslcode'] . "' AND "
               . "ccode = '" . $_POST['walrusccode'] . "'";

        if (DB::isError($mentionid = $db->getOne($query))) die($mentionid->getMessage());

        if ($mentionid)

          setcookie('walrusmention', $mentionid);

        else

          unset($_COOKIE['walrusmention']);

        $query = "SELECT username FROM MaintainedTranslations WHERE "
               . "lcode = '" . $_POST['walruslcode'] . "' AND "
               . "ccode = '" . $_POST['walrusccode'] . "'";

        if (DB::isError($maintainer = $db->getOne($query))) die($maintainer->getMessage());

        if ($maintainer)

          setcookie('walrusmaintainer', $maintainer);

        else

          unset($_COOKIE['walrusmaintainer']);

      } else {

        $error = "unexpected: no locale with given locale codes exists";

      }

    }

    // conditionally unset cookie values

    ensureUnsetTrans();

  }

  // step 4 actions

  else if ($_POST['walrusstep'] == 4) {

    if (!$_COOKIE['walrususername']) {

      $error = "you have not authenticated";

    } else if (!$_COOKIE['walruslcode'] or $_COOKIE['walruslcode'] == "--") {

      $error = "target language not specified";

    } else {

      $query = "SELECT lang FROM Languages WHERE lcode = '" . $_COOKIE['walruslcode'] . "'";

      if (DB::isError($lang = $db->getOne($query))) die($lang->getMessage());

      if (!$lang)

        $error = "unexpected: no language with given language code exists";

      else {

        $query = "SELECT country FROM Countries WHERE ccode = '" . $_COOKIE['walrusccode'] . "'";

        if (DB::isError($country = $db->getOne($query))) die($country->getMessage());

        if (!$country)

          $error = "unexpected: no specified country with given country code exists";

        else {

          if ($_COOKIE['walrusccode'] == '--')

            unset($country);

          $query = "SELECT COUNT(textid) FROM Source";

          if (DB::isError($count = $db->getOne($query))) die($count->getMessage());

          for ($textid = 1; $textid <= $count; ++$textid) {

            if ($_POST["walrustext$textid"]) {

              $query = "SELECT tran FROM WorkingTranslations WHERE "
                     . "username = '" . $_COOKIE['walrususername'] . "' AND "
                     . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                     . "ccode = '" . $_COOKIE['walrusccode'] . "' AND "
                     . "textid = " . $textid;

              $tran = $db->getOne($query);

              if (DB::isError($tran)) die($tran->getMessage());

              if (!$tran) {

                $query = "INSERT INTO WorkingTranslations (username, lcode, ccode, textid, tran) VALUES ('"
                       . $_COOKIE['walrususername'] . "', '" . $_COOKIE['walruslcode'] . "', '" . $_COOKIE['walrusccode'] . "', "
                       . $textid . ", '" . $_POST["walrustext$textid"] . "')";

                if (DB::isError($result = $db->query($query))) die($result->getMessage());

              } else {

                $query = "UPDATE WorkingTranslations SET tran = '" . $_POST["walrustext$textid"] . "' WHERE "
                       . "username = '" . $_COOKIE['walrususername'] . "' AND "
                       . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                       . "ccode = '" . $_COOKIE['walrusccode'] . "' AND "
                       . "textid = " . $textid;

                if (DB::isError($result = $db->query($query))) die($result->getMessage());

              }

            } else {

              $query = "DELETE FROM WorkingTranslations WHERE "
                     . "username = '" . $_COOKIE['walrususername'] . "' AND "
                     . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                     . "ccode = '" . $_COOKIE['walrusccode'] . "' AND "
                     . "textid = " . $textid;

              if (DB::isError($result = $db->query($query))) die($result->getMessage());

            }

          }

          if (isset($_COOKIE['walrusmention']))

            $mentionid = $_COOKIE['walrusmention'];

          if (isset($_COOKIE['walrusmaintainer']))

            $maintainer = $_COOKIE['walrusmaintainer'];

        }

      }

    }

  }

  // step 5 actions

  else if ($_POST['walrusstep'] == 5) {

    if (!$_COOKIE['walrususername']) {

      $error = "you have not authenticated";

    } else if (!$_COOKIE['walruslcode'] or $_COOKIE['walruslcode'] == "--") {

      $error = "target language not specified";

    } else {

      $query = "SELECT lang FROM Languages WHERE lcode = '" . $_COOKIE['walruslcode'] . "'";

      if (DB::isError($lang = $db->getOne($query))) die($lang->getMessage());

      if (!$lang)

        $error = "unexpected: no language with given language code exists";

      else {

        $query = "SELECT COUNT(textid) FROM Source";

        if (DB::isError($count = $db->getOne($query))) die($count->getMessage());

        $query = "SELECT COUNT(textid) FROM WorkingTranslations WHERE "
               . "username = '" . $_COOKIE['walrususername'] . "' AND "
               . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
               . "ccode = '" . $_COOKIE['walrusccode'] . "' AND "
               . "tran != ''";

        if (DB::isError($submitcount = $db->getOne($query))) die($submitcount->getMessage());

        if ($submitcount != $count) {

          $error = "translation is incomplete";

        } else {

          $query = "DELETE FROM Translations WHERE "
                 . "username = '" . $_COOKIE['walrususername'] . "' AND "
                 . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                 . "ccode = '" . $_COOKIE['walrusccode'] . "'";

          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          $query = "DELETE FROM SubmittedTranslations WHERE "
                 . "username = '" . $_COOKIE['walrususername'] . "' AND "
                 . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                 . "ccode = '" . $_COOKIE['walrusccode'] . "'";

          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          $query = "INSERT INTO SubmittedTranslations (username, lcode, ccode, textid, tran) "
                 . "SELECT username, lcode, ccode, textid, tran FROM WorkingTranslations WHERE "
                 . "username = '" . $_COOKIE['walrususername'] . "' AND "
                 . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                 . "ccode = '" . $_COOKIE['walrusccode'] . "'";

          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          $query = "INSERT INTO Translations (username, lcode, ccode, mentionid) VALUES ('"
                 . $_COOKIE['walrususername'] . "', '"
                 . $_COOKIE['walruslcode'] . "', '"
                 . $_COOKIE['walrusccode'] . "', '"
                 . $_POST['walrusmention'] . "')";

          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          $query = "DELETE FROM MaintainedTranslations WHERE "
                 . "username = '" . $_COOKIE['walrususername'] . "' AND "
                 . "lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                 . "ccode = '" . $_COOKIE['walrusccode'] . "'";

          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          if (isset($_POST['walrusmaintain']))

            if (!$_COOKIE['walrusmaintainer'] or $_COOKIE['walrusmaintainer'] == $_COOKIE['walrususername']) {

              $query = "INSERT INTO MaintainedTranslations (username, lcode, ccode) VALUES ('"
                     . $_COOKIE['walrususername'] . "', '"
                     . $_COOKIE['walruslcode'] . "', '"
                     . $_COOKIE['walrusccode'] . "')";

              if (DB::isError($result = $db->query($query))) die($result->getMessage());

            } else {

              $error = "A maintainer for all contributed translations into this particular target locale already exists";

            }

          $query = "SELECT realname FROM Translators WHERE username = '" . $_COOKIE['walrususername'] . "'";

          if (DB::isError($realname = $db->getOne($query))) die($realname->getMessage());

          $query = "SELECT lang FROM Languages WHERE "
                 . "lcode = '" . $_COOKIE['walruslcode'] . "'";

          if (DB::isError($lang = $db->getOne($query))) die($lang->getMessage());

          $query = "SELECT country FROM Countries WHERE "
                 . "ccode = '" . $_COOKIE['walrusccode'] . "'";

          if (DB::isError($country = $db->getOne($query))) die($country->getMessage());

          $message = "thank you $realname for your translation into " . $lang;

          if ($_COOKIE['walrusccode'] != '--')

            $message .= " (variant used in " . $country . ")";

          else

            unset($country);

          $query = "SELECT M.username AS username, U.realname AS realname, U.email AS email "
                 . "FROM MaintainedTranslations M, Translators U WHERE "
                 . "M.username = U.username AND "
                 . "M.lcode = '" . $_COOKIE['walruslcode'] . "' AND "
                 . "M.ccode = '" . $_COOKIE['walrusccode'] . "'";
          if (DB::isError($result = $db->query($query))) die($result->getMessage());

          $row = $result->fetchRow(DB_FETCHMODE_ASSOC);

          if (DB::isError($row)) die($row->getMessage());

          if ($row) {

            $maintainerusername = $row['username'];
            $maintainerrealname = $row['realname'];
            $maintaineremail = $row['email'];

            if ($maintainerusername == $_COOKIE['walrususername'])

              $message .= '! We would also like to thank you for volunteering to maintain translations into the specified locale';

            else

            $message .= '!<br /><br />The maintainer responsible for reviewing translations into the specified locale is:<br /<br /> ' . $maintainerrealname . ' <a href="mailto:' . $maintaineremail . '">' . $maintaineremail . '</a>.<br /><br /> You may wish to contact ' . $row['realname'] . ' concerning your translation';

          }

          if (!$error) {

            $savedusername = $_COOKIE['walrususername'];
            $savedlcode = $_COOKIE['walruslcode'];
            $savedccode = $_COOKIE['walrusccode'];

            setcookie('walrususername', '');
            setcookie('walruslcode', '');
            setcookie('walrusccode', '');
            setcookie('walrusmention', '');
            setcookie('walrusmaintainer', '');

          }

        }

      }

    }

  }

?>

<?php

      //$output = json_encode(array('type'=>'error', 'text' => 'Yes'));

      //die($output);

  include("settings.php");

  $boundaryString = "generateboundaryfromthis";

  $to_email = "nzanella@gmail.com";

  $from_email = "noreply@neilzanella.com";
    
  $replyTo_email = "noreply@neilzanella.com";

  if (isset($_POST)) {

    // check whether this is an ajax request, exit if not

    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        
      $output = json_encode(array(
        'type' =>' error', 
        'text' => 'Ajax POST request expected.'
      ));

      die($output); //exit script outputting json data

    } 
    
    // retrieve sanitized input data

    $form_firstname  = filter_var($_POST["form_firstname"], FILTER_SANITIZE_STRING);
    $form_lastname   = filter_var($_POST["form_lastname"], FILTER_SANITIZE_STRING);
    $form_address    = filter_var($_POST["form_address"], FILTER_SANITIZE_STRING);
    $form_city       = filter_var($_POST["form_city"], FILTER_SANITIZE_STRING);
    $form_email      = filter_var($_POST["form_email"], FILTER_SANITIZE_EMAIL);
    $form_phone      = filter_var($_POST["form_phone"], FILTER_SANITIZE_NUMBER_INT);
    $form_message    = filter_var($_POST["form_message"], FILTER_SANITIZE_STRING);

    $email_body = <<<EOT
Firstname: $form_firstname
Lastname: $form_lastname
Address: $form_address
City: $form_city
E-mail: $form_email
Phone: $form_phone
Message:
$form_message
EOT;

    // retrieve attached file

    $hasAttachment = false;

    if (isset($_FILES["form_attachment"])) {

      $hasAttachment = true;

      $fileTmpName = $_FILES["form_attachment"]['tmp_name'];
      $fileName    = $_FILES["form_attachment"]['name'];
      $fileSize    = $_FILES["form_attachment"]['size'];
      $fileType    = $_FILES["form_attachment"]['type'];
      $fileError   = $_FILES["form_attachment"]['error'];

      $handle = fopen($fileTmpName);

      $content = fread($handle, $fileSize);

      fclose($handle);

      $encodedContent = chunk_split(base64_encode($content));

    }

    if ($hasAttachment) {

      // user submitted an attachment

      $boundary = md5($boundaryString);

      // header

      $headers = "MIME-Version: 1.0\r\n"; 
      $headers .= "From:" . $from_email . "\r\n"; 
      $headers .= "Reply-To: " . $replyTo_email . "\r\n";
      $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
        
      // plain text 

      $body = "--$boundary\r\n";
      $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
      $body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
      $body .= chunk_split(base64_encode($email_body)); 
        
      // attachment

      $body .= "--$boundary\r\n";
      $body .="Content-Type: $fileType; name=\"$fileName\"\r\n";
      $body .="Content-Disposition: attachment; filename=\"$fileName\"\r\n";
      $body .="Content-Transfer-Encoding: base64\r\n";
      $body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
      $body .= $encodedContent; 

    } else {

      // user did not submit an attachment

      $headers = "From:" . $from_email . "\r\n" .
                 "Reply-To: " . $replyTo_email . "\n" .
                 "X-Mailer: PHP/" . phpversion();

      $body = $email_body;

    }

    $mailSentSuccessfully = mail($to_email, $subject, $body, $headers);
    
    if ($mailSentSuccessfully) {

      //$output = json_encode(array('type'=>'message', 'text' => $pageSettings->getContents("mailSentSuccess")));
      $output = json_encode(array('type'=>'message', 'text' => 'Message sent.'));

      die($output);

    } else {

      //$output = json_encode(array('type'=>'error', 'text' => $pageSettings->getContents("mailSentFailure")));
      $output = json_encode(array('type'=>'error', 'text' => 'Error encountered. Message not sent.'));

      die($output);

    }

  }

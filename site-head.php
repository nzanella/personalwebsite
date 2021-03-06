<?php
/**
 * Outputs the HTML code used to display the head section of our website.
 *
 * @package NeilGlenZanellaWebSite
 */
?><!DOCTYPE html>
<html lang="<?php echo $pageSettings->displayLang; ?>" itemscope itemtype="http://schema.org/WebPage">
  <head prefix="og: http://ogp.me/ns#">
    <title><?php echo $pageContents->getContents("pageTitle"); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?php echo $pageContents->getContents("metaDescription"); ?>" />
    <meta name="keywords" content="<?php echo $pageContents->getContents("metaKeywords"); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $pageSettings->siteURLCanonical; ?>css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $pageSettings->siteURLCanonical; ?>css/style-pages.css" />
    <script type="text/javascript" src="<?php echo $pageSettings->siteURLCanonical; ?>js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $pageSettings->siteURLCanonical; ?>js/angular-1.2.28.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
    <script>

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-67561454-1', 'auto');
  ga('send', 'pageview');

    </script>
    <!-- validate and submit form input -->
    <script type="text/javascript">

  $(document).ready(function() {

    matchFormFields = "#contactForm input:not([type=submit]), #contactForm textarea";

    matchFormFieldsRequired = "#contactForm input[required], #contactForm textarea[required]";

    matchContactSubmitResult = "#contactSubmitResult";

    errorColor = 'red';

    $("#form_send").click(function() { 

      var formIsValid = true;

      // loop through each field and change border color to red for invalid fields       

      $(matchFormFieldsRequired).each(function() {

        $(this).css('border-color', '');

        // check whether field is empty

        if(!$.trim($(this).val())) {

          $(this).css('border-color', errorColor);

          formIsValid = false;

        }

        // check whether email is valid

        var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 

        if($(this).attr("type") == "email" && !email_reg.test($.trim($(this).val()))) {

          $(this).css('border-color', errorColor);

          formIsValid = false;

        }   

      });

      // submit data to server if form field contents are valid

      if (formIsValid) {

        // retrieve input field values to be sent to server

        var post_data = new FormData();
        post_data.append('form_firstname',  $('input[name=form_firstname]').val());
        post_data.append('form_lastname',   $('input[name=form_lastname]').val());
        post_data.append('form_address',    $('input[name=form_address]').val());
        post_data.append('form_city',       $('input[name=form_city]').val());
        post_data.append('form_email',      $('input[name=form_email]').val());
        post_data.append('form_phone',      $('input[name=form_phone]').val());
        post_data.append('form_attachment', $('input[name=form_attachment]')[0].files[0]);
        post_data.append('form_message',    $('textarea[name=form_message]').val());

        // Ajax post data to server

        $.ajax({
          url: '<?php echo $pageSettings->getFile("sendContactFormEmail.php"); ?>',
          data: post_data,
          contentType: false,
          processData: false,
          type: 'POST',
          dataType: 'json',
          success: function(response) {  

            if (response.type == 'error') { // load json data from server and output message

              output = '<div class="error">' + response.text + '</div>';

            } else {

              output = '<div class="success">' + response.text + '</div>';

              // reset values in all form fields

              $(matchFormFields).val('');

            }

            // display an animation with the form submission results

            $(matchContactSubmitResult).hide().html(output).slideDown();

          }

        });

      }

    });

    // reset border on entering characters in form fields

    $(matchFormFields).keyup(function() {

      $(this).css('border-color', '');

      $(matchContactSubmitResult).slideUp();

    });

  });

    </script>
    <!-- Google Map -->
    <script type="text/javascript">

  function initialize() {

    var mapCanvas = document.getElementById('googleMap');

    var mapLatLng = new google.maps.LatLng(45.3918231, 11.863915);

    if (mapCanvas === null) return;

    var mapOptions = {

      center: mapLatLng,
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.ROADMAP,

    }

    var map = new google.maps.Map(mapCanvas, mapOptions);

    var marker = new google.maps.Marker({
      position: mapLatLng,
      map: map,
      title: "Neil Glen Zanella",
    });

  }

  google.maps.event.addDomListener(window, 'load', initialize);

    </script>
    <link rel="canonical" href="<?php echo $pageSettings->siteURLCanonicalLocalized; ?>" />
<?php 
  $langCodes = array_keys($pageSettings->siteURLArray);
  for ($i = 1 /* skip canonical URL at index 0 */; $i < count($langCodes); $i++) { ?>
    <link rel="alternate" hreflang="<?php echo $langCodes[$i]; ?>" href="<?php echo $pageSettings->siteURLArray[$langCodes[$i]]; ?>" />
<?php } ?>
    <link rel="shortcut icon" href="<?php echo $pageSettings->siteURLCanonical; ?>images/favicon.ico" sizes="16x16 32x32 48x48" />
    <!-- Facebook Open Graph Protocol -->
    <meta property="og:title" content="<?php echo $pageContents->getContents("ogTitle"); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $pageSettings->siteURLCanonicalLocalized; /* when user shares page on Facebook let the user share in the language they are viewing the page in */ ?>" />
<?php
  $openGraphImages = array(
    "images/NeilZanellaBusinessOpenGraphLogoPlusPortrait6-1200x630.png",
//    "images/NeilZanellaBusinessOpenGraphLogo315x315.png",
  );
  foreach ($openGraphImages as $openGraphImage) {
    file_exists($openGraphImage) or die("Could not access open graph image.");
    $openGraphImageData = getimagesize($openGraphImage);
?>
    <meta property="og:image" content="<?php echo $pageSettings->siteURLCanonical . $openGraphImage; ?>" />
    <meta property="og:image:type" content="<?php echo $openGraphImageData["mime"] ?>" />
    <meta property="og:image:width" content="<?php echo $openGraphImageData[0] ?>" />
    <meta property="og:image:height" content="<?php echo $openGraphImageData[1] ?>" />
<?php } ?>
    <meta property="og:description" content="<?php echo $pageContents->getContents("ogDescription"); ?>" />
    <meta property="og:locale" content="<?php echo $pageContents->getContents("ogLocale"); ?>" />
<?php
  // get og:locale:alternate property values from ogLocale content name
  $skipLanguage = $pageContents->getContentsLanguage("ogLocale");
  $langToContentMap = $pageContents->getLangToContentsMap("ogLocale");
  foreach ($langToContentMap as $lang => $content) {
    if (strcmp($lang, $skipLanguage) != 0) {
?>
    <meta property="og:locale:alternate" content="<?php echo $content ?>" />
<?php
    }
  }
?>
    <!-- Twitter Card with Large Image -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@NeilGlenZanella" />
    <meta name="twitter:creator" content="@NeilGlenZanella" />
    <meta name="twitter:title" content="<?php echo $pageContents->getContents("ogTitle"); ?>" />
    <meta name="twitter:description" content="<?php echo $pageContents->getContents("ogDescription"); ?>" />
    <meta name="twitter:image:src" content="<?php echo $openGraphImages[0]; ?>" />
    <!-- Google+ Schema.org Microdata -->
    <meta itemprop="name" content="<?php echo $pageContents->getContents("ogTitle"); ?>" />
    <meta itemprop="description" content="<?php echo $pageContents->getContents("ogDescription"); ?>" />
<?php
  $schemaOrgImage = "images/NeilZanellaBusinessGooglePlusSchemaOrgLogo150x150.png";
?>
    <meta itemprop="image" content="<?php echo $pageSettings->siteURLCanonical . $schemaOrgImage; ?>" />
    <!-- Google Analytics -->
  </head>

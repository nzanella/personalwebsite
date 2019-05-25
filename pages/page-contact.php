<?php
/**
 * @package NeilGlenZanellaWebSite
 */
?>
        <div id="contactPageLeft">
          <div id="contactInstructions">
            <h3><?php echo $pageContents->getContents("contactUs"); ?></h3>
            <p><?php echo $pageContents->getContents("formInstructions"); ?><br /><strong><?php echo $pageContents->getContents("formMandatoryFields"); ?></strong></p>
            <div id="contactSubmitResult"></div>
          </div>
          <div id="contactForm">
            <div class="col1">
              <label for="form_firstname"><?php echo $pageContents->getContents("firstname"); ?> <span class="required">*</span></label>
              <input type="text" id="form_firstname" name="form_firstname" value="" required />
            </div>
            <div class="col2">
              <label for="form_lastname"><?php echo $pageContents->getContents("lastname"); ?> <span class="required">*</span></label>
              <input type="text" id="form_lastname" name="form_lastname" value="" required />
            </div>
            <div class="col1">
              <label for="form_address"><?php echo $pageContents->getContents("address"); ?></label>
              <input type="text" id="form_address" name="form_address" value="" />
            </div>
            <div class="col2">
              <label for="form_city"><?php echo $pageContents->getContents("city"); ?></label>
              <input type="text" id="form_city" name="form_city" value="" />
            </div>
            <div class="col1">
              <label for="form_email"><?php echo $pageContents->getContents("email"); ?> <span class="required">*</span></label>
              <input type="email" id="form_email" name="form_email" value="" required />
            </div>
            <div class="col2">
              <label for="form_phone"><?php echo $pageContents->getContents("phone"); ?> <span class="required">*</span></label>
              <input type="tel" id="form_phone" name="form_phone" value="" required />
            </div>
<!--
            <div class="col12">
              <label for="form_attachment"><?php echo $pageContents->getContents("addAttachment"); ?></label>
              <input type="file" id="form_attachment" name="form_attachment" />
            </div>
-->
            <div class="col12">
              <label for="form_message"><?php echo $pageContents->getContents("message"); ?> <span class="required">*</span></label>
              <textarea id="form_message" name="form_message" required></textarea>
            </div>
            <div class="col12">
              <input type="submit" id="form_send" value="<?php echo $pageContents->getContents("send"); ?>" formnovalidate="formnovalidate" />
            </div>
          </div>
        </div>
        <div id="contactPageRight">
          <div id="contactInfo">
            <br />Neil Glen Zanella
            <br /><?php echo $pageContents->getContents("companyDescription"); ?>
            <br /><?php echo $pageContents->getContents("office"); ?>: via Siracusa 55/16, 35142 Padova (PD)
            <br />Telefono: <a target="_blank" href="tel:+393201837999">(+39) 320 1837999</a>
            <br />Skype: <a href="skype:neilzanella">neilzanella</a>
            <br />Email: <a href="mailto:nzanella@gmail.com">nzanella@gmail.com</a>
          </div>
          <div id="googleMap"></div>
        </div>

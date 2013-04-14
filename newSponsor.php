<?
/********************************************************************************
 * The contents of this file are subject to the Common Public Attribution License
 * Version 1.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 * http://www.wikiarguments.net/license/. The License is based on the Mozilla
 * Public License Version 1.1 but Sections 14 and 15 have been added to cover
 * use of software over a computer network and provide for limited attribution
 * for the Original Developer. In addition, Exhibit A has been modified to be
 * consistent with Exhibit B.
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 *
 * The Original Code is Wikiarguments. The Original Developer is the Initial
 * Developer and is Wikiarguments GbR. All portions of the code written by
 * Wikiarguments GbR are Copyright (c) 2012. All Rights Reserved.
 * Contributor(s):
 *     Andreas Wierz (andreas.wierz@gmail.com).
 *
 * Attribution Information
 * Attribution Phrase (not exceeding 10 words): Powered by Wikiarguments
 * Attribution URL: http://www.wikiarguments.net
 *
 * This display should be, at a minimum, the Attribution Phrase displayed in the
 * footer of the page and linked to the Attribution URL. The link to the Attribution
 * URL must not contain any form of 'nofollow' attribute.
 *
 * Display of Attribution Information is required in Larger Works which are
 * defined in the CPAL as a work which combines Covered Code or portions
 * thereof with code not governed by the terms of the CPAL.
 *******************************************************************************/

global $sTemplate, $sUser, $sDB, $sPacket, $sPage;
?>
<? include($sTemplate->getTemplateRootAbs()."header.php"); ?>

<div id = "content_wide">
  <div class = "thin">
    <form action = "<? echo $sTemplate->getRoot(); ?>become-sponsor/" method = "POST" id = "form_new_sponsor" enctype="multipart/form-data">
    <div class = "new_sponsor">
      <div class = "row">
        <div class = "headline"><? echo $sTemplate->getString("NEW_SPONSOR_HEADLINE"); ?></div>
      </div>
      <div class = "row subheadline">
        <? echo $sTemplate->getString("NEW_SPONSOR_CONTACT_INFO"); ?>
      </div>

       <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_NAME"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_name" id = "sponsor_name"><? echo $sRequest->getStringPlain("sponsor_name"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_COMPANY_NAME"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_company_name" id = "sponsor_company_name"><? echo $sRequest->getStringPlain("sponsor_company_name"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_STREET"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_street" id = "sponsor_street"><? echo $sRequest->getStringPlain("sponsor_street"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_CITY"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_zip" id = "sponsor_zip" style = "width: 80px;"><? echo $sRequest->getStringPlain("sponsor_zip"); ?></textarea>
          <textarea name = "sponsor_city" id = "sponsor_city" style = "width: 235px; margin-left: 20px;"><? echo $sRequest->getStringPlain("sponsor_city"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_PHONE"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_phone" id = "sponsor_phone"><? echo $sRequest->getStringPlain("sponsor_phone"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_EMAIL"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_email" id = "sponsor_email"><? echo $sRequest->getStringPlain("sponsor_email"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_ADDITIONAL_INFORMATION"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_additional_information" id = "sponsor_additional_information" style = "height: 80px;"><? echo $sRequest->getStringPlain("sponsor_additional_information"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_PASSWORD"); ?></div>
        <div class = "new_sponsor_col">
          <input name = "sponsor_password" id = "sponsor_password" type = "password" style = "width: 355px;"></input>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_PASSWORD_REPEAT"); ?></div>
        <div class = "new_sponsor_col">
          <input name = "sponsor_password2" id = "sponsor_password2" type = "password" style = "width: 355px;"></input>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row subheadline">
        <? echo $sTemplate->getString("NEW_SPONSOR_SPONSOR_INFO"); ?>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_SLOGAN"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_slogan" id = "sponsor_slogan"><? echo $sRequest->getStringPlain("sponsor_slogan"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_URL"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_url" id = "sponsor_url"><? echo $sRequest->getStringPlain("sponsor_url") ? $sRequest->getStringPlain("sponsor_url") : "http://"; ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_LOGO"); ?></div>
        <div class = "new_sponsor_col">
          <input type = "file" name = "sponsor_logo" id = "sponsor_logo" value = "<? echo $sRequest->getStringPlain("sponsor_logo"); ?>" />
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row subheadline">
        <? echo $sTemplate->getString("NEW_SPONSOR_PAYMENT_INFO"); ?>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_PAYMENT_METHOD"); ?></div>
        <div class = "new_sponsor_col">
          <select name = "sponsor_payment_method" id = "sponsor_payment_method" onchange = "wikiargument.newSponsorUpdatePaymentData();">
            <option value = "<? echo PAYMENT_METHOD_ELV; ?>"><? echo $sTemplate->getString("PAYMENT_METHOD_ELV"); ?></option>
            <option value = "<? echo PAYMENT_METHOD_BILL; ?>"><? echo $sTemplate->getString("PAYMENT_METHOD_BILL"); ?></option>
          </select>
        </div>
        <div class = "clear"></div>
      </div>

      <div id = "new_sponsor_elv">
        <div class = "row row_half_padding">
          <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_ELV_NAME"); ?></div>
          <div class = "new_sponsor_col">
            <textarea name = "sponsor_elv_name" id = "sponsor_elv_name"><? echo $sRequest->getStringPlain("sponsor_elv_name"); ?></textarea>
          </div>
          <div class = "clear"></div>
        </div>

        <div class = "row row_half_padding">
          <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_ELV_ACCOUNT_NUMBER"); ?></div>
          <div class = "new_sponsor_col">
            <textarea name = "sponsor_elv_account_number" id = "sponsor_elv_account_number"><? echo $sRequest->getStringPlain("sponsor_elv_account_number"); ?></textarea>
          </div>
          <div class = "clear"></div>
        </div>

        <div class = "row row_half_padding">
          <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_ELV_BANK_NUMBER"); ?></div>
          <div class = "new_sponsor_col">
            <textarea name = "sponsor_elv_bank_number" id = "sponsor_elv_bank_number"><? echo $sRequest->getStringPlain("sponsor_elv_bank_number"); ?></textarea>
          </div>
          <div class = "clear"></div>
        </div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_PAYMENT_INTERVAL"); ?></div>
        <div class = "new_sponsor_col">
          <select name = "sponsor_payment_interval" id = "sponsor_payment_interval">
<?
$cur = time();
for($i = 1; $i <= 12; $i++) {
  $cur = strtotime( "+1 month", $cur );
?>
            <option value = "<? echo $cur; ?>"<? echo $paymentData->paymentInterval == $ts ? ' selected' : ''; ?>>
              <? echo date("d.m.Y", $cur); ?>
            </option>
<? } ?>
            <option value = "-1"<? echo $paymentData->paymentInterval == -1 ? ' selected' : ''; ?>>
              Unbegrenzt
            </option>
          </select>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding">
        <div class = "new_sponsor_label"><? echo $sTemplate->getString("NEW_SPONSOR_AMOUNT"); ?></div>
        <div class = "new_sponsor_col">
          <textarea name = "sponsor_amount" id = "sponsor_amount"><? echo $sRequest->getStringPlain("sponsor_amount"); ?></textarea>
        </div>
        <div class = "clear"></div>
      </div>

      <div class = "row row_half_padding row_submit" style = "margin-right: 40px;">
        <span class = "button_orange right_align" onclick = "$('#form_new_sponsor').submit(); $(this).attr('disabled','disabled'); return false;"><? echo $sTemplate->getString("SUBMIT_NEW_SPONSOR"); ?></span>
        <div class = "clear"></div>
      </div>
    </div>
    <input type = "hidden" name = "new_sponsor" value = "1" />
    </form>
  </div>

</div>

<? include($sTemplate->getTemplateRootAbs()."footer.php"); ?>

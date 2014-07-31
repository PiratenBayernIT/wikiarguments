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
    <div class = "sponsors">
      <div class = "row">
        <div class = "headline"><? echo $sTemplate->getString("SPONSORS_HEADLINE"); ?></div>
      </div>
      <div class = "row seperator_no_border">
      </div>

      <div class = "sponsors_container">
<?
$sponsors = $sPage->getSponsors();
foreach($sponsors as $k => $v)
{
    $dat = sponsorData($v);
?>
        <div class = "sponsor">
          <a href = '<? echo $v->url; ?>' target = "_BLANK">
            <div class = "logo" style = "margin-top: <? echo $dat->marginTop; ?>px; margin-bottom: <? echo $dat->marginBottom; ?>px; margin-left: <? echo $dat->marginLeft; ?>px; width: <? echo $dat->logoWidth; ?>px; height: <?echo $dat->logoHeight; ?>px; background: url('<? echo $sTemplate->getSponsorLogosRoot().$dat->file; ?>.png');"></div>
            <div class = "title"><? echo htmlspecialchars($v->title); ?></div>
          </a>
          <div class = "seperator"></div>
          <div class = "amount"><? echo $sTemplate->getString("SPONSOR_AMOUNT", Array("[AMOUNT]"), Array($v->amount)); ?></div>
        </div>
<?
}
?>
        <a href = "<? echo $sTemplate->getRoot(); ?>become-sponsor/">
          <div class = "sponsor become_sponsor">
            <div class = "title"><? echo $sTemplate->getString("BECOME_SPONSOR"); ?></div>
          </div>
        </a>
        <div class = "clear"></div>
      </div>
      <div class = "row seperator_no_border">
      </div>
    <div id = "content_sponsors">
    <? echo $sTemplate->getString("SPONSORS_TEXT"); ?>
   </div>
    </div>
  </div>
</div>

<? include($sTemplate->getTemplateRootAbs()."footer.php"); ?>

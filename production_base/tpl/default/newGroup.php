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

$page       = "";
$language   = $sTemplate->getLangBase();
?>
<? include($sTemplate->getTemplateRootAbs()."header.php"); ?> 

<div id = "content_wide">
  <div class = "thin">
    <form action = "<? echo $sTemplate->getRoot(); ?>new-group/" method = "POST" id = "form_new_group">
      <div class = "new_question">
        <div class = "row">
          <div class = "headline"><? echo $sTemplate->getString("NEW_GROUP_HEADLINE"); ?></div>
        </div>
        <div class = "row">
          <div class = "label"><? echo $sTemplate->getString("NEW_GROUP_TITLE"); ?></div>
          <div class = "input full_width" id = "input_new_group_title">
            <input id = "new_group_title" name = "new_group_title" maxlength="<?echo MAX_GROUP_NAME_CHR_LENGTH ?>" /><br />
            <span class="characters_left"><span id="new_group_title_chars_left"><?echo MAX_GROUP_NAME_CHR_LENGTH ?></span> <? echo $sTemplate->getString("CHARS_WRITTEN_LEFT"); ?> </span>
          </div>
        </div>

        <div class = "row">
          <div class="full_width">
			  <div class = "label"><? echo $sTemplate->getString("NEW_GROUP_VISIBILITY"); ?></div>
			  <div class = "input">
				<select name = "new_group_visibility" id = "new_group_visibility" class="wide">
				  <option value = "<? echo VISIBILITY_PUBLIC; ?>"><? echo $sTemplate->getString("GROUP_VISIBILITY_PUBLIC"); ?></option>
				  <option value = "<? echo VISIBILITY_PRIVATE; ?>"><? echo $sTemplate->getString("GROUP_VISIBILITY_PRIVATE"); ?></option>
				</select>
				<span class = "button_orange right_align" onclick = "$('#form_new_group').submit(); $(this).attr('disabled','disabled'); return false;"><? echo $sTemplate->getString("SUBMIT_NEW_GROUP"); ?></span>  
			  </div>
		  </div>
		  
  
		  
        </div>

        
      </div>
      <input type = "hidden" name = "new_group" value = "1" />
    </form>
  </div>
</div>

<? include($sTemplate->getTemplateRootAbs()."footer.php"); ?>

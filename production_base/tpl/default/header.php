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

global $sTemplate, $sUser, $sQuery, $sPage;
$lang = $sTemplate->getLangBase();

// TODO: move to Page<*>
$filterString = "";
$tags         = $sQuery->getCurrentTags();
foreach($tags as $k => $v)
{
    if($v != "")
    {
        $filterString .= "-".strtolower($v);
    }
}
if(strlen($filterString))
{
    $filterString = substr($filterString, 1)."/";
    $filterStringTop = "tags/top/".$filterString;
}else
{
    $filterStringTop = "";
}

$filterStringTop      = "tags/top/".$filterString;
$filterStringTitle   = "tags/title/".$filterString;

$topActive      = false;
$titleActive   = false;

if($sTemplate->isCurrentPage('default'))
{
    $sort = $sPage->getSort();

    $titleActive   = $sort == SORT_TITLE;
    $topActive      = $sort == SORT_TOP;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//<? echo strtoupper($sTemplate->getString("HTML_HEADER_META_LANG")); ?>"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml" xml:lang = "<? echo $sTemplate->getString("HTML_HEADER_META_LANG"); ?>" lang = "<? echo $sTemplate->getString("HTML_HEADER_META_LANG"); ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=1100" />
  <meta http-equiv="content-language" content="<? echo $sTemplate->getString("HTML_HEADER_META_LANG"); ?>" />
  <title><? echo $sPage->title(); ?></title>


  <link rel = "stylesheet" type = "text/css" href = "<? echo $sTemplate->getTemplateRoot(); ?>css/jquery-ui-1.7.2.custom.css.php" />
  <link rel = "stylesheet" type = "text/css" href = "<? echo $sTemplate->getTemplateRoot(); ?>css/style.css.php" />



  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/jquery.min.js'></script>
  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/jquery-ui.min.js'></script>
  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/jquery.fancynotification.js'></script>
  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/jquery.json.js'></script>
  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/wikiargument.js.php'></script>
  <script type='text/javascript' src='<? echo $sTemplate->getTemplateRoot(); ?>js/wikiargument_ui.js.php'></script>

<? if(PHPSPELLCHECK_ENABLED) { ?>
  <script type='text/javascript' src='<? echo PHPSPELLCHECK_ROOT; ?>include.js'></script>
<? } ?>



</head>
<? flush(); ?>
<body>

<script>
var wikiargument = new _Wikiargument('<? echo $sUser->getUserId(); ?>');
</script>

  <div id = "wrapper">
    <div id = "header">

      <div id = "header_orange">
        <div class="header_top_menu">
          <div class="header_title">
            <a href="<? echo $sTemplate->getRoot(); ?>"><? echo $sTemplate->getString("HEADER_TEXT"); ?></a>
          </div>
  <? if($sUser->isLoggedIn()) { ?>
          <div id = "header_menu" class = "header_menu">
            <div class = "username">
              <? echo htmlspecialchars($sUser->getUserName()); ?>
              <div class = "up_arrow"></div>
              <div class = "dn_arrow"></div>
            </div>

            <div class = "hidden profile_menu">
              <ul class="user_profile_list">
                  <li><div class = "icon_share_page"></div><a href = '#' onclick = "wikiargument.sharePage('<? echo $sPage->shortUrl() ? $sPage->shortUrl() : ''; ?>');"><? echo $sTemplate->getString("HEADER_NAVI_SHARE_PAGE"); ?></a></li>
                  <li><div class = "icon_manage_profile"></div><a href = '<? echo $sTemplate->getRoot(); ?>settings/'><? echo $sTemplate->getString("HEADER_NAVI_MANAGE_PROFILE"); ?></a></li>
                  <li><div class = "icon_my_profile"></div><a href = '<? echo $sTemplate->getRoot(); ?>user/<? echo $sUser->getUserId(); ?>/'><? echo $sTemplate->getString("HEADER_NAVI_MY_PROFILE"); ?></a></li>
                  <li><div class = "icon_logout"></div><a href = '<? echo $sTemplate->getRoot(); ?>logout/'><? echo $sTemplate->getString("HEADER_NAVI_LOGOUT"); ?></a></li>
              </ul>
            </div>
          </div>
  <? }else{ ?>
          <div class = "header_signup" style = "text-align: center;">
            <a href = '<? echo $sTemplate->getRoot(); ?>login/'><? echo $sTemplate->getString("HEADER_LOGIN"); ?></a>
          </div>
  <? } ?>
        </div>
      </div>
      <div class="header_white">
        <div class="header_navigation">
          <div class = "navi_point title <? echo $titleActive ? "current_page" : "";?>">
            <a href = '<? echo $sTemplate->getRoot(); ?><? echo $filterStringTitle; ?>'><? echo $sTemplate->getString("NAVIGATION_TITLE"); ?></a>
          </div>

          <div class = "navi_point top <? echo $topActive ? "current_page" : "";?>">
            <a href = '<? echo $sTemplate->getRoot(); ?><? echo $filterStringTop; ?>'><? echo $sTemplate->getString("NAVIGATION_TOP"); ?></a>
          </div>

          <div class = "navi_point overview">
            <a href = '<? echo $sTemplate->getRoot(); ?>overview/'><? echo $sTemplate->getString("NAVIGATION_OVERVIEW"); ?></a>
          </div>

          <form action = "#" onsubmit = "wikiargument.submitSearch(); return false;">
          <div class = "navi_point">
              <input type = "text" value = "<? echo $sTemplate->getString("NAVIGATION_SEARCH_DEFAULT"); ?>" id = "navi_search" name = "navi_search"
                     onfocus = "if($('#navi_search').val() == '<? echo $sTemplate->getString("NAVIGATION_SEARCH_DEFAULT"); ?>') {$('#navi_search').val(''); }">
          </div>
          </form>
        </div>
      </div>
    </div>

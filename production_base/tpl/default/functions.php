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

function voteUp($css, $questionId, $argumentId, $argumentType = ARGUMENT_INDEF)
{
    global $sUser, $sTemplate;
    $canVote  = true;
    $vote     = $sUser->getVoteState($questionId, $argumentId);
    $id       = "voteup_".$questionId."_".$argumentId;
    $onSubmit = "";

    if(!$sUser->isLoggedIn())
    {
        $canVote  = false;
        $onSubmit = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN")."\"); return false;";
    }

    if($argumentId && $canVote && CONSTRAIN_FACTIONS)
    {
        $faction = $sUser->getFactionByQuestionId($questionId);

        if($faction != $argumentType)
        {
            $canVote  = false;
            $onSubmit = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_VOTE_NOT_CHECKED_IN")."\"); return false;";
        }
    }

    $ret = "";

    if($vote != VOTE_UP)
    {
        $css .= " vote_up_inactive";
    }

    $onClick = "$('#".$id."').submit();";
    if(VOTES_AJAX && $onSubmit == "")
    {
        $onClick = "wikiargument.vote(".$questionId.", ".$argumentId.", ".($vote == VOTE_UP ? VOTE_NONE : VOTE_UP)."); return false;";
        $onSubmit = "return false;";
    }

    $ret = "<form action = '' method = 'POST' id = '".$id."' onsubmit = '".$onSubmit."'>";
    $ret .= "<div class = 'vote_up ".$css."' id = 'vote_up_".$questionId."_".$argumentId."' onclick = \"".$onClick."\"></div>";
    $ret .= "<input type = 'hidden' name = 'vote' value = '".($vote == VOTE_UP ? VOTE_NONE : VOTE_UP)."' />";
    $ret .= "<input type = 'hidden' name = 'questionId' value = '".$questionId."' />";
    $ret .= "<input type = 'hidden' name = 'argumentId' value = '".$argumentId."' />";
    $ret .= "<input type = 'hidden' name = 'vote_select' value = '1' />";
    $ret .= "</form>";

    return $ret;
}

function upvoteQuestion($questionId)
{
    global $sUser, $sTemplate;
    $canVote  = true;
    $vote     = $sUser->getVoteState($questionId, 0);
    $id       = "voteup_".$questionId."_0";
    $onSubmit = "";

    if(!$sUser->isLoggedIn())
    {
        $canVote  = false;
        $onSubmit = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN")."\"); return false;";
    }

    $ret = "";

    if($vote == VOTE_UP)
    {
        $title = $sTemplate->getString("QUESTION_REVOKE_VOTE");
        $css = "";
    } else
    {
        $title = $sTemplate->getString("QUESTION_VOTE_UP");
        $css = " vote_up_inactive";
    }

    $onClick = "$('#".$id."').submit();";
    if(VOTES_AJAX && $onSubmit == "")
    {
        $onClick = "wikiargument.vote(".$questionId.", 0, ".($vote == VOTE_UP ? VOTE_NONE : VOTE_UP)."); return false;";
        $onSubmit = "return false;";
    }

    $ret = "<form action = '' method = 'POST' id = '".$id."' onsubmit = '".$onSubmit."'>";
    $ret .= "<div class = 'question_vote_up vote_up ".$css."' title='".$title."' id = 'vote_up_".$questionId."_0' onclick = \"".$onClick."\"></div>";
    $ret .= "<input type = 'hidden' name = 'vote' value = '".($vote == VOTE_UP ? VOTE_NONE : VOTE_UP)."' />";
    $ret .= "<input type = 'hidden' name = 'questionId' value = '".$questionId."' />";
    $ret .= "<input type = 'hidden' name = 'argumentId' value = '0' />";
    $ret .= "<input type = 'hidden' name = 'vote_select' value = '1' />";
    $ret .= "</form>";

    return $ret;
}


function showVoteState($questionId) 
{
    global $sUser, $sTemplate;
    $vote     = $sUser->getVoteState($questionId, 0);
    $css = "";
    $title = $sTemplate->getString("QUESTION_VOTED");

    if($vote == VOTE_UP)
    {
        return "<div class='vote_up question_vote_up' title='".$title."'></div>";
    }

    return "";
}


function voteDn($css, $questionId, $argumentId, $argumentType = ARGUMENT_INDEF, $questionType = QUESTION_TYPE_LISTED, $questionFlags = 0)
{
    global $sUser, $sTemplate;
    $canVote  = true;
    $vote     = $sUser->getVoteState($questionId, $argumentId);
    $id       = "votedn_".$questionId."_".$argumentId;
    $onSubmit = "";

    if(!$sUser->isLoggedIn() &&
       ($questionType != QUESTION_TYPE_UNLISTED || !($questionFlags & QUESTION_FLAG_PART_ALL)))
    {
        $canVote  = false;
        $onSubmit = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN")."\"); return false;";
    }

    $ret = "";

    if($vote != VOTE_DN)
    {
        $css .= " vote_dn_inactive";
    }

    $onClick = "$('#".$id."').submit();";
    if(VOTES_AJAX)
    {

        $onClick  = "wikiargument.vote(".$questionId.", ".$argumentId.", ".($vote == VOTE_DN ? VOTE_NONE : VOTE_DN)."); return false;";
        $onSubmit = "return false;";
    }

    $ret = "<form action = '' method = 'POST' id = '".$id."' onsubmit = '".$onSubmit."'>";
    $ret .= "<div class = 'vote_dn ".$css."' id = 'vote_dn_".$questionId."_".$argumentId."' onclick = \"".$onClick."\"></div>";
    $ret .= "<input type = 'hidden' name = 'vote' value = '".($vote == VOTE_DN ? VOTE_NONE : VOTE_DN)."' />";
    $ret .= "<input type = 'hidden' name = 'questionId' value = '".$questionId."' />";
    $ret .= "<input type = 'hidden' name = 'argumentId' value = '".$argumentId."' />";
    $ret .= "<input type = 'hidden' name = 'vote_select' value = '1' />";
    $ret .= "</form>";

    return $ret;
}

function makeTags($sPage, $q, $sTemplate)
{
    $ret = '<div class = "tags"><ul>';

    $sorting = "title/";
    if (method_exists($sPage, "getSort") && $sPage->getSort() == SORT_TOP) {
        $sorting = "top/";
    }
    $tags = $q->tags();
    foreach($tags as $k => $v)
    {
        if($sPage->group() && $sPage->group()->url())
        {
            $ret .= '<li class = "tag"><a href = "'.$sTemplate->getRoot().'groups/'.$sPage->group()->url().'/tags/title/'.$v.'/">'.$v.'</a></li>';
        }else
        {
            $ret .= '<li class = "tag"><a href = "'.$sTemplate->getRoot().'tags/'.$sorting.$v.'/">'.$v.'</a></li>';
        }
    }
    $ret .= '</ul></div>';
    return $ret;
}

function drawQuestionBoxRaw(Question $q, $tabs = "", $appendDetails = false, $appendTags = true, $voting = true)
{
    global $sTemplate, $sPage, $sUser;

    $ret = "";

    if($tabs)
    {
        $ret .= '<div class = "question_tabs">'.$tabs.'</div>';
    }

    $numPoints    = $q->score();
    $numArguments = $q->numArguments();

    $ret .= '
<div class = "question'.($tabs ? ' question_no_margin' : '').'" id = "question_'.$q->questionId().'">
  <div class = "stats question_stats">
    <div class = "points question_points" id = "points_text_'.$q->questionId().'_0">'.$numPoints.'</div>
    <div class = "points_text question_points_text">'.$sTemplate->getStringNumber("QUESTION_POINTS", Array(), Array(), $numPoints).'</div>';


    if ($voting)
    {
        $ret .= upvoteQuestion($q->questionId());
    } else
    {
        $ret .= showVoteState($q->questionId());
    }

    $ret .= '
  </div>
  <div class = "question_title"><p><a href = "'.$q->url().'">'.$q->title().'</a></p></div>
  <div class = "question_num_arguments">
    <div class = "icon_num_arguments"></div>
    '.$sTemplate->getString("QUESTION_ARGUMENTS", Array("[NUM_ARGUMENTS]"), Array($numArguments)).'
  </div>';

    if($appendDetails)
    {
        $ret .= '
  <div class = "question_details">
    <a href=#arguments>'.$sTemplate->getString("QUESTION_GOTO_ARGUMENTS").'</a>
'.$q->details().'</div>
      ';
    }

    if($q->canEdit($sUser))
    {
        $ret .= '<div class = "author question_author" style="display:none"><a href = "'.$q->url().'edit/">'.$sTemplate->getString("QUESTION_EDIT", Array("[TIMELEFT]"), Array($q->timeLeftEdit())).'</a></div>';
    }else
    {
        $ret .= '<div class = "author question_author" style="display:none">'.$sTemplate->getString("QUESTION_AUTHOR", Array("[TIMESINCE]", "[USERNAME]"), Array($q->timeSince(), $q->authorLink())).'</div>';
    }

    if($appendTags) $ret .= makeTags($sPage, $q, $sTemplate);

    $ret .= '
  <div class = "clear"></div>
</div>
    <a name="arguments"></a>';

    return $ret;
}

function drawQuestionBox(Question $q)
{
    echo drawQuestionBoxRaw($q, "", false, true, false);
}

function drawQuestionBoxExtended(Question $q, $view, $basePath, $a = false, $appendDetails = false)
{
    global $sTemplate, $sUser;

    $tabs = "
<div class = 'tab".($view == VIEW_DISCUSSION ? " tab_active" : "")."'>
  <a href = '".$basePath."'>".$sTemplate->getString("QUESTION_TAB_DISCUSSION")."</a>
</div>
    ";

    $isFollowing = $sUser->isFollowing($q->questionId());
    $tabs .= "
<div class = 'tab'>
  <a href = '".$basePath."' id = 'tab_follow_question' onclick = \"wikiargument.follow('".$q->questionId()."'); return false;\" ".($isFollowing ? "style = 'display: none;'" : '').">
    ".$sTemplate->getString("QUESTION_TAB_UNFOLLOW")."
  </a>
  <a href = '".$basePath."' id = 'tab_unfollow_question' onclick = \"wikiargument.unfollow('".$q->questionId()."'); return false;\" ".($isFollowing ? '' : "style = 'display: none;'").">
    ".$sTemplate->getString("QUESTION_TAB_FOLLOW")."
  </a>
</div>";

    if($view == VIEW_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active ".($a->type() == ARGUMENT_PRO ? "tab_arg_pro" : "tab_arg_con")."'>
  <a href = '".$a->url($basePath)."'>".$sTemplate->getString("QUESTION_TAB_ARGUMENT", Array("[TITLE]"), Array($a->headline()))."</a>
</div>";
    }

    if($view == VIEW_NEW_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active'>
  ".$sTemplate->getString("QUESTION_TAB_NEW_ARGUMENT")."
</div>";
    }

    if($view == VIEW_EDIT_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active'>
  ".$sTemplate->getString("QUESTION_TAB_EDIT_ARGUMENT")."
</div>";
    }

    if($view == VIEW_NEW_COUNTER_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active'>
  ".$sTemplate->getString("QUESTION_TAB_NEW_COUNTER_ARGUMENT")."
</div>";
    }

    if($view == VIEW_EDIT_COUNTER_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active'>
  ".$sTemplate->getString("QUESTION_TAB_EDIT_COUNTER_ARGUMENT")."
</div>";
    }

    if($view == VIEW_DETAILS)
    {
        echo drawQuestionBoxRaw($q, $tabs, $appendDetails, false);
    }else
    {
        echo drawQuestionBoxRaw($q, $tabs, $appendDetails, true);
    }
}

function drawQuestionBoxDetails(Question $q)
{
    global $sTemplate;

    $ret = "";

    $ret .= '
<div class = "question">
  <div class = "question_title"><p><a href = "'.$q->url().'">'.$q->title().'</a></p></div>
</div>';

    echo $ret;
}

function drawArgumentBoxRaw(Question $q, $tabs, Argument $a, $basePath, $abstract = true)
{
    global $sTemplate, $sUser;

    $ret = "";

    if($tabs)
    {
        $ret .= '<div class = "question_tabs">'.$tabs.'</div>';
    }

    $argumentId = 0;
    if($a)
    {
        $argumentId = $a->argumentId();
    }

    $numPoints = $a->score();

    $ret .= '
<div class = "argument_extended '.($tabs ? "" : " argument_extended_no_tabs").'">
  <div class = "stats question_stats">
    <div class = "points question_points" id = "points_text_'.$q->questionId().'_'.$argumentId.'">'.$numPoints.'</div>
    <div class = "points_text question_points_text">'.$sTemplate->getStringNumber("ARGUMENT_POINTS", Array(), Array(), $numPoints).'</div>
    '.voteUp('argument_vote_up', $q->questionId(), $argumentId, $a->type()).'
    '.voteDn('argument_vote_dn', $q->questionId(), $argumentId, $a->type()).'
  </div>
  <div class = "argument_title"><a href = "'.$a->url($basePath).'">'.$a->headline().'</a></div>';

    if($abstract)
    {
        if(!$a->details())
        {
            $ret .= '<div class = "argument_abstract_extended">'.$a->abstractText().'</div>';
        }else
        {
            $ret .= '<div class = "argument_abstract_extended">'.$a->abstractText().' <a class="read_more" href = "'.$a->url($basePath).'">&gt;&gt;</a></div>';
        }
    }else
    {
        $ret .= '<div class = "argument_abstract_extended argument_abstract_cursive">'.$a->abstractText().'</div>';
        $ret .= '<div class = "argument_details_extended">'.$a->details().'</div>';
    }

    if($a->canEdit($sUser))
    {
        $ret .= '<div class = "author question_author"><a href = "'.$a->url($basePath).'edit/">'.$sTemplate->getString("ARGUMENT_EDIT", Array("[TIMELEFT]"), Array($a->timeLeftEdit())).'</a></div>';
    }else
    {
        $ret .= '<div class = "author question_author">'.$sTemplate->getString("QUESTION_AUTHOR", Array("[TIMESINCE]", "[USERNAME]"), Array($a->timeSince(), $a->authorLink())).'</div>';
    }

    $ret .= '
  <div class = "argument_'.($a->type() == ARGUMENT_PRO ? "pro" : "con").'_bar"></div>
</div>';

    return $ret;
}

function drawArgumentBoxExtended(Question $q, $view, $basePath, Argument $a, $tabUseParent = false, $abstract = true, $drawTabs = true, $customTabRoot = false)
{
    global $sTemplate, $sUser;

    $tabArg  = $a;
    $tabArg2 = $tabArg;

    if($tabUseParent)
    {
        $tabArg = $a->parent();
    }

    if($customTabRoot)
    {
        $tabArg  = $customTabRoot;
        $tabArg2 = $customTabRoot;

        if($tabUseParent)
        {
            $tabArg = $tabArg->parent();
        }
    }

    $tabs = "
<div class = 'tab".($view == VIEW_DISCUSSION ? " tab_active" : "")."'>
  <a href = '".$basePath."'>".$sTemplate->getString("QUESTION_TAB_DISCUSSION")."</a>
</div>
<div class = 'tab".($view == VIEW_DETAILS ? " tab_active" : "")."'>
  <a href = '".$basePath."details/'>".$sTemplate->getString("QUESTION_TAB_DETAILS")."</a>
</div>";

    $isFollowing = $sUser->isFollowing($q->questionId());
    $tabs .= "
<div class = 'tab'>
  <a href = '".$basePath."' id = 'tab_follow_question' onclick = \"wikiargument.follow('".$q->questionId()."'); return false;\" ".($isFollowing ? "style = 'display: none;'" : '').">
    ".$sTemplate->getString("QUESTION_TAB_UNFOLLOW")."
  </a>
  <a href = '".$basePath."' id = 'tab_unfollow_question' onclick = \"wikiargument.unfollow('".$q->questionId()."'); return false;\" ".($isFollowing ? '' : "style = 'display: none;'").">
    ".$sTemplate->getString("QUESTION_TAB_FOLLOW")."
  </a>
</div>";

/*<div class = 'tab ".($tabArg->type() == ARGUMENT_PRO ? "tab_arg_pro" : "tab_arg_con")."'>
  <a href = '".$tabArg->url($basePath)."'>".$sTemplate->getString("QUESTION_TAB_ARGUMENT", Array("[TITLE]"), Array($tabArg->headline()))."</a>
</div>*/
    $tabs .= "
<div class = 'tab ".((!$tabUseParent && $view != VIEW_NEW_COUNTER_ARGUMENT) ? " tab_active" : "")." ".($tabArg->type() != ARGUMENT_PRO ? "tab_arg_pro" : "tab_arg_con")."'>
  <a href = '".$tabArg->url($basePath)."ca/'>".$sTemplate->getString("QUESTION_TAB_COUNTER_ARGUMENT", Array("[TITLE]"), Array($tabArg->headline()))."</a>
</div>";

    if($tabUseParent)
    {
        $tabs .= "
<div class = 'tab tab_active ".($tabArg2->type() != ARGUMENT_PRO ? "tab_arg_pro" : "tab_arg_con")."'>
  <a href = '".$tabArg2->url($basePath)."'>".$sTemplate->getString("QUESTION_TAB_COUNTER_ARGUMENT", Array("[TITLE]"), Array($tabArg2->headline()))."</a>
</div>";
    }

    if($view == VIEW_NEW_COUNTER_ARGUMENT)
    {
        $tabs .= "
<div class = 'tab tab_active'>
  ".$sTemplate->getString("QUESTION_TAB_NEW_COUNTER_ARGUMENT")."
</div>";
    }

    if(!$drawTabs)
    {
        $tabs = "";
    }

    echo drawArgumentBoxRaw($q, $tabs, $a, $basePath, $abstract);
}

function drawPagination($cur, $max, $range, $url, $class)
{
    global $sTemplate;

    if($cur > $max)
    {
        $cur = $max;
    }

    $mn = $cur >= floor($range / 2)? $cur - floor($range / 2) : 0;
    $mx = min($mn + $range, $max);

    if($mx - $mn < $range && $mn > 0)
    {
        $mn = max($mx - $range, 0);
    }

    echo '<div class = "pagination'.($class ? ' '.$class : '').'">';

    echo '<div class = "pagination_x_of_y">'.$sTemplate->getString("PAGINATION_PAGE_X_OF_Y", Array("[CUR]", "[MAX]"), Array($cur + 1, $max)).'</div>';

    if($cur < $max - 1)
    {
        echo '<a href = "'.$url.($cur + 1).'/"><span><div class = "pagination_next"></div></span></a>';
    }

    for($i = $mx - 1; $i >= $mn; $i--)
    {
        echo '<a href = "'.$url.$i.'/"><span class = "pagination_icon'.($i == $cur ? ' pagination_icon_active' : '').'"><div>'.($i + 1).'</div></span></a>';
    }

    if($cur > 0)
    {
        echo '<a href = "'.$url.($cur - 1).'/"><span><div class = "pagination_prev"></div></span></a>';
    }

    echo '</div>';
}

function drawQuestionDistribution(Question $q)
{
    global $sTemplate, $sUser;

    $faction = $sUser->getFactionByQuestionId($q->questionId());

    $onSubmit = "";
    if(!$sUser->isLoggedIn() &&
       ($q->type() != QUESTION_TYPE_UNLISTED || !($q->hasFlag(QUESTION_FLAG_PART_ALL))))
    {
        $onSubmit = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_CHECKIN_NOT_LOGGED_IN")."\"); return false;";
    }

    $content = "
<div class = 'vote_distribution'>";



    if($faction == FACTION_NONE || $faction == FACTION_CON)
    {
        $content .= "
  <form action = '".$q->url()."' method = 'POST' id = 'faction_checkin_pro' onsubmit = '".$onSubmit."'>
    <input type = 'hidden' name = 'faction' value = '".FACTION_PRO."' />
    <input type = 'hidden' name = 'faction_select' value = '1' />
    <button class = 'checkin_pro' >".$sTemplate->getString("CHECKIN_BUTTON")."</button>
  </form>";
    }else
    {
        $content .= "
  <form action = '".$q->url()."' method = 'POST' id = 'faction_checkin_pro' onsubmit = '".$onSubmit."'>
    <input type = 'hidden' name = 'faction' value = '".FACTION_NONE."' />
    <input type = 'hidden' name = 'faction_select' value = '1' />
    <div class = 'checkin_pro_confirmed' onclick = \"$('#faction_checkin_pro').submit();\">
      <div class = 'checkin_icon'></div>
      <p>".$sTemplate->getString("CHECKIN_PRO_CONFIRMED")."</p>
    </div>
  </form>";
    }

    $numCheckins     = $q->numCheckIns();

  $content .= "
  <div class = 'pro_perc'>".$sTemplate->getString("QUESTION_DISTRIBUTION_PRO_PERC", Array("[PERC]"), Array(ceil($q->percPro() * 100)))."</div>
  <div class='question_vote_count'>".$sTemplate->getStringNumber("QUESTION_DISTRIBUTION_NUM_CHECKINS", Array("[NUM]"), Array($numCheckins), $numCheckins)."<div class='arrow'></div></div>
  <div class = 'distribution'>
    <div class = 'distribution_pro' style = 'width: ".ceil($q->percPro() * 372)."px'></div>
    <div class = 'distribution_con ' style = 'width: ".(372 - ceil($q->percPro() * 372))."px; border-radius:".computeBorderRadius(372 - ceil($q->percPro() * 372))."'></div>
  </div>";

    if($faction == FACTION_NONE || $faction == FACTION_PRO)
    {
        $content .= "
  <form action = '".$q->url()."' method = 'POST' id = 'faction_checkin_con' onsubmit = '".$onSubmit."'>
    <input type = 'hidden' name = 'faction' value = '".FACTION_CON."' />
    <input type = 'hidden' name = 'faction_select' value = '1' />
    <button class = 'checkin_con' >".$sTemplate->getString("CHECKIN_BUTTON")."</button>
  </form>";
    }else
    {
        $content .= "
  <form action = '".$q->url()."' method = 'POST' id = 'faction_checkin_con' onsubmit = '".$onSubmit."'>
    <input type = 'hidden' name = 'faction' value = '".FACTION_NONE."' />
    <input type = 'hidden' name = 'faction_select' value = '1' />
    <div class = 'checkin_con_confirmed' onclick = \"$('#faction_checkin_con').submit();\">
      <div class = 'checkin_icon'></div>
      <p>".$sTemplate->getString("CHECKIN_CON_CONFIRMED")."</p>
    </div>
  </form>";
    }

  $content .= "
  <div class = 'con_perc'>".$sTemplate->getString("QUESTION_DISTRIBUTION_CON_PERC", Array("[PERC]"), Array(floor($q->percCon() * 100)))."</div>
</div>
    ";
    echo $content;
}

function drawArgument(Question $q, Argument $a, $basePath, $abstract = true)
{
    global $sTemplate, $sUser;

    $url                 = $a->url($basePath);
    $urlCounterArguments = $a->urlCounterArguments($basePath);
    $suffix              = "";

    $content = '
<div class = "argument_wrapper" id = "argument_wrapper_'.$a->questionId().'_'.$a->argumentId().'">
    ';

    if($a->parentId() == 0)
    {
        $numArguments = $a->numArguments();

        $content .= '
  <a href = "'.$urlCounterArguments.'">
    <div class = "counter_argument_box '.($a->type() == ARGUMENT_PRO ? "counter_argument_box_pro" : "counter_argument_box_con").'">
      <div class = "count">'.$a->numArguments().'</div>
      <div class = "count_text">'.$sTemplate->getStringNumber("NUM_COUNTER_ARGUMENTS", Array(), Array(), $numArguments).'</div>
      <div class = "plus_sign"></div>
      <div class = "line"></div>
    </div>
  </a>';
    }else
    {
        $suffix = "_no_counter";
    }

    $numPoints = $a->score();

    $content .= '
  <div class = "argument '.($a->type() == ARGUMENT_PRO ? "argument_pro".$suffix : "argument_con".$suffix).'">
    <div class = "stats argument_stats">
      <div class = "points argument_points" id = "points_text_'.$q->questionId().'_'.$a->argumentId().'">'.$numPoints.'</div>
      <div class = "points_text argument_points_text">'.$sTemplate->getStringNumber("ARGUMENT_POINTS", Array(), Array(), $numPoints).'</div>
      '.voteUp('argument_vote_up', $a->questionId(), $a->argumentId(), $a->type(), $q->type(), $q->flags()).'
      '.voteDn('argument_vote_dn', $a->questionId(), $a->argumentId(), $a->type(), $q->type(), $q->flags()).'
    </div>';

    if($a->details())
    {
        $content .= '
    <div class = "argument_headline">
      <a href = "'.$url.'">'.$a->headline().'</a>
    </div>';
    }else
    {
        $content .= '
    <div class = "argument_headline">
      '.$a->headline().'
    </div>';
    }

    if($abstract)
    {
        if(!$a->details())
        {
            $content .= '<div class = "argument_abstract">'.$a->abstractText().'</div>';
        }else
        {
            $content .= '<div class = "argument_abstract">'.$a->abstractText().' <a class="read_more" href = "'.$url.'">&gt;&gt;</a></div>';
        }
    }else
    {
        $content .= '<div class = "argument_abstract argument_abstract_cursive">'.$a->abstractText().'</div>';
        $content .= '<div class = "argument_details">'.$a->details().'</div>';
    }

    if($a->canEdit($sUser))
    {
        $content .= '<div class = "author question_author"><a href = "'.$a->url($basePath).'edit/">'.$sTemplate->getString("ARGUMENT_EDIT", Array("[TIMELEFT]"), Array($a->timeLeftEdit())).'</a></div>';
    }else
    {
        $content .= '<div class = "author question_author">'.$sTemplate->getString("QUESTION_AUTHOR", Array("[TIMESINCE]", "[USERNAME]"), Array($a->timeSince(), $a->authorLink())).'</div>';
    }

    $content .= '
    <div class = "argument_'.($a->type() == ARGUMENT_PRO ? "pro" : "con").'_bar"></div>
  </div>
</div>
    ';

    return $content;
}

function drawArgumentList(Question $q, $basePath)
{
    global $sTemplate, $sUser, $sPermissions;

    $faction = $sUser->getFactionByQuestionId($q->questionId());
    $onClickHandler = "";
    if(!$sUser->isLoggedIn() && !$q->hasFlag(QUESTION_FLAG_PART_ALL))
    {
        $onClickHandler = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_NEW_ARGUMENT_NOT_LOGGED_IN")."\"); return false;";
    }else if($sPermissions->getPermission($sUser, ACTION_NEW_ARGUMENT) == PERMISSION_DISALLOWED ||
             ($q->group() && $q->group()->getPermission($sUser, ACTION_NEW_ARGUMENT) == PERMISSION_DISALLOWED))
    {
        $onClickHandler = "wikiargument.raiseError(\"".$sTemplate->getString("NOTICE_NEW_ARGUMENT_NO_PERMISSION")."\"); return false;";
    }

    $content = "
<div class = 'arguments'>
  <div class = 'arguments_pro'>";

    foreach($q->arguments(FILTER_PRO, SORT_SCORE) as $k => $v)
    {
        $content .= drawArgument($q, $v, $basePath);
    }

    $content .= "
    <a href = '".$q->urlNewArgument(true)."' onclick = '".$onClickHandler."'>
      <div class = 'button_argument button_new_argument_pro'><span>
        ".$sTemplate->getString("NEW_ARGUMENT")."</span>
      </div>
    </a>";

    $content .= "
  </div>
  <div class = 'arguments_con'>";

    foreach($q->arguments(FILTER_CON, SORT_SCORE) as $k => $v)
    {
        $content .= drawArgument($q, $v, $basePath);
    }

    $content .= "
    <a href = '".$q->urlNewArgument(false)."' onclick = '".$onClickHandler."'>
      <div class = 'button_argument button_new_argument_con'><span>
        ".$sTemplate->getString("NEW_ARGUMENT")."</span>
      </div>
    </a>";

    $content .= "
  </div>
  <div class = 'clear'></div>
</div>
<div class = 'clear'></div>
    ";

    echo $content;
}

function drawCounterArguments(Question $q, Argument $a, $basePath)
{
    $content = "";
    foreach($a->arguments() as $k => $v)
    {
        $content .= drawArgument($q, $v, $basePath);
    }

    echo $content;
}



function computeBorderRadius($argumentConWidth)
{
    $borderRadius = "";
    if($argumentConWidth > 357)
    {
        $borderRadius .= (15 - (372 - $argumentConWidth))."px "."15px 15px ".(15 - (372 - $argumentConWidth))."px";
    }else
    {
        $borderRadius .= "0px 15px 15px 0px";
    }
    return $borderRadius;
}

function drawArgumentBoxFull(Question $q, Argument $a, $basePath)
{
    global $sTemplate, $sUser;

    $ret        = "";
    $argumentId = $a->argumentId();
    $numPoints  = $a->score();

    $ret .= '
<div class = "argument_full">
  <div class = "stats question_stats">
    <div class = "points question_points" id = "points_text_'.$q->questionId().'_'.$argumentId.'">'.$numPoints.'</div>
    <div class = "points_text question_points_text">'.$sTemplate->getStringNumber("QUESTION_POINTS", Array(), Array(), $numPoints).'</div>
    '.voteUp('argument_vote_up', $q->questionId(), $argumentId, $a->type(), $q->type(), $q->flags()).'
    '.voteDn('argument_vote_dn', $q->questionId(), $argumentId, $a->type(), $q->type(), $q->flags()).'
  </div>
  <div class = "argument_title"><a href = "'.$a->url($basePath).'">'.$a->headline().'</a></div>';

    $ret .= '<div class = "argument_abstract_extended argument_abstract_cursive">'.$a->abstractText().'</div>';
    $ret .= '<div class = "argument_details_extended">'.$a->details().'</div>';


    if($a->canEdit($sUser))
    {
        $ret .= '<div class = "author question_author"><a href = "'.$a->url($basePath).'edit/">'.$sTemplate->getString("ARGUMENT_EDIT", Array("[TIMELEFT]"), Array($a->timeLeftEdit())).'</a></div>';
    }else
    {
        $ret .= '<div class = "author question_author">'.$sTemplate->getString("QUESTION_AUTHOR", Array("[TIMESINCE]", "[USERNAME]"), Array($a->timeSince(), $a->authorLink())).'</div>';
    }
    $ret .= '
  <div class = "argument_'.($a->type() == ARGUMENT_PRO ? "pro" : "con").'_bar"></div>
</div>';

    if(!$a->parentId())
    {
        $numArguments = $a->numArguments();

        $ret .= '
<div class = "counter_argument_box_full_line"></div>
<a href = "'.$a->urlCounterArguments($basePath).'">
  <div class = "counter_argument_box_full '.($a->type() == ARGUMENT_PRO ? "counter_argument_box_full_pro" : "counter_argument_box_full_con").'">
    <div class = "count">'.$numArguments.'</div>
    <div class = "count_text">'.$sTemplate->getStringNumber("NUM_COUNTER_ARGUMENTS", Array(), Array(), $numArguments).'</div>
    <div class = "plus_sign"></div>
  </div>
</a>';
    }

    return $ret;
}
?>

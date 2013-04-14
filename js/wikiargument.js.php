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

include("./commonHeaders.php");
header("Content-Type: text/javascript");

$localVars = Array("NOTICE_UNFOLLOW_SUCCESS",
                   "ERROR_UNFOLLOW_NOT_LOGGED_IN",
                   "NOTICE_FOLLOW_SUCCESS",
                   "ERROR_FOLLOW_NOT_LOGGED_IN",
                   "NOTICE_VOTE_NOT_LOGGED_IN");
?>
function _Wikiargument(group, userId)
{
    this.group  = group;
    this.userId = userId;

    this.initLocalVars();
};

_Wikiargument.prototype.group;
_Wikiargument.prototype.userId;
_Wikiargument.prototype.langVars;

_Wikiargument.prototype.raiseError = function(msg, callback)
{
    this.raiseNotice(msg, callback);
};

_Wikiargument.prototype.enforceInputlength = function()
{
   // Get all textareas that have a "maxlength" property.
    $('textarea[maxlength]').each(function() {
        // Store the jQuery object to be more efficient...
        var textarea = $(this);

        // Store the maxlength and value of the field.
        var maxlength = textarea.attr('maxlength');
        var val = textarea.val();

        // Trim the field if it has content over the maxlength.
        if(textarea.val().length > maxlength)
        {
            textarea.val(val.slice(0, maxlength));
        }

        // Bind the trimming behavior to the "keyup" event.
        textarea.bind('keyup', function() {
            if(textarea.val().length > maxlength)
            {
                textarea.val(textarea.val().slice(0, maxlength));
            }
        });

    });
};

_Wikiargument.prototype.raiseNotice = function(msg, callback)
{

    var notice = jQuery("<div></div>");
    notice.addClass('notice');

    var title = jQuery("<div></div>");
    title.addClass('notice_title');
    title.html(msg);

    notice.append(title);

    notice.dialog({bgiframe: true,
                   modal: true,
                   resizable:false,
                   draggable:false,
                   width:400,
                   buttons:
                   {
                       Close: function()
                       {
                           $(this).dialog('close');
                       }
                   },
                   create: function (event, ui)
                   {
                       $(".ui-dialog-buttonset button").attr("class","button_orange");
                       $(".ui-dialog-buttonset button").html("<? echo $sTemplate->getString("LIGHTBOX_CLOSE"); ?>");
                   },
                  });
    return;
};

_Wikiargument.prototype.raisePrompt = function(msg, callback_yes, callback_no)
{
    var prompt = jQuery("<div></div>");
    prompt.addClass('prompt');

    var title = jQuery("<div></div>");
    title.addClass('prompt_title');
    title.html(msg);

    prompt.append(title);

    prompt.dialog({bgiframe: true,
                   modal: true,
                   resizable:false,
                   draggable:false,
                   width:400,
                   buttons:
                   [
                       {
                           text: "<? echo $sTemplate->getString("LIGHTBOX_PROMPT_YES"); ?>",
                           click: function()
                           {
                               $(this).dialog('close');
                               if(callback_yes != 'undefined' && callback_yes != undefined)
                               {
                                    callback_yes();
                               }
                           }
                       },
                       {
                           text: "<? echo $sTemplate->getString("LIGHTBOX_PROMPT_NO"); ?>",
                           click: function()
                           {
                               $(this).dialog('close');
                               if(callback_no != 'undefined' && callback_no != undefined)
                               {
                                    callback_no();
                               }
                           }
                       }
                   ],
                   create: function (event, ui)
                   {
                       $(".ui-dialog-buttonset button").attr("class","button_orange prompt_yes");
                       $(".ui-dialog-buttonset button:nth-child(2)").attr("class","button_orange prompt_no");
                   }
                  });
};

_Wikiargument.prototype.submitArgument = function(formId, buttonId)
{
    var headline = $('#new_argument_headline').val();
    var abstract = $('#new_argument_abstract').val();

    if(headline == "")
    {
        this.raiseError('<? echo $sTemplate->getString("ERROR_NEW_ARGUMENT_MISSING_HEADLINE"); ?>');
        return false;
    }else if(abstract == "")
    {
        this.raiseError('<? echo $sTemplate->getString("ERROR_NEW_ARGUMENT_MISSING_ABSTRACT"); ?>');
        return false;
    }

    $(formId).submit();

    if(buttonId)
    {
        $(buttonId).attr('disabled','disabled');
    }
    return true;
};

_Wikiargument.prototype.submitSearch = function(sort)
{
    var query = $('#navi_search').val();
    //query = query.replace(" ", "-");

    var root  = '<? echo $sTemplate->getRoot(); ?>';
    if(this.group)
    {
        root = root + "groups/" + this.group + "/";
    }
    var url   = "";

    if(sort == <? echo SORT_TOP; ?>)
    {
        url = root + "tags/trending/" + query + "/";
    }else if(sort == <? echo SORT_NEWEST; ?>)
    {
        url = root + "tags/top/" + query + "/";
    }else
    {
        url = root + "tags/newest/" + query + "/";
    }

    window.location = url;

    return false;
};

_Wikiargument.prototype.passRequest = function()
{
    var username = $('#login_username').val();

    if(!username)
    {
        this.raiseError('<? echo $sTemplate->getString("ERROR_PASS_REQUEST_MISSING_USERNAME"); ?>');
        return false;
    }

    $('#login_mode_passRequest').val(1);
    $('#login_mode_login').val(0);

    $('#form_login').submit();

    return false;
};

_Wikiargument.prototype.sharePage = function(url)
{
    var share = '<div class="addthis_toolbox addthis_default_style addthis_32x32_style"';

    if(url != "")
    {
        share += 'addthis:url="' + url + '"';
    }
    share += '><a class="addthis_button_preferred_1"></a><a class="addthis_button_preferred_2"></a><a class="addthis_button_preferred_3"></a><a class="addthis_button_preferred_4"></a><a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a></div><script type="text/javascript">var addthis_config = {"data_track_clickback":false,"data_track_addressbar":false,"data_track_textcopy":false};</script><script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50449921064422f8"></script>';
    this.raiseNotice(share);
};

_Wikiargument.prototype.changeQuestionType = function()
{
    var val = $('#new_question_type').val();
    if(val == <? echo QUESTION_TYPE_LISTED; ?>)
    {
        $('#row_question_flags').hide();

        $('#row_question_unlisted_manipulation').hide();

        $('#new_question_flags').val(0);
    }else
    {
        $('#row_question_flags').show();
    }
};

_Wikiargument.prototype.changeQuestionFlags = function()
{
    var val = $('#new_question_flags').val();
    if(val == <? echo QUESTION_FLAG_PART_ALL; ?>)
    {
        $('#row_question_unlisted_manipulation').show();
    }else
    {
        $('#row_question_unlisted_manipulation').hide();
    }
};

_Wikiargument.prototype.deleteGroup = function()
{
    this.raisePrompt("<? echo $sTemplate->getString("GROUP_DELETE_GROUP_PROMPT"); ?>", function(){ $("#form_delete_group").submit(); }, function(){ return false; });
    return false;
};

_Wikiargument.prototype.changeOwnership = function()
{
    this.raisePrompt("<? echo $sTemplate->getString("GROUP_CHANGE_OWNERSHIP_PROMPT"); ?>", function(){ $("#form_change_ownership").submit(); }, function(){ return false; });
};

_Wikiargument.prototype.newSponsorUpdatePaymentData = function()
{
    var val = $('#sponsor_payment_method').val();
    if(val == <? echo PAYMENT_METHOD_ELV; ?>)
    {
        $('#new_sponsor_elv').show();
    }else
    {
        $('#new_sponsor_elv').hide();
    }
};

_Wikiargument.prototype.initSpellcheck = function(field, button, language)
{
    var sclang = "Deutsch";
    if(!language || language == "undefined")
    {
        language = "de";
    }

    if(language == "en")
    {
        sclang = "English (International)";
    }

    var mySpellInstance                     = new LiveSpellInstance();
    mySpellInstance.Fields                  = field;
    mySpellInstance.CaseSensitive           = true;
    mySpellInstance.CheckGrammar            = true;
    mySpellInstance.IgnoreAllCaps;
    mySpellInstance.IgnoreNumeric;
    mySpellInstance.Language                = sclang;
    mySpellInstance.CSSTheme                = "classic";
    mySpellInstance.SettingsFile            = "default-settings";
    mySpellInstance.UserInterfaceLanguage   = language;
    mySpellInstance.Delay                   = 1000;
    mySpellInstance.WindowMode              = "Modal";
    mySpellInstance.Strict                  = true;
    mySpellInstance.ShowSummaryScreen       = true;
    mySpellInstance.ShowMeanings            = true;
    mySpellInstance.MeaningProvider         = "http://www.thefreedictionary.com/{word}";
    mySpellInstance.UndoLimit               = 999;

    jQuery("#" + button).click(function() { livespell.spellingProviders[mySpellInstance.id()].CheckInWindow(); });
};

_Wikiargument.prototype.follow = function(qId)
{
    if(!this.userId || this.userId == 0)
    {
        this.raiseError(this.getString("ERROR_FOLLOW_NOT_LOGGED_IN"));
        return false;
    }

    var data =
    {
        "qId" : qId
    };

    var ts = this;
    this.sendPacket('<? echo CMSG_FOLLOW_QUESTION; ?>', data, function(data){ ts.handleFollowResponse(data); });
};

_Wikiargument.prototype.handleFollowResponse = function(resp)
{
    var data = resp.data;
    if(data.result == 1)
    {
        $("#tab_follow_question").hide();
        $("#tab_unfollow_question").show();
        this.raiseNotice(this.getString("NOTICE_FOLLOW_SUCCESS"));
    }else
    {
    }
};

_Wikiargument.prototype.unfollow = function(qId, callback)
{
    if(!this.userId || this.userId == 0)
    {
        this.raiseError(this.getString("ERROR_UNFOLLOW_NOT_LOGGED_IN"));
        return false;
    }

    var data =
    {
        "qId" : qId
    };

    var ts = this;
    this.sendPacket('<? echo CMSG_UNFOLLOW_QUESTION; ?>', data, function(data){ ts.handleUnfollowResponse(data); if(callback) { callback(data); } });
};

_Wikiargument.prototype.handleUnfollowResponse = function(resp)
{
    var data = resp.data;
    if(data.result == 1)
    {
        $("#tab_unfollow_question").hide();
        $("#tab_follow_question").show();
        this.raiseNotice(this.getString("NOTICE_UNFOLLOW_SUCCESS"));
    }else
    {
    }
};

_Wikiargument.prototype.vote = function(qId, aId, vote)
{
    if(!this.userId || this.userId == 0)
    {
        this.raiseError(this.getString("NOTICE_VOTE_NOT_LOGGED_IN"));
        return false;
    }

    var data =
    {
        "qId"  : qId,
        "aId"  : aId,
        "vote" : vote
    };

    var ts = this;
    this.sendPacket('<? echo CMSG_VOTE; ?>', data, function(data){ ts.handleVoteResponse(data); });
};

_Wikiargument.prototype.handleVoteResponse = function(resp)
{
    var data = resp.data;
    if(data.result == 1)
    {
        var qId   = data.qId;
        var aId   = data.aId;
        var vote  = data.vote;
        var score = data.score;

        if(vote == <? echo VOTE_UP ?>)
        {
            $('#vote_up_' + qId + '_' + aId).removeClass('vote_up_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_NONE; ?>); return false; });
            $('#vote_dn_' + qId + '_' + aId).addClass('vote_dn_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_DN; ?>); return false; });
        }else if(vote == <? echo VOTE_DN ?>)
        {
            $('#vote_up_' + qId + '_' + aId).addClass('vote_up_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_UP; ?>); return false; });
            $('#vote_dn_' + qId + '_' + aId).removeClass('vote_dn_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_NONE; ?>); return false; });
        }else
        {
            $('#vote_up_' + qId + '_' + aId).addClass('vote_up_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_UP; ?>); return false; });
            $('#vote_dn_' + qId + '_' + aId).addClass('vote_dn_inactive')
                                            .unbind("click")
                                            .attr('onclick','')
                                            .click(function(){ wikiargument.vote(qId, aId, <? echo VOTE_DN; ?>); return false; });
        }

        $('#points_text_' + qId + '_' + aId).text(score);
    }else
    {
        this.raiseError(data.error);
    }
};

_Wikiargument.prototype.initLocalVars = function()
{
    this.langVars = Array();

<?
    foreach($localVars as $k => $v)
    {
        $var = $sTemplate->getString($v);
        echo "this.langVars['".$v."'] = '".str_replace("'", "\'", $var)."';\n";
    }
?>
};

_Wikiargument.prototype.getString = function(key, search, replace)
{
    if(!this.langVars[key])
    {
        this.reportMissingLangVar(key);
        return "LANG_VAR_DOES_NOT_EXIST";
    }

    return this.langVars[key];
};

_Wikiargument.prototype.getStringNumber = function(key, search, replace, num)
{
    if(!this.langVars[key])
    {
        this.reportMissingLangVar(key);
        return "LANG_VAR_DOES_NOT_EXIST";
    }

    return this.langVars[key];
};

_Wikiargument.prototype.sendPacket = function(opcode, data, callback)
{
    var carrier =
    {
        "opcode" : opcode,
        "data"   : data
    };

    jQuery.post("/ajax.php",
                {"request" : jQuery.toJSON(carrier)},
                function(response)
                {
                    callback(jQuery.parseJSON(response));
                });
};

_Wikiargument.prototype.reportMissingLangVar = function(key)
{
    var additionalData =
    {
        "key" : key
    };

    this.reportError('<? echo ERROR_MISSING_LANG_VAR; ?>', additionalData);
};

_Wikiargument.prototype.reportError = function(type, additionalData)
{
    var data =
    {
        "type"           : type,
        "additionalData" : additionalData
    };

    var ts = this;
    this.sendPacket('<? echo CMSG_REPORT_ERROR; ?>', data, function(data){ });
};
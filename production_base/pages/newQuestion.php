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

class PageNewQuestion extends Page
{
    public function PageNewQuestion($row)
    {
        global $sDB, $sRequest, $sQuery, $sUser;
        parent::Page($row);
        $this->view     = VIEW_NEW_QUESTION;

        if($sRequest->getInt("new_question"))
        {
            if($this->handleNewQuestion())
            {
                header("Location: ".$this->redirectUrl);
                exit;
            }
        }
    }

    public function getView()
    {
        return $this->view;
    }

    public function canView()
    {
        global $sUser, $sTemplate, $sPermissions;

        if(!$sUser->isLoggedIn())
        {
            $this->setError($sTemplate->getString("ERROR_NOT_LOGGED_IN"));
            return false;
        }

        if($sPermissions->getPermission($sUser, ACTION_NEW_QUESTION) == PERMISSION_DISALLOWED)
        {
            $this->setError($sTemplate->getString("ERROR_NO_PERMISSION"));
            return false;
        }

        if($this->group && $this->group->getPermission($sUser, ACTION_NEW_QUESTION) == PERMISSION_DISALLOWED)
        {
            $this->setError($sTemplate->getString("NOTICE_NEW_QUESTION_NO_PERMISSION"));
            return false;
        }


        return true;
    }

    public function handleNewQuestion()
    {
        global $sRequest, $sTemplate, $sUser, $sPermissions;

        if(!$sUser->isLoggedIn() || $sPermissions->getPermission($sUser, ACTION_NEW_QUESTION) == PERMISSION_DISALLOWED)
        {
            return false;
        }

        $question       = substr($sRequest->getStringPlain("new_question_title"), 0, MAX_QUESTION_CHR_LENGTH);
        $tagsRaw        = substr($sRequest->getStringPlain("new_question_tags"), 0, MAX_TAGS_CHR_LENGTH);
        $details        = nl2br(htmlspecialchars($sRequest->getStringPlain("new_question_details")));
        $type           = $sRequest->getInt("new_question_type");
        $flags          = $sRequest->getInt("new_question_flags");

        validateQuestionType($type);
        validateQuestionFlags($flags);

        if($type == QUESTION_TYPE_LISTED)
        {
            $flags = 0;
        }


        $questionParsed = preg_replace("/[^0-9a-zßÄÖÜäöüáàâéèêíìîóòôúùû\[\]\{\} -]/i", "", $question);

        if($question == "" || $questionParsed == "")
        {
            $this->setError($sTemplate->getString("ERROR_NEW_QUESTION_INVALID_QUESTION"));

            return false;
        }

        $tags           = Array();
        $tagsNoQuestion = $this->tagsByString($tagsRaw);

        $tags = array_merge($tags, $tagsNoQuestion);
        $tags = array_merge($tags, $this->tagsByString(str_replace(" ", ",", $question)));
        $tags = $this->filterTags($tags);

        return $this->store($question, $questionParsed, $tags, $details, $tagsNoQuestion, $type, $flags);
    }

    private function store($question, $questionParsed, $tags, $details, $tagsNoQuestion, $type, $flags)
    {
        global $sDB, $sUser, $sTemplate, $sNotify;

        $url = url_sanitize($questionParsed);

        $i = 0;
        while(true)
        {
            $cur = $url.($i > 0 ? '-'.$i : '');
            $res = $sDB->exec("SELECT `url` FROM `questions` WHERE `url` = '".mysql_real_escape_string($cur)."' LIMIT 1;");
            if(mysql_num_rows($res))
            {
                $i++;
                continue;
            }

            break;
        }

        if($i > 0)
        {
            $url .= '-'.$i;
        }

        $additionalData = new stdClass();
        $additionalData->percPro     = 0;
        $additionalData->percCon     = 0;
        $additionalData->numCheckIns = 0;
        $additionalData->tags        = array_unique($tagsNoQuestion);

        $sDB->exec("INSERT INTO `questions` (`questionId`, `title`, `url`, `details`, `dateAdded`, `userId`, `score`, `scoreTrending`, `scoreTop`, `additionalData`, `groupId`, `type`, `flags`) VALUES
                                            (NULL, '".mysql_real_escape_string($question)."', '".mysql_real_escape_string($url)."', '".mysql_real_escape_string($details)."',
                                             '".time()."', '".$sUser->getUserId()."', '0', '0', '0', '".serialize($additionalData)."', '".i($this->groupId)."', '".i($type)."', '".i($flags)."');");

        $questionId = mysql_insert_id();

        if(!$questionId)
        {
            $this->setError($sTemplate->getString("ERROR_NEW_QUESTION_TRY_AGAIN"));
            return false;
        }

        foreach($tags as $k => $v)
        {
            $sDB->exec("INSERT INTO `tags` (`tagId`, `questionId`, `tag`, `groupId`) VALUES(NULL, '".i($questionId)."', '".mysql_real_escape_string($v)."', '".i($this->groupId)."');");
        }

        if($this->group)
        {
            $this->redirectUrl = $sTemplate->getRoot()."groups/".$this->group->url()."/".$url."/";
        }else
        {
            if($flags & QUESTION_FLAG_PART_ALL)
            {
                $url = "unregistered/".$url;
            }
            if($type == QUESTION_TYPE_UNLISTED)
            {
                $url = "unlisted/".$url;
            }
            $this->redirectUrl = $sTemplate->getRoot().$url."/";
        }

        $sUser->follow($questionId);

        return $questionId;
    }

    private function filterTags($tags)
    {
        $tags = array_unique($tags);
        return $tags;
    }

    private function tagsByString($string)
    {
        $tags = Array();

        $tagsRaw = str_replace(" ", "-", $string);
        $tagsRaw = str_replace(Array(",", "\n", "\r", "\t"), " ", $tagsRaw);
        $tagsRaw = explode(" ", $tagsRaw);

        foreach($tagsRaw as $k => $v)
        {
            $v = preg_replace('/[^a-z0-9ÄÖÜöäüáàâéèêíìîóòôúùûß\[\]\{\}_-]/i', '', $v);
            $v = trim($v, "-");

            if($v != "")
            {
                array_push($tags, $v);
            }
        }

        return $tags;
    }

    public function title()
    {
        global $sTemplate;
        return $sTemplate->getString("HTML_META_TITLE_NEW_QUESTION");
    }

    public function getFormUrl()
    {
        global $sTemplate;

        if($this->group)
        {
            return $sTemplate->getRoot()."groups/".$this->group->url()."/new-question/";
        }

        return $sTemplate->getRoot()."new-question/";
    }

    private $view;
    private $redirectUrl;
};
?>

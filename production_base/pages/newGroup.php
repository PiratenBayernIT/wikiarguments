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

class PageNewGroup extends Page
{
    public function PageNewGroup($row)
    {
        global $sDB, $sRequest, $sUser;
        parent::Page($row);
        $this->view     = VIEW_NEW_GROUP;

        if($sRequest->getInt("new_group"))
        {
            if($this->handleNewGroup())
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
            $this->setError($sTemplate->getString("ERROR_NOT_LOGGED_IN_GROUP"));
            return false;
        }

        if($sPermissions->getPermission($sUser, ACTION_NEW_GROUP) == PERMISSION_DISALLOWED)
        {
            $this->setError($sTemplate->getString("ERROR_NO_PERMISSION"));
            return false;
        }


        return true;
    }

    public function handleNewGroup()
    {
        global $sRequest, $sTemplate, $sUser, $sPermissions, $sDB;

        if(!$sUser->isLoggedIn() || $sPermissions->getPermission($sUser, ACTION_NEW_GROUP) == PERMISSION_DISALLOWED)
        {
            return false;
        }

        $title          = substr($sRequest->getStringPlain("new_group_title"), 0, MAX_GROUP_NAME_CHR_LENGTH);
        $visibility     = $sRequest->getInt("new_group_visibility");
        $titleParsed    = preg_replace("/[^0-9a-zÄÖÜäöüáàâéèêíìîóòôúùû\[\]\{\} -]/i", "", $title);

        if($title == "")
        {
            $this->setError($sTemplate->getString("ERROR_INVALID_GROUP_NAME"));

            return false;
        }

        $res = $sDB->exec("SELECT `title` FROM `groups` WHERE `title` = '".mysql_real_escape_string($title)."' LIMIT 1;");
        if(mysql_num_rows($res))
        {
            $this->setError($sTemplate->getString("ERROR_GROUP_NAME_ALREADY_EXISTS"));

            return false;
        }

        return $this->store($title, $titleParsed, $visibility);
    }

    private function store($title, $titleParsed, $visibility)
    {
        global $sDB, $sUser, $sTemplate;

        $url = url_sanitize($titleParsed);

        $i = 0;
        while(true)
        {
            $cur = $url.($i > 0 ? '-'.$i : '');
            $res = $sDB->exec("SELECT `url` FROM `groups` WHERE `url` = '".mysql_real_escape_string($cur)."' LIMIT 1;");
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

        $sDB->exec("INSERT INTO `groups` (`groupId`, `title`, `url`, `ownerId`, `dateAdded`, `visibility`) VALUES
                                            (NULL, '".mysql_real_escape_string($title)."', '".mysql_real_escape_string($url)."', '".mysql_real_escape_string($sUser->getUserId())."',
                                             '".time()."', '".i($visibility)."');");

        $groupId = mysql_insert_id();

        if(!$groupId)
        {
            $this->setError($sTemplate->getString("ERROR_NEW_GROUP_TRY_AGAIN"));
            return false;
        }

        $this->redirectUrl = $sTemplate->getRoot()."groups/".$url."/";

        return $groupId;
    }

    public function title()
    {
        global $sTemplate;
        return $sTemplate->getString("HTML_META_TITLE_NEW_GROUP");
    }

    private $view;
    private $redirectUrl;
};
?>
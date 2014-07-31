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

class PageManageGroup extends Page
{
    public function PageManageGroup($row)
    {
        global $sDB, $sRequest, $sQuery, $sUser, $sTemplate;
        parent::Page($row);

        if(!$this->group || !$this->group->groupId() || !$this->group->isAdmin($sUser->getUserId()))
        {
            header("Location: ".$sTemplate->getRoot());
            exit;
        }

        if($sRequest->getInt("update_other_settings"))
        {
            $this->handleUpdateOtherSettings();
        }

        if($sRequest->getInt("add_users"))
        {
            $this->handleAddUsers();
        }

        if($sRequest->getInt("delete_user"))
        {
            $this->handleDeleteUser();
        }

        if($sRequest->getInt("change_permissions"))
        {
            $this->handleChangePermissions();
        }

        if($sRequest->getInt("delete_group"))
        {
            $this->handleDeleteGroup();
        }

        if($sRequest->getInt("update_ownership"))
        {
            $this->handleChangeOwnership();
        }
    }

    public function canView()
    {
        global $sUser;

        if(!$sUser->isLoggedIn())
        {
            return false;
        }

        return true;
    }

    private function handleDeleteUser()
    {
        global $sDB, $sRequest, $sTemplate;

        $userId = $sRequest->getInt("userId");

        $sDB->exec("DELETE FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."' AND `userId` = '".i($userId)."' LIMIT 1;");

        header("Location: ".$sTemplate->getRoot()."groups/".$this->group()->url()."/manage-group/");
        exit;
    }

    private function handleChangePermissions()
    {
        global $sDB, $sRequest, $sTemplate;

        $userId      = $sRequest->getInt("userId");
        $permissions = $sRequest->getString("permissions");
        validateGroupPermissions($permissions);

        $sDB->exec("DELETE FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."' AND `userId` = '".i($userId)."' LIMIT 1;");
        $sDB->exec("INSERT INTO `group_permissions` (`id`, `groupId`, `userId`, `permission`, `dateAdded`)
                                                 VALUES (NULL, '".i($this->group()->groupId())."', '".i($userId)."', '".i($permissions)."', '".time()."');");


        header("Location: ".$sTemplate->getRoot()."groups/".$this->group()->url()."/manage-group/#permissions_".$userId);
        exit;
    }

    private function handleUpdateOtherSettings()
    {
        global $sDB, $sRequest, $sTemplate;

        $visibility = $sRequest->getInt("group_visibility");

        $sDB->exec("UPDATE `groups` SET `visibility` = '".i($visibility)."' WHERE `groupId` = '".$this->groupId()."' LIMIT 1;");

        header("Location: ".$sTemplate->getRoot()."groups/".$this->group()->url()."/manage-group/");
        exit;
    }

    private function handleAddUsers()
    {
        global $sDB, $sRequest, $sTemplate, $sQuery, $sUser;

        $users       = $sRequest->getString("group_users");
        $users       = explode(",", $users);
        $permissions = $sRequest->getString("permissions");
        validateGroupPermissions($permissions);

        foreach($users as $k => $v)
        {
            $v = trim($v);
            $user = $sQuery->getUser("userName=".$v);
            if(!$user)
            {
                $user = $sQuery->getUser("userEmail=".$v);
            }
            if(!$user || $this->group()->isOwner($user->getUserId()))
            {
                continue;
            }

            $sDB->exec("DELETE FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."' AND `userId` = '".i($user->getUserId())."' LIMIT 1;");
            $sDB->exec("INSERT INTO `group_permissions` (`id`, `groupId`, `userId`, `permission`, `dateAdded`)
                                                 VALUES (NULL, '".i($this->group()->groupId())."', '".i($user->getUserId())."', '".i($permissions)."', '".time()."');");
        }

        header("Location: ".$sTemplate->getRoot()."groups/".$this->group()->url()."/manage-group/");
        exit;
    }

    private function handleDeleteGroup()
    {
        global $sDB, $sRequest, $sTemplate, $sQuery, $sUser, $sSession;

        if(!$this->group()->isOwner($sUser->getUserId()))
        {
            return false;
        }

        $sDB->exec("DELETE FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."';");
        $sDB->exec("DELETE FROM `groups` WHERE `groupId` = '".i($this->group()->groupId())."';");

        $res = $sDB->exec("SELECT `questionId` FROM `questions` WHERE `groupId` = '".i($this->group()->groupId())."';");
        while($row = mysql_fetch_object($res))
        {
            $sDB->exec("DELETE FROM `arguments` WHERE `questionId` = '".i($row->questionId)."';");
            $sDB->exec("DELETE FROM `user_factions` WHERE `questionId` = '".i($row->questionId)."';");
            $sDB->exec("DELETE FROM `user_votes` WHERE `questionId` = '".i($row->questionId)."';");
        }
        $sDB->exec("DELETE FROM `questions` WHERE `groupId` = '".i($this->group()->groupId())."';");

        $sSession->setVal('notification', $sTemplate->getString("GROUP_DELETE_SUCCESS"));
        $sSession->serialize();
        header("Location: ".$sTemplate->getRoot());
        exit;
    }

    private function handleChangeOwnership()
    {
        global $sDB, $sRequest, $sTemplate, $sQuery, $sUser, $sSession;

        if(!$this->group()->isOwner($sUser->getUserId()))
        {
            return false;
        }

        $ownerId = $sRequest->getInt('group_owner');
        // check if the new owner is already and admin
        $res = $sDB->exec("SELECT * FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."' AND `userId` = '".i($ownerId)."' LIMIT 1;");
        if(!mysql_num_rows($res))
        {
            return false;
        }

        // update ownership
        $row = mysql_fetch_object($res);
        if($row->permission & GROUP_PERMISSION_ADMIN)
        {
            $permissions = GROUP_PERMISSION_ARGUMENTS + GROUP_PERMISSION_QUESTIONS + GROUP_PERMISSION_ADMIN;

            $sDB->exec("DELETE FROM `group_permissions` WHERE `groupId` = '".i($this->group()->groupId())."' AND `userId` = '".i($ownerId)."' LIMIT 1;");
            $sDB->exec("INSERT INTO `group_permissions` (`id`, `groupId`, `userId`, `permission`, `dateAdded`)
                                                 VALUES (NULL, '".i($this->group()->groupId())."', '".i($sUser->getUserId())."', '".i($permissions)."', '".time()."');");

            $sDB->exec("UPDATE `groups` SET `ownerId` = '".$ownerId."' WHERE `groupId` = '".i($this->group()->groupId())."' LIMIT 1;");

            $sSession->setVal('notification', $sTemplate->getString("GROUP_CHANGE_OWNERSHIP_SUCCESS"));
            $sSession->serialize();
        }

        header("Location: ".$sTemplate->getRoot()."groups/".$this->group()->url()."/manage-group/");
        exit;
    }

    public function title()
    {
        global $sTemplate;
        return $sTemplate->getString("HTML_META_TITLE_MANAGE_GROUP");
    }
};
?>
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

class Group
{
    public function Group($groupId, $row = false, $url = "")
    {
        global $sDB;

        if(!$row)
        {
            $res;
            if($groupId != -1)
            {
                $res = $sDB->exec("SELECT * FROM `groups` WHERE `groupId` = '".i($groupId)."' LIMIT 1;");
            }else if($url != "")
            {
                $res = $sDB->exec("SELECT * FROM `groups` WHERE `url` = '".mysql_real_escape_string($url)."' LIMIT 1;");
            }

            if(!mysql_num_rows($res))
            {
                $this->groupId = 0;
                return;
            }
            $row = mysql_fetch_object($res);
        }

        $this->groupId          = $row->groupId;
        $this->ownerId          = $row->ownerId;
        $this->title            = $row->title;
        $this->url              = $row->url;
        $this->visibility       = $row->visibility;
        $this->dateAdded        = $row->dateAdded;
        $this->userPermissions  = NULL;
    }

    public function groupId()
    {
        return $this->groupId;
    }

    public function url()
    {
        return $this->url;
    }

    public function title()
    {
        return $this->title;
    }

    public function ownerId()
    {
        return $this->ownerId;
    }

    public function visibility()
    {
        return $this->visibility;
    }

    public function userPermissions()
    {
        global $sDB;

        if($this->userPermissions)
        {
            return $this->userPermissions;
        }
        $this->userPermissions = Array();

        $res = $sDB->exec("SELECT * FROM `group_permissions` WHERE `groupId` = '".i($this->groupId)."';");
        while($row = mysql_fetch_object($res))
        {
            $this->userPermissions[$row->userId] = $row->permission;
        }

        return $this->userPermissions;
    }

    public function getPermission(User $user, $action)
    {
        if($user->getUserId() == $this->ownerId)
        {
            return PERMISSION_ALLOWED;
        }

        if($action == ACTION_VIEW_GROUP)
        {
            // easy check for view permission
            if($this->visibility == VISIBILITY_PUBLIC)
            {
                return PERMISSION_ALLOWED;
            }else
            {
                // check if the user has arbitrary rights here
                $permissions = $this->userPermissions();
                if($permissions[$user->getUserId()])
                {
                    return PERMISSION_ALLOWED;
                }
            }
        }else if($action == ACTION_NEW_QUESTION)
        {
            $permissions = $this->userPermissions();
            if($permissions[$user->getUserId()] && $permissions[$user->getUserId()] & GROUP_PERMISSION_QUESTIONS)
            {
                return PERMISSION_ALLOWED;
            }
        }else if(in_array($action, Array(ACTION_NEW_ARGUMENT, ACTION_NEW_COUNTER_ARGUMENT)))
        {
            $permissions = $this->userPermissions();
            if($permissions[$user->getUserId()] && $permissions[$user->getUserId()] & GROUP_PERMISSION_ARGUMENTS)
            {
                return PERMISSION_ALLOWED;
            }
        }else if($action == ACTION_VOTE)
        {
            // check if the user has arbitrary rights here
            $permissions = $this->userPermissions();
            if($permissions[$user->getUserId()])
            {
                return PERMISSION_ALLOWED;
            }
        }

        return PERMISSION_DISALLOWED;
    }

    public function isOwner($userId)
    {
        return $this->ownerId() == $userId;
    }

    public function isAdmin($userId)
    {
        if($this->isOwner($userId))
        {
            return true;
        }

        $permissions = $this->userPermissions();
        if($permissions[$userId] && $permissions[$userId] & GROUP_PERMISSION_ADMIN)
        {
            return true;
        }

        return false;
    }

    private $groupId;
    private $ownerId;
    private $visibility;
    private $dateAdded;
    private $title;
    private $url;
    private $userPermissions;
};
 ?>
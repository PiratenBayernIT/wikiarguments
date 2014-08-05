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

class CmsgReportError extends PacketHandler
{
    public function CmsgReportError(Request $requestObj, Packet $response)
    {
        parent::PacketHandler($requestObj, $response);

        $this->handlePacket();
    }

    public function handlePacket()
    {
    }
}

class CmsgFollowQuestion extends PacketHandler
{
    public function CmsgFollowQuestion(Request $requestObj, Packet $response)
    {
        parent::PacketHandler($requestObj, $response);

        $this->handlePacket();
    }

    public function handlePacket()
    {
        global $sUser, $sDB, $sTemplate, $sLog;
        $requestObj = $this->_requestObj;
        $response   = $this->_response;
        $qId        = $requestObj->getInt(Array("data", "qId"));

        $response->opcode = SMSG_FOLLOW_QUESTION_RESPONSE;

        $response->data->result  = 0;

        if($sUser->isLoggedIn() && $qId)
        {
            $response->data->result = 1;

            $sUser->follow($qId);
        }else
        {
        }
    }
}

class CmsgUnfollowQuestion extends PacketHandler
{
    public function CmsgUnfollowQuestion(Request $requestObj, Packet $response)
    {
        parent::PacketHandler($requestObj, $response);

        $this->handlePacket();
    }

    public function handlePacket()
    {
        global $sUser, $sDB, $sTemplate, $sLog;
        $requestObj = $this->_requestObj;
        $response   = $this->_response;
        $qId        = $requestObj->getInt(Array("data", "qId"));

        $response->opcode = SMSG_UNFOLLOW_QUESTION_RESPONSE;

        $response->data->result  = 0;

        if($sUser->isLoggedIn() && $qId)
        {
            $response->data->result = 1;

            $sUser->unfollow($qId);
        }else
        {
        }
    }
}

class CmsgVote extends PacketHandler
{
    public function CmsgVote(Request $requestObj, Packet $response)
    {
        parent::PacketHandler($requestObj, $response);

        $this->handlePacket();
    }

    public function handlePacket()
    {
        global $sUser, $sDB, $sTemplate, $sLog, $sStatistics;
        $requestObj = $this->_requestObj;
        $response   = $this->_response;
        $qId        = $requestObj->getInt(Array("data", "qId"));
        $aId        = $requestObj->getInt(Array("data", "aId"));
        $vote       = $requestObj->getInt(Array("data", "vote"));

        $question   = new Question($qId);
        $argument   = false;
        if($aId)
        {
            $argument = new Argument($aId);
        }

        $response->opcode = SMSG_VOTE_RESPONSE;

        $response->data->result = 0;
        $response->data->qId    = $qId;
        $response->data->aId    = $aId;
        $response->data->vote   = $vote;

        // check for valid question / argument
        if($question->questionId()  == 0 || ($argument && $argument->argumentId() == -1))
        {
            $response->data->error = $sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN");
            return false;
        }

        // user login state
        if(!$sUser->isLoggedIn() &&
           ($question->type() != QUESTION_TYPE_UNLISTED || !($question->flags() & QUESTION_FLAG_PART_ALL)))
        {
            $response->data->error = $sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN");
            return false;
        }

        // group validation
        if($question->group() && $question->group()->getPermission($sUser, ACTION_VOTE) == PERMISSION_DISALLOWED)
        {
            $response->data->error = $sTemplate->getString("NOTICE_VOTE_NOT_LOGGED_IN");
            return false;
        }

        $sStatistics->vote($question, $aId, $vote);

        $response->data->result = 1;

        // get new question score
        if($aId == 0)
        {
            $question = new Question($qId);
            $response->data->score = $question->score();
        }else
        {
            $argument = new Argument($aId);
            $response->data->score = $argument->score();
        }
    }
}

class CmsgSelectFaction extends PacketHandler
{
    public function CmsgSelectFaction(Request $requestObj, Packet $response)
    {
        parent::PacketHandler($requestObj, $response);

        $this->handlePacket();
    }

    public function handlePacket()
    {
        global $sUser, $sDB, $sTemplate, $sLog;
        $requestObj = $this->_requestObj;
        $response   = $this->_response;
        $qId        = $requestObj->getInt(Array("data", "qId"));
        $faction    = $requestObj->getInt(Array("data", "faction"));

        $response->data->result  = 0;
        $response->opcode = SMSG_SELECT_FACTION_RESPONSE;

        if(!validateFaction($faction, false))
        {
            return false;
        }

        $question = new Question($qId);
        if($question->questionId() == 0)
        {
            return false;
        }

        $sUser->setFactionByQuestionId($qId, $faction);

        $sStatistics->updateQuestionStats($qId);

        $response->data->result = 1;
    }
}
?>

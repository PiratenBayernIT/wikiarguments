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

class StatisticsMgr
{
    public function StatisticsMgr()
    {
        $this->queryCacheHits   = 0;
        $this->queryCacheMisses = 0;

        $this->run();
    }

    /*
    * run all necessary statistics
    */
    public function run()
    {
    }

    public function queryCacheHit()
    {
        $this->queryCacheHits++;
    }

    public function queryCacheMiss()
    {
        $this->queryCacheMisses++;
    }

    public function queryCacheHits()
    {
        return $this->queryCacheHits;
    }

    public function queryCacheMisses()
    {
        return $this->queryCacheMisses;
    }

    public function updateQuestionStats($questionId)
    {
        global $sDB, $sTimer;

        $sTimer->start("updateQuestionStats");

        $res = $sDB->exec("SELECT * FROM `questions` WHERE `questionId` = '".i($questionId)."' LIMIT 1;");
        if(!mysql_num_rows($res))
        {
            return false;
        }
        $q = mysql_fetch_object($res);

        if (VOTE_FACTIONS) {
            $sTimer->start("updateQuestionStats::factionData");

            $num = Array(FACTION_PRO => 0, FACTION_CON => 0);
            $res = $sDB->exec("SELECT COUNT(*) as `cnt`, `state` FROM `user_factions` WHERE `questionId` = '".i($questionId)."' GROUP BY `state`");
            while($row = mysql_fetch_object($res))
            {
                $num[$row->state] = $row->cnt;
            }

            $total = max($num[FACTION_PRO] + $num[FACTION_CON], 1);

            $additionalData              = unserialize($q->additionalData);
            $additionalData->percPro     = $num[FACTION_PRO] / $total;
            $additionalData->percCon     = $num[FACTION_CON] / $total;
            $additionalData->numCheckIns = $num[FACTION_PRO] + $num[FACTION_CON];

            $sDB->exec("UPDATE `questions` SET `additionalData` = '".mysql_real_escape_string(serialize($additionalData))."' WHERE `questionId` = '".i($questionId)."' LIMIT 1;");

            $sTimer->stop("updateQuestionStats::factionData");
        }

        $this->updateQuestionScore($questionId);

        $this->updateArgumentScoreBatch($questionId);

        $sTimer->stop("updateQuestionStats");
    }

    private function trendingScore($score, $dateAdded)
    {
        $timePassed = $dateAdded - 1338501600;
        $s          = 0;

        if($score > 0)
        {
            $s = 1;
        }else if($score < 0)
        {
            $s = -1;
        }

        $ret = round(log(max(abs($score), 1), 10) + $s * $timePassed / 45000);

        return $ret;
    }

    private function updateQuestionScore($questionId)
    {
        global $sDB, $sTimer;

        $sTimer->start("updateQuestionScore");


        $res = $sDB->exec("SELECT * FROM `questions` WHERE `questionId` = '".i($questionId)."' LIMIT 1;");
        if(!mysql_num_rows($res))
        {
            return false;
        }
        $q = mysql_fetch_object($res);

        $num = Array(VOTE_UP => 0, VOTE_DN => 0);
        $res = $sDB->exec("SELECT count(*) as `cnt`, `vote` FROM `user_votes` WHERE `questionId` = '".i($questionId)."' AND `argumentId` = '0' GROUP BY `vote`;");
        while($row = mysql_fetch_object($res))
        {
            $num[$row->vote] = $row->cnt;
        }

        $score          = $num[VOTE_UP];
        $score          = $score - $num[VOTE_DN];
        $scoreTop       = $score;
        $scoreTrending  = $this->trendingScore($score, $q->dateAdded);

        $sDB->exec("UPDATE `questions` SET `score` = '".i($score)."', `scoreTrending` = '".i($scoreTrending)."', `scoreTop` = '".i($scoreTop)."' WHERE `questionId` = '".i($questionId)."' LIMIT 1;");

        $sTimer->stop("updateQuestionScore");
    }

    /*
    * batch update argument scores for all arguments of the given question.
    */
    private function updateArgumentScoreBatch($questionId)
    {
        global $sDB, $sTimer;

        $sTimer->start("updateArgumentScoreBatch");
        $sTimer->start("updateArgumentScoreBatch::Fetch");

        $score = Array();

        $res = $sDB->exec("SELECT count(*) as `cnt`, `vote`, `argumentId` FROM `user_votes` WHERE `questionId` = '".i($questionId)."' GROUP BY `argumentId`, `vote`;");
        while($row = mysql_fetch_object($res))
        {
            if($row->argumentId == 0)
            {
                continue;
            }
            if(!$score[$row->argumentId])
            {
                $score[$row->argumentId] = 0;
            }

            $score[$row->argumentId] += $row->vote * $row->cnt;
        }

        $sTimer->stop("updateArgumentScoreBatch::Fetch");
        $sTimer->start("updateArgumentScoreBatch::Update");

        $sDB->exec("START TRANSACTION;");
        foreach($score as $k => $v)
        {
            $sDB->exec("UPDATE `arguments` SET `score` = '".i($v)."' WHERE `questionId` = '".i($questionId)."' AND `argumentId` = '".i($k)."' LIMIT 1;");
        }
        $sDB->exec("COMMIT;");

        $sTimer->stop("updateArgumentScoreBatch::Update");

        $sTimer->stop("updateArgumentScoreBatch");
    }

    private function updateArgumentScore($questionId, $argumentId)
    {
        global $sDB, $sTimer;

        $num = Array(VOTE_UP => 0, VOTE_DN => 0);

        $res = $sDB->exec("SELECT count(*) as `cnt`, `vote` FROM `user_votes` WHERE `questionId` = '".i($questionId)."' AND `argumentId` = '".i($argumentId)."' GROUP BY `vote`;");
        while($row = mysql_fetch_object($res))
        {
            $num[$row->vote] = $row->cnt;
        }

        $score = $num[VOTE_UP] - $num[VOTE_DN];

        $sDB->exec("UPDATE `arguments` SET `score` = '".i($score)."' WHERE `questionId` = '".i($questionId)."' AND `argumentId` = '".i($argumentId)."' LIMIT 1;");
    }

    /*
    * Remove all votes from this argument.
    */
    public function resetArgumentVotes(Argument $argument)
    {
        global $sDB, $sQuery;

        $inc = 0;

        $res = $sDB->exec("SELECT * FROM `user_votes` WHERE `argumentId` = '".i($argument->argumentId())."';");
        while($row = mysql_fetch_object($res))
        {
            $inc += $row->vote;
        }

        $sDB->execUsers("UPDATE `users` SET `scoreArguments` = `scoreArguments` - ".i($inc)." WHERE `userId` = '".i($argument->userId())."' LIMIT 1;");

        $sDB->exec("DELETE FROM `user_votes` WHERE `argumentId` = '".i($argument->argumentId())."';");
    }

    /*
    * Remove all votes from this question.
    * Make sure that no arguments exist before this call!
    */
    public function resetQuestionVotes(Question $question)
    {
        global $sDB, $sQuery;

        $inc = 0;

        $res = $sDB->exec("SELECT * FROM `user_votes` WHERE `questionId` = '".i($question->questionId())."';");
        while($row = mysql_fetch_object($res))
        {
            $inc += $row->vote;
        }

        $sDB->execUsers("UPDATE `users` SET `scoreQuestions` = `scoreQuestions` - ".i($inc)." WHERE `userId` = '".i($question->authorId())."' LIMIT 1;");

        $sDB->exec("DELETE FROM `user_votes` WHERE `questionId` = '".i($question->questionId())."';");
    }

    public function vote(Question $question, $argumentId, $vote, $user = false, $forceVote = false)
    {
        global $sUser, $sDB, $sQuery, $sPermissions;

        $questionId = $question->questionId();

        if($user == false)
        {
            $user = $sUser;
            if(!$sUser->isLoggedIn() &&
               ($question->type() != QUESTION_TYPE_UNLISTED || !($question->hasFlag(QUESTION_FLAG_PART_ALL))))
            {
                return false;
            }
        }

        if(!in_array($vote, Array(VOTE_UP, VOTE_DN, VOTE_NONE)))
        {
            return false;
        }

        if($sPermissions->getPermission($user, ACTION_VOTE) == PERMISSION_DISALLOWED)
        {
            return false;
        }

        $cookieData = false;
        if(!$user->isLoggedIn())
        {
            $cookieData = $_COOKIE['voteData'];
            if($cookieData)
            {
                $cookieData = unserialize($cookieData);
                if(is_array($cookieData))
                {
                    if(is_array($cookieData[$questionId]))
                    {
                        if($cookieData[$questionId][$argumentId])
                        {
                            validateVote($cookieData[$questionId][$argumentId]);
                        }
                    }else
                    {
                        $cookieData[$questionId] = Array();
                    }
                }else
                {
                    $cookieData = Array();
                    $cookieData[$questionId] = Array();
                }
            }else
            {
                $cookieData = Array();
                $cookieData[$questionId] = Array();
            }
        }

        if($argumentId && !$forceVote && VOTE_FACTIONS && CONSTRAIN_FACTIONS)
        {
            $faction = $user->getFactionByQuestionId($questionId);
            if($faction == FACTION_NONE)
            {
                return false;
            }

            $argument = $sQuery->getArgumentById($argumentId);
            if($argument->type() != $faction)
            {
                return false;
            }
        }

        if($user->isLoggedIn())
        {
            $this->lazyUpdateUserStats($questionId, $argumentId, $vote, $user->getUserId());

            $sDB->exec("DELETE FROM `user_votes` WHERE `userId` = '".i($user->getUserId())."' AND `questionId` = '".i($questionId)."' AND `argumentId` = '".i($argumentId)."';");
        }else
        {
            // check if a vote state exists in the user's cookie
            if($cookieData[$questionId][$argumentId])
            {
                validateVote($cookieData[$questionId][$argumentId]);
                $sDB->exec("DELETE FROM `user_votes` WHERE `userId` = '".i($user->getUserId())."' AND `questionId` = '".i($questionId)."' AND `argumentId` = '".i($argumentId)."' LIMIT 1;");
                unset($cookieData[$questionId]);
            }
        }

        if($vote != VOTE_NONE)
        {
            $sDB->exec("INSERT INTO `user_votes` (`voteId`, `userId`, `questionId`, `argumentId`, `vote`, `dateAdded`)
                        VALUES (NULL, '".i($user->getUserId())."', '".i($questionId)."', '".i($argumentId)."', '".i($vote)."', '".time()."');");

            if(!$user->isLoggedIn())
            {
                $cookieData[$questionId][$argumentId] = $vote;
            }
        }

        $this->updateQuestionStats($questionId);

        if(!$user->isLoggedIn())
        {
            setcookie("voteData", serialize($cookieData));
        }

        return true;
    }

    /*
    * Update user score lazily.
    * After this call, user stats are updated such that the vote of userId is taken into account.
    */
    private function lazyUpdateUserStats($questionId, $argumentId, $vote, $userId)
    {
        global $sUser;
        if($sUser->getUserId() == 0)
        {
            return;
        }
        global $sDB, $sQuery, $sUser;

        $res = $sDB->exec("SELECT * FROM `user_votes` WHERE `userId` = '".i($sUser->getUserId())."' AND `questionId` = '".i($questionId)."' AND `argumentId` = '".i($argumentId)."';");

        $inc = $vote;

        while($row = mysql_fetch_object($res))
        {
            $inc -= $row->vote;
        }

        $userId = $sQuery->getAuthorById($questionId, $argumentId);

        if($argumentId)
        {
            $sDB->execUsers("UPDATE `users` SET `scoreArguments` = `scoreArguments` + ".i($inc)." WHERE `userId` = '".i($userId)."' LIMIT 1;");
        }else
        {
            $sDB->execUsers("UPDATE `users` SET `scoreQuestions` = `scoreQuestions` + ".i($inc)." WHERE `userId` = '".i($userId)."' LIMIT 1;");
        }
    }

    private $queryCacheHits;
    private $queryCacheMisses;
}
?>

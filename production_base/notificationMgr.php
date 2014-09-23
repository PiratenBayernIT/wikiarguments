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

class NotificationMgr
{
    public function NotificationMgr()
    {
    }

    public function quality($subject, $message)
    {
    }

    public function info($subject, $message)
    {
    }

    public function development($subject, $message)
    {
    }

    public function notifyNewArgument(Question $q, Argument $a)
    {
        global $sDB, $sTimer, $sTemplate;

        $sTimer->start("notifyNewArgument");

        $res = $sDB->exec("SELECT `notifications`.`userId`, `notifications`.`flags`, `users`.`email`, `users`.`userName` FROM `notifications`
                           LEFT JOIN `users` ON `users`.`userId` = `notifications`.`userId`
                           WHERE `questionId` = '".i($q->questionId())."';");

        while($row = mysql_fetch_object($res))
        {
            // no notifications for our own arguments.
            if($a->userId() == $row->userId)
            {
                continue;
            }

            $uId         = new BaseConvert($row->userId);
            $qId         = new BaseConvert($q->questionId());
            $profileUrl  = $sTemplate->getShortUrlBase()."u".$uId->val();
            $unfollowUrl = $sTemplate->getShortUrlBase()."f".$qId->val();
            $url         = $a->shortUrl();
            if(!SHORTURL_BASE)
            {
                $profileUrl  = $sTemplate->getRoot()."user/".$row->userId."/";
                $unfollowUrl = $sTemplate->getRoot()."unfollow.php?qId=".$q->questionId();
                $url         = $a->fullurl();
            }

            $subject = $sTemplate->getString("NOTIFICATION_NEW_ARGUMENT_SUBJECT",
                Array("[QUESTION]", "[ARGUMENT]"), Array($q->title(), $a->headline()));
            $message = $sTemplate->getString("NOTIFICATION_NEW_ARGUMENT_BODY",
                                             Array("[USERNAME]", "[AUTHOR]", "[URL]", "[QUESTION]", "[ARGUMENT]", "[UNFOLLOW_URL]", "[PROFILE_URL]"),
                                             Array($row->userName, $a->author(), $url, $q->title(), $a->headline(), $unfollowUrl, $profileUrl));
            $this->sendMail($row->email, "", $subject, $message);
        }

        $sTimer->stop("notifyNewArgument");
    }

    private function sendMail($to, $prefix, $subject, $message)
    {
        if($prefix == "")
        {
            send_mail_from(SENDMAIL_FROM, SENDMAIL_FROM_NAME, $to, $subject, $message);
        }else
        {
            send_mail_from(SENDMAIL_FROM, SENDMAIL_FROM_NAME, $to, "[".$prefix."]".$subject, $message);
        }
    }
};
?>

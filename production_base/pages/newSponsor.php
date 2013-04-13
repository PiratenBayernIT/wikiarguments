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

class PageNewSponsor extends Page
{
    public function PageNewSponsor($row)
    {
        global $sDB, $sRequest, $sQuery, $sTemplate, $sUser;
        parent::Page($row);

        if($sRequest->getInt("new_sponsor"))
        {
            if($this->handleNewSponsor())
            {
                header("Location: ".$sTemplate->getRoot());
                exit;
            }
        }
    }

    public function title()
    {
        global $sTemplate;
        return $sTemplate->getString("HTML_META_TITLE_NEW_SPONSOR");
    }

    private function handleNewSponsor()
    {
        global $sDB, $sRequest, $sQuery, $sTemplate, $sSession, $sNotify;

        // contact info
        $name                   = $sRequest->getString("sponsor_name");
        $companyName            = $sRequest->getString("sponsor_company_name");
        $street                 = $sRequest->getString("sponsor_street");
        $zip                    = $sRequest->getInt("sponsor_zip");
        $city                   = $sRequest->getString("sponsor_city");
        $phone                  = $sRequest->getString("sponsor_phone");
        $email                  = $sRequest->getString("sponsor_email");
        $additionalInformation  = $sRequest->getString("sponsor_additional_information");
        $password               = $sRequest->getString("sponsor_password");
        $salt                   = salt();
        $passwordHash           = crypt($password, '$6$rounds=5000$'.$salt.'$');
        $password2              = $sRequest->getString("sponsor_password2");

        // sponsor info
        $slogan                 = $sRequest->getString("sponsor_slogan");
        $url                    = $sRequest->getString("sponsor_url");

        $fileName = $fileExt = "";
        if($_FILES['sponsor_logo'] && $_FILES['sponsor_logo']['name'])
        {
            $userFileName           = @$_FILES['sponsor_logo']['name'];
            $fileName               = basename($userFileName);
            $fileExt                = end(explode(".", $userFileName));
        }

        // payment info
        $paymentMethod              = $sRequest->getInt("sponsor_payment_method");
        $paymentData                = new stdClass();
        $paymentData->paymentMethod = $paymentMethod;
        if($paymentMethod == PAYMENT_METHOD_ELV)
        {
            $paymentELVName                         = $sRequest->getString("sponsor_elv_name");
            $paymentELVAccountNumber                = $sRequest->getString("sponsor_elv_account_number");
            $paymentELVBankNumber                   = $sRequest->getString("sponsor_elv_bank_number");

            $paymentData->paymentELVName            = $paymentELVName;
            $paymentData->paymentELVAccountNumber   = $paymentELVAccountNumber;
            $paymentData->paymentELVBankNumber      = $paymentELVBankNumber;
        }
        $paymentInterval                = $sRequest->getInt("sponsor_payment_interval");
        $paymentAmount                  = $sRequest->getInt("sponsor_amount");

        $paymentData->paymentInterval   = $paymentInterval;
        $paymentData->paymentAmount     = $paymentAmount;

        // validate data
        if($name == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_NAME"));
            return false;
        }
        if($street == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_STREET"));
            return false;
        }
        if($zip == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_ZIP"));
            return false;
        }
        if($city == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_CITY"));
            return false;
        }
        if($phone == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_PHONE"));
            return false;
        }
        if($email == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_EMAIL"));
            return false;
        }
        $res = $sDB->exec("SELECT * FROM `sponsors_data` WHERE `email` = '".mysql_real_escape_string($email)."' LIMIT 1;");
        if(mysql_num_rows($res))
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_EMAIL_EXISTS"));
            return false;
        }
        if($password == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_PASSWORD"));
            return false;
        }
        if($password != $password2)
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_PASSWORD_MISSMATCH"));
            return false;
        }
        if($slogan == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_SLOGAN"));
            return false;
        }
        if($url == "")
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_URL"));
            return false;
        }
        if(!in_array($paymentMethod, Array(PAYMENT_METHOD_ELV, PAYMENT_METHOD_BILL)))
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_PAYMENT_METHOD"));
            return false;
        }
        if($paymentInterval < time())
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_PAYMENT_INTERVAL"));
            return false;
        }
        if($paymentAmount <= 0)
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_AMOUNT"));
            return false;
        }
        if($paymentMethod == PAYMENT_METHOD_ELV)
        {
            if($paymentELVName == "")
            {
                $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_ELV_NAME"));
                return false;
            }
            if($paymentELVAccountNumber == "")
            {
                $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_ELV_ACCOUNT_NUMBER"));
                return false;
            }
            if($paymentELVBankNumber == "")
            {
                $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_ELV_BANK_NUMBER"));
                return false;
            }
        }

        if($fileExt && !in_array($fileExt, Array("png", "jpg", "jpeg")))
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_INVALID_FILE_EXTENSION"));
            return false;
        }

        $logoHeight = $logoWidth = 0;
        $thumb;
        if($fileExt)
        {
            try
            {
                $thumb      = new Imagick($_FILES["sponsor_logo"]["tmp_name"]);
                $imgData    = $thumb->getImageGeometry();
                if($imgData['height'] > 160 || $imgData['width'] > 160)
                {
                    $thumb->resizeImage(160,160, imagick::FILTER_LANCZOS, 1, true);
                }

                $imgData    = $thumb->getImageGeometry();
                $logoHeight = $imgData["height"];
                $logoWidth  = $imgData["width"];
            }catch(Exception $e)
            {

            }
        }

        $res = $sDB->exec("INSERT INTO `sponsors_data` (`sponsorId`, `name`, `companyName`, `street`, `zip`, `city`,
                                                        `phone`, `email`, `password`, `slogan`, `paymentMethod`,
                                                        `paymentData`, `amount`, `dateAdded`, `approved`, `currentLogoApproved`, `logoHeight`, `logoWidth`, `url`, `additionalInformation`)
                                  VALUES(NULL, '".mysql_real_escape_string($name)."', '".mysql_real_escape_string($companyName)."',
                                         '".mysql_real_escape_string($street)."', '".mysql_real_escape_string($zip)."',
                                         '".mysql_real_escape_string($city)."', '".mysql_real_escape_string($phone)."',
                                         '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($passwordHash)."',
                                         '".mysql_real_escape_string($slogan)."', '".mysql_real_escape_string($paymentMethod)."',
                                         '".mysql_real_escape_string(serialize($paymentData))."', '".mysql_real_escape_string($paymentAmount)."',
                                         '".time()."', '0', '0', '".mysql_real_escape_string($logoHeight)."',
                                         '".mysql_real_escape_string($logoWidth)."', '".mysql_real_escape_string($url)."',
                                         '".mysql_real_escape_string($additionalInformation)."')");

        $sponsorId = mysql_insert_id();
        if(!$sponsorId)
        {
            $this->setError($sTemplate->getString("SPONSOR_ERROR_TRY_AGAIN_LATER"));
            return false;
        }

        if($fileExt)
        {
            try
            {
                // move and resize logo.
                $logoPath = $sTemplate->getSponsorLogosRootAbs().$sponsorId.".png";
                $thumb->writeImage($logoPath);
                $thumb->destroy();
            } catch(Exception $e)
            {

            }
        }

        $sSession->setVal('notification', $sTemplate->getString("SPONSOR_SIGNUP_SUCCESS"));
        $sSession->serialize();

        $subject = $sTemplate->getString("SPONSOR_CONFIRMATION_EMAIL_SUBJECT");
        $message = $sTemplate->getString("SPONSOR_CONFIRMATION_EMAIL_BODY",
                                         Array("[NAME]"),
                                         Array($name));

        $mail = new HTMLMail($email, $email, SENDMAIL_FROM_NAME, SENDMAIL_FROM);
        $mail->buildMessage($subject, $message);
        $mail->sendmail();

        $sNotify->sponsor("new sponsor", "email: ".$email."<br />\n"."amount: ".$paymentAmount);

        return true;
    }
};
?>

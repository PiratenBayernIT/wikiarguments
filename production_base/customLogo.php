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


include("commonHeaders.php");
header("Content-Type: image/png");

$sTimer->start('logoRun');

$groupId = $sRequest->getInt("group");
$group = new Group($groupId);
if(!$group || $group->getPermission($sUser, ACTION_VIEW_GROUP) == PERMISSION_DISALLOWED)
{
    $fp = fopen($sTemplate->getTemplateRootAbs()."img/header_logo.png", 'rb');
    fpassthru($fp);
    fclose($fp);
    exit;
}

$im        = imagecreate(400, 18);
imagecolorallocate($im, 255, 255, 255);

// get font
$font = $sTemplate->getTemplateRootAbs()."fonts/font.otf";

// transparent background
imagesavealpha($im, true);
$trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $trans_colour);

// group title
$textcolor = imagecolorexact($im, 255, 255, 255);
mb_internal_encoding("UTF-8");
imagefttext($im, 14, 0, 5, 17, $textcolor, $font, mb_strtoupper($group->title()));


imagepng($im);
imagedestroy($im);

$sTimer->stop('logoRun');
?>

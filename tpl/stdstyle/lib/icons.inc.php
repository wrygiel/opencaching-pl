<?php

/* * *************************************************************************

  $Id: icons.inc.php,v 1.9 2006/11/11 10:35:48 oliver Exp $
  $Date: 2006/11/11 10:35:48 $
  $Revision: 1.9 $
  begin                : Fr Sept 9 2005
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  set template specific variables for icons

 * ************************************************************************** */

$poweTrailMarkers = powerTrailBase::getPowerTrailTypes();

function icon_log_type($icon_small, $text)
{
    global $stylepath;
    return "<img src='$stylepath/images/$icon_small' class='icon16' alt='$text' title='$text'/>";
}

function icon_cache_status($status, $text)
{
    global $stylepath;
    switch ($status) {
        case 1: $icon = "log/16x16-go.png";
            break;
        case 2: $icon = "log/16x16-stop.png";
            break;
        case 3: $icon = "log/16x16-trash.png";
            break;
        case 4: $icon = "log/16x16-wattend.png";
            break;
        case 5: $icon = "log/16x16-wattend.png";
            break;
        case 6: $icon = "log/16x16-stop.png";
            break;

        default: $icon = "log/16x16-go.png";
            break;
    }
    return "<img src='$stylepath/images/$icon' class='icon16' alt='$text' title='$text'/>";
}

function icon_difficulty($what, $difficulty)
{
    global $stylepath;
    global $difficulty_text_diff;
    global $difficulty_text_terr;

    if ($what != "diff" && $what != "terr")
        die("Wrong difficulty-identifier!");

    $difficulty = (int) $difficulty;
    if ($difficulty < 2 || $difficulty > 10)
        die("Wrong difficulty-value $what: $difficulty");

    $icon = sprintf("$stylepath/images/difficulty/$what-%d%d.gif", (int) $difficulty / 2, ((float) ($difficulty / 2) - (int) ($difficulty / 2)) * 10);
    $text = sprintf($what == "diff" ? $difficulty_text_diff : $difficulty_text_terr, $difficulty / 2);
    return "<img src='$icon' border='0' width='19' height='16' hspace='2' alt='$text' title='$text'/>";
}

function icon_rating($founds, $topratings)
{
    global $stylepath;
    global $rating_text;
    global $not_rated;

    if ($topratings == 0)
        return '';

    $sAltText = $topratings . ' Rekomendacji';

    if ($topratings > 3)
        $nIconsCount = 2;
    else
        $nIconsCount = $topratings;

    $sRetval = '';
    $sRetval .= str_repeat('<img src="images/rating-star.png" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />', $nIconsCount);

    if ($topratings > 3)
        $sRetval .= '<img src="images/rating-plus.gif" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />';

    return '<nobr>' . $sRetval . '</nobr>&nbsp;';
}

function icon_geopath_small($ptID, $ptImg, $ptName, $ptType, $pt_cache_intro_tr, $pt_icon_title_tr)
{
    /*
      attributes:
      $ptID   = GeoPatch Name
      $ptImg  = GeoPath Image (link)
      $ptName = GeoPath name
      $ptTyp  = GeoPath Type (atr for $poweTrailMarkers below)
      $pt_cache_intro_tr =  translated tooltip into ("This cache belongs to..")
      $pt_icon_title_tr = translate attr. for icon ALT and NAME
     */
    global $stylepath, $poweTrailMarkers;
    if ($ptImg == '')
        $ptImg = $stylepath . '/images/blue/powerTrailGenericLogo.png';
    // for testing use: $ptImg = 'ocpl-dynamic-files/images/uploads/powerTrailLogoId13.png';
    $PT_tip = $pt_cache_intro_tr . '<BR>';
    $PT_tip.='<table width=\'99%\'>';
    $PT_tip.='  <tr>';
    $PT_tip.='      <td width=\'51\'><img border=\'0\' width=\'50\' src=\'' . $ptImg . '\' /></td>';
    $PT_tip.='      <td align=\'center\'><span style=\'font-size:13px;\'><B>' . $ptName . '</B></span></td>';
    $PT_tip.='  </tr>';
    $PT_tip.='</table>';
    $PT_tip = str_replace('\\', '\\\\', $PT_tip);
    $PT_tip = str_replace('\'', '\\\'', $PT_tip);
    $PT_tip = htmlspecialchars($PT_tip, ENT_QUOTES, 'UTF-8');
    // no tabled version: $PT_tip= $pt_cache_intro_tr.'<BR><span align=center><B>'.$ptName.'</B><BR>    <img border=0 width=50 src='.$ptImg.' /></span>';
    $PT_icon = '<a href="powerTrail.php?ptAction=showSerie&ptrail=' . $ptID . '" onmouseover="Tip(\'' . $PT_tip . '\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,220,SHADOW,true)" onmouseout="UnTip()" class="links">';
    $PT_icon.='<img src="' . $poweTrailMarkers[$ptType]['icon'] . '" class="icon16" alt="' . $pt_icon_title_tr . '" title="' . $pt_icon_title_tr . '" /></a>';

    return $PT_icon;
}

//function icon_rating($founds, $topratings)
//{
//  global $stylepath;
//  global $rating_text;
//  global $not_rated;
//
//  if ($founds < 3)
//  {
//      $icon = sprintf("$stylepath/images/rating/rat-%d.gif", 0);
//      $text = $not_rated;
//  }
//  else
//  {
//      $rating = round($topratings/$founds*100, 0);
//      $ratpic = ceil($topratings/$founds*5);
//
//      $icon = sprintf("$stylepath/images/rating/rat-%d.gif", $ratpic);
//      $text = mb_ereg_replace('{rating}', $rating, $rating_text);
//  }
//
//  return "<img src='$icon' border='0' width='19' height='16' hspace='2' alt='$text' title='$text' />";
//}
?>

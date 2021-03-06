<?php

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ăĄă˘

  new logs

 * ************************************************************************** */
global $lang, $rootpath, $usr;

if (!isset($rootpath))
    $rootpath = '';

require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
require_once($rootpath . 'lib/found_caches.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'myn_newlogs_jg';
    require($stylepath . '/newlogs.inc.php');

    $LOGS_PER_PAGE = 50;
    $PAGES_LISTED = 10;
    $rs = sql("SELECT count(id) FROM cache_logs WHERE deleted=0");
    $total_logs = mysql_result($rs, 0);
    $pages = "";
    $total_pages = ceil($total_logs / $LOGS_PER_PAGE);

    mysql_free_result($rs);

    if (!isset($_GET['start']) || intval($_GET['start']) < 0 || intval($_GET['start']) > $total_logs)
        $start = 0;
    else
        $start = intval($_GET['start']);

    $startat = max(0, floor((($start / $LOGS_PER_PAGE) + 1) / $PAGES_LISTED) * $PAGES_LISTED);

    if ($start > 0) {
        $pages .= '<a href="myn_newlogs.php?start=' . max(0, ($startat - $PAGES_LISTED - 1) * $LOGS_PER_PAGE) . '">{first_img}</a> ';
        $pages .= '<a href="myn_newlogs.php?start=' . max(0, $start - $LOGS_PER_PAGE) . '">{prev_img}</a> ';
    } else
        $pages .= "{first_img_inactive} {prev_img_inactive} ";

    for ($i = max(1, $startat); $i < $startat + $PAGES_LISTED; $i++) {
        $page_number = ($i - 1) * $LOGS_PER_PAGE;
        if ($page_number == $start)
            $pages .= "<b>$i</b> ";
        else
            $pages .= "<a href='myn_newlogs.php?start=$page_number'>$i</a> ";
    }
    if ($total_pages > $PAGES_LISTED) {
        $pages .= '<a href="myn_newlogs.php?start=' . ($start + $LOGS_PER_PAGE) . '">{next_img}</a> ';
        $pages .= '<a href="myn_newlogs.php?start=' . (($i - 1) * $LOGS_PER_PAGE) . '">{last_img}</a> ';
    } else
        $pages .= ' {next_img_inactive} {last_img_inactive}';

    //get user record
    $user_id = $usr['userid'];
    $latitude = sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
    $longitude = sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

    tpl_set_var('userid', $user_id);

    if (($longitude == NULL && $latitude == NULL) || ($longitude == 0 && $latitude == 0)) {
        tpl_set_var('info', '<br><div class="notice" style="line-height: 1.4em;font-size: 120%;"><b>' . tr("myn_info") . '</b></div><br>');
    } else {
        tpl_set_var('info', '');
    }

    if ($latitude == NULL || $latitude == 0)
        $latitude = 52.24522;
    if ($longitude == NULL || $longitude == 0)
        $longitude = 21.00442;

    $distance = sqlValue("SELECT `notify_radius` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
    if ($distance == 0)
        $distance = 35;

    $distance_unit = 'km';
    $radius = $distance;
    //get the users home coords
    $lat = $latitude;
    $lon = $longitude;
    $lon_rad = $lon * 3.14159 / 180;
    $lat_rad = $lat * 3.14159 / 180;
    //all target caches are between lat - max_lat_diff and lat + max_lat_diff
    $max_lat_diff = $distance / 111.12;
    //all target caches are between lon - max_lon_diff and lon + max_lon_diff
    //TODO: check!!!
    $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * 3.14159);

    sql('DROP TEMPORARY TABLE IF EXISTS local_caches' . $user_id . '');
    sql('CREATE TEMPORARY TABLE local_caches' . $user_id . ' ENGINE=MEMORY
        SELECT
            (' . getSqlDistanceFormula($lon, $lat, $distance, 1) . ')   AS `distance`,
            `caches`.`cache_id`                                         AS `cache_id`,
            `caches`.`wp_oc`                                            AS `wp_oc`,
            `caches`.`type`                                             AS `type`,
            `caches`.`name`                                             AS `name`,
            `caches`.`longitude`                                        AS `longitude`,
            `caches`.`latitude`                                         AS `latitude`,
            `caches`.`date_hidden`                                      AS `date_hidden`,
            `caches`.`date_created`                                     AS `date_created`,
            `caches`.`country`                                          AS `country`,
            `caches`.`difficulty`                                       AS `difficulty`,
            `caches`.`terrain`                                          AS `terrain`,
            `caches`.`status`                                           AS `status`,
            `caches`.`user_id`                                          AS `user_id`
        FROM `caches`
        WHERE `caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . $user_id . '\')
            AND caches.status<>4 AND caches.status<>5
            AND caches.status <>6
            AND `longitude` > ' . ($lon - $max_lon_diff) . '
            AND `longitude` < ' . ($lon + $max_lon_diff) . '
            AND `latitude` > ' . ($lat - $max_lat_diff) . '
            AND `latitude` < ' . ($lat + $max_lat_diff) . '
        HAVING `distance` < ' . $distance);

    sql('ALTER TABLE local_caches' . $user_id . '
            ADD PRIMARY KEY ( `cache_id` ),
            ADD INDEX(`cache_id`),
            ADD INDEX (`wp_oc`),
            ADD INDEX(`type`),
            ADD INDEX(`name`),
            ADD INDEX(`user_id`),
            ADD INDEX(`date_hidden`),
            ADD INDEX(`date_created`)');

    $rs = sql(' SELECT `cache_logs`.`id`
                FROM `cache_logs`, local_caches' . $user_id . '
                WHERE `cache_logs`.`cache_id`=local_caches' . $user_id . '.cache_id
                    AND `cache_logs`.`deleted`=0
                    AND local_caches' . $user_id . '.`status` != 4
                    AND local_caches' . $user_id . '.`status` != 5
                    AND local_caches' . $user_id . '.`status` != 6
                ORDER BY  `cache_logs`.`date_created` DESC
                LIMIT ' . intval($start) . ', ' . intval($LOGS_PER_PAGE));
    $log_ids = '';

    if (mysql_num_rows($rs) == 0)
        $log_ids = '0';

    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $record = sql_fetch_array($rs);
        if ($i > 0) {
            $log_ids .= ', ' . $record['id'];
        } else {
            $log_ids = $record['id'];
        }
    }
    mysql_free_result($rs);

    $rs = sql('SELECT   cache_logs.id, cache_logs.cache_id          AS cache_id,
                        cache_logs.type                             AS log_type,
                        cache_logs.date                             AS log_date,
                        `cache_logs`.`encrypt`                      AS `encrypt`,
                        cache_logs.user_id                          AS luser_id,
                        cache_logs.text                             AS log_text,
                        cache_logs.text_html                        AS text_html,
                        caches.name                                 AS cache_name,
                        caches.user_id                              AS cache_owner,
                        user.username                               AS user_name,
                        user.user_id                                AS user_id,
                        caches.wp_oc                                AS wp_name,
                        caches.type                                 AS cache_type,
                        cache_type.icon_small                       AS cache_icon_small,
                        log_types.icon_small                        AS icon_small,
                        IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                        COUNT(gk_item.id)                           AS geokret_in
                FROM (cache_logs
                    INNER JOIN caches               ON (caches.cache_id = cache_logs.cache_id))
                    INNER JOIN user                 ON (cache_logs.user_id = user.user_id)
                    INNER JOIN log_types            ON (cache_logs.type = log_types.id)
                    INNER JOIN cache_type           ON (caches.type = cache_type.id)
                    LEFT JOIN `cache_rating`        ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                    LEFT JOIN gk_item_waypoint      ON gk_item_waypoint.wp = caches.wp_oc
                    LEFT JOIN gk_item               ON gk_item.id = gk_item_waypoint.id AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
                WHERE cache_logs.deleted=0 AND cache_logs.id IN (' . $log_ids . ')
                GROUP BY cache_logs.id
                ORDER BY cache_logs.date_created DESC');

    $file_content = '';

    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $log_record = sql_fetch_array($rs);
        $found_icon = is_cache_found($log_record['cache_id'], $user_id) ? '16x16-found.png' : '16x16-blank.png';
        $logicon_found = 'tpl/stdstyle/images/cache/' . $found_icon;

        $file_content .= '<tr >';
        $file_content .= '<td style="width: 70px;">' . htmlspecialchars(date("Y-m-d", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';

        if ($log_record['geokret_in'] != '0') {
            $file_content .= '<td width="22">&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" /></td>';
        } else {
            $file_content .='<td width="22">&nbsp;</td>';
        }
        //$rating_picture
        if ($log_record['recommended'] == 1 && $log_record['log_type'] == 1) {
            $file_content .= '<td width="22"><img src="images/rating-star.png" border="0" alt="" title="Rekomendacja" /></td>';
        } else {
            $file_content .= '<td width="22">&nbsp;</td>';
        }
        $file_content .= '<td width="22"><img src="tpl/stdstyle/images/' . $log_record['icon_small'] . '" border="0" alt="" /></td>';
        $file_content .= '<td width="22" ><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '"><img src="tpl/stdstyle/images/' . $log_record['cache_icon_small'] . '" border="0" alt="" title="Kliknij aby zobaczyć skrzynke" /></a></td>';
        $file_content .= '<td width="22" ><img src="' . $logicon_found . '" border="0" alt="znaleziona" /></td>';
        $file_content .= '<td><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($log_record['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';

        // ukrywanie autora komentarza COG przed zwykłym userem
        // (Łza)
        if ($log_record['log_type'] == 12 && !$usr['admin']) {
            $log_record['user_id'] = '0';
            $log_record['user_name'] = 'Centrum Obsługi Geocachera ';
        }
        // koniec ukrywania autora komentarza COG przed zwykłym userem

        $file_content .= '<b>' . $log_record['user_name'] . '</b>:&nbsp;';
        if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {
            $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";
        }
        if ($log_record['encrypt'] == 1 && ($log_record['cache_owner'] == $usr['userid'] || $log_record['luser_id'] == $usr['userid'])) {
            $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";
        }
        $data = cleanup_text(str_replace("\r\n", " ", $log_record['log_text']));
        $data = str_replace("\n", " ", $data);
        if ($log_record['encrypt'] == 1 && $log_record['cache_owner'] != $usr['userid'] && $log_record['luser_id'] != $usr['userid']) {
            $data = str_rot13_html($data);
        } else {
            $file_content .= "<br/>"; //crypt the log ROT13, but keep HTML-Tags and Entities
        }
        $file_content .= $data;
        $file_content .= '\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
        $file_content .= '<td><b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($log_record['user_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
        $file_content .= "</tr>";
    }


    $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
    $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
    $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
    $pages = mb_ereg_replace('{first_img}', $first_img, $pages);
    $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
    $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
    $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
    $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

    tpl_set_var('file_content', $file_content);
    tpl_set_var('pages', $pages);
}

function cleanup_text($str)
{

    $str = strip_tags($str, "<li>");
    $from[] = '<p>&nbsp;</p>';
    $to[] = '';
    $from[] = '&nbsp;';
    $to[] = ' ';
    $from[] = '<p>';
    $to[] = '';
    $from[] = '\n';
    $to[] = '';
    $from[] = '\r';
    $to[] = '';
    $from[] = '</p>';
    $to[] = "";
    $from[] = '<br>';
    $to[] = "";
    $from[] = '<br />';
    $to[] = "";
    $from[] = '<br/>';
    $to[] = "";
    $from[] = '<li>';
    $to[] = " - ";
    $from[] = '</li>';
    $to[] = "";
    $from[] = '&oacute;';
    $to[] = 'o';
    $from[] = '&quot;';
    $to[] = '"';
    $from[] = '&[^;]*;';
    $to[] = '';
    $from[] = '&';
    $to[] = '';
    $from[] = '\'';
    $to[] = '';
    $from[] = '"';
    $to[] = '';
    $from[] = '<';
    $to[] = '';
    $from[] = '>';
    $to[] = '';
    $from[] = ']]>';
    $to[] = ']] >';
    $from[] = '';
    $to[] = '';

    for ($i = 0; $i < count($from); $i++)
        $str = str_replace($from[$i], $to[$i], $str);

    return filterevilchars($str);
}

function filterevilchars($str)
{
    return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
}

function cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? 1 : -1;
}

//make the template and send it out
tpl_BuildTemplate();
?>

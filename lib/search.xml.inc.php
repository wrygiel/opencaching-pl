<?php

    /***************************************************************************
        *
        *   This program is free software; you can redistribute it and/or modify
        *   it under the terms of the GNU General Public License as published by
        *   the Free Software Foundation; either version 2 of the License, or
        *   (at your option) any later version.
        *
        ***************************************************************************/

    /****************************************************************************

        XML search output

    ****************************************************************************/

    global $content, $bUseZip, $sqldebug, $dbcSearch;

    $lang = 'PL';
    $encoding = 'UTF-8';

    $xmlLine = "    <cache>
        <name><![CDATA[{mod_suffix}{cachename}]]></name>
        <owner><![CDATA[{owner}]]></owner>
        <id>{cacheid}</id>
        <waypoint>{waypoint}</waypoint>
        <hidden>{time}</hidden>
        <status>{status}</status>
        <lon>{lon}</lon>
        <lat>{lat}</lat>
        <distance unit=\"".$distance_unit."\">{distance}</distance>
        <type>{type}</type>
        <difficulty>{difficulty}</difficulty>
        <terrain>{terrain}</terrain>
        <size>{container}</size>
        <country>{country}</country>
        <link><![CDATA[http://www.opencaching.pl/viewcache.php?wp={waypoint}]]></link>
        <desc><![CDATA[{shortdesc}]]></desc>
        <hints><![CDATA[{hints}]]></hints>
    </cache>
";

    $txtLogs = "";

    //prepare the output
    $caches_per_page = 20;

    $sql = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad))
    {
        $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    }
    else
    {
        if ($usr === false)
        {
            $sql .= '0 distance, ';
        }
        else
        {
            //get the users home coords
            if (!isset($dbc)) {$dbc = new dataBase();};
            $dbc->multiVariableQuery("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= :1", $usr['userid'] );
            $record_coords = $dbc->dbResultFetch();

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
            {
                $sql .= '0 distance, ';
            }
            else
            {
                //TODO: load from the users-profile
                $distance_unit = 'km';

                $sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            $dbc->reset();
        }
    }

    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`,
        `caches`.`user_id` `user_id`, ';
    if ($usr === false)
    {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    }
    else
    {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
            . $usr['userid'];
    }
    $sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
    {
        $sql .= ' ORDER BY distance ASC';
    }
    else if ($sortby == 'bycreated')
    {
        $sql .= ' ORDER BY date_created DESC';
    }
    else // by name
    {
        $sql .= ' ORDER BY name ASC';
    }

    //startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (!is_numeric($startat)) $startat = 0;

    if (isset($_REQUEST['count']))
        $count = $_REQUEST['count'];
    else
        $count = $caches_per_page;

    if ($count == 'max') $count = 500;
    if (!is_numeric($count)) $count = 0;
    if ($count < 1) $count = 1;
    if ($count > 500) $count = 500;

    $sqlLimit .= ' LIMIT ' . $startat . ', ' . $count;

    // tempor?re tabelle erstellen

    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `xmlcontent` ' . $sql . $sqlLimit);
    $dbcSearch->reset();

    $dbcSearch->simpleQuery('SELECT COUNT(cache_id) `count` FROM ('.$sql.') query');
    $rCount = $dbcSearch->dbResultFetch();


    // ok, ausgabe starten

    if ($sqldebug == false)
    {
        header("Content-type: application/xml; charset=".$encoding);
        //header("Content-Disposition: attachment; filename=" . $sFilebasename . ".txt");
    }

    echo "<?xml version=\"1.0\" encoding=\"".$encoding."\"?>\n";
    echo "<result>\n";

    echo "  <docinfo>\n";
    echo "      <results>" . $rCount['count'] . "</results>\n";
    echo "      <startat>" . $startat . "</startat>\n";
    echo "      <perpage>" . $count . "</perpage>\n";
    echo "  </docinfo>\n";
    $dbcSearch->reset();
    // ok, ausgabe ...

    $dbcSearch->simpleQuery('SELECT `xmlcontent`.`cache_id` `cacheid`, `xmlcontent`.`longitude` `longitude`, `xmlcontent`.`latitude` `latitude`, `xmlcontent`.cache_mod_cords_id, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`type` `type_id`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `cache_size`.`'.$lang.'` `size`, `cache_type`.`'.$lang.'` `type`, `cache_status`.`'.$lang.'` `status`, `user`.`username` `username`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`desc_html` `html`, `xmlcontent`.`distance` `distance` FROM `xmlcontent`, `caches`, `user`, `cache_desc`, `cache_type`, `cache_status`, `cache_size` WHERE `xmlcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `xmlcontent`.`user_id`=`user`.`user_id` AND `caches`.`type`=`cache_type`.`id` AND `caches`.`status`=`cache_status`.`id` AND `caches`.`size`=`cache_size`.`id`');

    while($r = $dbcSearch->dbResultFetch())
    {
        if (@$enable_cache_access_logs)
        {
            if (!isset($dbc)) {$dbc = new dataBase();};
            $cache_id = $r['cacheid'];
            $user_id = $usr !== false ? $usr['userid'] : null;
            $access_log = @$_SESSION['CACHE_ACCESS_LOG_VC_'.$user_id];
            if ($access_log === null)
            {
                $_SESSION['CACHE_ACCESS_LOG_VC_'.$user_id] = array();
                $access_log = $_SESSION['CACHE_ACCESS_LOG_VC_'.$user_id];
            }
            if (@$access_log[$cache_id] !== true){
                $dbc->multiVariableQuery(
                    'INSERT INTO CACHE_ACCESS_LOGS
                        (event_date, cache_id, user_id, source, event, ip_addr, user_agent, forwarded_for)
                     VALUES
                        (NOW(), :1, :2, \'B\', \'download_xml\', :3, :4, :5)',
                        $cache_id, $user_id,
                        $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_X_FORWARDED_FOR']
                );
                $access_log[$cache_id] = true;
                $_SESSION['CACHE_ACCESS_LOG_VC_'.$user_id] = $access_log;
            }
        }

        $thisline = $xmlLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', help_latToDegreeStr($lat), $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', help_lonToDegreeStr($lon), $thisline);

        $time = date('d.m.Y', strtotime($r['date_hidden']));
        $thisline = str_replace('{time}', $time, $thisline);
        $thisline = str_replace('{waypoint}', $r['waypoint'], $thisline);
        $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);

        //modified coords
        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
            $thisline = str_replace('{mod_suffix}', '(F)', $thisline);
        } else {
            $thisline = str_replace('{mod_suffix}', '', $thisline);
        }


        $thisline = str_replace('{cachename}', filterevilchars($r['name']), $thisline);
        $thisline = str_replace('{country}', db_CountryFromShort($r['country']), $thisline);

        if ($r['hint'] == '')
            $thisline = str_replace('{hints}', '', $thisline);
        else
            $thisline = str_replace('{hints}', filterevilchars(strip_tags($r['hint'])), $thisline);

        $thisline = str_replace('{shortdesc}', filterevilchars($r['short_desc']), $thisline);

        if ($r['html'] == 0)
        {
            $thisline = str_replace('{htmlwarn}', '', $thisline);
            $thisline = str_replace('{desc}', filterevilchars(strip_tags($r['desc'])), $thisline);
        }
        else
        {
            $thisline = str_replace('{htmlwarn}', ' (Text p�eveden z HTML)', $thisline);
            $thisline = str_replace('{desc}', html2txt(filterevilchars($r['desc'])), $thisline);
        }

        $thisline = str_replace('{type}', $r['type'], $thisline);
        $thisline = str_replace('{container}', $r['size'], $thisline);
        $thisline = str_replace('{status}', $r['status'], $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', filterevilchars($r['username']), $thisline);
        $thisline = str_replace('{distance}', htmlspecialchars(sprintf("%01.1f", $r['distance'])), $thisline);

        // logs ermitteln
        $logentries = '';
        $thisline = lf2crlf($thisline);

        echo $thisline;
    }
    $dbcSearch->reset();
    unset($dbc);
    $dbcSearch->simpleQuery('DROP TABLE `xmlcontent` ');
    $dbcSearch->reset();
    if ($sqldebug == true) sqldbg_end();
    echo "</result>\n";

    exit;

    function html2txt($html)
    {
        $str = str_replace("\r\n", '', $html);
        $str = str_replace("\n", '', $str);
        $str = str_replace('<br />', "\n", $str);
        $str = strip_tags($str);
        return $str;
    }

    function lf2crlf($str)
    {
        return str_replace("\r\r\n" ,"\r\n" , str_replace("\n" ,"\r\n" , $str));
    }

    function filterevilchars($str)
    {
        $evilchars = array(31 => 31, 30 => 30,
                                            29 => 29, 28 => 28, 27 => 27, 26 => 26, 25 => 25, 24 => 24,
                                            23 => 23, 22 => 22, 21 => 21, 20 => 20, 19 => 19, 18 => 18,
                                            17 => 17, 16 => 16, 15 => 15, 14 => 14, 12 => 12, 11 => 11,
                                            9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3,
                                            2 => 2, 1 => 1, 0 => 0);

        foreach ($evilchars AS $ascii)
            $str = str_replace(chr($ascii), '', $str);

        $str = preg_replace('/&([a-zA-Z]{1})caron;/', '\\1', $str);
        $str = preg_replace('/&([a-zA-Z]{1})acute;/', '\\1', $str);
        $str = preg_replace('/[[:cntrl:]]/', '', $str);
        return $str;
    }
?>

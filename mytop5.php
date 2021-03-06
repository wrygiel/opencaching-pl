<?php

/* * *************************************************************************
  ./mytop5.php
  -------------------
  begin                : November 4 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  show the list of rated caches

 * ************************************************************************** */

require('./lib/common.inc.php');
require($stylepath . '/mytop5.inc.php');

if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
        die();
    }

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    tpl_set_var('msg_delete', '');

    $dbc = new dataBase();

    if ($action == 'delete') {
        $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
        if ($cache_id != 0) {

            //$count = sqlValue('SELECT COUNT(*) FROM `cache_rating` WHERE `cache_id`=\''.sql_escape($cache_id).'\' AND `user_id`=\''.sql_escape($usr['userid']).'\'', -1);
            $query = "SELECT cache_id FROM cache_rating WHERE cache_id = :cache_id AND user_id = :user_id ";
            $params = array(
                "cache_id" => array(
                    "value" => $cache_id,
                    "data_type" => "int"
                ),
                "user_id" => array(
                    "value" => $usr['userid'],
                    "data_type" => "int"
                )
            );


            $dbc->paramQuery($query, $params);

            if ($dbc->rowCount() == 0) {
                // cache is not on top list of this user => ignore
            } else {
                //sql("DELETE FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $usr['userid']);
                $query = "DELETE FROM cache_rating WHERE cache_id = :cache_id AND user_id = :user_id";
                $dbc->paramQuery($query, $params);

                // Notify OKAPI's replicate module of the change.
                // Details: https://code.google.com/p/opencaching-api/issues/detail?id=265
                require_once($rootpath . 'okapi/facade.php');
                \okapi\Facade::schedule_user_entries_check($cache_id, $usr['userid']);
                \okapi\Facade::disable_error_handling();

                //$cachename = sqlValue('SELECT `name` FROM `caches` WHERE `cache_id`=\''.sql_escape($cache_id).'\'', '-----');
                $query = "SELECT name FROM caches WHERE cache_id = :cache_id";
                $params = array(
                    "cache_id" => array(
                        "value" => $cache_id,
                        "data_type" => "int"
                ));

                $cachename = "!!!!!!!";
                $dbc->paramQuery($query, $params);
                if ($dbc->rowCount() != 0) {
                    $res = $dbc->dbResultFetch();
                    $cachename = $res["name"];
                } else
                    $cachename = "-----";

                $msg_delete = mb_ereg_replace('{cacheid}', $cache_id, $msg_delete);
                tpl_set_var('msg_delete', mb_ereg_replace('{cachename}', $cachename, $msg_delete));
                tpl_set_var('jt', $cachename);
            }
        }
        else {
            // ignore, invalid Cache-ID ... when it's boring somewhen we can give a msg
        }
    }

    $tplname = 'mytop5';

    $i = 0;
    $content = '';
    /* $rs = sql("   SELECT `cache_rating`.`cache_id` AS `cache_id`, `caches`.`name` AS `cachename`,
      `user`.`username` AS `ownername`, `user`.`user_id` AS `owner_id`
      FROM `cache_rating`, `caches` , `user`
      WHERE `cache_rating`.`cache_id` = `caches`.`cache_id`
      AND `caches`.`user_id`=`user`.`user_id`
      AND `cache_rating`.`user_id`='&1' ORDER BY `caches`.`name` ASC", $usr['userid']); */

    $query = "  SELECT `cache_rating`.`cache_id` AS `cache_id`, `caches`.`name` AS `cachename`,
                `user`.`username` AS `ownername`, `user`.`user_id` AS `owner_id`
                FROM `cache_rating`, `caches` , `user`
                WHERE `cache_rating`.`cache_id` = `caches`.`cache_id`
                AND `caches`.`user_id`=`user`.`user_id`
                  AND `cache_rating`.`user_id`= :user_id ORDER BY `caches`.`name` ASC";

    $params = array(
        "user_id" => array(
            "value" => $usr['userid'],
            "data_type" => "int"
        )
    );

    $dbc->paramQuery($query, $params);

    if ($dbc->rowCount() != 0) {
        while ($r = $dbc->dbResultFetch()) {
            $thisline = $viewtop5_line;

            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheid}', htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{ownername}', htmlspecialchars($r['ownername'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{owner_id}', htmlspecialchars($r['owner_id'], ENT_COMPAT, 'UTF-8'), $thisline);


            if (($i % 2) == 1)
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor2, $thisline);
            else
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor1, $thisline);

            $content .= $thisline;
            $i++;
        }
        //mysql_free_result($rs);
    }
    else {
        $content = $notop5;
    }

    unset($dbc);
    tpl_set_var('top5', $content);
    tpl_BuildTemplate();
}
?>

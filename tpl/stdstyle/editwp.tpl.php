<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */
?>
<script type="text/javascript">
<!--
    function _chkType()
    {
        var nextstage = document.forms['waypoints_form'].nextstage.value;
        if (document.waypoints_form.type.value == "4" || document.waypoints_form.type.value == "5")
        {
            document.waypoints_form.stage.value = "0";
            document.waypoints_form.stage.disabled = true;
        }
        else {
            document.waypoints_form.stage.value = nextstage;
            document.waypoints_form.stage.disabled = false;
        }

        // ======= opensprawdzacz checkbox start ==============================
        // this part of script display or hide section witch checkbox
        // which allow final waypoint to be used into OpenSprawdzacz
        //---------------------------------------------------------------------
        if (document.waypoints_form.type.value == "3")
        {
            document.getElementById('osprc').style.display = 'block';
        }
        else
        {
            document.getElementById('osprc').style.display = 'none';
            document.getElementById('oprawdzacz').checked = false;
        }
        // ====== opensprawdzacz checkbox stop ================================

        return false;
    }
//-->
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" />&nbsp;{{edit_wp}} {{for_cache}}: <font color="black">{cache_name}</font></div>
{general_message}
<form action="editwp.php" method="post" enctype="application/x-www-form-urlencoded" name="waypoints_form" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="wpid" value="{wpid}"/>
    <input type="hidden" name="nextstage" value="{nextstage}"/>
    <div class="searchdiv">
        <table width="90%" class="table" border="0">
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade">{{type_wp2}}:</td>
                <td>
                    <select name="type" class="input200" onChange="return _chkType()">
                        {typeoptions}
                    </select>
                </td>
            </tr>
            <tr><td>&nbsp;</td>
                <td><div class="notice" style="width:500px;min-height:24px;height:auto;"><a class="links" href="{wiki_link_additionalWaypoints}" target="_blank">{{show_info_about_wp}}</a></div></td>
            </tr>
            {start_stage}
            <tr>
                <td class="content-title-noshade">{{number_stage_wp}}:</td>
                <td>
                    <input type="text" name="stage" maxlength="2" value="{stage}" class="input30" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><div class="notice" style="width:350px;height:44px;">{{show_info_for_value}}</div>
                </td>
            </tr>
            {end_stage}
            <tr>
                <td valign="top" class="content-title-noshade">{{coordinates}}:</td>
                <td class="content-title-noshade">
                    <fieldset style="border: 1px solid black; width: 200px; height: 32%; background-color: #FAFBDF;">
                        <legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
                        <select name="latNS" class="input40">
                            <option value="N"{selLatN}>N</option>
                            <option value="S"{selLatS}>S</option>
                        </select>
                        &nbsp;<input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />
                        &deg;&nbsp;<input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input50" onkeyup="this.value = this.value.replace(/,/g, '.');" />&nbsp;'&nbsp;
                        {lat_message}<br />
                        &nbsp;&nbsp;&nbsp;
                        <select name="lonEW" class="input40">
                            <option value="E"{selLonE}>E</option>
                            <option value="W"{selLonW}>W</option>
                        </select>
                        &nbsp;<input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />
                        &deg;&nbsp;<input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input50" onkeyup="this.value = this.value.replace(/,/g, '.');" />&nbsp;'&nbsp;
                        {lon_message}
                    </fieldset>

                    <!-- === opensprawdzacz section checkbox start ================================== -->
                    <div name="osprc" id="osprc" style="display: {opensprawdzacz_display};">
                        <br />
                        <input type="checkbox"  id="oprawdzacz" name="oprawdzacz" {opensprawdzacz_checked}/> {{Open_Sprawdzacz}}<br/>
                        <div class="notice" style="width:350px;height:80px;">{{os_tak_chce}} </div>
                    </div>
                    <!-- === opensprawdzacz section checkbox stop ==================================== -->




                </td>
            </tr>
            <tr><td colspan="2"><div class="buffer"></div></td></tr>
            <tr>
                <td valign="top" class="content-title-noshade">{{describe_wp}}:</td>
                <td class="content-title-noshade">
                    <textarea name="desc" rows="10" cols="60">{desc}</textarea>{desc_message}</td>
                </td>
            </tr>
            <tr>
                <td valign="top" class="content-title-noshade">{{status_wp}}:</td>
            </tr>
            <tr>
                <td vAlign="top" align="left" colSpan="2">
                    <table border="0" style="width:600px;font-size: 12px; line-height: 1.6em;">
                        <tr><td><input type="radio" name="status" value="1" {checked1} /><label for="status" style="font-size: 12px; line-height: 1.6em;">{{wp_status1}}</label>
                            </td></tr>
                        <tr><td>
                                <input type="radio" name="status" value="2" {checked2} /><label for="status" style="font-size: 12px; line-height: 1.6em;">{{wp_status2}}</label>
                            </td></tr>
                        <tr><td>
                                <input  type="radio" name="status" value="3" {checked3} /><label for="status" style="font-size: 12px; line-height: 1.6em;">{{wp_status3}}</label>
                            </td></tr></td>
        </table>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td vAlign="top" align="left" colSpan="2">
                <button type="submit" name="back" value="back" style="font-size:12px;width:140px"><b>{{cancel}}</b></button>&nbsp;&nbsp;
                <button type="submit" name="delete" value="delete" onclick="return confirm('Czy usunąć ten waypoint?');" style="font-size:12px;width:140px"><b>{{delete_wp}}</b></button> &nbsp;&nbsp;
                <button type="submit" name="submit" value="submit" style="font-size:12px;width:140px"><b>{{edit_wp}}</b></button>
                <br /><br /></td>
        </tr>

        </table>
</form>
</div>

<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
	***************************************************************************/
?>
<script language="javascript" type="text/javascript">

function sync_options(element)
{

	var recommendations = 0;
	if (document.optionsform.cache_rec[0].checked == true) {
		document.optionsform.cache_min_rec.disabled = 'disabled';
		recommendations = 0;
	}
	else if (document.optionsform.cache_rec[1].checked == true) {
		document.optionsform.cache_min_rec.disabled = false;
		recommendations = document.optionsform.cache_min_rec.value;
	}
	
		document.forms['optionsform'].f_inactive.value = document.optionsform.f_inactive.checked ? 1 : 0;
		document.forms['optionsform'].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
		document.forms['optionsform'].f_userfound.value = document.optionsform.f_userfound.checked ? 1 : 0;
		document.forms['optionsform'].f_userowner.value = document.optionsform.f_userowner.checked ? 1 : 0;
		
		document.forms['optionsform'].cachetype1.value = document.optionsform.cachetype1.checked ? 1 : 0;
		document.forms['optionsform'].cachetype2.value = document.optionsform.cachetype2.checked ? 1 : 0;
		document.forms['optionsform'].cachetype3.value = document.optionsform.cachetype3.checked ? 1 : 0;
		document.forms['optionsform'].cachetype4.value = document.optionsform.cachetype4.checked ? 1 : 0;
		document.forms['optionsform'].cachetype5.value = document.optionsform.cachetype5.checked ? 1 : 0;
		document.forms['optionsform'].cachetype6.value = document.optionsform.cachetype6.checked ? 1 : 0;
		document.forms['optionsform'].cachetype7.value = document.optionsform.cachetype7.checked ? 1 : 0;
		document.forms['optionsform'].cachetype8.value = document.optionsform.cachetype8.checked ? 1 : 0;
		document.forms['optionsform'].cachetype9.value = document.optionsform.cachetype9.checked ? 1 : 0;
		document.forms['optionsform'].cachetype10.value = document.optionsform.cachetype10.checked ? 1 : 0;
		
		document.forms['optionsform'].cachesize_2.value = document.optionsform.cachesize_2.checked ? 1 : 0;
		document.forms['optionsform'].cachesize_3.value = document.optionsform.cachesize_3.checked ? 1 : 0;
		document.forms['optionsform'].cachesize_4.value = document.optionsform.cachesize_4.checked ? 1 : 0;
		document.forms['optionsform'].cachesize_5.value = document.optionsform.cachesize_5.checked ? 1 : 0;
		document.forms['optionsform'].cachesize_6.value = document.optionsform.cachesize_6.checked ? 1 : 0;
		document.forms['optionsform'].cachesize_7.value = document.optionsform.cachesize_7.checked ? 1 : 0;
		document.forms['optionsform'].cachevote_1.value = document.optionsform.cachevote_1.value;
		document.forms['optionsform'].cachevote_2.value = document.optionsform.cachevote_2.value;
		document.forms['optionsform'].cachenovote.value = document.optionsform.cachenovote.checked ? 1 : 0;
		document.forms['optionsform'].cachedifficulty_1.value = document.optionsform.cachedifficulty_1.value;
		document.forms['optionsform'].cachedifficulty_2.value = document.optionsform.cachedifficulty_2.value;
		document.forms['optionsform'].cacheterrain_1.value = document.optionsform.cacheterrain_1.value;
		document.forms['optionsform'].cacheterrain_2.value = document.optionsform.cacheterrain_2.value;
		document.forms['optionsform'].cacherating.value = recommendations;

}
//-->
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{search_caches_along_route}}: {route_name}</div>
<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="optionsform" dir="ltr">
<input type="hidden" name="routeid" value="{routeid}"/>
<input type="hidden" name="distance" value="{distance}"/>
<div class="searchdiv">

<p class="content-title-noshade-size3">{{search_options}}</p>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td class="content-title-noshade">{{omit_caches}}:</td>
			<td class="content-title-noshade" colspan="2">
				<input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)" {f_inactive_checked} /> <label for="l_inactive">{{not_active}}</label>
				<input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)" {f_ignored_disabled} /> <label for="l_ignored">{{ignored}}</label>
				<input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)" {f_userfound_disabled} /> <label for="l_userfound">{{founds}}</label>&nbsp;&nbsp;
				<input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)" {f_userowner_disabled} /> <label for="l_userowner">{{of_owner}}</label>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">{{cache_type}}:</td>
			<td>

				<table class="table">
					<tr>
						<td><input type="checkbox" id="cachetype2" name="cachetype2" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype2">Traditional Cache</label></td>
						<td><input type="checkbox" id="cachetype3" name="cachetype3" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype3">Multi cache</label></td>
						<td><input type="checkbox" id="cachetype5" name="cachetype5" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype5">Webcam Cache</label></td>
						<td><input type="checkbox" id="cachetype6" name="cachetype6" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype6">Wydarzenie</label></td>

					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype7" name="cachetype7" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype7">Quiz</label></td>
						<td><input type="checkbox" id="cachetype8" name="cachetype8" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype8">Mobilna</label></td>
						<td><input type="checkbox" id="cachetype9" name="cachetype9" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype9">PodCast</label></td>
						<td><input type="checkbox" id="cachetype10" name="cachetype10" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype10">Own Cache</label></td>

					</tr>
					<tr>
						<td><input type="checkbox" id="cachetype4" name="cachetype4" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype4">Wirtualna</label></td>
						<td><input type="checkbox" id="cachetype1" name="cachetype1" value="1" onclick="javascript:sync_options(this)" class="checkbox"  checked="checked" /> <label for="cachetype1">Nietypowa</label></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>

				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top">{{cache_size}}:</td>

			<td>
				<table class="table">
					<tr>
						<td>				
<input type="checkbox" name="cachesize_2" value="1" id="l_cachesize_2" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_2} /><label for="l_cachesize_2">Mikro</label>
<input type="checkbox" name="cachesize_3" value="1" id="l_cachesize_3" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_3} /><label for="l_cachesize_3">Mała</label>
<input type="checkbox" name="cachesize_4" value="1" id="l_cachesize_4" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_4} /><label for="l_cachesize_4">Normalna</label>
<input type="checkbox" name="cachesize_5" value="1" id="l_cachesize_5" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_5} /><label for="l_cachesize_5">Duża</label>
<input type="checkbox" name="cachesize_6" value="1" id="l_cachesize_6" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_6} /><label for="l_cachesize_6">Bardzo duża</label>
<input type="checkbox" name="cachesize_7" value="1" id="l_cachesize_7" class="checkbox" onclick="javascript:sync_options(this)" {cachesize_7} /><label for="l_cachesize_7">Bez pojemnika</label>
</td>

						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="searchdiv">
	<table class="table">
		<tr>
			<td valign="top" class="content-title-noshade">{{task_difficulty}}:</td>
			<td class="content-title-noshade">
				{{from}} <select name="cachedifficulty_1" class="input40" onchange="javascript:sync_options(this)">
					<option value="1" selected="selected">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5">5</option>
				</select>
				{{to}} <select name="cachedifficulty_2" class="input40" onchange="javascript:sync_options(this)">
					<option value="1">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5" selected="selected">5</option>
				</select>
			</td>
		</tr>
		<tr><td class="buffer" colspan="3"></td></tr>
		<tr>
			<td valign="top" class="content-title-noshade">{{terrain_difficulty}}:</td>
			<td class="content-title-noshade">
				{{from}} <select name="cacheterrain_1" class="input40" onchange="javascript:sync_options(this)">
					<option value="1" selected="selected">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5">5</option>
				</select>
				{{to}} <select name="cacheterrain_2" class="input40" onchange="javascript:sync_options(this)">
					<option value="1">1</option>
					<option value="1.5">1.5</option>
					<option value="2">2</option>
					<option value="2.5">2.5</option>
					<option value="3">3</option>
					<option value="3.5">3.5</option>
					<option value="4">4</option>
					<option value="4.5">4.5</option>
					<option value="5" selected="selected">5</option>
				</select>
			</td>
		</tr>	
	</table>
</div>

<div class="searchdiv">
	<table class="table">
			<tr>
			<td valign="top" class="content-title-noshade">{{score}}:</td>
			<td class="content-title-noshade">
				{{from}} <select name="cachevote_1" onchange="javascript:sync_options(this)">
	                <option value="-3">{{rating_poor}}</option>
	                <option value="0.5">{{rating_mediocre}}</option>
	                <option value="1.2">{{rating_avarage}}</option>
	                <option value="2">{{rating_good}}</option>
	                <option value="2.5">{{rating_excellent}}</option>
				</select>
				{{to}} <select name="cachevote_2" onchange="javascript:sync_options(this)">
	                <option value="0.499">{{rating_poor}}</option>
	                <option value="1.199">{{rating_mediocre}}</option>
	                <option value="1.999">{{rating_avarage}}</option>
	                <option value="2.499">{{rating_good}}</option>
	                <option value="3.000" selected="selected">{{rating_excellent}}</option>
				</select>
				<input type="checkbox" name="cachenovote" value="1" id="l_cachenovote" class="checkbox" onclick="javascript:sync_options(this)" checked="checked"/><label for="l_cachenovote">{{with_hidden_score}}</label>
			</td>
		</tr>
				<tr><td class="buffer" colspan="3"></td></tr>
		<tr>
			<td class="content-title-noshade">{{search_recommendations}}:</td>

			<td class="content-title-noshade" colspan="2">
				<input type="radio" name="cache_rec" value="0" tabindex="0" id="l_all_caches" class="radio" onclick="javascript:sync_options(this)" {all_caches_checked} /> <label for="l_all_caches">{{search_all_caches}}</label>&nbsp;
				<input type="radio" name="cache_rec" value="1" tabindex="1" id="l_recommended_caches" class="radio" onclick="javascript:sync_options(this)" {recommended_caches_checked} /> <label for="l_recommended_caches">{{search_recommended_caches}}</label>&nbsp;
				<input type="text" name="cache_min_rec" value="{cache_min_rec}" maxlength="3" class="input50" onchange="javascript:sync_options(this)" {min_rec_caches_disabled} />
			</td>
		</tr>

	</table>
</div>








</div>
<br/>
			<button type="submit" name="back_list" value="back_list" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
			<button type="submit" name="submit" value="submit" style="font-size:12px;width:160px"><b>{{search}}</b></button>
</form>
			<br/><br/><br/>




<script type="text/javascript">
function initialize() {

if (GBrowserIsCompatible()) {

var icon_yellow = new GIcon();
 icon.image = "tpl/stdstyle/images/google_maps/yellow.png";
 icon.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon.iconSize = new GSize(12, 20);
 icon.shadowSize = new GSize(22, 20);
 icon.iconAnchor = new GPoint(6, 20);
 icon.infoWindowAnchor = new GPoint(5, 1);

var icon_red = new GIcon();
 icon2.image = "tpl/stdstyle/images/google_maps/red.png";
 icon2.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon2.iconSize = new GSize(12, 20);
 icon2.shadowSize = new GSize(22, 20);
 icon2.iconAnchor = new GPoint(6, 20);
 icon2.infoWindowAnchor = new GPoint(5, 1);

var icon_green = new GIcon();
 icon3.image = "tpl/stdstyle/images/google_maps/green.png";
 icon3.shadow = "tpl/stdstyle/images/google_maps/shadow.png";
 icon3.iconSize = new GSize(12, 20);
 icon3.shadowSize = new GSize(22, 20);
 icon3.iconAnchor = new GPoint(6, 20);
 icon3.infoWindowAnchor = new GPoint(5, 1);

 
var map = new GMap2(document.getElementById("map"));
map.addControl(new GSmallMapControl());
map.addControl(new GMapTypeControl());

map.setCenter(new GLatLng({mapcenterLat},{mapcenterLon}), 6);
{route}
{points}

      }
    }


</script>
</head>
<div class="content2-pagetitle">&nbsp;<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="" title=""/>&nbsp;&nbsp;{{route_cache}} <font color="black">{cachename}</font></div>
<div class="content2-container">
<br/><p class="content-title-noshade-size1">
<img src="tpl/stdstyle/images/google_maps/red.png" alt="a" width="12" height="20" title="begin" /> = {{start_point}}
<img src="tpl/stdstyle/images/google_maps/yellow.png" alt="b" width="12" height="20" title="point" /> = {{trp_points}}
<img src="tpl/stdstyle/images/google_maps/green.png" alt="c" width="12" height="20" title="end"/> = {{recently_seen}}
</p><bre/><br/>
<div id="map" style="width: 780px; height: 500px"></div>
</div>

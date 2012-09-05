<?php
require('php/class.php');

$eq = new earthquake();
$eq->get_earthquakes();
?><html>
    <head>
        <title>Quake</title>

        <link rel="stylesheet" href="css/style.css" type="text/css" media="all" /> 
        <script src="http://www.google.com/jsapi?key=AIzaSyCNGZY4tvgHNRtraZKJYDPq95s-VsoxYNs"></script>
        <script src="http://earth-api-samples.googlecode.com/svn/trunk/lib/ge-poly-fit-hack.js"></script>
        <script src="http://earth-api-utility-library.googlecode.com/svn/trunk/extensions/dist/extensions.pack.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script> 
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="http://timeago.yarp.com/jquery.timeago.js"></script>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" type="text/css" media="all" /> 

        <script>
            google.load("maps", "3", {other_params:"sensor=false"});
            
            function add_map_earthquake(eq,map){
                //   console.log(eq);
                var i=eq.index;
                var infowindow = new google.maps.InfoWindow();
                $('#list').prepend(eq.html);
                var myLatlng = new google.maps.LatLng(eq.lat, eq.lon);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    title:eq.magnitude+' '+eq.region
                });
                marker.setMap(map);
              
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        $($('.infowindow').parent().parent().parent().parent()).remove;
                        infowindow.setContent('<div class="infowindow">'+eq.magnitude+': '+eq.region+'<br/>'+eq.balloon+'</div>');
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            function doMyThing(ct){
                $('#status').html('Done loading '+ct.loaded+' earthquakes at '+ct.datetime);
            }
            function load(){
                $('#status').html('Checking for new earthquakes');
                var loaded=0;
                $.getJSON('/json',function(data){
                    var count=$(data.feed.earthquakes).length;
                    $(data.feed.earthquakes).each(function(idx,eq){
                        loaded++;
                        if($('#quake'+eq.eqid).length==0){
                            eq.index=idx;
                            add_map_earthquake(eq,map);
                            $('#status').delay(500).html('Loading '+eq.eqid);
                            if (!--count){
                                data.feed.loaded=loaded;
                                doMyThing(data.feed);
                            }
                        }
                    });
                   
                   
                });
            }
            $(document).ready(function(){
                var myOptions = {
                    zoom: 3,
                    center: new google.maps.LatLng(<?php echo $eq->largest['lat']; ?>, <?php echo $eq->largest['lon']; ?>),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('map2d'),
                myOptions);
                $('button').button();
                $( "#toolbar" ).buttonset(); 
                $('#list').css('height',$('#list_container').height()).show();
               load();
                var int=self.setInterval("load()",30000);
                
            });
            

          
            //            function init() {
            //               initCallback();
            //            }
            //            function failureCallback(){
            //                alert("we can't seem to be able to load Google Earth. let's try Google Maps...");
            //            }
           
        </script>
        <?php
        #  require('php/mapscript.php');
        ?>
    </head>
    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-30865277-1']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

    </script>
    <body>


        <!--        <div  class='guide round' style='border:2px solid black;height:auto;position:absolute;top:0px;left:0px;width:25%;'>
                    <h3>Legend / Help</h3>
                    <div class='legend'>
                        Earthquake Guide:<br/>
        <?php
        foreach ($eq->colors as$mag => $color) {
            ?>
                                                                <div class='set_slider' data-min='<?php echo $mag; ?>' >
                                                                    <span style='display:inline-block;width:200px;'><?php echo $mag; ?>.0 - <?php echo $mag; ?>.9 : <?php echo $color; ?> </span>
                                                                    <span style='display:inline-block;width:12px;height:12px;background-color:<?php echo $color; ?>;'>&nbsp;</span>
                                                                </div>
        <?php } ?>
                        <br/>
                        Note: Clicking on a earthquake in the left-hand list will automatically navigate the earth to the Earthquake location and list more details about this Earthquake.
                        All data is for earthquakes since <?php echo date("Y-m-d h:i:s", strtotime($eq->data_period)); ?>
                        <br/><br/>Website by: <a href="http://www.fatherstorm.com" target='_fatherstorm'>FatherStorm</a>
                    </div>
                </div>-->

        <div id="page">
            <table 
                <tr style='height:40px;'>
                    <td colspan="2" style='height:40px;'>
                        <div id="toolbar" class="ui-widget-header ui-corner-all">
                            <button id="beginning">The Earthquake Project </button>
                            <button id="rewind">Use Google Maps</button>
                            <button id="play">About This Website</button>
                            <span id="status"></span>
                            <button id="rewind" style="float:right !important">Preferences</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td id="list_container">
                        <div id="list">
                            <!--                            <div id="sliderVal" style="color:yellow;width:1.5em;float:left;">10</div>
                                                        <div  style="float:left;" id="slider"></div>
                                                        <span style="float:right;font-weight:bold;background:#fff;padding:3px;cursor:pointer;" class="tour">Take Tour</span>
                                                        <span style="float:right;font-weight:bold;background:#fff;padding:3px;cursor:pointer;display:none;" class="notour">Stop Tour</span>
                            -->

                        </div> 
                    </td>
                    <td id='map3d' ></td>
                </tr>
            </table>
        </div>


    </body>
</html>

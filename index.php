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
        <script>
            var base_zoom=5;
            var sloc_obj = new Object();
            sloc_obj.lat1=<?php echo $eq->largest['lat']; ?>;
            sloc_obj.lat2=<?php echo $eq->largest['lat']; ?>;
            sloc_obj.lon1=<?php echo $eq->largest['lon']; ?>;
            sloc_obj.lon2=<?php echo $eq->largest['lon']; ?>;
            var latest_lat=<?php echo $eq->latest['lat']; ?>;
            var latest_lon=<?php echo $eq->latest['lon']; ?>;
        </script>
        <script src="/js/code.js"></script>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" type="text/css" media="all" /> 


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
                            <a href='http://m.earthquakeproject.com' id="use_mobile" >Mobile</a>
                            <a href='?maps' id="use_maps" >Use Google Maps</a>
                            <a href='?earth' id="use_earth"  >Use Google Earth</a>
                            <button class="backward" id="backward"><</button>
                            <button class="forward" id="forward">></button>
                            <button class="notour" id="notour" style="display:none;">Stop</button>
                            <!--<button id="play">About This Website</button>-->
                            <span id="status" style="overflow:hidden;"></span>
                            <button id="show_preferences" style="float:right !important;">Preferences</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td id="list_container">
                        <div id="list">


                        </div> 
                    </td>
                    <td id="maps">
                        <div id='map3d' ></div>
                        </div>


                    </td>
                </tr>
            </table>
        </div>

        <div id="off_page" style="display:block;width:1px;height:1px;position:absolute;right:0px;bottom:0px;">

        </div>
        <div id="preferences" style="display:none;">
            <div class='legend'>
                Earthquake Guide:<br/>
                <?php
                foreach ($eq->colors as $mag => $color) {
                    ?>
                    <div class='set_slider' data-min='<?php echo $mag; ?>' >
                        <span style='display:inline-block;width:200px;'><?php echo $mag; ?>.0 - <?php echo $mag; ?>.9 : <?php echo $color; ?> </span>
                        <span style='display:inline-block;width:12px;height:12px;background-color:<?php echo $color; ?>;'>&nbsp;</span>
                    </div>
<?php } ?>
                <br/>
                <div id="sliderVal" style="color:yellow;width:1.5em;float:left;">10</div>
                <div  style="float:left;" id="slider"></div>
                <br/>
                Note: Clicking on a earthquake in the left-hand list will automatically navigate the earth to the Earthquake location and list more details about this Earthquake.
                All data is for earthquakes since <?php echo date("Y-m-d h:i:s", strtotime($eq->data_period)); ?>
                <br/><br/>Website by: <a href="http://www.fatherstorm.com" target='_fatherstorm'>FatherStorm</a>
            </div>


        </div>
        <div id="earth_unavailable" style="display:none">
            <div >Sorry, your browser/device does not seem to support Google Earth. We are redirecting you to the Google maps version of this page.</div>

        </div>
    </body>
</html>

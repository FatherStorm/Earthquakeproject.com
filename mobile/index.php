<?php
require('../php/class.php');

$eq = new earthquake();
$eq->get_earthquakes();
?><html>
    <head>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Quake</title>
         <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" type="text/css" media="all" /> 

        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />



        <style>
            /* App custom styles */
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js">
        </script>

        <script src="http://www.google.com/jsapi?key=AIzaSyCNGZY4tvgHNRtraZKJYDPq95s-VsoxYNs"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script> 
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" type="text/javascript"></script>
        <script src="http://timeago.yarp.com/jquery.timeago.js"></script>
        
        <style>
            .text-align-center {
                text-align: center;
            }
            .text-align-right {
                text-align: right;
            }

            html, body,#page, #content{
                margin:0px;
                padding:0px;
                height:100%;
            }
            ul{
                margin:0px;
            }
            #map3d{
                width:50%;
                height:100%;
            }
            #list{
                overflow:auto;
                margin-top:0px;

            }
            abbr{
                float:right;
            }
            .detail img{
                margin-right:15px;
            }
        </style>
        <script>
            var base_zoom=5;
            var sloc_obj = new Object();
            sloc_obj.lat1=<?php echo $eq->largest['lat']; ?>;
            sloc_obj.lat2=<?php echo $eq->largest['lat']; ?>;
            sloc_obj.lon1=<?php echo $eq->largest['lon']; ?>;
            sloc_obj.lon2=<?php echo $eq->largest['lon']; ?>;
            (function($) {
                $.cookie = function(key, value, options) {

                    // key and at least value given, set cookie...
                    if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
                        options = $.extend({}, options);

                        if (value === null || value === undefined) {
                            options.expires = -1;
                        }

                        if (typeof options.expires === 'number') {
                            var days = options.expires, t = options.expires = new Date();
                            t.setDate(t.getDate() + days);
                        }

                        value = String(value);

                        return (document.cookie = [
                            encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                            options.path    ? '; path=' + options.path : '',
                            options.domain  ? '; domain=' + options.domain : '',
                            options.secure  ? '; secure' : ''
                        ].join(''));
                    }

                    // key and possibly options given, get cookie...
                    options = value || {};
                    var decode = options.raw ? function(s) { return s; } : decodeURIComponent;

                    var pairs = document.cookie.split('; ');
                    for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
                        if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
                    }
                    return null;
                };
            })(jQuery);
    
            var page_type='maps';
           
            var offset=0;
            var max=100;
           
         
            google.load("maps", "3", {other_params:"sensor=false"});
           
          

           
           
          
            function add_map_earthquake(eq){
                $('#list').prepend(eq.mobile);  
                if($('#list li:first').attr('rel')< $.cookie('chart_min')){
                    $('#list li:first').hide();
                }
                var i=eq.index;
                var infowindow = new google.maps.InfoWindow();
                
                var myLatlng = new google.maps.LatLng(eq.lat, eq.lon);
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    title:eq.magnitude+' '+eq.region
                });
                marker.setMap(map);
              
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        //   $($('.infowindow').parent().parent().parent().parent().parent().parent().parent()).remove;
                        infowindow.setContent('<div class="infowindow">'+eq.magnitude+': '+eq.region+'<br/>'+eq.balloon+'</div>');
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            //            
           
            function doMyThing(ct){
                $('#status').html('Done loading '+ct.loaded+' earthquakes ');
                if($($('#list li').is(':visible')).length==0 && $('#earth_unavailable').is(':visible').length==0 ){
                    //var div=$('<div>You appear to have your earthquake sensitivity set a bit high ['+$.cookie('chart_min')+'] You can lower it below</div>');
                    $('#preferences').dialog({modal:true,width:500,height:500});
                }
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
                           
                            add_map_earthquake(eq);
                           
           
                            $($("abbr.timeago").not('timeagoloaded')).addClass('timeagoloaded').timeago();
                           
                            $('#status').html('Loading '+eq.eqid);
                            if (!--count){
                                data.feed.loaded=loaded;
                                doMyThing(data.feed);
                            }
                        }
                    });
                });
            }
             
            
            $(document).ready(function(){
            
            
        
               
                   
                map = new google.maps.Map(document.getElementById('map3d'),  
                {
                    zoom: 7,
                    center: new google.maps.LatLng(<?php echo $eq->latest['lat']; ?>, <?php echo $eq->latest['lon']; ?>),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                    
               
                load();
                //                $('button').button();
                //                $( "#toolbar" ).buttonset(); 
                //                $('#list').css('height',$('#list_container').height()).show();
                var int=self.setInterval("load()",30000);
                var max=$('.quake').length;
                var obj=$('.quake');
                //                                $( "#forward" ).button({
                //                                   
                //                                });
                //                                $( "#backward" ).button({
                //                                   
          
            //                                $('.notour').bind('click',function(){
            //                                    window.clearInterval($(this).data('timeoutID'));
            //                                    $('#status').html('Tour Ended');
            //                               
            //                                    $('.tour,.notour').toggle();
            //                                });
            $('#forward').bind('click',function(){
                var obj=$('.quake:visible');
                if(offset==$(obj).length){
                    offset=0;
                }
                offset++;
                $(obj[offset]).trigger('click');
            });
            $('#backward').bind('click',function(){
                var obj=$('.quake:visible');
                if(offset==0){
                    offset=$(obj).length;
                }
                offset--;
                $(obj[offset]).trigger('click');
            });
            //                                $('.tour').bind('click',function(){
            //                                    $('#status').html('Tour Started');
            //                                    var obj=$('.quake');
            //                                    $('.tour,.notour').toggle();
            //                                    $(obj[offset]).trigger('click');
            //                                    $('.notour').data('timeoutID', window.setInterval(function(){
            //                                        offset++;
            //                                        $(obj[offset]).trigger('click');
            //                                        if(offset>max){
            //                                            offset=0;
            //                                        }
            //                                    }, 5000)
            //                                );
                           
            $($("abbr.timeago").not('timeagoloaded')).addClass('timeagoloaded').timeago();
            //
            $('.quake').live('click',function(){
                   
                $('.detail').hide();
                  
                $($(this).find('.detail')).show();
                $('#list').scrollTop(0).scrollTop(($(this).position().top)-65);
                    
                 
                    
                var l1=$(this).data('lat');
                var l2=$(this).data('lon');
                map.panTo(new google.maps.LatLng(l1,l2),8);
                       
                        
                 
            });
            $('#prefs_hide').live('click',function(){
                $('#preferences_dialog').slideUp();
            })
            $('#prefs').live('click',function(){
                if($('#preferences_dialog').is(':visible')){
                    $('#preferences_dialog').slideUp();
                }else{
                    $('#preferences_dialog').slideDown();
                }
            });
            var slideVal= $.cookie('chart_min')
            $('.set_slider').click(function(){
                $('#slider').slider('option','value',$(this).data('min')); 
            });
            $( "#slider" ).slider({min:2.5,max:10,value:slideVal,step:.1});
            $( "#slider" ).slider({
                change: function(event, ui) {
                    $('#sliderVal').html($(this).slider("value"));
                    var max=$(this).slider("value");

                    $.cookie('chart_min', max, { expires: 365 });

                    $('.filterable').each(function(){
                        if($(this).attr('rel')>=max){
                            $(this).show();
                        }else{
                            $(this).hide();
                        }
                    })

                }
            });
            $('#slider').slider('option','value',slideVal);
            var supportsOrientationChange = "onorientationchange" in window,
            orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

            window.addEventListener(orientationEvent, function() {
           
                var poss_height=($(window).height()-$('#navbar').height())*.95;
                var poss_width=$('#content').width()*.97;
                $('#content').height(poss_height);
                if(poss_height>poss_width){
                    $('#map3d,#list').width(poss_width).height(poss_height/2).css('display','block').css('float','left');
                    
                }else{
                    $('#map3d,#list').width(poss_width/2).height(poss_height).css('display','inline-block').css('float','left');
                }
                
                 
            });
               
            var poss_height=($(window).height()-$('#navbar').height())*.95;
            var poss_width=$('#content').width()*.97;
                
            if(poss_height>poss_width){
                $('#map3d,#list').width(poss_width).height(poss_height/2).css('display','block').css('float','left');
                    
            }else{
                $('#map3d,#list').width(poss_width/2).height(poss_height).css('display','inline-block').css('float','left');
            }
        });
            
           
            

         
        </script>

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

        <div data-role="page" id="page" data-theme="a">

            <div data-role="content" id="content">
                <div id="preferences_dialog" style="display:none;">
<div data-role="header"> 
	<h1>Preferences</h1> 
</div> 
                    <fieldset data-role="controlgroup">
                        <label for="slider">
                            Minimum Magnitude to show
                        </label>
                        <div id="sliderVal" style="width:1.5em;float:left;">10</div>
                        <div  style="float:left;" id="slider" data-theme="a"></div>
                        <br/>
                        <br/>
                    </fieldset>
                    <ul class='legend' data-role="listview"  >

                        <?php
                        foreach ($eq->colors as $mag => $color)
                        {
                            ?>
                            <li class='set_slider' data-min='<?php echo $mag; ?>' >
                                <span style='display:inline-block;width:200px;'><?php echo $mag; ?>.0 - <?php echo $mag; ?>.9 : <?php echo $color; ?> </span>
                                <span style='display:inline-block;width:12px;height:12px;background-color:<?php echo $color; ?>;'>&nbsp;</span>
                            </li>
                        <?php } ?>
                    </ul>
                    <br/>
                    Note: Clicking on a earthquake in the left-hand list will automatically navigate the earth to the Earthquake location and list more details about this Earthquake.
                    All data is for earthquakes since <?php echo date("Y-m-d h:i:s", strtotime($eq->data_period)); ?>
                    <br/><br/>Website by: <a href="http://www.fatherstorm.com" target='_fatherstorm'>FatherStorm</a><br/>

                    <a href="#index" data-role="button" data-inline="true"  id="prefs_hide" data-theme="a">Done</a>


                    <br style="clear:both;"/>
                </div>
                <div data-role="navbar" data-iconpos="top" id="navbar"  data-theme="a">
                    <ul>
                        <li>
                            <a href="http://www.earthquakeproject.com?force=desktop" data-theme="a" data-icon="home">
                                Desktop Version
                            </a>
                        </li>
                        <li  id="backward"><a href="#" data-theme="a"  data-icon="arrow-l">&nbsp;</a>

                        </li>
                        <li   id="forward">
                            <a href="#" data-theme="a" data-icon="arrow-r">&nbsp;</a>
                        </li>
                        <li>
                            <a href="#prefs" id="prefs" data-rel="dialog" data-transition="slidedown" data-theme="a" data-icon="gear">
                                Preferences
                            </a>
                        </li>
                    </ul>
                </div>
                <ul data-role="listview"  data-inset="true"  id="list"  data-theme="a"></ul>
                <div id="map3d" style="color:#000;" ></div>

            </div>
        </div>


    </body>
</html>

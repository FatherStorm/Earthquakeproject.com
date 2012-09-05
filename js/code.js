
         
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
    
            var page_type=$.cookie('type');
            if(page_type==''){
                page_type='earth';
            }
            var ge=false;
            var map=false;
            var offset=0;
            var max=100;
            if(page_type=='earth'){
                google.load("earth", "1");
            }else{
                google.load("maps", "3", {other_params:"sensor=false"});
            }
           
          

            function init_earth() {
                google.earth.createInstance('map3d', initCB, failureCB);
                

               
            }
            function successCallback(pluginInstance) {
                ge = pluginInstance;
                ge.getWindow().setVisibility(true);

                var gex = new GEarthExtensions(ge);
                gex.dom.addPointPlacemark(gex.util.getLookAt(), { name: 'Hello World!' });
            }
            function initCB(instance) {
                ge = instance;
                ge.getWindow().setVisibility(true);
                load();
            }

            function failureCB(errorCode) {
                $('#map3d').empty();
                $( "#earth_unavailable" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                            document.location='?maps'
                        }
                    }
                });
            
            
                   
            }

           
          
            function add_map_earthquake(eq){
                $('#list').prepend(eq.html);  
                if($('#list div:first').attr('rel')< $.cookie('chart_min')){
                    $('#list div:first').hide();
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
            function add_earth_earthquake(eq){
                loop=0;
                while(ge==false){
                    loop++
                    if(loop==1000){
                        return
                    }
                }
                $('#list').prepend(eq.html);  
            
                var i=eq.index;
                var textPlacemark=eq.magnitude + " - "+eq.region;
                var placemark = ge.createPlacemark('');
                
                 
                 
                placemark.setName(textPlacemark);
                ge.getFeatures().appendChild(placemark);
                
                
                var icon = ge.createIcon('');
                icon.setHref('http://maps.google.com/mapfiles/kml/paddle/red-circle.png');
                var style = ge.createStyle('');
                style.getIconStyle().setIcon(icon);
                placemark.setStyleSelector(style);
                 
                var point = ge.createPoint('');
                var lat=Number(eq.lat)
                var lon=Number(eq.lon)
                point.setLatitude(lat);
                point.setLongitude(lon);
                placemark.setGeometry(point);
                
                
               
                var radius=Math.pow(1.25, eq.magnitude);
                var ring = ge.createLinearRing('');
                var steps = 24;
                var pi2 = Math.PI * 2;
                for (var i = 0; i < steps; i++) {
                    var rlat = lat +  radius * Math.cos(i / steps * pi2);
                    var rlng = lon + radius * Math.sin(i / steps * pi2);
                    ring.getCoordinates().pushLatLngAlt(rlat, rlng, 0);
                }
                var polygonPlacemark = ge.createPlacemark('');
                polygonPlacemark.setGeometry(ge.createPolygon(''));
                var outer = ge.createLinearRing('');
                polygonPlacemark.getGeometry().setOuterBoundary(ring);
                ge.getFeatures().appendChild(polygonPlacemark);
                polygonPlacemark.setStyleSelector(ge.createStyle(''));
                var lineStyle = polygonPlacemark.getStyleSelector().getLineStyle();
                lineStyle.setWidth(lineStyle.getWidth() + 2);
                lineStyle.getColor().set('00000000');
                var polyColor = polygonPlacemark.getStyleSelector().getPolyStyle().getColor();
                polyColor.setA(64);
                polyColor.setR(255);
                polyColor.setG(0);
                polyColor.setB(0);
                google.earth.addEventListener(placemark, 'click', function(event) {
                    event.preventDefault();
                    var balloon = ge.createHtmlStringBalloon('');
                    balloon.setFeature(placemark);
                    balloon.setContentString('<div class="infowindow">'+eq.magnitude+': '+eq.region+'<br/>'+eq.balloon+'</div>');
                    ge.setBalloon(balloon);
                });
                
                       
               
            }
            function doMyThing(ct){
                $('#status').html('Done loading '+ct.loaded+' earthquakes ');
                if($($('#list div').is(':visible')).length==0 && $('#earth_unavailable').is(':visible').length==0 ){
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
                            if(page_type=='earth'){
                                add_earth_earthquake(eq);
                            }else{
                                add_map_earthquake(eq);
                            }
           
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
                var slideVal= $.cookie('chart_min')
                $('#show_preferences').bind('click',function(){
                    $('#preferences').dialog({width:'33%',height:400,position: [0, 45]});
                });
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
        
                if(page_type=='earth'){
                    $('#use_earth').hide();
                    init_earth();
                }else{
                    $('#use_maps').hide();
                    map = new google.maps.Map(document.getElementById('map3d'),  
                    {
                        zoom: 6,
                        center: new google.maps.LatLng(latest_lat, latest_lon),
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });
                    
                }
                load();
                $('button').button();
                $( "#toolbar" ).buttonset(); 
                $('#list').css('height',$('#list_container').height()).show();
                var interval=self.setInterval("load()",30000);
                var max=$('.quake').length;
                var obj=$('.quake');
                $( "#forward" ).button({
                   
                });
                $( "#backward" ).button({
                   
                });
                $('.notour').bind('click',function(){
                    window.clearInterval($(this).data('timeoutID'));
                    $('#status').html('Tour Ended');
               
                    $('.tour,.notour').toggle();
                });
                $('.forward').bind('click',function(){
                    var obj=$('.quake:visible');
                    if(offset==$(obj).length){
                        offset=0;
                    }
                    offset++;
                    $(obj[offset]).trigger('click');
                });
                $('.backward').bind('click',function(){
                    var obj=$('.quake:visible');
                    if(offset==0){
                        offset=$(obj).length;
                    }
                    offset--;
                    $(obj[offset]).trigger('click');
                });
                $('.tour').bind('click',function(){
                    $('#status').html('Tour Started');
                    var obj=$('.quake');
                    $('.tour,.notour').toggle();
                    $(obj[offset]).trigger('click');
                    $('.notour').data('timeoutID', window.setInterval(function(){
                        offset++;
                        $(obj[offset]).trigger('click');
                        if(offset>max){
                            offset=0;
                        }
                    }, 5000)
                );
       
                });
                $
                $('button').button();
                $( "#toolbar" ).buttonset(); 
                $('#list').css('height',$('#list_container').height()).show();
                
               
                $($("abbr.timeago").not('timeagoloaded')).addClass('timeagoloaded').timeago();

                $('.quake').live('click',function(){
                   
                    $('.detail').hide();
                  
                    $($(this).find('.detail')).show();
                    $('#list').scrollTop(0).scrollTop(($(this).position().top)-40);
                    
                    if(page_type=='earth'){
                        $('#use_earth').hide();
                        try{
                        ge.setBalloon(null);
                        var lookAt = ge.getView().copyAsLookAt(ge.ALTITUDE_RELATIVE_TO_GROUND);
                        
                        lookAt.setLatitude($(this).data('lat'));
                        lookAt.setLongitude($(this).data('lon'));
                        lookAt.setRange(3000000);
                        ge.getView().setAbstractView(lookAt);
                        }catch(e){
                            $('#status').html('error panning Google Earth to that location. Try the Maps version.')
//                            alert(e.message);
                        }
                         
                    }else{
                      
                        var l1=$(this).data('lat');
                        var l2=$(this).data('lon');
                        //set up array for times actions
                        var cmd=[]
                        //get the zoom-level based on the distance between two lat/lon spots
                        var d=sloc(l1, l2);
                        //based on our max flyout level, populate our times commands
                        if(d>=1){cmd[cmd.length]="map.setZoom(5)";}
                        if(d>=2){cmd[cmd.length]="map.setZoom(4)";}
                        if(d>=3){cmd[cmd.length]="map.setZoom(3)";}
                        if(d>=4){cmd[cmd.length]="map.setZoom(2)";}
                        cmd[cmd.length]="map.panTo(new google.maps.LatLng("+l1+","+l2+"),"+(1+d)+");";
                        if(d>=4){cmd[cmd.length]="map.setZoom("+3+")";}
                        if(d>=3){cmd[cmd.length]="map.setZoom("+4+")";}
                        if(d>=2){cmd[cmd.length]="map.setZoom("+5+")";}
                        if(d>=1){cmd[cmd.length]="map.setZoom("+6+")";}
                        timer_offset=0;
                        
                        //get the number of steps for our cmd iterator. subtract 1 so we get the middle step in our math since the number of steps will always be odd
                        steps=cmd.length-1
                        //iterate over ALL the steps.
                        for(x=0;x<=steps+1;x++){
                            //for the middle step, wait a bit more than normal
                            if(x>=steps/2){
                                timer_offset=(100*d);
                            }
                            //at the end of the middle action pan, wait a bit even more than that
                            if(x>=(steps/2)+1){
                                timer_offset=(100*d)+200;
                            }
                            //set our timeout
                            time=200+(x*100+timer_offset)
                            //load the command on the timeout
                            setTimeout(cmd[x],time);
                        }
                        
                    }
                });
            });
            

          
            function sloc(lat,lon){
                sloc_obj.lat1=sloc_obj.lat2;
                sloc_obj.lon1=sloc_obj.lon2;
                sloc_obj.lat2=lat;
                sloc_obj.lon2=lon;
                var R = 6371; // km
                var d = Math.acos(Math.sin(sloc_obj.lat1)*Math.sin(sloc_obj.lat2) + 
                    Math.cos(sloc_obj.lat1)*Math.cos(sloc_obj.lat2) *
                    Math.cos(sloc_obj.lon2-sloc_obj.lon1)) * R;
               
                if(d<1000){
                    return 1;
                }
                if(d<5000){
                    return 2;
                }
                if(d<10000){
                    return 3;
                }
                return(4);
            }

   
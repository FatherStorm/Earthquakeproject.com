<script>
  
    function initCallback(object) {
        //                ge = object;
        //                ge.getWindow().setVisibility(true);
        var myOptions = {
            zoom: 3,
            center: new google.maps.LatLng(<?php echo $eq->largest['lat']; ?>, <?php echo $eq->largest['lon']; ?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map3d'),
        myOptions);
        var myLatlng = new google.maps.LatLng(<?php echo $eq->largest['lat']; ?>, <?php echo $eq->largest['lon']; ?>);
        var marker = new google.maps.Marker({
            position: myLatlng,
            title:"<?php echo $eq->largest['balloon']; ?>"
        });
        marker.setMap(map);
<?php
// To add the marker to the map, call setMap();

foreach ($eq->earthquake_data as $earthquake) {
    #  echo"$('#status').html('Loading earthquake [{$earthquake['eqid']}] {$earthquake['magnitude']} - {$earthquake['region']}');";
    ?>
                    
                //                var point<?php echo $earthquake['eqid']; ?> = new GLatLng(<?php echo $earthquake['lat']; ?>, <?php echo $earthquake['lon']; ?>);
                //                map.setCenter(point, 10);
                //                var marker<?php echo $earthquake['eqid']; ?> = new GMarker(point<?php echo $earthquake['eqid']; ?>);
                //                map.addOverlay(marker<?php echo $earthquake['eqid']; ?>);
                //                    
                //                var markerOptions<?php echo $earthquake['eqid']; ?> = {map: map, position: new google.maps.LatLng(<?php echo $earthquake['lat']; ?>, <?php echo $earthquake['lon']; ?>)};
                //                var marker<?php echo $earthquake['eqid']; ?> = new google.maps.Marker(markerOptions<?php echo $earthquake['eqid']; ?>);
                //                markers.push(marker<?php echo $earthquake['eqid']; ?>);
                //                var content<?php echo $earthquake['eqid']; ?> = "<?php echo str_replace("\n", "", str_replace("\r", "\n", $earthquake['balloon'])); ?>";
                //
                //                google.maps.event.addListener(marker<?php echo $earthquake['eqid']; ?>, 'click', function(e) {
                //                    var infobox = new SmartInfoWindow({position: marker<?php echo $earthquake['eqid']; ?>.getPosition(), map: map, content: content<?php echo $earthquake['eqid']; ?>});
                //                });
             
    <?php
}
?>
    }

 


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
    var offset=0;
    var max=100;
    //    var timeoutID=false
    function tour(){
        console.log('fired');
    
        offset++;
        var obj=$('.quake');
        $(obj[offset]).trigger('click');
        if(offset>max){
            offset=0;
        }
    }
    
    $(document).ready(function(){
        var max=$('.quake').length;
        var obj=$('.quake');
       
        $('.notour').bind('click',function(){
            window.clearInterval($(this).data('timeoutID'));
      
               
            $('.tour,.notour').toggle();
        });
        $('.tour').bind('click',function(){
            $('.tour,.notour').toggle();
            $(obj[offset]).trigger('click');
            $('.notour').data('timeoutID', window.setInterval(function(){
                offset++;
                var obj=$('.quake');
                $(obj[offset]).trigger('click');
              
                if(offset>max){
                    offset=0;
                }
    
            }, 5000)
        );
       
        });

        init(); 
        $('.set_slider').click(function(){
            $('#slider').slider('option','value',$(this).data('min')); 
        });
        $('.guide').click(function(){
            $('.legend,#slider').toggle(); 
        });





        $("abbr.timeago").timeago();

        $('.quake').click(function(){
            $('.detail').hide();
            $($(this).find('.detail')).show();
            ge.setBalloon(null);
            var lookAt = ge.getView().copyAsLookAt(ge.ALTITUDE_RELATIVE_TO_GROUND);
            lookAt.setLatitude($(this).data('lat'));
            lookAt.setLongitude($(this).data('lon'));
            lookAt.setRange(3000000);
            ge.getView().setAbstractView(lookAt);
            $('embed').focus();
        });



        if(  $.cookie('chart_min')==false){
            $.cookie('chart_min', 5, { expires: 365 });

        }

        var slideVal= $.cookie('chart_min')

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
    });

</script>
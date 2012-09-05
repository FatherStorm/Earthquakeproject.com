<script>
  
   function initCallback(object) {
                ge = object;
                ge.getWindow().setVisibility(true);
<?php
foreach ($eq->earthquake_data as $earthquake)
{
    echo"$('#status').html('Loading earthquake [{$earthquake['eqid']}] {$earthquake['magnitude']} - {$earthquake['region']}');";
    ?>
                //----------------------------------------------------------------------------------------------//
                var textPlacemark='<?php echo $earthquake['magnitude'] . " - " . $earthquake['region']; ?>';
                var placemark<?php echo $earthquake['eqid']; ?> = ge.createPlacemark('');
                placemark<?php echo $earthquake['eqid']; ?>.setName(textPlacemark);
                ge.getFeatures().appendChild(placemark<?php echo $earthquake['eqid']; ?>);
                var icon = ge.createIcon('');
                icon.setHref('http://maps.google.com/mapfiles/kml/paddle/red-circle.png');
                var style = ge.createStyle('');
                style.getIconStyle().setIcon(icon);
                placemark<?php echo $earthquake['eqid']; ?>.setStyleSelector(style);
                var point = ge.createPoint('');
                point.setLatitude(<?php echo $earthquake['lat']; ?>);
                point.setLongitude(<?php echo $earthquake['lon']; ?>);
                placemark<?php echo $earthquake['eqid']; ?>.setGeometry(point);
//                var radius=<?php echo pow(1.25, $earthquake['magnitude']); ?> 
//                var ring = ge.createLinearRing('');
//                var steps = 8;
//                var pi2 = Math.PI * 2;
//                for (var i = 0; i < steps; i++) {
//                    var lat = <?php echo $earthquake['lat']; ?> +  radius * Math.cos(i / steps * pi2);
//                    var lng = <?php echo $earthquake['lon']; ?> + radius * Math.sin(i / steps * pi2);
//                    ring.getCoordinates().pushLatLngAlt(lat, lng, 0);
//                }
//                var polygonPlacemark<?php echo $earthquake['eqid']; ?> = ge.createPlacemark('');
//                polygonPlacemark<?php echo $earthquake['eqid']; ?>.setGeometry(ge.createPolygon(''));
//                var outer = ge.createLinearRing('');
//                polygonPlacemark<?php echo $earthquake['eqid']; ?>.getGeometry().setOuterBoundary(ring);
//                ge.getFeatures().appendChild(polygonPlacemark<?php echo $earthquake['eqid']; ?>);
//                polygonPlacemark<?php echo $earthquake['eqid']; ?>.setStyleSelector(ge.createStyle(''));
//                var lineStyle = polygonPlacemark<?php echo $earthquake['eqid']; ?>.getStyleSelector().getLineStyle();
//                lineStyle.setWidth(lineStyle.getWidth() + 2);
//                lineStyle.getColor().set('00000000');
//                var polyColor = polygonPlacemark<?php echo $earthquake['eqid']; ?>.getStyleSelector().getPolyStyle().getColor();
//                polyColor.setA(64);
//                polyColor.setR(255);
//                polyColor.setG(0);
//                polyColor.setB(0);
                google.earth.addEventListener(placemark<?php echo $earthquake['eqid']; ?>, 'click', function(event) {
                    event.preventDefault();
                    var balloon = ge.createHtmlStringBalloon('');
                    balloon.setFeature(placemark<?php echo $earthquake['eqid']; ?>);
                    balloon.setContentString(" <?php echo $earthquake['magnitude']; ?>  <?php echo $earthquake['region']; ?> <br/><hr> <?php echo str_replace("\n", "", str_replace("\r", "\n", $earthquake['balloon'])); ?>");
                    ge.setBalloon(balloon);
                });
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
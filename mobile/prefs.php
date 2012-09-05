<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
        </title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
        <style>
          .text-align-center {
  text-align: center;
}
.text-align-right {
  text-align: right;
}

        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js">
        </script>
    </head>
    <body>
        <div data-role="page" id="page1">
            <div data-role="content">
               
                <div data-role="dialog" style="display:none;">
                    <fieldset data-role="controlgroup">
                        <label for="slider1">
                            Minimum Magnitude to show
                        </label>
                        <div id="sliderVal" style="color:yellow;width:1.5em;float:left;">10</div>
                        <div  style="float:left;" id="slider"></div>
                    </fieldset>
                     <a href="#index" data-role="button" data-rel="back" data-theme="b" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" class="ui-btn ui-shadow ui-btn-corner-all ui-btn-up-b"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Done</span></span></a>
                </div>
               
            </div>
        </div>
        <script>
       
        </script>
    </body>
</html>
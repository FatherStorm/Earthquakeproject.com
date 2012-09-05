    <?php
//   echo phpinfo();
//   die();
    date_default_timezone_set('America/Chicago');
    $hostname = "db411885494.db.1and1.com";
    $database = "db411885494";
    $username = "dbo411885494";
    $password = "audi5000";

    $link = mysql_connect($hostname, $username, $password);
    if (!$link)
    {
        die('Connection failed: ' . mysql_error());
    } else
    {
        
    }

    $db_selected = mysql_select_db($database, $link);
    if (!$db_selected)
    {
        die('Can\'t select database: ' . mysql_error());
    } else
    {
        
    }
    $colors = array(
        '2' => 'green'
        , '3' => 'lightgreen'
        , '4' => 'khaki'
        , '5' => 'orange'
        , '6' => 'tomato'
        , '7' => 'red'
        , '8' => 'firebrick'
        , '9' => 'darkred'
    );
    $mMin = 10;
    $mMax = 0;

$export=date("Y-m-d h:i.s ")."<br/><hr>";
    $data_url = 'http://earthquake.usgs.gov/earthquakes/catalogs/eqs1day-M0.txt';
   // $data_url = 'http://earthquake.usgs.gov/earthquakes/catalogs/eqs7day-M0.txt';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $data_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);

    curl_close($ch);
    $eq_data = explode("\n", $output);
    array_shift($eq_data);
    $new = $update = 0;
    foreach ($eq_data as $entry)
    {


        $unmapped = str_getcsv($entry);
        $row = array(
            'src' => $unmapped[0]
            , 'eqid' => $unmapped[1]
            , 'version' => $unmapped[2]
            , 'datetime' => $unmapped[3]
            , 'lat' => $unmapped[4]
            , 'lon' => $unmapped[5]
            , 'magnitude' => $unmapped[6]
            , 'depth' => $unmapped[7]
            , 'nst' => $unmapped[8]
            , 'region' => $unmapped[9]
        );

        $html = '';

        $row['width'] = $row['magnitude'] * 10;
        if ($key == 1)
        {
            #  $earthquake['Magnitude']=9.9;
        }
        if ($row['magnitude'] > $mMax
        )
            $mMax = $row['magnitude'];
        if ($row['magnitude'] < $mMin
        )
            $mMin = $row['magnitude'];
        $strength = number_format(pow(2, $row['magnitude']), 0);
        #  print_r($earthquake);
        $color = $colors[intval($row['magnitude'])];
        $img = "http://maps.google.com/maps/api/staticmap?center={$row['lat']},{$row['lon']}$&zoom=5&size=150x200&maptype=roadmap&markers=color:$color%7Clabel:{$row['magnitude']}%7C{$row['lat']},{$row['lon']}&&sensor=true";
        $fullpath ='../images/' . $row['eqid'] . '.jpg';
        save_image($img, $fullpath);
         $row['image'] = '/images/'.$row['eqid'].'.jpg';
        $lName = md5($img) . ".png";
        $html = "<div 
            id='quake{$row['eqid']}'  
            class=' quake round filterable' 
            data-placemark='{$row['region']} {$row['magnitude'] }' 
            data-lat='{$row['lat']}' data-lon='{$row['lon']}' 
            rel='{$row['magnitude']}' 
            style='border:2px solid $color;border-right:solid {$strength}px $color;'>
            {$row['magnitude']}
            <abbr class='timeago' title='" . date("Y-m-d\TH:i:sO", strtotime($row['datetime'])) . "'>"
                . ( date('l jS \of F Y h:i:s A ', strtotime($row['datetime']))) . " CDT</abbr>
           <br/> {$row['region']}";

        $detail = "<div class='detail'>
           <a target='google' href='http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q={$row['lat']},{$row['lon']}&aq=&sll=37.0625,-95.677068&sspn=53.564699,89.824219&ie=UTF8&t=h&z=5'>
           
                <img style='float:left;' src='{$row['image']}'>
           </a>" .
                ( date('l jS \of F Y', strtotime($row['datetime']))) . "
                       <br/>" .
                ( date('h:i:s A ', strtotime($row['datetime']))) . "CDT
                       <br/>
              Lat: <strong>{$row['lat']}</strong><br/>Lon: <strong>" . $row['lon'] . "</strong>
               <br/>EQ ID: <strong>" . $row['eqid'] . "</strong>
             <br/>Depth: <strong>" . $row['depth'] . "</strong>
              <br/>Reporting Stations: <strong>" . $row['nst'] . "</strong>
              <br/>Data version: <strong>" . $row['version'] . "</strong>
              <br/>USGS Data: <strong><a  target='usgs' href='http://earthquake.usgs.gov/earthquakes/recenteqsww/Quakes/{$row['src']}{$row['eqid']}.php'>{$row['src']}{$row['eqid']}</a></strong>
              <br style='clear:both;'/></div>";
        $html.=$detail;
        $html.="<br style='clear:both;'/>
                </div>";
        $row['balloon'] = $detail;
        $row['html'] = $html;
        
        $row['html']=mysql_escape_string(str_replace("\r\n","",$row['html']));
        $row['balloon']=mysql_escape_string(str_replace("\r\n","",$row['balloon']));
       
        $row['epoch']=strtotime($row['datetime']);
        
 $earthquake = $row;
        $sql = "select count(*) as existing from earthquake where eqid='{$earthquake['eqid']}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        if($earthquake['eqid']!=false){
        if ($row['existing'] == 0)
        {
            $new++;
            $sql = "  INSERT INTO earthquake (src,eqid,version,`datetime`,epoch,lat,lon,magnitude,depth,nst,region,image,balloon,html) VALUES (
             '{$earthquake['src']}'
             ,'{$earthquake['eqid']}'
             ,'{$earthquake['version']}'
             ,'{$earthquake['datetime']}'
             ,'{$earthquake['epoch']}'
             ,'{$earthquake['lat']}'
             ,'{$earthquake['lon']}'
             ,'{$earthquake['magnitude']}'
             ,'{$earthquake['depth']}'
             ,'{$earthquake['nst']}'
             ,'{$earthquake['region']}'
             ,'{$earthquake['image']}'
             ,'{$earthquake['balloon']}'
             ,'{$earthquake['html']}'
            )";
             
              $export.=implode("\r\n",$earthquake); 
              #print_r($earthquake);echo"</pre>";
        } else
        {
            $update++;
            $sql = "  UPDATE earthquake  set 
             src='{$earthquake['src']}'
             ,version='{$earthquake['version']}'
             ,datetime='{$earthquake['datetime']}'
             ,epoch='{$earthquake['epoch']}'
             ,lat='{$earthquake['lat']}'
             ,lon='{$earthquake['lon']}'
             ,magnitude='{$earthquake['magnitude']}'
             ,depth='{$earthquake['depth']}'
             ,nst='{$earthquake['nst']}'
             ,region='{$earthquake['region']}'
             ,image='{$earthquake['image']}'
             ,balloon='{$earthquake['balloon']}'
             ,html='{$earthquake['html']}'
             WHERE eqid='{$earthquake['eqid']}';";
        }
     

     
            mysql_query($sql);
        }
        #print_r($earthquake);
    }


    mysql_close($link);
    echo "Added $new  update $update at" . date('Y-m-d h:i.s');
   // mail('fatherstorm@gmail.com', "earthquakeproject $new/$update", "Added $new  update $update at" . date('Y-m-d h:i.s')."<br/>".$export);
    
    
    function save_image($img, $fullpath)
    {
       # echo $fullpath;
        if (file_exists($fullpath))
        {
           $size = getimagesize($fullpath);
           if($size[0]==100 && $size[1]==100){
            unlink($fullpath);
           }else{
            return;
           }
        }
        $ch = curl_init($img);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        if (file_exists($fullpath))
        {
            unlink($fullpath);
        }
        $fp = fopen($fullpath, 'x');
        fwrite($fp, $rawdata);
        fclose($fp);
        return;
    }
    ?>

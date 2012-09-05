<?php

date_default_timezone_set('America/Chicago');
include("db.class.php");

class earthquake {

    function __construct() {
        if(!$_COOKIE['type']){
             setcookie('type', 'earth',strtotime('+1 year'));
        }
        if(isset($_REQUEST['maps'])){
            setcookie('type', 'maps',strtotime('+1 year'));
        }
        if(isset($_REQUEST['earth'])){
            setcookie('type', 'earth',strtotime('+1 year'));
        }
        $this->hostname = "db411885494.db.1and1.com";
        $this->database = "db411885494";
        $this->username = "dbo411885494";
        $this->password = "audi5000";
        $this->data_url = 'http://earthquake.usgs.gov/earthquakes/catalogs/eqs1day-M0.txt';
        $this->data_period = isset($_COOKIE['data_period']) ? $_COOKIE['data_period'] : '-4 days';
        $this->colors = array(
            '2' => 'green'
            , '3' => 'lightgreen'
            , '4' => 'khaki'
            , '5' => 'orange'
            , '6' => 'tomato'
            , '7' => 'red'
            , '8' => 'firebrick'
            , '9' => 'darkred'
        );
        $this->mMin = 10;
        $this->mMax = 0;
        $this->chart_min = isset($_COOKIE['chart_min']) ? $_COOKIE['chart_min'] : 3;

        $this->db = mysql_connect($this->hostname, $this->username, $this->password);
        if (!$this->db) {
            die('Connection failed: ' . mysql_error());
        } else {
            
        }


        $db_selected = mysql_select_db($this->database, $this->db);
        if (!$db_selected) {
            die('Can\'t select database: ' . mysql_error());
        } else {



            $this->get_csv();
        }
    }

    function save_image($img, $fullpath) {
        if (file_exists($fullpath)) {
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
        if (file_exists($fullpath)) {
            unlink($fullpath);
        }
        $fp = fopen($fullpath, 'x');
        fwrite($fp, $rawdata);
        fclose($fp);
        return;
    }

    function get_csv() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->data_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);
        $eq_data = explode("\n", $output);
        array_shift($eq_data);
        $new = $update = 0;
        foreach ($eq_data as $entry) {


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
            if ($key == 1) {
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
            $color = $this->colors[intval($row['magnitude'])];
            $img = "http://maps.google.com/maps/api/staticmap?center={$row['lat']},{$row['lon']}$&zoom=5&size=150x200&maptype=roadmap&markers=color:$color%7Clabel:{$row['magnitude']}%7C{$row['lat']},{$row['lon']}&&sensor=true";
            $fullpath = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $row['eqid'] . '.jpg';
            $this->save_image($img, str_replace("mobile/","",$fullpath));
            $lName = md5($img) . ".png";
            $row = array_change_key_case($row, CASE_LOWER);
            $row['image'] = '/images/' . $row['eqid'] . '.jpg';
            $html = "<div 
            id='quake{$row['eqid']}'  
            class=' quake round filterable' 
            data-placemark='{$row['region']} {$row['magnitude'] }' 
            data-lat='{$row['lat']}'
            data-lon='{$row['lon']}' 
            rel='{$row['magnitude']}' 
            style='border:2px solid $color;border-right:solid {$strength}px $color;'>
            {$row['magnitude']}
            <abbr class='timeago' title='" . date("Y-m-d\TH:i:sO", strtotime($row['datetime'])) . "'>"
                    . ( date('l jS \of F Y h:i:s A ', strtotime($row['datetime']))) . " CST</abbr>
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
              <br/>USGS Data: <strong><a  target='usgs' href='http://earthquake.usgs.gov/earthquakes/eventpage/{$row['src']}{$row['eqid']}#summary'>{$row['src']}{$row['eqid']}</a></strong>
              <br style='clear:both;'/></div>";
            $html.=$detail;
            $html.="<br style='clear:both;'/>
                </div>";
            $row['image'] = $fullpath;
            $row['balloon'] = $detail;
            $row['epoch'] = strtotime($row['datetime']);
            $row['html'] = $html;
            $row['html'] = mysql_escape_string(str_replace("\r\n", "", $row['html']));
            $row['balloon'] = mysql_escape_string(str_replace("\r\n", "", $row['balloon']));

            $earthquake = $row;




            $sql = "select count(*) as existing , version from earthquake where eqid='{$earthquake['eqid']}'";
            $result = mysql_query($sql);
            $row = mysql_fetch_object($result);
            if ($row->existing == 0) {
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
                mysql_query($sql);
            } else {
                if ($row->version < $earthquake['version']) {
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
                    mysql_query($sql);
                }
            }
        }
    }
   

    function get_earthquakes() {
        $this->data_period='-18 hours';
        $sql = "Select * from earthquake where( magnitude > 2.5 and epoch > '" . (strtotime($this->data_period)) . "') or ( magnitude > 7.5 and epoch > '" . (strtotime("-7 days")) . "') order by epoch DESC";

        try {
            $result = mysql_query($sql);
        } catch (Exception $e) {

            print_r($e);
        }
        $key = 0;
        $eqs = array();
        $this->largest=array('lat'=>false,'lon'=>false,'magnitude'=>2.5);
        $this->latest=array('epoch'=>strtotime('-2 days'));
        while ($row = mysql_fetch_assoc($result)) {
         
            if (!isset($eqs[$row['eqid']])) {
                $row['balloon']=str_replace(array("\r","\n"),"",$row['balloon']);
                if($row['magnitude']> $this->largest['magnitude']){
                    $this->largest=$row;
                }
                if($row['epoch']> $this->latest['epoch']){
                    $this->latest=$row;
                }
                $eqs[$row['eqid']] = true;
                $this->html[] = $row['html'];
                $this->earthquake_data[] = $row;
            }
        }
    }
    function get_mobile_earthquakes() {
        $this->data_period='-18 hours';
        $sql = "Select * from earthquake where( magnitude > 2.5 and epoch > '" . (strtotime($this->data_period)) . "') or ( magnitude > 7.5 and epoch > '" . (strtotime("-7 days")) . "') order by epoch DESC";

        try {
            $result = mysql_query($sql);
        } catch (Exception $e) {

            print_r($e);
        }
        $key = 0;
        $eqs = array();
        $this->largest=array('lat'=>false,'lon'=>false,'magnitude'=>2.5);
        $this->latest=array('epoch'=>strtotime('-2 days'));
        while ($row = mysql_fetch_assoc($result)) {
         $row['balloon']=str_replace('/images/','http://earthquakeproject.com/images/',$row['balloon']);
            if (!isset($eqs[$row['eqid']])) {
                $row['balloon']=str_replace(array("\r","\n"),"",$row['balloon']);
                if($row['magnitude']> $this->largest['magnitude']){
                    $this->largest=$row;
                }
                if($row['epoch']> $this->latest['epoch']){
                    $this->latest=$row;
                }
                $eqs[$row['eqid']] = true;
                
              
                       
                $row['mobile']="
                          <li
                data-theme='a' 
              
                id='quake{$row['eqid']}'
                  data-placemark='{$row['region']} {$row['magnitude'] }' 
            data-lat='{$row['lat']}'
            data-lon='{$row['lon']}' 
            rel='{$row['magnitude']}'
            class='quake round filterable ui-btn ui-btn-up-a ui-btn-icon-right ui-li-has-arrow ui-li ui-corner-top'>
                <div class='ui-btn-inner ui-li ui-corner-top'>
                <div class='ui-btn-text'>
                      
                          
            
          <a data-theme='a' href='#page1' class='ui-link-inherit' data-transition='slide'>{$row['magnitude'] } {$row['region']}   <abbr class='timeago' title='" . date("Y-m-d\TH:i:sO", strtotime($row['datetime'])) . "'>"
                    . ( date('l jS \of F Y h:i:s A ', strtotime($row['datetime']))) . " CST</abbr></a></div></div>
          <div class='detail' style='display:none;'>{$row['balloon']}</div></li>";
                $this->html[] =$row['mobile'];
                $this->earthquake_data[] = $row;
            }
        }
    }

    function add_epoch() {
        $sql = "Select * from earthquake ";

        #echo $sql;
        try {
            $result = mysql_query($sql);
        } catch (Exception $e) {

            print_r($e);
        }
        $key = 0;
        while ($row = mysql_fetch_assoc($result)) {
            echo "update earthquake set epoch=" . strtotime($row['datetime']) . " where eqid= '" . $row['eqid'] . "';\r\n";
        }
    }

    function check_form() {
        if (isset($_REQUEST['data_period'])) {
            $_COOKIE['data_period'] = $this->data_period = $_COOKIE['data_period'];
            setcookie('data_period', $this->data_period, strtotime('+1 year'));
        }
        if (isset($_REQUEST['chart_min'])) {
            $_COOKIE['chart_min'] = $this->chart_min = $_COOKIE['chart_min'];
            setcookie('chart_min', $this->chart_min, strtotime('+1 year'));
        }
    }

}

// Open the base (construct the object):
?>

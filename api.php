<?php
  date_default_timezone_set("Asia/Dhaka");

  if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo 'GET OPTION FROM: '. $_GET['SN'] .'
            ATTLOGStamp=None
            OPERLOGStamp=9999
            ATTPHOTOStamp=None
            ErrorDelay=60
            Delay=45
            TransTimes=00: 00;14: 05
            TransInterval=1
            TransFlag=TransData AttLog OpLog
            TimeZone=6
            Realtime=1
            Encrypt=None';
  } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($post_data)){
    if($_GET['table'] == 'ATTLOG') {  //&& $_GET['Stamp'] == 9999
     header("Access-Control-Allow-Origin: *");
     header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
     header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

     $post_data = file_get_contents("php://input", true);
     $att_count = substr_count($post_data, "\n");
     $att_data = explode("\n", trim($post_data));
     $gross_array = [];
     
     // db connection
     $conn = new mysqli("localhost", "killabd_innovaatt", "innovaatt.123", "killabd_innovaatt");

     for($i = 0; $i < $att_count; $i++) {
      // ekhane main kaaj hobe...
      $line = explode("\t", trim($att_data[$i]));
      $sql ="INSERT INTO attendances (device_pin, timestampdata, device_id, count, institute_id, created_at, updated_at) VALUES ('".$line[0]."', '".$line[1]."', '". $_GET['SN'] ."', '". substr_count($post_data, "\n") ."', 0, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
      if ($conn->query($sql)===true) {
          $att_count = substr_count($post_data, "\n");
      }
     }  
     
     $message ='OK: '.$att_count;
     $conn->close();
     $att_count = 0;
     echo $message;
  } elseif ($_GET['table'] == 'OPERLOG') {
      echo 'OK: 1';
  }
}
<?php
  date_default_timezone_set("Asia/Dhaka");

  if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(!empty($_GET['SN'])) {
      echo 'GET OPTION FROM: '. $_GET['SN'] .'
            ATTLOGStamp=None
            OPERLOGStamp=9999
            ATTPHOTOStamp=None
            ErrorDelay=300
            Delay=60
            TransTimes=00: 00;14: 05
            TransInterval=1
            TransFlag=TransData AttLog OpLog
            TimeZone=6
            Realtime=1
            Encrypt=None';
    } else {
      echo 'Too clever!';
    }
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
     $conn = new mysqli("localhost", "innoatt_user", "Mannan.Rifat.123", "innoatt_db");

     for($i = 0; $i < $att_count; $i++) {
      // ekhane main kaaj hobe...
      $line = explode("\t", trim($att_data[$i]));
      $timestampdata = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($line[1]))); // jehetu china time deoa ache machine e
      // check old data
      $check_device_pin = $line[0];
      $check_device_id = $_GET['SN'];
      $today = date('Y-m-d');
      $sqlcheck = "SELECT * FROM attendances WHERE device_pin='$check_device_pin' AND device_id='$check_device_id' AND DATE_FORMAT(timestampdata, '%Y-%m-%d')='$today' order by timestampdata ASC";
      $checkold = $conn->query($sqlcheck);
      // check old data
      if ($checkold->num_rows > 1) {
          $datearray = [];
          $counter = 0;
          while($row = $checkold->fetch_assoc()) {
              $datearray[$counter]['id'] = $row["id"];
              $counter++;
          }
          $old_id = $datearray[1]['id'];
          $sql = "UPDATE attendances SET timestampdata='$timestampdata', updated_at='".date('Y-m-d H:i:s')."' WHERE id='$old_id'";
          if ($conn->query($sql) === TRUE) {
              $att_count = substr_count($post_data, "\n");
          }
      } else {
          $sql ="INSERT INTO attendances (device_pin, timestampdata, device_id, count, created_at, updated_at) VALUES ('".$line[0]."', '".$timestampdata."', '". $_GET['SN'] ."', '". substr_count($post_data, "\n") ."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
          if ($conn->query($sql) === TRUE) {
              $att_count = substr_count($post_data, "\n");
          }
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
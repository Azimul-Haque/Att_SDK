<?php
  header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
  header("Pragma: no-cache"); // HTTP 1.0.
  header("Expires: 0"); // Proxies.
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
            TransInterval=10
            TransFlag=TransData AttLog OpLog
            TimeZone=6
            Realtime=1
            Encrypt=None';
    } else {
      echo 'Too clever!';
    }
  } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($post_data)){
    if(!empty($_GET['table']) && $_GET['table'] == 'ATTLOG') {
     header("Access-Control-Allow-Origin: *");
     header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
     header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

     $post_data = file_get_contents("php://input", true);
     $att_count = substr_count($post_data, "\n");
     $att_data = explode("\n", trim($post_data));
     $gross_array = [];
     
     // db connection
     $conn = new mysqli("localhost", "innoatt_user", "Mannan.Rifat.123", "innoatt_database");

     for($i = 0; $i < $att_count; $i++) {
      // ekhane main kaaj hobe...
      $line = explode("\t", trim($att_data[$i]));

      // chech the time if it is bd time or china time
      $machinetime = strtotime($line[1]);
      $bdtime = strtotime(date('Y-m-d H:i:s'));
      $chechbdchinadiff = ($machinetime - $bdtime) / 60; // in minutes
      if($chechbdchinadiff > 60) {
        $timestampdata = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($line[1])));
      } else {
        $timestampdata = date('Y-m-d H:i:s', strtotime($line[1]));
      }
      // chech the time if it is bd time or china time

      // check old data
      $check_device_pin = $line[0];
      $check_device_id = $_GET['SN'];
      $today = date('Y-m-d');
      $sqlcheck = "SELECT * FROM attendances WHERE device_pin='$check_device_pin' AND device_id='$check_device_id' AND DATE_FORMAT(timestampdata, '%Y-%m-%d')='$today' order by timestampdata ASC";
      $checkold = $conn->query($sqlcheck);
      // check old data
      if ($checkold->num_rows > 1) { //$checkold != false && 
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
              // $conn->close();
          }
      } else {
          $sql ="INSERT INTO attendances (device_pin, timestampdata, device_id, count, created_at, updated_at) VALUES ('".$line[0]."', '".$timestampdata."', '". $_GET['SN'] ."', '". substr_count($post_data, "\n") ."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
          if ($conn->query($sql) === TRUE) {
              $att_count = substr_count($post_data, "\n");
              // $conn->close();
          }
      }
     }  
     
     $message ='OK: '.$att_count;
     $conn->close();
     // mysqli_close($conn);
     $att_count = 0;
     echo $message;
  } elseif(!empty($_GET['table']) && $_GET['table'] == 'OPERLOG') {
      echo 'OK: 1';
  }
}
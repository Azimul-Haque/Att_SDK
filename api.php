<?php
  date_default_timezone_set("Asia/Dhaka");

  if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo 'GET OPTION FROM: '. $_GET['SN'] .'\n
            ATTLOGStamp=None\n
            OPERLOGStamp=9999\n
            ATTPHOTOStamp=None\n
            ErrorDelay=60\n
            Delay=40\n
            TransTimes=00: 00;14: 05\n
            TransInterval=1\n
            TransFlag=TransData AttLog OpLog\n
            TimeZone=6\n
            Realtime=1\n
            Encrypt=None';
  } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($post_data)){
    if($_GET['table'] == 'ATTLOG') {  //&& $_GET['Stamp'] == 9999
       header("Access-Control-Allow-Origin: *");
       header("Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
       header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

       $post_data = file_get_contents("php://input", true);
       $data = $post_data;
       $outp = 0;
       
       $conn = new mysqli("localhost", "killabd_user", "Mannan.KillaBD.123", "killabd_db");
       $sql ="INSERT INTO attendances (data, sn, count, created_at, updated_at) VALUES ('".$data."', '". $_GET['SN'] ."', '". count($data) ."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
       if ($conn->query($sql)===true) {
           $outp = count($data);
       }
       $outp ='OK: '.$outp;
       // $conn->close();
       echo $outp; 
    } elseif ($_GET['table'] == 'OPERLOG') {
        echo 'OK: 1';
    }
  }
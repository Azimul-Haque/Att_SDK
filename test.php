<?php
  date_default_timezone_set("Asia/Dhaka");

  // db connection
  $conn = new mysqli("localhost", "killabd_innovaatt", "innovaatt.123", "killabd_innovaatt");

  // ekhane main kaaj hobe...
  // $timestampdata = date('Y-m-d H:i:s', strtotime('-2 hours', strtotime($line[1])));
  $device_pin = 1;
  $device_id = 'CJRJ194261918';
  $timestampdata = date('Y-m-d H:i:s');
  $today = date('Y-m-d');
  
  $sqlcheck = "SELECT * FROM attendances WHERE device_pin='$device_pin' AND device_id='$device_id' AND DATE_FORMAT(timestampdata, '%Y-%m-%d')='$today' order by timestampdata ASC";
  $checkold = $conn->query($sqlcheck);
  // check old data
  if ($checkold->num_rows > 1) {
      $datearray = [];
      $counter = 0;

      while($row = $checkold->fetch_assoc()) {
          echo "id: " . $row["id"]. " - Pin: " . $row["device_pin"]. " - Time: " . $row["timestampdata"]. " " . $row["device_id"]. "<br>";
          $datearray[$counter]['id'] = $row["id"];
          $counter++;
      }
      $oldid = $datearray[1]['id'];
      $sql = "UPDATE attendances SET timestampdata='$timestampdata' WHERE id='$oldid'";

      if ($conn->query($sql) === TRUE) {
          echo "Record updated successfully";
      }
  } else {
      echo 'ekta ache, porer gula lagbe.';
  } 
  
  echo  $timestampdata;
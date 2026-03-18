<?php
header('Content-Type: application/json');
session_start();

$host = 'sql209.infinityfree.com';
$username = 'if0_40541077';
$pass = 'NqoZSkOjWWEF9Y';
$db = 'if0_40541077_system';

$conn=new mysqli($host,$username,$pass,$db);
 $response = [];
 
 if ($conn->connect_error){
   $response = [
     'error_code' => 1,
     'isValidUser' => 0,
     'name' => '',
     'msg' => 'Connection_Failed' .$conn->connect_error
     ];
     echo json_encode($response);
     exit();
 }
  try{
    $stmt=$conn->prepare("select
                  concat(first_name,'',middle_name,'',last_name) as name from users
                  where 
                  email_address = ? and password = ?");
    $stmt->bind_param("ss",$_POST['email_address'],$_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0){
      $user = $result->fetch_assoc();
      $response = [ 'error_code' => 0 ,  'isValidUser' => 1,
       'name' => $user['name'],
      'msg' => 'Valid User/Successfuly Login'
      ];
    }else{
     $response = [ 'error_code' => 0 ,    'isValidUser' => 0,
            'name' => '',
            'msg' => 'Invalid User, Try Again.'
      ];
  }
    $result->free();
    $stmt->close();
    $conn->close();
    echo json_encode($response);
  }
  catch(Exeption $e){
    $response = [ 'error_code' => 2 ,      'isValidUser' => 0,
            'name' => '',
            'msg' => $e->getMessage()
      ];
    echo json_encode($response);
  }
?>
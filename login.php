<?php
date_default_timezone_set('Asia/Shanghai');
header('Content-Type: application/json; charset=utf-8'); //json
//header("Content-type: text/html; charset=utf-8");
header('Access-Control-Allow-Origin:*');
//$context = stream_context_create(array('http'=>array('ignore_errors'=>true)));
//$url='http://localhost:8080';
$ro =file_get_contents('php://input');
$user=json_decode($ro,true);
$myusername=$user['username'];
$mypassword=$user['password'];


function preg_username($username){
    if(preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",$username)){
        return true;
    }else{
        return false;
    }
}

function checkpassword($password){
    $length = strlen($password);
    if($length>=6&&$length<=27){
        return true;
    }else{
        return false;
    }
}

if(preg_username($myusername)&&checkpassword($mypassword)){

    $con = mysqli_connect("localhost","root","","users");

    if($con) {
        $mypassword = md5($mypassword);
        $select= mysqli_select_db($con,"users");
        $sql="SELECT * FROM `users` WHERE `users---username` = '$myusername' AND `users---password` = '$mypassword'";
        $result=mysqli_query($con,$sql);
        $re = mysqli_num_rows($result);
        mysqli_free_result($result);

        if ($re!=0) {
            $row['status']="1";
            $row['err']="0";
//            $last = mysqli_query($con, "SELECT `users---last_login_at` FROM `users` ");
//            $la = mysqli_fetch_array($last);
            $last_login = date("Y-m-d H:i:s");

            mysqli_query($con,"UPDATE `users` SET `users---last_login_at`='$last_login' WHERE `users---username`='$myusername' ");



        } else {
            $row['status']="0";
            $row['err']="fail";


        }
        echo(json_encode($row));
        mysqli_close($con);
    }
}else{
    $row['status'] = '2' ;
    $row['err'] = 'fail';
    echo(json_encode($row));//后台数据校验不通过
}











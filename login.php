<?php
header('Content-Type: application/json; charset=utf-8'); //json
//header("Content-type: text/html; charset=utf-8");
header('Access-Control-Allow-Origin:*');
//$context = stream_context_create(array('http'=>array('ignore_errors'=>true)));
//$url='http://localhost:8080';
$ro =file_get_contents('php://input');
$user=json_decode($ro,true);
$myusername=$user['username'];
$mypassword=$user['password'];
//$myusername = $_GET[username];
//$mypassword = md5($_GET[password]);
if($myusername!=''&&$mypassword!=''){

    $con = mysqli_connect("localhost","root","","users");

    if($con) {
        $select= mysqli_select_db($con,"users");
        $sql="SELECT * FROM `users` WHERE `users---username` = '$myusername' AND `users---password` = '$mypassword'";
        $result=mysqli_query($con,$sql);
        $re = mysqli_num_rows($result);
        mysqli_free_result($result);



//        $sql = "select users---username,users---password FROM users where users---username='$myusername' and users---password = '$mypassword' ";
//        $result = $con->query($sql);
//        $resArray = mysqli_fecth_array($result);
        if ($re!=0) {
            $row['status']="1";
            $row['err']="0";

        } else {
            $row['status']="0";
            $row['err']="fail";
            echo "用户名或密码错误";

        }
        echo(json_encode($row));
        mysqli_close($con);
    }
}
//    else{
//        echo "无法连接数据库";
//    }










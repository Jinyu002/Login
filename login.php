<?php
header('Content-Type:application/json; charset=utf-8');
$myusername = $_GET[username];
$mypassword = md5($_GET[password]);
if($myusername!=''&&$mypassword!=''){
    $con = mysqli_connect("localhost","root","","users");
    if($con) {
        $sql = "select users---username,users---password FROM users where users---username='$_GET[username]' and password = '$_GET[password]' ";
        $result = $con->query($sql);
        $resArray = mysqli_fecth_array($result);
        if ($resArray) {
            $row = mysqli_num_rows($sql);
            $user[] = $row;
            echo(json_encode($user));
        } else {
            echo "用户名或密码错误";
        }
    }
    else{
        echo "无法连接数据库";
    }
}








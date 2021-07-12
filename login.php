<?php
date_default_timezone_set('Asia/Shanghai');
header('Content-Type: application/json; charset=utf-8'); //json
header('Access-Control-Allow-Origin:*');
//监听端口文件
$ro = file_get_contents('php://input');//获取json文件
$user = json_decode($ro, true);  //将json文件转换为php数组，供操作
$myusername = $user['username'];
$mypassword = $user['password'];


//校验用户名格式
function preg_username($username)
{
    if (preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/", $username)) {
        return true;
    } else {
        return false;
    }
}

//校验密码
function checkpassword($password)
{
    $length = strlen($password);
    if ($length >= 6 && $length <= 27) {
        return true;
    } else {
        return false;
    }
}

//用户输入数据符合规范则连接数据库
if (!preg_username($myusername)) {
    $row['status'] = "0";
    $row['err'] = "fail";
    $row['msg'] = "用户名格式错误";
} else if (!checkpassword($mypassword)) {
    $row['status'] = "0";
    $row['err'] = "fail";
    $row['msg'] = "请填写6-27位密码";
} else {
    $con = mysqli_connect("localhost", "root", "", "users"); //连接数据库

    if ($con) {
        $mypassword = md5($mypassword); //md5加密密码
        $select = mysqli_select_db($con, "users");  //选择数据库表
        //操作数据库表
        $sql = "SELECT * FROM `users` WHERE `username` = '$myusername' AND `password` = '$mypassword'";
        $result = mysqli_query($con, $sql); //执行数据库
        $re = mysqli_num_rows($result);  //返回结果集的函数，若存在则返回1（一行结果集）
        mysqli_free_result($result);  //释放查询结果内存

        //判断结果集，返回相应的查询结果给前端
        if ($re != 0) {
            $value = $myusername;
            setcookie("username", $value, time() + 3600 * 24);
            $row['status'] = "1";
            $row['err'] = "0";
            $row['msg'] = "登陆成功";
            $last_login = date("Y-m-d H:i:s"); //获取本地时间，用以更新上次登录时间
            //操作数据库，更新上次登录时间
            mysqli_query($con, "UPDATE `users` SET `last_login_at`='$last_login' WHERE `username`='$myusername' ");

        } else {
            //用户不存在数据库中
            $row['status'] = "3";
            $row['err'] = "fail";
            $row['msg'] = "用户名或密码错误";

        }

    }
}
echo(json_encode($row));











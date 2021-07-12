<?php
date_default_timezone_set('Asia/Shanghai');
header("Content-Type: text/html;charset=utf-8");
header('Access-Control-Allow-Origin:*');
$ro = file_get_contents('php://input');
$user = json_decode($ro, true);
$myusername = $user['username'];
$mypassword = $user['password'];
$myconfirm = $user['confirm'];
$mynickname = $user['nickname'];
$myemail = $user['email'];
$mybirthday = $user['birthday'];
$mysex = $user['sex'];
$myprovince = $user['province'];
$mycity = $user['city'];
$myarea = $user['area'];
$address = $myprovince . $mycity . $myarea;
$birth = str_replace("T16:00:00.000Z", "", $mybirthday);////转换传入后端的日期格式,使其符合数据库字段类型
$birth = date("Y-m-d", strtotime("+1 day", strtotime($birth)));//日期+1，因为之前的日期格式直接转换成字符串会少一天

//检查输入是否符合规范

//验证用户名
function preg_username($username)
{
    if (preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/", $username)) {
        return true;
    } else {
        return false;
    }
}


//验证昵称
function checknicknam($nickname)
{
    $length = strlen($nickname);
    if ($nickname != '' && $length >= 4 && $length <= 20) {
        return true;
    } else {
        return false;
    }
}

//验证密码
function checkpassword($password)
{
    $length = strlen($password);
    if ($length >= 6 && $length <= 27) {
        return true;
    } else {
        return false;
    }
}

//验证再次输入密码
function checkconfirm($confirm, $password)
{
    if ($confirm == $password) {
        return true;
    } else {
        return false;
    }
}

//验证性别
function checksex($sex)
{
    if ($sex != '') {
        return true;
    } else {
        return false;
    }
}


//验证生日
function checkbirth($birth)
{
    if ($birth != '') {
        return true;
    } else {
        return false;
    }
}

//验证所在地
function checklocation($location)
{
    if ($location != '') {
        return true;
    } else {
        return false;
    }
}

//验证邮箱
function preg_email($mail)
{
    if (preg_match("/^[a-z0-9A-Z]+[- | a-z0-9A-Z . _]+@([a-z0-9A-Z]+(-[a-z0-9A-Z]+)?\\.)+[a-z]{2,}$/", $mail)) {
        return true;
    } else {
        return false;
    }
}

if (!preg_username($myusername)) {
    $row['status'] = "0";
    $row['err'] = "fail";
    $row['msg'] = "用户名格式错误";
    //echo(json_encode($row));
} else if (!checknicknam($mynickname)) {
    $row['status'] = "2";
    $row['err'] = "fail";
    $row['msg'] = "昵称格式错误";
    //echo(json_encode($row));
} else if (!checkpassword($mypassword)) {
    $row['status'] = "3";
    $row['err'] = "fail";
    $row['msg'] = "密码格式错误";
    //echo(json_encode($row));
} else if (!checkconfirm($myconfirm, $mypassword)) {
    $row['status'] = "4";
    $row['err'] = "fail";
    $row['msg'] = "两次输入密码不同";
    //echo(json_encode($row));
} else if (!checksex($mysex)) {
    $row['status'] = "5";
    $row['err'] = "fail";
    $row['msg'] = "性别未输入";
    //echo(json_encode($row));
} else if (!checkbirth($mybirthday)) {
    $row['status'] = "6";
    $row['err'] = "fail";
    $row['msg'] = "生日未输入";
    //echo(json_encode($row));
} else if (!checklocation($address)) {
    $row['status'] = "7";
    $row['err'] = "fail";
    $row['msg'] = "所在地未输入";
    //echo(json_encode($row));
} else if (!preg_email($myemail)) {
    $row['status'] = "8";
    $row['err'] = "fail";
    $row['msg'] = "邮箱格式错误";
    //echo(json_encode($row));
} else {
    $created = date("Y-m-d H:i:s"); //获取本地时间，用以插入数据库创建时间
    $con = mysqli_connect("localhost", "root", "", "users");
    if ($con) {
        $mypassword = md5($mypassword); //md5加密密码
        mysqli_select_db($con, "users"); //选择操作的数据库表
        $sql1 = "SELECT * FROM `users` WHERE `username` = '$myusername'";//检查用户名是否已经注册
        $result_username = mysqli_query($con, $sql1);//执行mysql语句
        $ru = mysqli_num_rows($result_username);//返回结果集的函数，若存在则返回1（一行结果集）
        ////判断查询结果集，将结果返回给前端
        if ($ru != 0) {
            //数据库中查询到注册时填写的用户名，注册失败
            $row['status'] = "10";
            $row['err'] = "fail";
            $row['msg'] = "用户名已存在";
        } else {
            //数据库中没有重复用户名
            //操作数据库，插入注册信息
            $sql = "INSERT INTO `users` (`username`, `password`, `nickname`, `head`, `email`, `birthday`, `sex`, `address`, `last_login_at`, `updated_at`, `created_at`) 
VALUES ('$myusername', '$mypassword', '$mynickname', NULL , '$myemail', '$birth', '$mysex','$address', NULL, NULL, '$created');";
            mysqli_query($con, $sql);//执行数据库操作
            $row['status'] = "1";
            $row['err'] = "0";
            $row['msg'] = "注册成功";
        }
        //echo(json_encode($row));

    } else {
        $row['status'] = '9';
        $row['err'] = "false";
        $row['msg'] = "数据库连接失败";
        echo(json_encode($row));//数据库未连接

    }
}
echo(json_encode($row));

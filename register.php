<?php
date_default_timezone_set('Asia/Shanghai');
header("Content-Type: text/html;charset=utf-8");
header('Access-Control-Allow-Origin:*');
$ro =file_get_contents('php://input');
$user=json_decode($ro,true);
$myusername=$user['username'];
$mypassword=$user['password'];
$mynickname=$user['nickname'];
$myemail=$user['email'];
$mybirthday=$user['birthday'];
$mysex=$user['sex'];
$myprovince=$user['province'];
$mycity=$user['city'];
$myarea=$user['area'];
$address=$myprovince.$mycity.$myarea;
$birth = str_replace("T16:00:00.000Z","",$mybirthday);////转换传入后端的日期格式,使其符合数据库字段类型
$birth = date("Y-m-d",strtotime("+1 day",strtotime($birth)));//日期+1，因为之前的日期格式直接转换成字符串会少一天

//检查输入是否符合规范

//验证用户名
function preg_username($username){
    if(preg_match("/^[a-zA-Z][a-zA-Z0-9_]{3,19}/",$username)){
        return true;
    }else{
        return false;
    }
}


//验证昵称
function checknicknam($nickname){
    $length = strlen($nickname);
    if($nickname!=''&&$length>=4&&$length<=20){
        return true;
    }else{
        return false;
    }
}

//验证密码
function checkpassword($password){
    $length = strlen($password);
    if($length>=6&&$length<=27){
        return true;
    }else{
        return false;
    }
}

//验证性别
function checksex($sex){
    if($sex!=''){
        return true;
    }else{
        return false;
    }
}


//验证生日
function checkbirth($birth){
    if($birth!=''){
        return true;
    }else{
        return  false;
    }
}

//验证所在地
function checklocation($location){
    if($location!=''){
        return true;
    }else{
        return  false;
    }
}

//验证邮箱
function  preg_email($mail){
    if(preg_match("/^[a-z0-9A-Z]+[- | a-z0-9A-Z . _]+@([a-z0-9A-Z]+(-[a-z0-9A-Z]+)?\\.)+[a-z]{2,}$/",$mail)){
        return true;
    }else {
        return false;
    }
}

if(preg_username($myusername)&&checknicknam($mynickname)&&checkpassword($mypassword)&&checkbirth($mybirthday)&&checklocation($address)&&preg_email($myemail)) {
    $created=date("Y-m-d H:i:s");

    $con = mysqli_connect("localhost", "root", "", "users");
    if ($con) {
        $mypassword = md5($mypassword);
        mysqli_select_db($con, "users");
        $sql1 = "SELECT * FROM `users` WHERE `users---username` = '$myusername'";//检查用户名是否已经注册
        $check = mysqli_query($con, $sql1);//执行mysql语句
        $ch = mysqli_num_rows($check);//返回结果集的函数，若存在则返回1（一行结果集）
        mysqli_free_result($check);//释放查询结果内存
        ////判断查询结果集，将结果返回给前端
        if ($ch != 0) {
            //数据库中查询到注册时填写的用户名，注册失败
            $row['status'] = "0";
            $row['err'] = "fail";
        } else {
            //数据库中没有重复用户名
            $result = mysqli_query($con, "SELECT `users---id` FROM `users` WHERE `users---id` = (select max(`users---id`)from `users`)");
            $re = mysqli_fetch_array($result);
            $userid = $re['users---id'] + 1;

            $sql = "INSERT INTO `users` (`users---id`, `users---username`, `users---password`, `users---nickname`, `users---head`, `users---email`, `users---birthday`, `users---sex`, `user--address`, `users---last_login_at`, `users---updated_at`, `users---created_at`) 
VALUES ('$userid', '$myusername', '$mypassword', '$mynickname', NULL , '$myemail', '$birth', '$mysex','$address', NULL, NULL, '$created');";
            mysqli_query($con, $sql);
            $row['status'] = "1";
            $row['err'] = "0";
        }
        echo(json_encode($row));
        mysqli_close($con);//关闭数据库

    }else{
        $row['status']='3';
        $row['err']='false';
        echo(json_encode($row));//数据库未连接

    }
}else{
    $row['status'] = '2' ;
    $row['err'] = 'fail';
    echo(json_encode($row));//后台数据校验不通过
}

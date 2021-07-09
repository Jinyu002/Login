<?php
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
$address1="江苏江苏江苏";
$con = mysqli_connect("localhost","root","","users");
//检查输入是否符合规范
//checkName() {
//    //焦点消失 判断用户名输入值
//      var regula = new RegExp("^[a-zA-Z][a-zA-Z0-9_]{3,19}");
//      if (regula.test(this.inputName)) {
//          this.messageName = '';
//          //合法
//      } else {
//          //不合法
//          console.log('不合法');
//          //不合法红色警告符
//          this.messageName =
//              '!请输入4-20字符，首字母为英文，由字母、数字、下划线组成';
//      }
//    },
//
//    checkNickname() {
//      var nick = this.inputNick;
//      var name = this.inputName;
//      if (nick === '') {
//          nick = name;
//          this.inputNick = this.inputName;
//      }
//      if (nick.length < 4) {
//          console.log('不合法');
//          this.messageNick = '昵称太短了';
//      } else {
//          this.messageNick = '';
//      }
//    },
//
//    checkKey() {
//      var key = this.inputKey;
//      var confirm = this.inputConfirm;
//      if (key === '') {
//          console.log('不合法');
//          this.messageKey = '!请输入密码';
//      } else {
//          this.messageKey = '';
//      }
//      if (key.length < 6) {
//          console.log('不合法');
//          this.messageKey = '!密码太短了';
//      } else {
//          this.messageKey = '';
//      }
//
//      if (confirm != key) {
//          console.log('不合法');
//          this.messageConfirm = '!两次密码不一致';
//      } else {
//          this.messageConfirm = '';
//      }
//    },
//
//    checkConfirm() {
//      var key = this.inputKey;
//      var confirm = this.inputConfirm;
//      if (confirm != key) {
//          console.log('不合法');
//          this.messageConfirm = '!两次密码不一致';
//      } else {
//          this.messageConfirm = '';
//      }
//    },
//
//    checkProcince(a) {
//    console.log(a)
//      this.province=a.value
//      this.messageprovince='1'
//
//    },
//
//     checkCity(a) {
//     console.log(a)
//      this.city=a.value
//      this.messagecity='1'
//    },
//
//     checkArea(a) {
//     console.log(a)
//      this.area=a.value
//      this.messagearea='1'
//    },
//
//   checkMail() {
//      var regula = new RegExp(
//    "^[a-z0-9A-Z]+[- | a-z0-9A-Z . _]+@([a-z0-9A-Z]+(-[a-z0-9A-Z]+)?\\.)+[a-z]{2,}$"
//);
//      if (regula.test(this.inputMail)) {
//          //合法
//          this.messageMail='';
//      } else {
//          console.log('不合法');
//          this.messageMail = '请输入正确的邮箱格式';
//          return false;
//      }
//    },

if($con) {
    mysqli_select_db($con, "users");
    $sql1 = "SELECT * FROM `users` WHERE `users---username` = '$myusername'";
    $check = mysqli_query($con, $sql1);
    $ch = mysqli_num_rows(check);
    mysqli_free_result($check);
    if ($ch != 0) {
        $row['status'] = "0";
        $row['err'] = "fail";
    } else {
        $result = mysqli_query($con, "SELECT `users---id` FROM `users` WHERE `users---id` = (select max(`users---id`)from `users`)");
        $re = mysqli_fetch_array($result);
        $userid = $re['users---id'] + 1;

        $sql = "INSERT INTO `users` (`users---id`, `users---username`, `users---password`, `users---nickname`, `users---head`, `users---email`, `users---birthday`, `users---sex`, `user--address`, `users---last_login_at`, `users---updated_at`, `users---created_at`) 
VALUES ('$userid', '$myusername', '$mypassword', '$mynickname', NULL, '$myemail', NULL , '$mysex','$address1', NULL, NULL, NULL);";
        mysqli_query($con, $sql);
        $row['status']="0";
        $row['err'] = "0";
    }
    echo(json_encode($row));
    mysqli_close($con);

}

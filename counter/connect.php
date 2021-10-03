<?php
$server = "sql113.byethost7.com"; // host server
$username= "b7_20734500"; // tên truy cập MySQL
$password = "random"; // mật khảu truy cập MySQL
$connectserver = mysql_connect($server, $username, $password);
mysql_select_db("b7_20734500_presetconverter");
mysql_set_charset('utf8',$connectserver);

if ( !$connectserver )
{
    die("không nết nối được vào MySQL server"); //Thông báo không kết nối được
}
?>

<?php
$timezone  = +7; //(GMT +7:00)  
$time_now = time();    // lưu thời gian hiện tại
$time_out = 60; // khoảng thời gian chờ để tính một kết nối mới (tính bằng giây)
$ip_address = $_SERVER['REMOTE_ADDR'];    // lưu lại IP của kết nối


require_once('counter/connect.php');    // nhúng file kết nối CSDL vào


if (!mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE UNIX_TIMESTAMP(`last_visit`) + $time_out > $time_now AND `ip_address` = '$ip_address'")))
    mysql_query("INSERT INTO `counter` VALUES ('$ip_address', NOW())");


$online = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE UNIX_TIMESTAMP(`last_visit`) + $time_out > $time_now"));


$day = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE DAYOFYEAR(`last_visit`) = " . (date('z') + 1) . " AND YEAR(`last_visit`) = " . date('Y')));


$week = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE WEEKOFYEAR(`last_visit`) = " . date('W') . " AND YEAR(`last_visit`) = " . date('Y')));


$month = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE MONTH(`last_visit`) = " . date('n') . " AND YEAR(`last_visit`) = " . date('Y')));


$year = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter` WHERE YEAR(`last_visit`) = " . date('Y')));


$visit = mysql_num_rows(mysql_query("SELECT `ip_address` FROM `counter`"));

function counter(){
    global $day, $week, $month, $visit;
    return array($day, $week, $month, $visit);
}
?>

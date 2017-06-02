<?php 
//正常情况是只统计到当前日期，，这里写的是统计整个月的星期情况，
//实际项目还得微调下
$cur_day = date('j', time());
$cur_week = date('w', time());//5

//方式1，只算当前月的星期，不能超过本月
$time = time();
$time = strtotime('2017-2');

$cur_year = date('Y', $time);
$cur_month = date('m', $time);
$date_format = $cur_year . '-' . $cur_month;

//$total_month_day = cal_days_in_month(CAL_GREGORIAN, $cur_month, $cur_year);//貌似这个方法需要--enable-calendar
$total_month_day = date('t', $cur_year. '-' . $cur_month);

$start_week = date('w', strtotime($date_format . '-01'));
if($start_week == 0){
    $start_week = 7;//直接把周末的0 变成7 ，更容易计算
}

//第一周
$i = 1;
$diff = 7 - $start_week;
$next_day = $i + $diff;
$week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $next_day;
$i = $next_day + 1;

while(($i + 6) <= $total_month_day){
    $next_day = $i + 6;
    $week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $next_day;
    $i = $next_day + 1;
}
$week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $total_month_day;//拼接最后一个日子
var_dump($week_arr);die;

//方式2，计算连接的星期，超出也算
//第一周
//上月的总共天数，考虑跨年的情况
//
unset($week_arr);

if($cur_month == 1){
    $tmp_cur_month = 12;
    $tmp_cur_year = $cur_year - 1;
}else{
    $tmp_cur_month = $cur_month - 1;
    $tmp_cur_year = $cur_year;
}
$total_last_month_day = cal_days_in_month(CAL_GREGORIAN, $tmp_cur_month, $tmp_cur_year);
$diff = $start_week - 2;
if($diff){
    $tmp =  $total_last_month_day - $diff;
    $first_start_date = $tmp_cur_year . '-' . $tmp_cur_month . '-' . $tmp;
}

$i = 1;
$diff = 7 - $start_week;
$next_day = $i + $diff;
if($first_start_date){
    $week_arr[] = $first_start_date . ' ~ ' . $date_format . '-' . $next_day;
}else{
    $week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $next_day;
}
$i = $next_day + 1;

while(($i + 6) <= $total_month_day){
    $next_day = $i + 6;
    $week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $next_day;
    $i = $next_day + 1;
}

//最后一个礼拜
$last_week = date('w', strtotime($date_format . '-' . $total_month_day));
$diff = 7 - $last_week;
if($diff){
    if($cur_month == 12){
        $tmp_cur_month = 1;
        $tmp_cur_year = $cur_year + 1;
    }else{
        $tmp_cur_month = $cur_month + 1;
        $tmp_cur_year = $cur_year;
    }
    $week_arr[] = $date_format . '-' . $i . ' ~ ' . $tmp_cur_year . '-' . $tmp_cur_month . '-' . $diff;
}else{
    $week_arr[] = $date_format . '-' . $i . ' ~ ' . $date_format . '-' . $total_month_day;//拼接最后一个日子
}
var_dump($week_arr);die;

die;

<?php
var_dump(getWeek(strtotime('2017-2-16'), true));
var_dump(getWeek(strtotime('2017-2-16'), false));

/**
 * 计算一个月中所有星期的开始和结束，可配置成截至到当前
 * @Author   zhongzs
 * @DateTime 2017-06-05T16:26:41+0800
 * @param    当前日期的时间戳                   $timestamp    可定位到当前月与日
 * @param    boolean                  $end_to_today 是否截至到今日
 * @return   4个成员的数组，开始xxxx-xx-xx，结束yyyy-yy-yy', 时间戳1, 时间戳2
 */
function getWeek($timestamp, $end_to_today = true)
{
	$cur_month = date('Y-m', $timestamp);
	if($end_to_today){
		$cur_day = date('j', $timestamp);//不含前导零的当前日期
	}else{
		$cur_day = date('t', $timestamp);//当前月的总天数
	}

	$i=1;
	$continue = true;
	while($i <= $cur_day && $continue) { 
		$tmp = $cur_month . '-' . $i;
		$today_week = date('N', strtotime($tmp));
		$before_stamp = strtotime($tmp . ' -' . ($today_week - 1) . ' day');
		$after_stamp = strtotime($tmp . ' 23:59:59 +' . (7 - $today_week) . ' day');
		if($end_to_today){
			if($after_stamp >= $timestamp){
				$after_stamp = strtotime(date('Y-m-d', $timestamp)) + 86399;
				$continue = false;//不再参与下一个循环
			}
		}
		
		$i = $i + 8 - $today_week;// $i + 7 - $today_week + 1
		
		$return_arr[] = array(
			date('Y-m-d H:i:s', $before_stamp),
			date('Y-m-d H:i:s', $after_stamp),//截至日期加上了 23:59:59
			$before_stamp,
			$after_stamp
			);
	}
	return $return_arr;
}

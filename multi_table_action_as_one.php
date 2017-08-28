<?php
//表1 表2 表3 连表输出
$len1 + $len2 + $len3

//可以剥离 $com_sql 便于复用查询 总条数 和 具体结果
$total_len = $len1 + $len2 + $len3;
$next_allow = false;//是否能执行下一步
$next_step = '';

$result = '';//结果集

if($offset > $len1){
	$offset -= $len1;//直接 -= 去运算，下一个阶段可以复用
	if($offset > $len2){
		//省略一步,没有数据，直接返回
		return json_encode(['total'=>0, 'rows'=>'']);
	}else{
		$next_step = 'x2';
	}
}else{
	$next_step = 'x1';
}

if($next_step == 'x1'){
	$result = M()->offset->limit;//第一部可以用result
	$limit -= count($result);
	
	if($limit){
		$offset = 0;//除了第一阶段需要 $offset 到了下一个阶段都是从0开始
		$next_allow = true;
	}
}
if($next_step == 'x2' || $next_allow){
	$tmp = M()->offset->limit;
	//合并$tmp 到 $result
	$limit -= count($tmp);//是$tmp 而不是$result
	
	if($limit){
		$offset = 0;
		$next_step = true;
	}
}
if($next_step == 'x3' || $next_allow){
	$result = M()->offset->limit;
	//合并$tmp 到 $result
}
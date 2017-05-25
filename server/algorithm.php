<?php

// 冒泡排序法
function bubbleSort(&$arr) {
	$temp = null;

	$flag = false;
	for($i=0, $icount=count($arr); $i < $icount -1; $i++) {

		for($j=0, $jcount=count($arr); $j < $jcount - 1 - $i; $j++) {
			//从小到大排序
			if($arr[$j] > $arr[$j + 1]) {
				$temp = $arr[$j + 1];
				$arr[$j + 1] = $arr[$j];
				$arr[$j] = $temp;
				$flag = true;
			}
		}

		if(!$flag) {
			break;
		}

		$flag = false;
	}
}

// 选择排序法
function selectSort(&$arr) {
	$temp = null;
	for($i=0, $count=count($arr); $i < $count; $i++) {
		$minIndex = $i;
		$minValue = $arr[$i];
		for($j=$i+1; $j < $count; $j++) {
			if($minValue > $arr[$j]) {
				$minValue = $arr[$j];
				$minIndex = $j;
			}
		}

		$temp = $arr[$i];
		$arr[$i] = $arr[$minIndex];
		$arr[$minIndex] = $temp;
	}
}

// 插入排序法
function insertSort(&$arr) {
	$temp = null;
	for($i=0, $len=count($arr); $i < $len; $i++) {
		$temp = $arr[$i];

		for($j=$i-1; $j >= 0; $j--) {
			if($temp < $arr[$j]) {
				$arr[$j + 1] = $arr[$j];
				$arr[$j] = $temp;
			} else {
				break;
			}
		}
	}
}

// 快速排序法

function quickSort(&$arr) {
	if(empty($arr) || count($arr) === 1) {
		return $arr;
	}

	$mid = $arr[0];
	$l = $r = [];

	foreach($arr as $v) {
		if($v < $mid) {
			$l[] = $v;
		}elseif($v > $mid) {
			$r[] = $v;
		}
	}

	$l = quickSort($l);
	$l[] = $mid;
	$r = quickSort($r);
	return array_merge($l, $r);
}

//二分查找 数组必须有序
function binarySearch(&$arr, $findValue, $leftIndex, $rightIndex) {
	if($leftIndex > $rightIndex) {
		return;
	}

	$middleIndex = round(($leftIndex + $rightIndex) / 2);
	if($findValue > $arr[$middleIndex]) {
		binarySearch($arr, $findValue, $middleIndex + 1, $rightIndex);
	} else if($findValue < $arr[$middleIndex]) {
		binarySearch($arr, $findValue, $leftIndex, $middleIndex -1);
	} else {
		echo "value exist, indexValue: $middleIndex";
	}
}

$array = [9, 3, 1, 7, 10, 2, 1, 0, 6, 8, 15];
// echo 'sort:';
// print_r($array);
// selectSort($array);
// echo PHP_EOL;
// print_r($array);


insertSort($array);
$rightIndex = count($array) - 1;
print_r($array);
binarySearch($array, 8, 0, $rightIndex);



// 自动加载
spl_autoload_register(function($class) {
	$prefix = 'Foo\\Bar\\';
	$base_dir = __DIR__ . '/src/';

	$len = strlen($prefix);

	if(strncmp($prefix, $class, $len) !== 0) {
		return;
	}

	$relative_class = substr($class, $len);
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	if(file_exists($file)) {
		require $file;
	}
});
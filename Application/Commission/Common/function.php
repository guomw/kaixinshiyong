<?php
/*多维数组排序
 $multi_array:多维数组名称
$sort_key:二维数组的键名
$sort:排序常量	SORT_ASC || SORT_DESC
*/
function multi_array_sort($multi_array, $sort_key, $sort = 'SORT_DESC') {
	if (is_array($multi_array)) {
		$key_array = array();
		foreach ($multi_array as $row_array) {
			if (is_array($row_array)) {
				$key_array[] = $row_array[$sort_key];
			} else {
				return FALSE;
			}
		}
	} else {
		return FALSE;
	}
	array_multisort($key_array, $sort, $multi_array);
	return $multi_array;
}
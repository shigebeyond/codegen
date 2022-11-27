<?php
/**
 * 渲染多个选项
 * @param $options 选项的数组, key是选项键, value是选项名, 如 ['' => '选择性别', 1 => '男', 2 => '女']
 * @param $curr_key 当前值, 如 $_GET['sex']
 * @return string
 */
// if (!function_exists('render_options')) {
    function render_options($options, $curr_key='')
    {
        $html = "";
        foreach ($options as $key => $name) {
            $key = is_numeric($key) ? (int)$key : $key;
            $curr_key = is_numeric($curr_key) ? (int)$curr_key : $curr_key;
            $selected = $curr_key === $key ? 'selected' : '';//解决All == 0 的情况
            $html .= "<option value='$key' $selected>$name</option>";
        }
        return $html;
    };
//}

/**
 * 最后一个匹配字符串之前的字符串
 *   如 a.b.c, 返回  a.b
 * @param $str
 * @param $before
 * @return bool|string
 */
// if (!function_exists('substr_before_last')) {
    function substr_before_last($str, $before)
    {
        $i = strrpos($str, $before);
        if ($i === false)
            return false;

        return substr($str, 0, $i);
    };
//}
<?php
/**
 * 渲染多个选项
 * @param $options 选项的数组, key是选项键, value是选项名, 如 ['' => '选择性别', 1 => '男', 2 => '女']
 * @param $curr_key 当前值, 如 $_GET['sex']
 * @return string
 */
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
}

/**
 * 最后一个匹配字符串之前的字符串
 *   如 a.b.c, 返回  a.b
 * @param $str
 * @param $before
 * @return bool|string
 */
function substr_before_last($str, $before)
{
    $i = strrpos($str, $before);
    if ($i === false)
        return false;

    return substr($str, 0, $i);
}


/**
* 下划线转驼峰
* 思路:step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
*     step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
*/
function camelize($uncamelized_words,$separator='_')
{
    $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
}

/**
* 驼峰命名转下划线命名
* 思路:小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
*/
function uncamelize($camelCaps,$separator='_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

/**
 * 判断 $str 是否以 $substr 开头
 *
 * @param string $str 被找的字符串
 * @param string $substr 要找的子字符串
 * @return boolean
 */
function start_with($str, $substr)
{
    return strpos($str, $substr) === 0; // pos为0
}

/**
 * 判断 $str 是否以 $substr 结尾
 *
 * @param string $str 被找的字符串
 * @param string $substr 要找的子字符串
 * @return boolean
 */
function end_with($str, $substr)
{
    $end_pos = strlen($str) - strlen($substr);
    return strpos($str, $substr) === $end_pos; // pos为$end_pos
}
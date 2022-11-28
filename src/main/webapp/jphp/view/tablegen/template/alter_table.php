ALTER TABLE `<?= $controller->db->getTablePrefix() . $table_name ?>`
COMMENT '<?=$table_comment?>'
<?php
//修改字段
$db_fields_name = $db_fields ? array_column($db_fields, null, 'name') : [];
$n = count($fields);
$i = 0;
foreach ($fields as $field) {
    //不存在字段则添加字段
    $col_sql = $field['name'] . ' ' . $field['type'] . ' ' . $field['is_null'] . ' ' . $field['default'] . $field['comment'];
    if (!isset($db_fields_name[$field['name']])) {
        /**
         * ALTER TABLE `ziyoukang_2.0_dev`.`xxx`
         * ADD COLUMN `vsa` varchar(255) NULL AFTER `sdfs`,
         * ADD COLUMN `ggg` varchar(255) NULL AFTER `vsa`;
         */
        $col_sql = ' ADD ' . $col_sql;
    } else {
        /**
         * ALTER TABLE `ziyoukang_2.0_dev`.`xxx`
         * MODIFY COLUMN `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'sdf' FIRST,
         * MODIFY COLUMN `age` int(11) NULL DEFAULT NULL COMMENT 'ccs' AFTER `name`,
         * MODIFY COLUMN `duable` double(12, 2) NOT NULL COMMENT 'bb' AFTER `age`;
         */
        //判断 is_null，default，type，comment
        $is_null = $db_fields_name[$field['name']]['is_null'] !== $field['is_null_orgin'];
        $default = $db_fields_name[$field['name']]['default'] !== $field['default_orgin'];
        $type = $db_fields_name[$field['name']]['type'] !== $field['type'];
        $comment = $db_fields_name[$field['name']]['label'] !== $field['comment_orgin'];
        if ($is_null || $default || $type || $comment) {
            $col_sql = " MODIFY COLUMN " . $col_sql;
        }
    }
    echo $col_sql;
    if(++$i < $n)
        echo ",\n";
}?>;
CREATE TABLE `<?= $controller->db->getTablePrefix() . $table_name ?>` (
<?php
$key = ''; // 主键字段
$n = count($fields);
foreach ($fields as $i => $v){
    $auto_incr = '';
    if(!empty($v['key'])) { // 做主键
        $key = $v['name'];
        $auto_incr = 'AUTO_INCREMENT'; // 自增
    }
    echo "`{$v['name']}` {$v['type']} {$v['is_null']} {$auto_incr} {$v['default']} {$v['comment']}";
    if ($i + 1 < $n || !empty($key))
        echo ",\n";
} ?>
<?php if (!empty($key)): ?>
    PRIMARY KEY (`<?= $key ?>`)
<?php endif; ?>
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='<?= $table_comment ?>'
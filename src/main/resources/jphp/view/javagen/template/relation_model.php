
    protected $<?=$relation;?> = array(
    <?php foreach ($list as $col):?>
        '<?=$col['slave_table']?>' => array(      // 别名，用于访问关联模型
                'model' => '<?=$col['slave_table']?>_model', // 模型：拥有哪个模型
                'foreign_key' => '<?=$col['foreign_key']?>', // 外键
                'key' => '<?=$col['key']?>'
              ),
        <?php endforeach; ?>
);
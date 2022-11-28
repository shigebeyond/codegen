//--codegen_start-model-col
<?php foreach ($db_columns as $col){?>
    public var <?=camelize($col['name'])?>:<?=$col['java_type'].' '?> by property();

<?php }?>
//--codegen_end-model-col
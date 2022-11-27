//--codegen_start-model-col
<?php foreach ($columns as $col){?>

    public var <?=$col['name']?>:<?=$col['type']?> by property();

    <?php }}?>//--codegen_end-model-col
}
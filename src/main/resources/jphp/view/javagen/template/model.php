package net.jkcode.jkmvc.tests.model

import net.jkcode.jkmvc.orm.Orm
import net.jkcode.jkmvc.orm.OrmMeta

/**
 * <?=$table_label?>模型
 */
class <?=ucfirst($model)?>Model(id:Int? = null): Orm(id) {

    companion object m: OrmMeta(UserModel::class, "<?=$table_label?>", "<?=$model?>"){
        init {
            addRule("name", "姓名", "notEmpty")
            addRule("age", "年龄", "between(1,120)");
        }

        //--codegen_start-listModel
        <?php foreach ($columns as $col){?>
            <?php if($col['type'] == 'select'){?>

            /**
             * <?=$col['label']?> -- 选项
             */
            public fun <?=$col['name']?>Options(): Map<String, String?>
            {
                // key是选项值, value是选项名
                return emptyMap();
            }

            <?php }}?>//--codegen_end-listModel
    }

    <?=$cols_code?>
}
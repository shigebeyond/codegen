package net.jkcode.jkmvc.tests.model

import net.jkcode.jkmvc.orm.Orm
import net.jkcode.jkmvc.orm.OrmMeta

/**
 * <?=$table_label?>模型
 */
class <?=ucfirst($model)?>Model(id:Int? = null): Orm(id) {

    companion object m: OrmMeta(<?=ucfirst($model)?>Model::class, "<?=$table_label?>", "<?=$model?>", "<?=$pk?>"){}

<?=$cols_code?>
}
<?php $model = ucfirst($model); ?>
package net.jkcode.jkmvc.tests.controller

import net.jkcode.jkmvc.http.controller.Controller

/**
* <?=$table_label?> API控制器
*/
class <?=$model?>Controller: Controller()
{
    /**
     * 列表
     */
    public fun index()
    {
        val query: OrmQueryBuilder = <?=$model?>Model.queryBuilder()
        // 统计个数
        val counter:OrmQueryBuilder = query.clone() as OrmQueryBuilder // 复制query builder
        val count = counter.count()
        // 查询所有
        val items = query.findModels<<?=$model?>Model>()
        res.renderJson(0, null, mapOf("count" to count, "items" to items))
    }

    /**
     * 详情
     */
    public fun detail()
    {
        val id:Int? = req["id"]
        // 查询单个
        val item = <?=$model?>Model(id)
        if(!item.isLoaded()){
            res.renderJson(1, "<?=$table_label?>[$id]不存在")
            return
        }
        res.renderJson(0, null, item)
    }

    /**
     * 新建
     */
    public fun new()
    {
        // 新建
        if(req.isPost){
            val item = <?=$model?>Model()
            item.fromRequest(req)
            item.create()
            res.renderJson(0, "创建<?=$table_label?>成功")
        }
    }

    /**
     * 编辑
     */
    public fun edit()
    {
        // 查询单个
        val id: Int = req["id"]!!
        val item = <?=$model?>Model(id)
        if(!item.isLoaded()){
            res.renderJson(1, "<?=$table_label?>[" + id + "]不存在")
            return
        }
        // 编辑
        if(req.isPost){
            item.fromRequest(req)
            item.update()
            res.renderJson(0, "修改<?=$table_label?>成功")
        }
    }

    /**
     * 删除
     */
    public fun delete()
    {
        val id:Int? = req["id"]
        // 查询单个
        val item = <?=$model?>Model(id)
        if(!item.isLoaded()){
            res.renderJson(1, "<?=$table_label?>[" + id + "]不存在")
            return
        }
        // 删除
        item.delete();
        res.renderJson(0, "删除<?=$table_label?>成功")
    }
}
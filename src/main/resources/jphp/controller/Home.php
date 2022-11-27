<?php
use php\jkmvc\http\IController;
use php\jkmvc\http\HttpRequest;

class Home extends IController{

    public static $menus = [
        ['title' => '建表改表', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '建表改表', 'icon' => '', 'href' => '/$tablegen/gen_table'],
            ]
        ],
        ['title' => 'Java代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '模型生成', 'icon' => '', 'href' => '/$javagen/gen_model'],
                ['title' => '模型关联关系生成', 'icon' => '', 'href' => '/$javagen/gen_model_relation'],
                ['title' => 'api生成', 'icon' => '', 'href' => '/$javagen/gen_api'],
            ]
        ],
        ['title' => 'Php代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '模型生成', 'icon' => '', 'href' => '/$phpgen/gen_model'],
                ['title' => '模型关联关系生成', 'icon' => '', 'href' => '/$phpgen/gen_model_relation'],
                ['title' => '列表页生成', 'icon' => '', 'href' => '/$phpgen/gen_list'],
                ['title' => '表单或详情页生成', 'icon' => '', 'href' => '/$phpgen/gen_form'],
                ['title' => 'api和testapi生成', 'icon' => '', 'href' => '/$phpgen/gen_api'],
            ]
        ],
        ['title' => 'Uniapp代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => 'uniapp列表页生成', 'icon' => '', 'href' => '/$uniappgen/gen_app_list?type=app'],
                ['title' => 'uniapp详情页生成', 'icon' => '', 'href' => '/$uniappgen/gen_app_detail?type=app'],
                ['title' => 'uniapp表单页生成', 'icon' => '', 'href' => '/$uniappgen/gen_app_form?type=app'],
            ]
        ],
        ['title' => '小程序代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '小程序列表页生成', 'icon' => '', 'href' => '/$wechatgen/gen_app_list?type=wx'],
                ['title' => '小程序详情页生成', 'icon' => '', 'href' => '/$wechatgen/gen_app_detail?type=wx'],
                ['title' => '小程序表单页生成', 'icon' => '', 'href' => '/$wechatgen/gen_app_form?type=wx'],
            ]
        ],
    ];

    /**
     * 菜单
     */
    public function menu()
    {
        echo json_encode(self::$menus);
    }

    /**
     * 操作入口
     */
    public function index(){
        $data = ['domain_static' => 'http://static.jym0.com/'];
        //$this->view('index-old', $data);
        $this->view('index', $data);
    }

}
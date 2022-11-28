<?php
use php\jkmvc\http\IController;
use php\jkmvc\http\HttpRequest;
use php\lang\Log;

class Home extends IController{

    public static $menus = [
        ['title' => '建表改表', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '建表改表', 'icon' => '', 'href' => '/$tablegen/gen_table'],
            ]
        ],
        ['title' => 'Java代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => 'java模型生成', 'icon' => '', 'href' => '/$javagen/gen_model'],
                ['title' => 'java模型关联关系生成', 'icon' => '', 'href' => '/$javagen/gen_model_relation'],
                ['title' => 'java api生成', 'icon' => '', 'href' => '/$javagen/gen_api'],
            ]
        ],
        ['title' => 'Php代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => 'php模型生成', 'icon' => '', 'href' => '/$phpgen/gen_model'],
                ['title' => 'php模型关联关系生成', 'icon' => '', 'href' => '/$phpgen/gen_model_relation'],
                ['title' => 'php列表页生成', 'icon' => '', 'href' => '/$phpgen/gen_list'],
                ['title' => 'php表单或详情页生成', 'icon' => '', 'href' => '/$phpgen/gen_form'],
                ['title' => 'php api和testapi生成', 'icon' => '', 'href' => '/$phpgen/gen_api'],
            ]
        ],
        ['title' => 'Uniapp代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => 'uniapp列表页生成', 'icon' => '', 'href' => '/$appgen/gen_app_list?type=app'],
                ['title' => 'uniapp详情页生成', 'icon' => '', 'href' => '/$appgen/gen_app_detail?type=app'],
                ['title' => 'uniapp表单页生成', 'icon' => '', 'href' => '/$appgen/gen_app_form?type=app'],
            ]
        ],
        ['title' => '小程序代码生成', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '小程序列表页生成', 'icon' => '', 'href' => '/$appgen/gen_app_list?type=wx'],
                ['title' => '小程序详情页生成', 'icon' => '', 'href' => '/$appgen/gen_app_detail?type=wx'],
                ['title' => '小程序表单页生成', 'icon' => '', 'href' => '/$appgen/gen_app_form?type=wx'],
            ]
        ],
        ['title' => '查看代码', 'icon' => '&#xe613;', 'spread' => false, 'children' =>
            [
                ['title' => '查看代码', 'icon' => '', 'href' => '/$home/view_code'],
            ]
        ]
    ];

    /**
     * 菜单
     */
    public function menu()
    {
        $this->res->renderText(json_encode(self::$menus));
    }

    /**
     * 渲染视图：添加域名
     * @param $file
     * @param null $data
     * @param false $is_return
     * @return mixed
     */
    public function view($file, $data = NULL, $is_return = FALSE){
        $domains = ['domain_static' => 'http://static.jym0.com/'];
        if($data === NULL)
            $data = $domains;
        else
            $data += $domains;
        return parent::view($file, $data, $is_return);
    }

    /**
     * 操作入口
     */
    public function index(){
        //$this->view('index-old');
        $this->view('index');
    }

    /**
     * 查看生成的代码
     */
    public function view_code()
    {
        $files = [];
        $dir = rtrim(CODEPATH, '/');
        $this->get_files($dir, $files);
        $this->view('view_code', ['files' => $files]);
    }

    /**
     * 递归收集目录下的文件
     * @param string $dir
     * @return array
     */
    protected function get_files(string $dir, array &$files)
    {
        $fs = scandir($dir);
        foreach ($fs as $f) {
            if (strpos($f, '.') === 0)
                continue;

            $path = $dir.'/'.$f;

            if(is_dir($path)){ // 子目录递归调用
                $this->get_files($path, $files);
            }else{ // 收集文件
                //Log::info("收集文件: " . $path);
                if(end_with($path, '.zip'))
                    $content = '压缩文件不支持查看内容';
                else
                    $content = file_get_contents($path);
                $files[] = [
                    'title' => str_replace(CODEPATH, '', $path),
                    'content' => $content,
                ];
            }
        }
    }

    /**
     * 下载文件
     * @param $path 本地文件路径
     */
    function download(){
        $path = CODEPATH . $this->req->param('path');
        $this->res->renderFile($path);
    }
}
laravel-admin 插件 wangEditor V4 版本富文本编辑器

======
laravel-admin extension

wangEditor 5 富文本编辑器插件  beta 版

https://github.com/jonexyz/wang-editor-v5

======

首先声明，此插件是参照 https://github.com/laravel-admin-extensions/wangEditor 进行修改而来

其次，在原有基础上做了些改进，同一页面支持多个富文本编辑器，且可单独配置改富文本编辑器的配置项。

laravel-admin 插件的富文本编辑器插件本人相中了 wangEditor ，但是相关的插件好像许久都没人维护了，奈何就自己动手吧，根据 wangEditor 的插件的源码与 laravel-admin 中扩展开发的文档，完成了此V4版本的 wangEditor 富文本编辑器插件。

V4版本与V3相比，还是好用很多了。

---
使用方法，此插件仅使用与 laravel-admin 1.* 版本

1.安装扩展
composer require jonexyz/wang-editor-v4

2.发布静态资源包
php artisan vendor:publish --tag=jonexyz-wangEditorV4

3.配置富文本编辑器配置，参考如下
其中 `default` 表示默认配置，
`title` 表示对字段为 `title` 的表单进行单独的富文本编辑器设置，


```
'extensions' => [

        'wang-editor-v4' => [
            'enable' => true,
            'config' => [
                'default'=>[
                    'uploadImgServer' => '/'.env('ADMIN_ROUTE_PREFIX', 'admin').'/upload',
                    'uploadImgMaxSize' => 3 * 1024 * 1024, // 将图片大小限制为 3M
                    'uploadFileName' => 'images[]', //定义上传的filename，即上传图片的名称
                    'height'=>500
                ],
                'title'=>[
                    'height'=>100,
                    'menus' => [
                        'head',
                        'bold',
                        'fontSize',

                    ]
                ]
            ]
        ]
    ]
```
4.修改 `\app\Admin\bootstrap.php` 中设置删除数组中的 `editor` 字段

5.在form表单中使用它：
```
$form->editor('content');
```
   
6.图片上传默认使用base64格式化后与文本内容一起存入数据库，如果要上传图片到本地接口，可参照如下代码，另还需设置插件上传路径参数 `uploadImgServer`
```
<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Encore\Admin\Controllers\AdminController;

class CommonController extends AdminController
{
    public function upload(Request $request)
    {
        $type = $request->get('type');

        $dir_path = 'uploads/' . config('admin.upload.directory.image'); // 文件存储路径
        $rule = ['jpg', 'png', 'gif']; //允许的图片后缀


        if ($request->hasFile('images')) {

            $files = $request->file('images'); //接前台图片

            $path = [];
            foreach ($files as $file) {

                $clientName = $file->getClientOriginalName();
                // $tmpName = $file->getFileName();
                // $realPath = $file->getRealPath();
                $size = $file->getSize();
                $entension = $file->getClientOriginalExtension();
                if (!in_array($entension, $rule)) {
                    continue;
                }
                $newName = md5(date("Y-m-d H:i:s") . $clientName) . "." . $entension;
                $path[] = [
                    'path' => $file->move($dir_path, $newName),
                    'file_name' => $clientName,
                    'size' => $size
                ];
                //$namePath = $url_path . '/' . $newName;
                //return $path;

            }

            $insert_data = [];
            foreach ($path as $p) {
                $file_path = str_replace("\\", "/", $p['path']->getPathname());
                $data[] = config('APP.URL') . '/' . $file_path;
                $insert_data[] = [
                    'file_name' => $p['file_name'],
                    'path' => $file_path,
                    'size' => $p['size'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }


            return $res = ['errno' => 0, 'data' => $data];
        }

    }
}

```
**水平有限，如有不当支持见谅。**

![avatar](https://tva1.sinaimg.cn/large/b6559090gy1gskhbb3m7ij21gb0o3dgy.jpg)

<?php

namespace Jonexyz\wangEditorV4;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected $view = 'jonexyz-wangEditorV4::editor';

    protected static $js = [
        'vendor/jonexyz/wang-editor-v4/wangeditor-4.7.5/wangEditor.js',
    ];

    public function render()
    {
        $id = $this->formatName($this->id);

        $config_arr = (array) wangEditorV4::config('config');

        if(isset($config_arr['default'])) $config= $config_arr['default'];

        if(isset($config_arr[$id])) $config= $config_arr[$id];

        if(empty($config)) $config = [];

        $config = json_encode(array_merge([
            'zIndex'              => 0,
            'uploadImgShowBase64' => true,
        ], $config, $this->options));

        $token = csrf_token();

        $this->script = <<<EOT
(function ($) {

    if ($('#{$this->id}').attr('initialized')) {
        return;
    }

    var E = window.wangEditor
    var editor = new E('#{$this->id}');

    editor.config.uploadImgParams = {_token: '$token'}

    Object.assign(editor.config, {$config})

    editor.config.onchange = function (html) {
        $('#input-$id').val(html);
    }
    editor.create();

    $('#{$this->id}').attr('initialized', 1);
})(jQuery);
EOT;
        return parent::render();
    }
}

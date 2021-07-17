<?php

namespace Jonexyz\wangEditorV4;

use Encore\Admin\Form;
use Encore\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class wangEditorV4ServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(wangEditorV4 $extension)
    {
        if (! wangEditorV4::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'jonexyz-wangEditorV4');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/jonexyz/wang-editor-v4')],
                'jonexyz-wangEditorV4' // php artisan vendor:publish --tag=jonexyz-wangEditorV4

            );
        }

        Admin::booting(function () {
            Form::extend('editor', Editor::class);
        });

    }
}

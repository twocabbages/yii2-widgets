<?php
/**
 * Created by PhpStorm.
 * User: air
 * Date: 14-7-27
 * Time: 上午8:25
 */

namespace cabbage\widgets;


use Yii;

class Select2Asset extends AssetBundle{
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];

    public function init()
    {

        $this->setSourcePath(__DIR__ . '/../lib/select2');

        $this->setupAssets('css', [
            'select2',
        ]);
        $this->setupAssets('js', [
            'select2.min',
        ]);

        parent::init(); // TODO: Change the autogenerated stub

    }
}
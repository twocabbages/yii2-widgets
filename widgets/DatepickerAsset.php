<?php
/**
 * Created by PhpStorm.
 * User: air
 * Date: 14-7-27
 * Time: 上午8:25
 */

namespace cabbage\widgets;


use Yii;

class DatepickerAsset extends AssetBundle{

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset'
    ];

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../lib/bootstrap-datepicker');

        $this->setupAssets('js', [
            'js/date-time/bootstrap-datepicker.min',
        ]);
        parent::init(); // TODO: Change the autogenerated stub
    }
}
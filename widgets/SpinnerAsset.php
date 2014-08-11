<?php
/**
 * Created by PhpStorm.
 * User: air
 * Date: 14-7-27
 * Time: 上午8:25
 */

namespace cabbage\widgets;


use Yii;

class SpinnerAsset extends AssetBundle{
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public function init()
    {
        $this->setupAssets('js', [
            'js/fuelux/fuelux.spinner.min',
        ]);
        parent::init(); // TODO: Change the autogenerated stub
    }
}
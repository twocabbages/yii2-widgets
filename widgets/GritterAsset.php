<?php
/**
 * Created by PhpStorm.
 * User: air
 * Date: 14-7-27
 * Time: 上午8:25
 */

namespace cabbage\widgets;


use Yii;

/**
 * Class GitterAsset
 * @package cabbage\widgets
 */
class GritterAsset extends AssetBundle{
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public function init()
    {
       $this->setupAssets('css',[
            'css/jquery.gritter',
        ]);
        $this->setupAssets('js', [
            'js/jquery.gritter.min',
        ]);
        parent::init(); // TODO: Change the autogenerated stub
    }
}
<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-widgets
 * @version 1.0.0
 */

namespace cabbage\widgets;
use Yii;

/**
 * Base asset bundle for all widgets
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class AssetBundle extends \yii\web\AssetBundle
{
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../assets');
        parent::init(); // TODO: Change the autogenerated stub
    }




    /**
     * Set up CSS and JS asset arrays based on the base-file names
     *
     * @param string $type whether 'css' or 'js'
     * @param array $files the list of 'css' or 'js' basefile names
     */
    protected function setupAssets($type, $files = [])
    {
        $srcFiles = [];
        foreach ($files as $file) {
            $srcFiles[] = "{$file}.{$type}";
        }
        $this->$type = $srcFiles;
    }

    /**
     * Sets the source path if empty
     *
     * @param string $path the path to be set
     */
    protected function setSourcePath($path)
    {
        if (empty($this->sourcePath)) {
            $this->sourcePath = $path;
        }
    }
}

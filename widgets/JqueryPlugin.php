<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace cabbage\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Editable renders the amazing x-editable js plugin from vitalets. For more information please visit the
 * [plugin site](http://vitalets.github.io/x-editable/index.html).
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\editable
 */
class JqueryPlugin extends Widget
{

    const PLUGIN_SLIM_SCROLL = 'slimScroll';
    const PLUGIN_GITTER = 'gritter';
    public $id = null;

    public $options = [];

    public $plugin = null;

    public $fn = null;

    public $pluginAsset = [
        self::PLUGIN_SLIM_SCROLL => '\cabbage\widgets\SlimScrollAsset',
        self::PLUGIN_GITTER => '\cabbage\widgets\GritterAsset',
    ];
    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        $class = $this->pluginAsset[$this->plugin];
        $class::register($view);

        $options = Json::encode($this->options);

        $fn = $this->fn ? ".{$this->fn}" : '';
        $id = $this->id ? "('#$this->id')" : '';
        $js = "jQuery{$id}.{$this->plugin}{$fn}($options);";
        $view->registerJs($js);
    }
}
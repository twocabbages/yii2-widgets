<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cabbage\widgets;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * \Yii::$app->getSession()->setFlash('error', 'This is the message');
 * \Yii::$app->getSession()->setFlash('success', 'This is the message');
 * \Yii::$app->getSession()->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * \Yii::$app->getSession()->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Notification extends Widget
{
    const FUNCTION_ADD = 'add';
    const FUNCTION_REMOVE = 'removeAll';

    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARMING = 'warming';
    const TYPE_ERROR = 'error';

    public $title = 'this is a title';

    public $text = 'this is a content';

    public $image = null;

    public $fn = self::FUNCTION_ADD;

    public $type = self::TYPE_INFO;
    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        isset($this->options['type']) && $this->type = $this->options['type'];
    }

    /**
     * Renders the widget.
     */
    public function run()
    {

        if($this->fn === self::FUNCTION_ADD){
            $options = [
                'text' => $this->text,
                'title' => $this->title,
            ];
            $this->image && $options['image'] = $this->image;

            $options['sticky'] = ArrayHelper::getValue($this->options, 'sticky', false);

            $class_center = ArrayHelper::getValue($this->options, 'light') ? ' gritter-light' : '';
            $class_light = ArrayHelper::getValue($this->options, 'center') ? ' gritter-center' : '';

            $options['class_name'] = "gritter-{$this->type}" . $class_center . $class_light;
        }else{
            $options = [];
        }


        echo JqueryPlugin::widget([
            'fn' => $this->fn,
            'plugin' => JqueryPlugin::PLUGIN_GITTER,
            'options' => $options,
        ]);

    }
}

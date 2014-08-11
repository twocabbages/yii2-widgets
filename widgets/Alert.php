<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cabbage\widgets;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * \Yii::$app->getSession()->setFlash('error', ['this is message']);
 * \Yii::$app->getSession()->setFlash('success', ['this is message']);
 * \Yii::$app->getSession()->setFlash('info', ['this is message']);
 * \Yii::$app->getSession()->setFlash('error', [['title'=>'this is title', 'text'=>'this is message', 'image'=>'image.jpg']]);
 * \Yii::$app->getSession()->setFlash('success', [['title'=>'this is title', 'text'=>'this is message', 'image'=>'image.jpg']]);
 * \Yii::$app->getSession()->setFlash('info', [['title'=>'this is title', 'text'=>'this is message', 'image'=>'image.jpg']]);
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * \Yii::$app->getSession()->setFlash('info', [['title'=>'this is title', 'text'=>'this is message', 'image'=>'image.jpg'],['title'=>'this is title', 'text'=>'this is message', 'image'=>'image.jpg']]);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Alert extends \yii\bootstrap\Widget
{

    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => Notification::TYPE_ERROR,
        'success' => Notification::TYPE_SUCCESS,
        'info'    => Notification::TYPE_INFO,
        'warning' => Notification::TYPE_WARMING
    ];

    public $options = [
        'sticky' => false,
        'light' => false,
        'center' => false,  //fix center
    ];

    public $default_title = null;
    
    public $default_text = null;

    public function init()
    {
        parent::init();

        $this->default_title = Yii::t('yii', 'Alert');
        $this->default_text = Yii::t('yii', 'Not Set');

    }
    public function run(){

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();

        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                foreach ($data as $message) {
                    /* initialize css class for each alert box */
                    $this->options['type'] = $this->alertTypes[$type];

                    if(is_array($message)){
                        $title = ArrayHelper::getValue($message, 'title', $this->default_title);
                        $text = ArrayHelper::getValue($message, 'text', $this->default_text);
                        $image = ArrayHelper::getValue($message, 'image', null);
                    }else{
                        $title = $this->default_title;
                        $text = (String)$message;
                    }

                    echo Notification::widget(array_merge([
                        'title' => $title,
                        'text' => $text,
                        'options' => $this->options,
                    ], isset($image) && $image !== null ? ['image'=>$image]:[]));
                }

                $session->removeFlash($type);
            }
        }
    }
}

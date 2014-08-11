<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace cabbage\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * Editable renders the amazing x-editable js plugin from vitalets. For more information please visit the
 * [plugin site](http://vitalets.github.io/x-editable/index.html).
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\editable
 */
class Editable extends InputWidget
{
    const TYPE_TEXT = 'text';
    const TYPE_SELECT = 'select2';
    const TYPE_DATE = 'date';
    const TYPE_SPINNER = 'spinner';
    const TYPE_SLIDER = 'slider';
    const TYPE_IMAGE = 'image';


    /**
     * @var string the type of input. Type of input.
     */
    public $type = 'text';
    /**
     * @var string the Mode of editable, can be popup or inline.
     */
    public $mode = 'inline';
    /**
     * @var string|array Url for submit, e.g. '/post'. If function - it will be called instead of ajax. Function should
     * return deferred object to run fail/done callbacks.
     *
     * ```
     * url: function(params) {
     *  var d = new $.Deferred;
     *  if(params.value === 'abc') {
     *      return d.reject('error message'); //returning error via deferred object
     *  } else {
     *      //async saving data in js model
     *      someModel.asyncSaveMethod({
     *          ...,
     *          success: function(){
     *              d.resolve();
     *          }
     *      });
     *      return d.promise();
     *  }
     * }
     * ```
     */
    public $url;
    /**
     * @var array the options for the X-editable.js plugin.
     * Please refer to the X-editable.js plugin web page for possible options.
     * @see http://vitalets.github.io/x-editable/docs.html#editable
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the Selectize.js plugin.
     * Please refer to the Selectize.js plugin web page for possible options.
     * @see http://vitalets.github.io/x-editable/docs.html#editable
     */
    public $clientEvents = [];

    public $tag = 'span';
    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        if ($this->url === null) {
            throw new InvalidConfigException("'Url' property must be specified.");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel()
                ? Html::getInputId($this->model, $this->attribute)
                : $this->getId();
        }
        if( $this->type === self::TYPE_IMAGE ){
            Html::addCssClass($this->options, 'editable img-responsive editable-click editable-empty');
        }else{
            Html::addCssClass($this->options, 'editable editable-click');

        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::tag($this->tag, $this->model->{$this->attribute}, $this->options);
        } else {
            echo Html::tag($this->tag, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    /**
     * Registers X-Editable plugin and the related events
     */
    protected function registerClientScript()
    {
        $view = $this->getView();

        $language = ArrayHelper::getValue($this->clientOptions, 'language');

        $id = ArrayHelper::remove($this->clientOptions, 'selector', '#' . $this->options['id']);
        $view->registerJs(<<<STR
$.fn.editable.defaults.mode = 'inline';
$.fn.editableform.loading = "<div class='editableform-loading'><i class='light-blue icon-2x icon-spinner icon-spin'></i></div>";
$.fn.editableform.buttons = '<button type="submit" class="btn btn-info editable-submit"><i class="icon-ok icon-white"></i></button>'+
                            '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
STR
        );
        switch ($this->type) {
            case self::TYPE_IMAGE:
                $view->registerJs('var last_gritter');
                $url = Yii::$app->urlManager->createUrl([$this->url]);
                $request = Yii::$app->getRequest();
                $token = '<input name="' . $request->csrfParam . '" value="' . $request->csrfToken . '" type="hidden">';
                    $this->clientOptions['image'] = new JsExpression(<<<STR
{
    //specify ace file input plugin's options here
    btn_choose: 'Change Avatar',
    droppable: true,
    /**
    //this will override the default before_change that only accepts image files
    before_change: function(files, dropped) {
        return true;
    },
    */

    //and a few extra ones here
    name: '$this->attribute',//put the field name here as well, will be used inside the custom plugin
    max_size: 1100000,//~100Kb
    on_error : function(code) {//on_error function will be called when the selected file has a problem
        if(last_gritter) $.gritter.remove(last_gritter);
        if(code == 1) {//file format error
            last_gritter = $.gritter.add({
                title: 'File is not an image!',
                text: 'Please choose a jpg|gif|png image!',
                class_name: 'gritter-error gritter-center'
            });
        } else if(code == 2) {//file size rror
            last_gritter = $.gritter.add({
                title: 'File too big!',
                text: 'Image size should not exceed 100Kb!',
                class_name: 'gritter-error gritter-center'
            });
        }
        else {//other error
        }
    },
    on_success : function() {
        $.gritter.removeAll();
    }
},
url: function(params) {
    // ***UPDATE AVATAR HERE*** //
    //You can replace the contents of this function with examples/profile-avatar-update.js for actual upload


    var deferred = new $.Deferred

    //if value is empty, means no valid files were selected
    //but it may still be submitted by the plugin, because "" (empty string) is different from previous non-empty value whatever it was
    //so we return just here to prevent problems
    var value = $('$id').next().find('input[type=hidden]:eq(0)').val();
    if(!value || value.length == 0) {
        deferred.resolve();
        return deferred.promise();
    }


    var _form = $('$id').next().find('form');
    _form.attr('action', '$url');
    _form.attr('method', 'post');
    _form.attr('enctype', 'multipart/form-data');

    _form.prepend('$token');

    $.ajax({
        url : _form.attr('action'),
        type: 'post',
        data : _form.serialize(),
        success: function(res){
            console.log(res);
            deferred.resolve({'status':'OK'});
        }
    })


//    _form.submit();

    //dummy upload
//    setTimeout(function(){
//        if("FileReader" in window) {
//            //for browsers that have a thumbnail of selected image
//            var form = $('$id').next().find('form');
//            form.attr('action','$this->url');
//            alert(form.attr('action'));
//            form.submit();

//        }
//
//        deferred.resolve({'status':'OK'});
//
//        if(last_gritter) $.gritter.remove(last_gritter);
//        last_gritter = $.gritter.add({
//            title: 'Avatar Updated!',
//            text: 'Uploading to server can be easily implemented. A working example is included with the template.',
//                class_name: 'gritter-info gritter-center'
//            });
//
//     } , parseInt(Math.random() * 800 + 2000))

    return deferred.promise();
},
success: function(response, newValue) {
    console.log(response);
    console.log(newValue);
}
STR
                );

                break;
            case self::TYPE_TEXT:
                break;
            default:

        }
        EditableAsset::register($view);

        if( $this->type !== self::TYPE_IMAGE) $this->clientOptions['url'] = Url::toRoute($this->url);
        $this->clientOptions['type'] = $this->type;
        $this->clientOptions['mode'] = $this->mode;
        $this->clientOptions['name'] = $this->attribute ? : $this->name;
        $this->clientOptions['pk'] = ArrayHelper::getValue(
            $this->clientOptions,
            'pk',
            $this->hasModel() ? $this->model->getPrimaryKey() : null
        );
        if ($this->hasModel() && $this->model->isNewRecord) {
            $this->clientOptions['send'] = 'always'; // send to server without pk
        }

        $options = Json::encode($this->clientOptions);
        $js = "jQuery('$id').editable($options);";
        $view->registerJs($js);

        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('$id').on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }

    }
} 
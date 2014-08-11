<?php


namespace cabbage\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * A custom extended side navigation menu extending Yii Menu
 *
 * For example:
 *
 * ```php
 * echo SideNav::widget([
 *     'items' => [
 *         [
 *             'url' => ['/site/index'],
 *             'label' => 'Home',
 *             'icon' => 'home'
 *         ],
 *         [
 *             'url' => ['/site/about'],
 *             'label' => 'About',
 *             'icon' => 'info-sign',
 *             'items' => [
 *                  ['url' => '#', 'label' => 'Item 1'],
 *                  ['url' => '#', 'label' => 'Item 2'],
 *             ],
 *         ],
 *     ],
 * ]);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 */
class SideNav extends \yii\widgets\Menu
{

    /**
     * Panel contextual states
     */
    const TYPE_DEFAULT = 'default';
    /**
     *
     */
    const TYPE_PRIMARY = 'primary';
    /**
     *
     */
    const TYPE_INFO = 'info';
    /**
     *
     */
    const TYPE_SUCCESS = 'success';
    /**
     *
     */
    const TYPE_DANGER = 'danger';
    /**
     *
     */
    const TYPE_WARNING = 'warning';

    /**
     * @var string the menu container style. This is one of the bootstrap panel
     * contextual state classes. Defaults to `default`.
     * @see http://getbootstrap.com/components/#panels
     */
    public $type = self::TYPE_DEFAULT;

    /**
     * @var string prefix for the icon in [[items]]. This string will be prepended
     * before the icon name to get the icon CSS class. This defaults to `glyphicon glyphicon-`
     * for usage with glyphicons available with Bootstrap.
     */
    public $iconPrefix = 'icon-';

    /**
     * @var array string/boolean the sidenav heading. This is not HTML encoded
     * When set to false or null, no heading container will be displayed.
     */
    public $heading = false;

    /**
     * @var array options for the sidenav heading
     */
    public $headingOptions = [];

    /**
     * @var array options for the sidenav container
     */
    public $containerOptions = [];

    /**
     * @var string indicator for a menu sub-item
     */
    public $indItem = '';

    /**
     * @var string indicator for a opened sub-menu
     */
    public $indMenuOpen = '<i class="indicator glyphicon glyphicon-chevron-down"></i>';

    /**
     * @var string indicator for a closed sub-menu
     */
    public $indMenuClose = '<i class="indicator glyphicon glyphicon-chevron-right"></i>';

    /**
     * @var array list of sidenav menu items. Each menu item should be an array of the following structure:
     *
     * - label: string, optional, specifies the menu item label. When [[encodeLabels]] is true, the label
     *   will be HTML-encoded. If the label is not specified, an empty string will be used.
     * - icon: string, optional, specifies the glyphicon name to be placed before label.
     * - url: string or array, optional, specifies the URL of the menu item. It will be processed by [[Url::to]].
     *   When this is set, the actual menu item content will be generated using [[linkTemplate]];
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - items: array, optional, specifies the sub-menu items. Its format is the same as the parent items.
     * - active: boolean, optional, whether this menu item is in active state (currently selected).
     *   If a menu item is active, its CSS class will be appended with [[activeCssClass]].
     *   If this option is not set, the menu item will be set active automatically when the current request
     *   is triggered by [[url]]. For more details, please refer to [[isItemActive()]].
     * - template: string, optional, the template used to render the content of this menu item.
     *   The token `{url}` will be replaced by the URL associated with this menu item,
     *   and the token `{label}` will be replaced by the label of the menu item.
     *   If this option is not set, [[linkTemplate]] will be used instead.
     * - options: array, optional, the HTML attributes for the menu item tag.
     *
     */
    public $items;

    /**
     * Allowed panel stypes
     */
    private static $_validTypes = [
        self::TYPE_DEFAULT,
        self::TYPE_PRIMARY,
        self::TYPE_INFO,
        self::TYPE_SUCCESS,
        self::TYPE_DANGER,
        self::TYPE_WARNING,
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();
        SideNavAsset::register($this->getView());
        $this->activateParents = true;
        $this->submenuTemplate = "\n<ul class='submenu'>\n{items}\n</ul>\n";
        $this->linkTemplate = '<a href="{url}">{icon}{label}</a>';
        $this->labelTemplate = '{icon}{label}';
        $this->markTopItems();
        Html::addCssClass($this->options, 'nav nav-list');
    }

    /**
     * Renders the side navigation menu.
     * with the heading and panel containers
     */
    public function run()
    {
        $heading = '';
        if (isset($this->heading) && $this->heading != '') {
            $heading = Html::tag('div', <<<STR
                <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                    <button class="btn btn-success">
                        <i class="icon-signal"></i>
                    </button>

                    <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                    </button>

                    <button class="btn btn-warning">
                        <i class="icon-group"></i>
                    </button>

                    <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                    </button>
                </div>

                <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                    <span class="btn btn-success"></span>

                    <span class="btn btn-info"></span>

                    <span class="btn btn-warning"></span>

                    <span class="btn btn-danger"></span>
                </div>
STR
            , ['class'=>'sidebar-shortcuts', 'id'=>'sidebar-shortcuts']);
        }
        $collapse = '';
        $collapse = Html::tag('div','<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>', ['class'=>'sidebar-collapse', 'id'=>'sidebar-collapse'] );
        $this->getView()->registerJs("try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}");
        $type = in_array($this->type, self::$_validTypes) ? $this->type : self::TYPE_DEFAULT;
        Html::addCssClass($this->containerOptions, "sidebar");
        $this->containerOptions['id'] = 'sidebar';
        $this->getView()->registerJs("try{ace.settings.check('sidebar' , 'fixed')}catch(e){}");
        echo Html::tag('a', '<span class="menu-text"></span>', ['class'=>'menu-toggler', 'id'=>'menu-toggler']);
        echo Html::tag('div', $heading . $this->renderMenu() . $collapse, $this->containerOptions);
    }

    /**
     * Renders the main menu
     */
    protected function renderMenu()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = $_GET;
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');

        return Html::tag($tag, $this->renderItems($items), $options);
    }

    /**
     * Marks each topmost level item which is not a submenu
     */
    protected function markTopItems()
    {
        $items = [];
        foreach ($this->items as $item) {
            if (empty($item['items'])) {
                $item['top'] = true;
            }
            $items[] = $item;
        }
        $this->items = $items;
    }

    /**
     * Renders the content of a side navigation menu item.
     *
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     * @throws InvalidConfigException
     */
    protected function renderItem($item)
    {
        $this->validateItems($item);
        $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
        $url = Url::to(ArrayHelper::getValue($item, 'url', '#'));
        if (empty($item['top'])) {
            if (empty($item['items'])) {
                $template = str_replace('{icon}', $this->indItem . '{icon}', $template);
            } else {
                $template = '<a href="{url}" class="dropdown-toggle">{icon}{label}</a>';
                $openOptions = ($item['active']) ? ['class' => 'opened'] : ['class' => 'opened', 'style' => 'display:none'];
                $closeOptions = ($item['active']) ? ['class' => 'closed', 'style' => 'display:none'] : ['class' => 'closed'];
                $indicator = '<b class="arrow icon-angle-down"></b>';
                $template = str_replace('{label}', '{label}' . $indicator, $template);
            }
        }
        $icon = empty($item['icon']) ? '<i class="' . $this->iconPrefix . 'list' . '"></i> &nbsp;' : '<i class="' . $this->iconPrefix . $item['icon'] . '"></i> &nbsp;';
        unset($item['icon'], $item['top']);
        return strtr($template, [
            '{url}' => $url,
            '{label}' => Html::tag('span', $item['label'], ['class'=>'menu-text']),
            '{icon}' => $icon
        ]);
    }

    /**
     * Validates each item for a valid label and url.
     *
     * @throws InvalidConfigException
     */
    protected function validateItems($item)
    {
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
    }
}

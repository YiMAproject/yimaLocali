<?php
namespace yimaLocali\Controller\Plugin;

use yimaLocali\Service\AbstractLocaleHelper;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

/**
 * Class WidgetLoader
 *
 * @package yimaLocali\Controller\Plugin
 */
class Locale extends AbstractLocaleHelper
    implements PluginInterface
{
    /**
     * @var null|Dispatchable
     */
    protected $controller;

    /**
     * Set the current controller instance
     *
     * @param  Dispatchable $controller
     * @return void
     */
    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get the current controller instance
     *
     * @return null|Dispatchable
     */
    public function getController()
    {
        return $this->controller;
    }
}

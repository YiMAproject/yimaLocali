<?php
namespace yimaLocali\View\Helper;

use yimaLocali\Service\AbstractLocaleHelper;
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Class Locale
 *
 * @package yimaLocali\View\Helper
 */
class Locale extends AbstractLocaleHelper
    implements HelperInterface
{
    /**
     * View object instance
     *
     * @var Renderer
     */
    protected $view = null;

    /**
     * Set the View object
     *
     * @param  Renderer $view
     *
     * @return $this
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }
}

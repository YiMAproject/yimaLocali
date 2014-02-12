<?php
namespace yimaLocali\Plugin;
use yimaLocali\LocalePluginInterface as PluginInterface;
use yimaLocali\Locale;

abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Locale object
     *
     * @var Renderer
     */
    protected $locale;

    /**
     * Set the Locale object
     *
     * @param  Locale $locale
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get the locale object
     *
     * @return null|Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
}

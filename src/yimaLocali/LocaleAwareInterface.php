<?php
namespace yimaLocali;

/**
 * Interface LocaleInterface
 *
 * ** Note: Object that implement this interface has locale injected
 *          by yimaLocali Initializer Service Manager Config
 *
 * @package yimaLocali
 */
interface LocaleAwareInterface
{
    /**
     * Set Locale
     *
     * @param string $locale Locale
     *
     * @return static
     */
    public function setLocale($locale);
}

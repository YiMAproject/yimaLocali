<?php
namespace yimaLocali;

/**
 * Interface LocaleInterface
 *
 * @package yimaLocali
 */
interface LocaleAwareInterface
{
    /**
     * Set Locale
     *
     * @param $locale
     *
     * @return static
     */
    public function setLocale($locale);
}

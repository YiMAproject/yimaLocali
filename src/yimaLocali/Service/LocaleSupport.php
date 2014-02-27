<?php
namespace yimaLocali\Service;

/**
 * Class LocaleSupport
 *
 * this class used to share information behalf of available locales to
 * detectors classes and others one need to get use of this info.
 *
 * note: by default this info set on bootstrap from merged config and before detectors call
 *
 * @package yimaLocali\Service
 */
class LocaleSupport
{
    protected static $localesData;

    /**
     * @var boolean is class initialized?
     */
    protected static $isInitialized;

    /**
     * This is one time set and use class.
     *
     * note: you can set data for a first time and on other call class will use that data
     *
     * @param array $localesData
     */
    public function __construct(array $localesData = array())
    {
        if (self::$isInitialized && !empty($localesData)) {
            throw new \Exception('This class is one time initializer of data, this will be happened and you cant set data again.');
        }

        if (!self::$isInitialized) {
            $this->init($localesData);
        }
    }

    /**
     * Initialize class with available locales
     *
     * @param array $localesData
     */
    protected function init(array $localesData)
    {
        // validate locales data
        // ...

        self::$localesData = $localesData;

        self::$isInitialized = true;
    }

    /**
     * Get Available Locales
     *
     * @return array
     */
    public static function getAvailablesLocale()
    {
        $config = self::$localesData;

        if (isset($config['locales']) && is_array($config['locales'])) {
            $supported = $config['locales'];
        } else {
            $supported = array();
        }

        if ($defLocale = self::getDefaultLocale()) {
            if (! in_array($defLocale, $supported)) {
                array_unshift($supported, $defLocale);
            }
        }

        return $supported;
    }

    /**
     * Get default locale if present and false otherwise
     *
     * @return bool|string
     */
    public static function getDefaultLocale()
    {
        $return = false;

        $config = self::$localesData;
        if (isset($config['default']) && is_scalar($config['default'])) {
            $return = $config['default'];
        }

        return $return;
    }

    /**
     * Is locale a supported available locale?
     *
     * @param string $locale Locale name
     *
     * @return bool
     */
    public static function isValidLocale($locale)
	{
		$locale = (string) $locale;
		$locale = self::getLocaleFromAlias($locale);
		
		$supported = self::getAvailablesLocale();
		if (in_array($locale, $supported)) {
            // this is valid locale
			return true;
		}
		
		return false;
	}

    /**
     * Get Locale for an given alias. if not present return false
     *
     * @param $alias
     *
     * @return string|false
     */
    public static function getLocaleFromAlias($alias)
	{
        $alias = (string) $alias;
		
		if (! self::isAlias($alias)) {
			// we dont have alias to this

			return false;
		}

        $locale = $alias;
		$aliases = self::getAliases();
		do {
            $locale = $aliases[$locale];
		} while (self::isAlias($alias));
		
		return $locale;
	}

    /**
     * Is available alias name?
     *
     * @param  string $alias
     * @return bool
     */
    public static function isAlias($alias)
    {
        $alias = (string) $alias;
        $aliases = self::getAliases();

        return (isset($aliases[$alias]));
    }

    /**
     * Get aliases list
     *
     * @return array
     */
    public static function getAliases()
	{
		$config = self::$localesData;

        $aliases = array();
		if (isset($config['aliases']) && is_array($config['aliases'])) {
            $aliases = $config['aliases'];
		}
		
		return $aliases;
	}

    /**
     * Get config locales data
     *
     * @return array
     */
    public static function getLocalesData()
    {
        return self::$localesData;
    }
}

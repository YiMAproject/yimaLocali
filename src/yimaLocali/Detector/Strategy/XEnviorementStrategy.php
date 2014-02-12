<?php 
	/**
     * Expects the Systems standard locale
     *
     * For Windows:
     * f.e.: LC_COLLATE=C;LC_CTYPE=German_Austria.1252;LC_MONETARY=C
     * would be recognised as de_AT
     *
     * @return array
     */
    public static function getEnvironment()
    {
        if (self::$_environment !== null) {
            return self::$_environment;
        }

        require_once 'Zend/Locale/Data/Translation.php';

        $language      = setlocale(LC_ALL, 0);
        $languages     = explode(';', $language);
        $languagearray = array();

        foreach ($languages as $locale) {
            if (strpos($locale, '=') !== false) {
                $language = substr($locale, strpos($locale, '='));
                $language = substr($language, 1);
            }

            if ($language !== 'C') {
                if (strpos($language, '.') !== false) {
                    $language = substr($language, 0, strpos($language, '.'));
                } else if (strpos($language, '@') !== false) {
                    $language = substr($language, 0, strpos($language, '@'));
                }

                $language = str_ireplace(
                    array_keys(Zend_Locale_Data_Translation::$languageTranslation),
                    array_values(Zend_Locale_Data_Translation::$languageTranslation),
                    (string) $language
                );

                $language = str_ireplace(
                    array_keys(Zend_Locale_Data_Translation::$regionTranslation),
                    array_values(Zend_Locale_Data_Translation::$regionTranslation),
                    $language
                );

                if (isset(self::$_localeData[$language]) === true) {
                    $languagearray[$language] = 1;
                    if (strpos($language, '_') !== false) {
                        $languagearray[substr($language, 0, strpos($language, '_'))] = 1;
                    }
                }
            }
        }

        self::$_environment = $languagearray;
        return $languagearray;
    }
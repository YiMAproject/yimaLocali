<?php 
	/**
     * Return an array of all accepted languages of the client
     * Expects RFC compilant Header !!
     *
     * The notation can be :
     * de,en-UK-US;q=0.5,fr-FR;q=0.2
     *
     * @return array - list of accepted languages including quality
     */
    public static function getBrowser()
    {
        if (self::$_browser !== null) {
            return self::$_browser;
        }

        $httplanguages = getenv('HTTP_ACCEPT_LANGUAGE');
        if (empty($httplanguages) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $httplanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        $languages     = array();
        if (empty($httplanguages)) {
            return $languages;
        }

        $accepted = preg_split('/,\s*/', $httplanguages);

        foreach ($accepted as $accept) {
            $match  = null;
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i',
                                 $accept, $match);

            if ($result < 1) {
                continue;
            }

            if (isset($match[2]) === true) {
                $quality = (float) $match[2];
            } else {
                $quality = 1.0;
            }

            $countrys = explode('-', $match[1]);
            $region   = array_shift($countrys);

            $country2 = explode('_', $region);
            $region   = array_shift($country2);

            foreach ($countrys as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }

            foreach ($country2 as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }

            if ((isset($languages[$region]) === false) || ($languages[$region] < $quality)) {
                $languages[$region] = $quality;
            }
        }

        self::$_browser = $languages;
        return $languages;
    }
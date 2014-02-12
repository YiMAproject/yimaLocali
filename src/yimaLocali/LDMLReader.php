<?php
namespace yimaLocali;

use yimaLocali\Locale;
use yimaLocali\Exception;

class LDMLReader
{
	/**
	 * Actual set locale
	 * 
	 * @var yimaLocali\Locale Locale
	 */
	protected $locale;
	
	/**
     * Locale files
     *
     * @var ressource
     * @access private
     */
    private static $_ldml = array();

    /**
     * List of values which are collected
     *
     * @var array
     * @access private
     */
    private static $_list = array();

    
    public function __construct($locale)
    {
    	$this->setLocale($locale);
    }
    
    /**
     * Sets a new locale
     *
     * @param  string|yimaLocali\Locale $locale
     * @return void
     */
    public function setLocale($locale)
    {
    	if (is_string($locale) && ($locale !== null)) {
    		// check locale validation later
    		$locale = new Locale($locale,false);
    	}
    	
    	if (! $locale instanceof Locale) {
    		throw new \Exception(sprintf(
    			'Locale Object must instance of "%s" or valid universe locale string.', $locale
    		));
    	}
    	
    	if (false == $locale->isValidLocale()) {
    		throw new Exception\InvalidLocaleException(sprintf(
    			'The Locale "%s" not universe valid locale.', $locale
    		));
    	}
    
    	$this->locale = $locale;
    	return $this;
    }
    
    public function getLocale()
    {
    	return $this->locale;
    }    
    
    /**
     * Read the LDML file, get a array of multipath defined value
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $value
     * @return array
     * @access public
     */
    public function getList($path, $value = false)
    {
    	$locale = (string) $this->getLocale();
    
    	$val = $value;
    	if (is_array($value)) {
    		$val = implode('_' , $value);
    	}
    
    	$val = urlencode($val);
    	$temp = array();
    	switch(strtolower($path)) {
    		case 'language':
    			$temp = $this->getFile($locale, '/ldml/localeDisplayNames/languages/language', 'type');
    			break;
    
    		case 'script':
    			$temp = $this->getFile($locale, '/ldml/localeDisplayNames/scripts/script', 'type');
    			break;
    
    		case 'territory':
    			$temp = $this->getFile($locale, '/ldml/localeDisplayNames/territories/territory', 'type');
    			if ($value === 1) {
    				foreach($temp as $key => $value) {
    					if ((is_numeric($key) === false) and ($key != 'QO') and ($key != 'QU')) {
    						unset($temp[$key]);
    					}
    				}
    			} else if ($value === 2) {
    				foreach($temp as $key => $value) {
    					if (is_numeric($key) or ($key == 'QO') or ($key == 'QU')) {
    						unset($temp[$key]);
    					}
    				}
    			}
    			break;
    
    		case 'variant':
    			$temp = $this->getFile($locale, '/ldml/localeDisplayNames/variants/variant', 'type');
    			break;
    
    		case 'key':
    			$temp = $this->getFile($locale, '/ldml/localeDisplayNames/keys/key', 'type');
    			break;
    
    		case 'type':
    			/**
    			 * @fixme: fixed by me
    			 * 		   change $type to $value
    			 */
    			if (empty(/* $type */$value)) {
    				$temp = $this->getFile($locale, '/ldml/localeDisplayNames/types/type', 'type');
    			} else {
    				if (($value == 'calendar') or
    						($value == 'collation') or
    						($value == 'currency')) {
    					$temp = $this->getFile($locale, '/ldml/localeDisplayNames/types/type[@key=\'' . $value . '\']', 'type');
    				} else {
    					$temp = $this->getFile($locale, '/ldml/localeDisplayNames/types/type[@type=\'' . $value . '\']', 'type');
    				}
    			}
    			break;
    
    		case 'layout':
    			$temp  = $this->getFile($locale, '/ldml/layout/orientation',                 'lines',      'lines');
    			$temp += $this->getFile($locale, '/ldml/layout/orientation',                 'characters', 'characters');
    			$temp += $this->getFile($locale, '/ldml/layout/inList',                      '',           'inList');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'currency\']',  '',           'currency');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'dayWidth\']',  '',           'dayWidth');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'fields\']',    '',           'fields');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'keys\']',      '',           'keys');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'languages\']', '',           'languages');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'long\']',      '',           'long');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'measurementSystemNames\']', '', 'measurementSystemNames');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'monthWidth\']',   '',        'monthWidth');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'quarterWidth\']', '',        'quarterWidth');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'scripts\']',   '',           'scripts');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'territories\']',  '',        'territories');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'types\']',     '',           'types');
    			$temp += $this->getFile($locale, '/ldml/layout/inText[@type=\'variants\']',  '',           'variants');
    			break;
    
    		case 'characters':
    			$temp  = $this->getFile($locale, '/ldml/characters/exemplarCharacters',                           '', 'characters');
    			$temp += $this->getFile($locale, '/ldml/characters/exemplarCharacters[@type=\'auxiliary\']',      '', 'auxiliary');
    			$temp += $this->getFile($locale, '/ldml/characters/exemplarCharacters[@type=\'currencySymbol\']', '', 'currencySymbol');
    			break;
    
    		case 'delimiters':
    			$temp  = $this->getFile($locale, '/ldml/delimiters/quotationStart',          '', 'quoteStart');
    			$temp += $this->getFile($locale, '/ldml/delimiters/quotationEnd',            '', 'quoteEnd');
    			$temp += $this->getFile($locale, '/ldml/delimiters/alternateQuotationStart', '', 'quoteStartAlt');
    			$temp += $this->getFile($locale, '/ldml/delimiters/alternateQuotationEnd',   '', 'quoteEndAlt');
    			break;
    
    		case 'measurement':
    			$temp  = $this->getFile('supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'metric\']', 'territories', 'metric');
    			$temp += $this->getFile('supplementalData', '/supplementalData/measurementData/measurementSystem[@type=\'US\']',     'territories', 'US');
    			$temp += $this->getFile('supplementalData', '/supplementalData/measurementData/paperSize[@type=\'A4\']',             'territories', 'A4');
    			$temp += $this->getFile('supplementalData', '/supplementalData/measurementData/paperSize[@type=\'US-Letter\']',      'territories', 'US-Letter');
    			break;
    
    		case 'months':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/default', 'choice', 'context');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/default', 'choice', 'default');
    			$temp['format']['abbreviated'] = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'abbreviated\']/month', 'type');
    			$temp['format']['narrow']      = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'narrow\']/month', 'type');
    			$temp['format']['wide']        = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/monthWidth[@type=\'wide\']/month', 'type');
    			$temp['stand-alone']['abbreviated']  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'abbreviated\']/month', 'type');
    			$temp['stand-alone']['narrow']       = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'narrow\']/month', 'type');
    			$temp['stand-alone']['wide']         = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'stand-alone\']/monthWidth[@type=\'wide\']/month', 'type');
    			break;
    
    		case 'month':
    			if (empty($value)) {
    				$value = array("gregorian", "format", "wide");
    			}
    			$temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month', 'type');
    			break;
    
    		case 'days':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/default', 'choice', 'context');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/default', 'choice', 'default');
    			$temp['format']['abbreviated'] = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'abbreviated\']/day', 'type');
    			$temp['format']['narrow']      = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'narrow\']/day', 'type');
    			$temp['format']['wide']        = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/dayWidth[@type=\'wide\']/day', 'type');
    			$temp['stand-alone']['abbreviated']  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'abbreviated\']/day', 'type');
    			$temp['stand-alone']['narrow']       = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'narrow\']/day', 'type');
    			$temp['stand-alone']['wide']         = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'stand-alone\']/dayWidth[@type=\'wide\']/day', 'type');
    			break;
    
    		case 'day':
    			if (empty($value)) {
    				$value = array("gregorian", "format", "wide");
    			}
    			$temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day', 'type');
    			break;
    
    		case 'week':
    			$minDays   = $this->calendarDetail($locale, $this->getFile('supplementalData', '/supplementalData/weekData/minDays', 'territories'));
    			$firstDay  = $this->calendarDetail($locale, $this->getFile('supplementalData', '/supplementalData/weekData/firstDay', 'territories'));
    			$weekStart = $this->calendarDetail($locale, $this->getFile('supplementalData', '/supplementalData/weekData/weekendStart', 'territories'));
    			$weekEnd   = $this->calendarDetail($locale, $this->getFile('supplementalData', '/supplementalData/weekData/weekendEnd', 'territories'));
    
    			$temp  = $this->getFile('supplementalData', "/supplementalData/weekData/minDays[@territories='" . $minDays . "']", 'count', 'minDays');
    			$temp += $this->getFile('supplementalData', "/supplementalData/weekData/firstDay[@territories='" . $firstDay . "']", 'day', 'firstDay');
    			$temp += $this->getFile('supplementalData', "/supplementalData/weekData/weekendStart[@territories='" . $weekStart . "']", 'day', 'weekendStart');
    			$temp += $this->getFile('supplementalData', "/supplementalData/weekData/weekendEnd[@territories='" . $weekEnd . "']", 'day', 'weekendEnd');
    			break;
    
    		case 'quarters':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp['format']['abbreviated'] = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
    			$temp['format']['narrow']      = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
    			$temp['format']['wide']        = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'format\']/quarterWidth[@type=\'wide\']/quarter', 'type');
    			$temp['stand-alone']['abbreviated']  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'abbreviated\']/quarter', 'type');
    			$temp['stand-alone']['narrow']       = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'narrow\']/quarter', 'type');
    			$temp['stand-alone']['wide']         = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/quarters/quarterContext[@type=\'stand-alone\']/quarterWidth[@type=\'wide\']/quarter', 'type');
    			break;
    
    		case 'quarter':
    			if (empty($value)) {
    				$value = array("gregorian", "format", "wide");
    			}
    			$temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter', 'type');
    			break;
    
    		case 'eras':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp['names']       = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNames/era', 'type');
    			$temp['abbreviated'] = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraAbbr/era', 'type');
    			$temp['narrow']      = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/eras/eraNarrow/era', 'type');
    			break;
    
    		case 'era':
    			if (empty($value)) {
    				$value = array("gregorian", "Abbr");
    			}
    			$temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era', 'type');
    			break;
    
    		case 'date':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medium');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'short');
    			break;
    
    		case 'time':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp  = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medium');
    			$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'short');
    			break;
    
    		case 'datetime':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    
    			$timefull = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'full\']/timeFormat/pattern', '', 'full');
    			$timelong = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'long\']/timeFormat/pattern', '', 'long');
    			$timemedi = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'medium\']/timeFormat/pattern', '', 'medi');
    			$timeshor = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/timeFormatLength[@type=\'short\']/timeFormat/pattern', '', 'shor');
    
    			$datefull = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'full\']/dateFormat/pattern', '', 'full');
    			$datelong = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'long\']/dateFormat/pattern', '', 'long');
    			$datemedi = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'medium\']/dateFormat/pattern', '', 'medi');
    			$dateshor = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/dateFormatLength[@type=\'short\']/dateFormat/pattern', '', 'shor');
    
    			$full = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'full\']/dateTimeFormat/pattern', '', 'full');
    			$long = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'long\']/dateTimeFormat/pattern', '', 'long');
    			$medi = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'medium\']/dateTimeFormat/pattern', '', 'medi');
    			$shor = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'short\']/dateTimeFormat/pattern', '', 'shor');
    
    			$temp['full']   = str_replace(array('{0}', '{1}'), array($timefull['full'], $datefull['full']), $full['full']);
    			$temp['long']   = str_replace(array('{0}', '{1}'), array($timelong['long'], $datelong['long']), $long['long']);
    			$temp['medium'] = str_replace(array('{0}', '{1}'), array($timemedi['medi'], $datemedi['medi']), $medi['medi']);
    			$temp['short']  = str_replace(array('{0}', '{1}'), array($timeshor['shor'], $dateshor['shor']), $shor['shor']);
    			break;
    
    		case 'dateitem':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$_temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem', 'id');
    			foreach($_temp as $key => $found) {
    				$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $key . '\']', '', $key);
    			}
    			break;
    
    		case 'dateinterval':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$_temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem', 'id');
    			foreach($_temp as $key => $found) {
    				$temp[$key] = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $key . '\']/greatestDifference', 'id');
    			}
    			break;
    
    		case 'field':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp2 = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field', 'type');
    			foreach ($temp2 as $key => $keyvalue) {
    				$temp += $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field[@type=\'' . $key . '\']/displayName', '', $key);
    			}
    			break;
    
    		case 'relative':
    			if (empty($value)) {
    				$value = "gregorian";
    			}
    			$temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/fields/field/relative', 'type');
    			break;
    
    		case 'symbols':
    			$temp  = $this->getFile($locale, '/ldml/numbers/symbols/decimal',         '', 'decimal');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/group',           '', 'group');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/list',            '', 'list');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/percentSign',     '', 'percent');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/nativeZeroDigit', '', 'zero');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/patternDigit',    '', 'pattern');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/plusSign',        '', 'plus');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/minusSign',       '', 'minus');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/exponential',     '', 'exponent');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/perMille',        '', 'mille');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/infinity',        '', 'infinity');
    			$temp += $this->getFile($locale, '/ldml/numbers/symbols/nan',             '', 'nan');
    			break;
    
    		case 'nametocurrency':
    			$_temp = $this->getFile($locale, '/ldml/numbers/currencies/currency', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
    			}
    			break;
    
    		case 'currencytoname':
    			$_temp = $this->getFile($locale, '/ldml/numbers/currencies/currency', 'type');
    			foreach ($_temp as $key => $keyvalue) {
    				$val = $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
    				if (!isset($val[$key])) {
    					continue;
    				}
    				if (!isset($temp[$val[$key]])) {
    					$temp[$val[$key]] = $key;
    				} else {
    					$temp[$val[$key]] .= " " . $key;
    				}
    			}
    			break;
    
    		case 'currencysymbol':
    			$_temp = $this->getFile($locale, '/ldml/numbers/currencies/currency', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/symbol', '', $key);
    			}
    			break;
    
    		case 'question':
    			$temp  = $this->getFile($locale, '/ldml/posix/messages/yesstr',  '', 'yes');
    			$temp += $this->getFile($locale, '/ldml/posix/messages/nostr',   '', 'no');
    			break;
    
    		case 'currencyfraction':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'digits', $key);
    			}
    			break;
    
    		case 'currencyrounding':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info', 'iso4217');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $key . '\']', 'rounding', $key);
    			}
    			break;
    
    		case 'currencytoregion':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
    			}
    			break;
    
    		case 'regiontocurrency':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
    			foreach ($_temp as $key => $keyvalue) {
    				$val = $this->getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
    				if (!isset($val[$key])) {
    					continue;
    				}
    				if (!isset($temp[$val[$key]])) {
    					$temp[$val[$key]] = $key;
    				} else {
    					$temp[$val[$key]] .= " " . $key;
    				}
    			}
    			break;
    
    		case 'regiontoterritory':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
    			}
    			break;
    
    		case 'territorytoregion':
    			$_temp2 = $this->getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
    			$_temp = array();
    			foreach ($_temp2 as $key => $found) {
    				$_temp += $this->getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
    			}
    			foreach($_temp as $key => $found) {
    				$_temp3 = explode(" ", $found);
    				foreach($_temp3 as $found3) {
    					if (!isset($temp[$found3])) {
    						$temp[$found3] = (string) $key;
    					} else {
    						$temp[$found3] .= " " . $key;
    					}
    				}
    			}
    			break;
    
    		case 'scripttolanguage':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
    				if (empty($temp[$key])) {
    					unset($temp[$key]);
    				}
    			}
    			break;
    
    		case 'languagetoscript':
    			$_temp2 = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
    			$_temp = array();
    			foreach ($_temp2 as $key => $found) {
    				$_temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
    			}
    			foreach($_temp as $key => $found) {
    				$_temp3 = explode(" ", $found);
    				foreach($_temp3 as $found3) {
    					if (empty($found3)) {
    						continue;
    					}
    					if (!isset($temp[$found3])) {
    						$temp[$found3] = (string) $key;
    					} else {
    						$temp[$found3] .= " " . $key;
    					}
    				}
    			}
    			break;
    
    		case 'territorytolanguage':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
    				if (empty($temp[$key])) {
    					unset($temp[$key]);
    				}
    			}
    			break;
    
    		case 'languagetoterritory':
    			$_temp2 = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
    			$_temp = array();
    			foreach ($_temp2 as $key => $found) {
    				$_temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
    			}
    			foreach($_temp as $key => $found) {
    				$_temp3 = explode(" ", $found);
    				foreach($_temp3 as $found3) {
    					if (empty($found3)) {
    						continue;
    					}
    					if (!isset($temp[$found3])) {
    						$temp[$found3] = (string) $key;
    					} else {
    						$temp[$found3] .= " " . $key;
    					}
    				}
    			}
    			break;
    
    		case 'timezonetowindows':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone', 'other');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone[@other=\'' . $key . '\']', 'type', $key);
    			}
    			break;
    
    		case 'windowstotimezone':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone[@type=\'' .$key . '\']', 'other', $key);
    			}
    			break;
    
    		case 'territorytotimezone':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem', 'type');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@type=\'' . $key . '\']', 'territory', $key);
    			}
    			break;
    
    		case 'timezonetoterritory':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem', 'territory');
    			foreach ($_temp as $key => $found) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@territory=\'' . $key . '\']', 'type', $key);
    			}
    			break;
    
    		case 'citytotimezone':
    			$_temp = $this->getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
    			foreach($_temp as $key => $found) {
    				$temp += $this->getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
    			}
    			break;
    
    		case 'timezonetocity':
    			$_temp  = $this->getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
    			$temp = array();
    			foreach($_temp as $key => $found) {
    				$temp += $this->getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
    				if (!empty($temp[$key])) {
    					$temp[$temp[$key]] = $key;
    				}
    				unset($temp[$key]);
    			}
    			break;
    
    		case 'phonetoterritory':
    			$_temp = $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
    			}
    			break;
    
    		case 'territorytophone':
    			$_temp = $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
    			foreach ($_temp as $key => $keyvalue) {
    				$val = $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
    				if (!isset($val[$key])) {
    					continue;
    				}
    				if (!isset($temp[$val[$key]])) {
    					$temp[$val[$key]] = $key;
    				} else {
    					$temp[$val[$key]] .= " " . $key;
    				}
    			}
    			break;
    
    		case 'numerictoterritory':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'numeric', $key);
    			}
    			break;
    
    		case 'territorytonumeric':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'numeric');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\'' . $key . '\']', 'type', $key);
    			}
    			break;
    
    		case 'alpha3toterritory':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'type');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\'' . $key . '\']', 'alpha3', $key);
    			}
    			break;
    
    		case 'territorytoalpha3':
    			$_temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes', 'alpha3');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\'' . $key . '\']', 'type', $key);
    			}
    			break;
    
    		case 'postaltoterritory':
    			$_temp = $this->getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex', 'territoryId');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $key . '\']', 'territoryId');
    			}
    			break;
    
    		case 'numberingsystem':
    			$_temp = $this->getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem', 'id');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . $key . '\']', 'digits', $key);
    				if (empty($temp[$key])) {
    					unset($temp[$key]);
    				}
    			}
    			break;
    
    		case 'chartofallback':
    			$_temp = $this->getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp2 = $this->getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
    				$temp[current($temp2)] = $key;
    			}
    			break;
    
    		case 'fallbacktochar':
    			$_temp = $this->getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
    			}
    			break;
    
    		case 'localeupgrade':
    			$_temp = $this->getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag', 'from');
    			foreach ($_temp as $key => $keyvalue) {
    				$temp += $this->getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $key . '\']', 'to', $key);
    			}
    			break;
    
    		case 'unit':
    			$_temp = $this->getFile($locale, '/ldml/units/unit', 'type');
    			foreach($_temp as $key => $keyvalue) {
    				$_temp2 = $this->getFile($locale, '/ldml/units/unit[@type=\'' . $key . '\']/unitPattern', 'count');
    				$temp[$key] = $_temp2;
    			}
    			break;
    
    		default :
    			throw new Exception\InvalidLdmlPathException("Unknown list ($path) for parsing locale data.");
    			break;
    	}
    
    	return $temp;
    }
    
    /**
     * Read the right LDML file
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @access private
     */
    private function getFile($locale , $path, $attribute = false, $value = false, $temp = array())
    {
    	$result = $this->findRoute($locale, $path, $attribute, $value, $temp);
    	if ($result) {
    		$temp = $this->readFile($locale, $path, $attribute, $value, $temp);
    	}
    	
    	// parse required locales reversive
    	// example: when given zh_Hans_CN
    	// 1. -> zh_Hans_CN
    	// 2. -> zh_Hans
    	// 3. -> zh
    	// 4. -> root
    	if (($locale != 'root') && ($result)) {
    		$locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
    		if (!empty($locale)) {
    			$temp = $this->getFile($locale, $path, $attribute, $value, $temp);
    		} else {
    			$temp = $this->getFile('root', $path, $attribute, $value, $temp);
    		}
    	}
    	return $temp;
    }
    
    /**
     * Find possible routing to other path or locale
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @param  array  $temp
     * @throws Zend_Locale_Exception
     * @access private
     */
    private function findRoute($locale, $path, $attribute, $value, &$temp)
    {
    	// load locale file if not already in cache
    	// needed for alias tag when referring to other locale
    	if (empty(self::$_ldml[(string) $locale])) {
    		$filename = dirname(__FILE__) . '/CLDR/' . $locale . '.xml';
    		if (!file_exists($filename)) {
    			throw new Exception\LdlmXmlNotFound("Missing locale file '$filename' for '$locale' locale.");
    		}
    
    		self::$_ldml[(string) $locale] = simplexml_load_file($filename);
    	}
    
    	// search for 'alias' tag in the search path for redirection
    	$search = '';
    	$tok = strtok($path, '/');
    
    	// parse the complete path
    	if (!empty(self::$_ldml[(string) $locale])) {
    		while ($tok !== false) {
    			$search .=  '/' . $tok;
    			if (strpos($search, '[@') !== false) {
    				while (strrpos($search, '[@') > strrpos($search, ']')) {
    					$tok = strtok('/');
    					if (empty($tok)) {
    						$search .= '/';
    					}
    					$search = $search . '/' . $tok;
    				}
    			}
    			$result = self::$_ldml[(string) $locale]->xpath($search . '/alias');
    
    			// alias found
    			if (!empty($result)) {
    
    				$source = $result[0]['source'];
    				$newpath = $result[0]['path'];
    
    				// new path - path //ldml is to ignore
    				if ($newpath != '//ldml') {
    					// other path - parse to make real path
    
    					while (substr($newpath,0,3) == '../') {
    						$newpath = substr($newpath, 3);
    						$search = substr($search, 0, strrpos($search, '/'));
    					}
    
    					// truncate ../ to realpath otherwise problems with alias
    					$path = $search . '/' . $newpath;
    					while (($tok = strtok('/'))!== false) {
    						$path = $path . '/' . $tok;
    					}
    				}
    
    				// reroute to other locale
    				if ($source != 'locale') {
    					$locale = $source;
    				}
    
    				$temp = $this->getFile($locale, $path, $attribute, $value, $temp);
    				return false;
    			}
    
    			$tok = strtok('/');
    		}
    	}
    	return true;
    }

    /**
     * Read the content from locale
     *
     * Can be called like:
     * <ldml>
     *     <delimiter>test</delimiter>
     *     <second type='myone'>content</second>
     *     <second type='mysecond'>content2</second>
     *     <third type='mythird' />
     * </ldml>
     *
     * Case 1: _readFile('ar','/ldml/delimiter')             -> returns [] = test
     * Case 1: _readFile('ar','/ldml/second[@type=myone]')   -> returns [] = content
     * Case 2: _readFile('ar','/ldml/second','type')         -> returns [myone] = content; [mysecond] = content2
     * Case 3: _readFile('ar','/ldml/delimiter',,'right')    -> returns [right] = test
     * Case 4: _readFile('ar','/ldml/third','type','myone')  -> returns [myone] = mythird
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $attribute
     * @param  string $value
     * @access private
     * @return array
     */
    private function readFile($locale, $path, $attribute, $value, $temp)
    {
        // without attribute - read all values
        // with attribute    - read only this value
        if (!empty(self::$_ldml[(string) $locale])) {
            $result = self::$_ldml[(string) $locale]->xpath($path);
            if (!empty($result)) {
                foreach ($result as &$found) {
                    if (empty($value)) {
                        if (empty($attribute)) {
                            // Case 1
                            $temp[] = (string) $found;
                        } else if (empty($temp[(string) $found[$attribute]])){
                            // Case 2
                            $temp[(string) $found[$attribute]] = (string) $found;
                        }
                    } else if (empty ($temp[$value])) {
                        if (empty($attribute)) {
                            // Case 3
                            $temp[$value] = (string) $found;
                        } else {
                            // Case 4
                            $temp[$value] = (string) $found[$attribute];
                        }
                    }
                }
            }
        }
        return $temp;
    }

    /**
     * Find the details for supplemental calendar datas
     *
     * @param  string $locale Locale for Detaildata
     * @param  array  $list   List to search
     * @return string         Key for Detaildata
     */
    private function calendarDetail($locale, $list)
    {
        $ret = "001";
        foreach ($list as $key => $value) {
            if (strpos($locale, '_') !== false) {
                $locale = substr($locale, strpos($locale, '_') + 1);
            }
            if (strpos($key, $locale) !== false) {
                $ret = $key;
                break;
            }
        }
        return $ret;
    }

    /**
     * Read the LDML file, get a single path defined value
     *
     * @param  string $locale
     * @param  string $path
     * @param  string $value
     * @return string
     * @access public
     */
    public function getContent($path, $value = false)
    {
    	$locale = (string) $this->getLocale();
    	
        $val = $value;
        if (is_array($value)) {
            $val = implode('_' , $value);
        }
        $val = urlencode($val);
        switch(strtolower($path)) {
            case 'language':
                $temp = $this->getFile($locale, '/ldml/localeDisplayNames/languages/language[@type=\'' . $value . '\']', 'type');
                break;

            case 'script':
                $temp = $this->getFile($locale, '/ldml/localeDisplayNames/scripts/script[@type=\'' . $value . '\']', 'type');
                break;

            case 'country':
            case 'territory':
                $temp = $this->getFile($locale, '/ldml/localeDisplayNames/territories/territory[@type=\'' . $value . '\']', 'type');
                break;

            case 'variant':
                $temp = $this->getFile($locale, '/ldml/localeDisplayNames/variants/variant[@type=\'' . $value . '\']', 'type');
                break;

            case 'key':
                $temp = $this->getFile($locale, '/ldml/localeDisplayNames/keys/key[@type=\'' . $value . '\']', 'type');
                break;

            case 'defaultcalendar':
                $temp = $this->getFile($locale, '/ldml/dates/calendars/default', 'choice', 'default');
                break;

            case 'monthcontext':
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/default', 'choice', 'context');
                break;

            case 'defaultmonth':
                if (empty ($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/months/monthContext[@type=\'format\']/default', 'choice', 'default');
                break;

            case 'month':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/months/monthContext[@type=\'' . $value[1] . '\']/monthWidth[@type=\'' . $value[2] . '\']/month[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'daycontext':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/default', 'choice', 'context');
                break;

            case 'defaultday':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/days/dayContext[@type=\'format\']/default', 'choice', 'default');
                break;

            case 'day':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/days/dayContext[@type=\'' . $value[1] . '\']/dayWidth[@type=\'' . $value[2] . '\']/day[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'quarter':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "format", "wide", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/quarters/quarterContext[@type=\'' . $value[1] . '\']/quarterWidth[@type=\'' . $value[2] . '\']/quarter[@type=\'' . $value[3] . '\']', 'type');
                break;

            case 'am':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/am', '', 'am');
                break;

            case 'pm':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/pm', '', 'pm');
                break;

            case 'era':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", "Abbr", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/eras/era' . $value[1] . '/era[@type=\'' . $value[2] . '\']', 'type');
                break;

            case 'defaultdate':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/dateFormats/default', 'choice', 'default');
                break;

            case 'date':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                break;

            case 'defaulttime':
                if (empty($value)) {
                    $value = "gregorian";
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value . '\']/timeFormats/default', 'choice', 'default');
                break;

            case 'time':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                break;

            case 'datetime':
                if (empty($value)) {
                    $value = array("gregorian", "medium");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }

                $date     = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateFormats/dateFormatLength[@type=\'' . $value[1] . '\']/dateFormat/pattern', '', 'pattern');
                $time     = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/timeFormats/timeFormatLength[@type=\'' . $value[1] . '\']/timeFormat/pattern', '', 'pattern');
                $datetime = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/dateTimeFormatLength[@type=\'' . $value[1] . '\']/dateTimeFormat/pattern', '', 'pattern');
                $temp = str_replace(array('{0}', '{1}'), array(current($time), current($date)), current($datetime));
                break;

            case 'dateitem':
                if (empty($value)) {
                    $value = array("gregorian", "yyMMdd");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/availableFormats/dateFormatItem[@id=\'' . $value[1] . '\']', '');
                break;

            case 'dateinterval':
                if (empty($value)) {
                    $value = array("gregorian", "yMd", "y");
                }
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp, $temp[0]);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/dateTimeFormats/intervalFormats/intervalFormatItem[@id=\'' . $value[1] . '\']/greatestDifference[@id=\'' . $value[2] . '\']', '');
                break;

            case 'field':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/fields/field[@type=\'' . $value[1] . '\']/displayName', '', $value[1]);
                break;

            case 'relative':
                if (!is_array($value)) {
                    $temp = $value;
                    $value = array("gregorian", $temp);
                }
                $temp = $this->getFile($locale, '/ldml/dates/calendars/calendar[@type=\'' . $value[0] . '\']/fields/field/relative[@type=\'' . $value[1] . '\']', '', $value[1]);
                break;

            case 'defaultnumberingsystem':
                $temp = $this->getFile($locale, '/ldml/numbers/defaultNumberingSystem', '', 'default');
                break;

            case 'decimalnumber':
                $temp = $this->getFile($locale, '/ldml/numbers/decimalFormats/decimalFormatLength/decimalFormat/pattern', '', 'default');
                break;

            case 'scientificnumber':
                $temp = $this->getFile($locale, '/ldml/numbers/scientificFormats/scientificFormatLength/scientificFormat/pattern', '', 'default');
                break;

            case 'percentnumber':
                $temp = $this->getFile($locale, '/ldml/numbers/percentFormats/percentFormatLength/percentFormat/pattern', '', 'default');
                break;

            case 'currencynumber':
                $temp = $this->getFile($locale, '/ldml/numbers/currencyFormats/currencyFormatLength/currencyFormat/pattern', '', 'default');
                break;

            case 'nametocurrency':
                $temp = $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                break;

            case 'currencytoname':
                $temp = $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/displayName', '', $value);
                $_temp = $this->getFile($locale, '/ldml/numbers/currencies/currency', 'type');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $key . '\']/displayName', '', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'currencysymbol':
                $temp = $this->getFile($locale, '/ldml/numbers/currencies/currency[@type=\'' . $value . '\']/symbol', '', $value);
                break;

            case 'question':
                $temp = $this->getFile($locale, '/ldml/posix/messages/' . $value . 'str',  '', $value);
                break;

            case 'currencyfraction':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'digits', 'digits');
                break;

            case 'currencyrounding':
                if (empty($value)) {
                    $value = "DEFAULT";
                }
                $temp = $this->getFile('supplementalData', '/supplementalData/currencyData/fractions/info[@iso4217=\'' . $value . '\']', 'rounding', 'rounding');
                break;

            case 'currencytoregion':
                $temp = $this->getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $value . '\']/currency', 'iso4217', $value);
                break;

            case 'regiontocurrency':
                $_temp = $this->getFile('supplementalData', '/supplementalData/currencyData/region', 'iso3166');
                $temp = array();
                foreach ($_temp as $key => $keyvalue) {
                    $val = $this->getFile('supplementalData', '/supplementalData/currencyData/region[@iso3166=\'' . $key . '\']/currency', 'iso4217', $key);
                    if (!isset($val[$key]) or ($val[$key] != $value)) {
                        continue;
                    }
                    if (!isset($temp[$val[$key]])) {
                        $temp[$val[$key]] = $key;
                    } else {
                        $temp[$val[$key]] .= " " . $key;
                    }
                }
                break;

            case 'regiontoterritory':
                $temp = $this->getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $value . '\']', 'contains', $value);
                break;

            case 'territorytoregion':
                $_temp2 = $this->getFile('supplementalData', '/supplementalData/territoryContainment/group', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += $this->getFile('supplementalData', '/supplementalData/territoryContainment/group[@type=\'' . $key . '\']', 'contains', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'scripttolanguage':
                $temp = $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'scripts', $value);
                break;

            case 'languagetoscript':
                $_temp2 = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'scripts', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'territorytolanguage':
                $temp = $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $value . '\']', 'territories', $value);
                break;

            case 'languagetoterritory':
                $_temp2 = $this->getFile('supplementalData', '/supplementalData/languageData/language', 'type');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += $this->getFile('supplementalData', '/supplementalData/languageData/language[@type=\'' . $key . '\']', 'territories', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'timezonetowindows':
                $temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone[@other=\''.$value.'\']', 'type', $value);
                break;

            case 'windowstotimezone':
                $temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/mapTimezones[@type=\'windows\']/mapZone[@type=\''.$value.'\']', 'other', $value);
                break;

            case 'territorytotimezone':
                $temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@type=\'' . $value . '\']', 'territory', $value);
                break;

            case 'timezonetoterritory':
                $temp = $this->getFile('supplementalData', '/supplementalData/timezoneData/zoneFormatting/zoneItem[@territory=\'' . $value . '\']', 'type', $value);
                break;

            case 'citytotimezone':
                $temp = $this->getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $value . '\']/exemplarCity', '', $value);
                break;

            case 'timezonetocity':
                $_temp  = $this->getFile($locale, '/ldml/dates/timeZoneNames/zone', 'type');
                $temp = array();
                foreach($_temp as $key => $found) {
                    $temp += $this->getFile($locale, '/ldml/dates/timeZoneNames/zone[@type=\'' . $key . '\']/exemplarCity', '', $key);
                    if (!empty($temp[$key])) {
                        if ($temp[$key] == $value) {
                            $temp[$temp[$key]] = $key;
                        }
                    }
                    unset($temp[$key]);
                }
                break;

            case 'phonetoterritory':
                $temp = $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $value . '\']/telephoneCountryCode', 'code', $value);
                break;

            case 'territorytophone':
                $_temp2 = $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory', 'territory');
                $_temp = array();
                foreach ($_temp2 as $key => $found) {
                    $_temp += $this->getFile('telephoneCodeData', '/supplementalData/telephoneCodeData/codesByTerritory[@territory=\'' . $key . '\']/telephoneCountryCode', 'code', $key);
                }
                $temp = array();
                foreach($_temp as $key => $found) {
                    $_temp3 = explode(" ", $found);
                    foreach($_temp3 as $found3) {
                        if ($found3 !== $value) {
                            continue;
                        }
                        if (!isset($temp[$found3])) {
                            $temp[$found3] = (string) $key;
                        } else {
                            $temp[$found3] .= " " . $key;
                        }
                    }
                }
                break;

            case 'numerictoterritory':
                $temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'numeric', $value);
                break;

            case 'territorytonumeric':
                $temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@numeric=\''.$value.'\']', 'type', $value);
                break;

            case 'alpha3toterritory':
                $temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@type=\''.$value.'\']', 'alpha3', $value);
                break;

            case 'territorytoalpha3':
                $temp = $this->getFile('supplementalData', '/supplementalData/codeMappings/territoryCodes[@alpha3=\''.$value.'\']', 'type', $value);
                break;

            case 'postaltoterritory':
                $temp = $this->getFile('postalCodeData', '/supplementalData/postalCodeData/postCodeRegex[@territoryId=\'' . $value . '\']', 'territoryId');
                break;

            case 'numberingsystem':
                $temp = $this->getFile('numberingSystems', '/supplementalData/numberingSystems/numberingSystem[@id=\'' . strtolower($value) . '\']', 'digits', $value);
                break;

            case 'chartofallback':
                $_temp = $this->getFile('characters', '/supplementalData/characters/character-fallback/character', 'value');
                foreach ($_temp as $key => $keyvalue) {
                    $temp2 = $this->getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $key . '\']/substitute', '', $key);
                    if (current($temp2) == $value) {
                        $temp = $key;
                    }
                }
                break;

                $temp = $this->getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '', $value);
                break;

            case 'fallbacktochar':
                $temp = $this->getFile('characters', '/supplementalData/characters/character-fallback/character[@value=\'' . $value . '\']/substitute', '');
                break;

            case 'localeupgrade':
                $temp = $this->getFile('likelySubtags', '/supplementalData/likelySubtags/likelySubtag[@from=\'' . $value . '\']', 'to', $value);
                break;

            case 'unit':
                $temp = $this->getFile($locale, '/ldml/units/unit[@type=\'' . $value[0] . '\']/unitPattern[@count=\'' . $value[1] . '\']', '');
                break;

            default :
                require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Unknown detail ($path) for parsing locale data.");
                break;
        }

        if (is_array($temp)) {
            $temp = current($temp);
        }
        if (isset(self::$_cache)) {
            if (self::$_cacheTags) {
                self::$_cache->save( serialize($temp), $id, array('Zend_Locale'));
            } else {
                self::$_cache->save( serialize($temp), $id);
            }
        }

        return $temp;
    }

	
}

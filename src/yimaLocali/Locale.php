<?php
namespace yimaLocali;

use yimaLocali\LDMLReader;

class Locale
{
	/**
	 * Actual set locale
	 *
	 * @var string Locale
	 */
	protected $locale;
	
	/**
	 * Faghat Locale haaye valid ghabel e set shodan baashad
	 * 
	 * @var boolean
	 */
	protected $allowValidLocale = true;
	
	/**
	 * Locale data markup language Parser
	 * 
	 * @var yimaLocali\LDLMReader
	 */
	protected $ldmlReader;
	
	/**
	 * Locale plugin manager
	 *
	 * @var LocalePluginManager
	 */
	protected $localePluginManager;
	
	/**
	 * Class wide Locale Constants
	 *
	 * @var array $_localeData
	 */
	private static $localeData = array(
		'root'  => true, 'aa_DJ' => true, 'aa_ER' => true, 'aa_ET' => true, 'aa'    => true,
		'af_NA' => true, 'af_ZA' => true, 'af'    => true, 'ak_GH' => true, 'ak'    => true,
		'am_ET' => true, 'am'    => true, 'ar_AE' => true, 'ar_BH' => true, 'ar_DZ' => true,
		'ar_EG' => true, 'ar_IQ' => true, 'ar_JO' => true, 'ar_KW' => true, 'ar_LB' => true,
		'ar_LY' => true, 'ar_MA' => true, 'ar_OM' => true, 'ar_QA' => true, 'ar_SA' => true,
		'ar_SD' => true, 'ar_SY' => true, 'ar_TN' => true, 'ar_YE' => true, 'ar'    => true,
		'as_IN' => true, 'as'    => true, 'az_AZ' => true, 'az'    => true, 'be_BY' => true,
		'be'    => true, 'bg_BG' => true, 'bg'    => true, 'bn_BD' => true, 'bn_IN' => true,
		'bn'    => true, 'bo_CN' => true, 'bo_IN' => true, 'bo'    => true, 'bs_BA' => true,
		'bs'    => true, 'byn_ER'=> true, 'byn'   => true, 'ca_ES' => true, 'ca'    => true,
		'cch_NG'=> true, 'cch'   => true, 'cop'   => true, 'cs_CZ' => true, 'cs'    => true,
		'cy_GB' => true, 'cy'    => true, 'da_DK' => true, 'da'    => true, 'de_AT' => true,
		'de_BE' => true, 'de_CH' => true, 'de_DE' => true, 'de_LI' => true, 'de_LU' => true,
		'de'    => true, 'dv_MV' => true, 'dv'    => true, 'dz_BT' => true, 'dz'    => true,
		'ee_GH' => true, 'ee_TG' => true, 'ee'    => true, 'el_CY' => true, 'el_GR' => true,
		'el'    => true, 'en_AS' => true, 'en_AU' => true, 'en_BE' => true, 'en_BW' => true,
		'en_BZ' => true, 'en_CA' => true, 'en_GB' => true, 'en_GU' => true, 'en_HK' => true,
		'en_IE' => true, 'en_IN' => true, 'en_JM' => true, 'en_MH' => true, 'en_MP' => true,
		'en_MT' => true, 'en_NA' => true, 'en_NZ' => true, 'en_PH' => true, 'en_PK' => true,
		'en_SG' => true, 'en_TT' => true, 'en_UM' => true, 'en_US' => true, 'en_VI' => true,
		'en_ZA' => true, 'en_ZW' => true, 'en'    => true, 'eo'    => true, 'es_AR' => true,
		'es_BO' => true, 'es_CL' => true, 'es_CO' => true, 'es_CR' => true, 'es_DO' => true,
		'es_EC' => true, 'es_ES' => true, 'es_GT' => true, 'es_HN' => true, 'es_MX' => true,
		'es_NI' => true, 'es_PA' => true, 'es_PE' => true, 'es_PR' => true, 'es_PY' => true,
		'es_SV' => true, 'es_US' => true, 'es_UY' => true, 'es_VE' => true, 'es'    => true,
		'et_EE' => true, 'et'    => true, 'eu_ES' => true, 'eu'    => true, 'fa_AF' => true,
		'fa_IR' => true, 'fa'    => true, 'fi_FI' => true, 'fi'    => true, 'fil_PH'=> true,
		'fil'   => true, 'fo_FO' => true, 'fo'    => true, 'fr_BE' => true, 'fr_CA' => true,
		'fr_CH' => true, 'fr_FR' => true, 'fr_LU' => true, 'fr_MC' => true, 'fr_SN' => true,
		'fr'    => true, 'fur_IT'=> true, 'fur'   => true, 'ga_IE' => true, 'ga'    => true,
		'gaa_GH'=> true, 'gaa'   => true, 'gez_ER'=> true, 'gez_ET'=> true, 'gez'   => true,
		'gl_ES' => true, 'gl'    => true, 'gsw_CH'=> true, 'gsw'   => true, 'gu_IN' => true,
		'gu'    => true, 'gv_GB' => true, 'gv'    => true, 'ha_GH' => true, 'ha_NE' => true,
		'ha_NG' => true, 'ha_SD' => true, 'ha'    => true, 'haw_US'=> true, 'haw'   => true,
		'he_IL' => true, 'he'    => true, 'hi_IN' => true, 'hi'    => true, 'hr_HR' => true,
		'hr'    => true, 'hu_HU' => true, 'hu'    => true, 'hy_AM' => true, 'hy'    => true,
		'ia'    => true, 'id_ID' => true, 'id'    => true, 'ig_NG' => true, 'ig'    => true,
		'ii_CN' => true, 'ii'    => true, 'in'    => true, 'is_IS' => true, 'is'    => true,
		'it_CH' => true, 'it_IT' => true, 'it'    => true, 'iu'    => true, 'iw'    => true,
		'ja_JP' => true, 'ja'    => true, 'ka_GE' => true, 'ka'    => true, 'kaj_NG'=> true,
		'kaj'   => true, 'kam_KE'=> true, 'kam'   => true, 'kcg_NG'=> true, 'kcg'   => true,
		'kfo_CI'=> true, 'kfo'   => true, 'kk_KZ' => true, 'kk'    => true, 'kl_GL' => true,
		'kl'    => true, 'km_KH' => true, 'km'    => true, 'kn_IN' => true, 'kn'    => true,
		'ko_KR' => true, 'ko'    => true, 'kok_IN'=> true, 'kok'   => true, 'kpe_GN'=> true,
		'kpe_LR'=> true, 'kpe'   => true, 'ku_IQ' => true, 'ku_IR' => true, 'ku_SY' => true,
		'ku_TR' => true, 'ku'    => true, 'kw_GB' => true, 'kw'    => true, 'ky_KG' => true,
		'ky'    => true, 'ln_CD' => true, 'ln_CG' => true, 'ln'    => true, 'lo_LA' => true,
		'lo'    => true, 'lt_LT' => true, 'lt'    => true, 'lv_LV' => true, 'lv'    => true,
		'mk_MK' => true, 'mk'    => true, 'ml_IN' => true, 'ml'    => true, 'mn_CN' => true,
		'mn_MN' => true, 'mn'    => true, 'mo'    => true, 'mr_IN' => true, 'mr'    => true,
		'ms_BN' => true, 'ms_MY' => true, 'ms'    => true, 'mt_MT' => true, 'mt'    => true,
		'my_MM' => true, 'my'    => true, 'nb_NO' => true, 'nb'    => true, 'nds_DE'=> true,
		'nds'   => true, 'ne_IN' => true, 'ne_NP' => true, 'ne'    => true, 'nl_BE' => true,
		'nl_NL' => true, 'nl'    => true, 'nn_NO' => true, 'nn'    => true, 'no'    => true,
		'nr_ZA' => true, 'nr'    => true, 'nso_ZA'=> true, 'nso'   => true, 'ny_MW' => true,
		'ny'    => true, 'oc_FR' => true, 'oc'    => true, 'om_ET' => true, 'om_KE' => true,
		'om'    => true, 'or_IN' => true, 'or'    => true, 'pa_IN' => true, 'pa_PK' => true,
		'pa'    => true, 'pl_PL' => true, 'pl'    => true, 'ps_AF' => true, 'ps'    => true,
		'pt_BR' => true, 'pt_PT' => true, 'pt'    => true, 'ro_MD' => true, 'ro_RO' => true,
		'ro'    => true, 'ru_RU' => true, 'ru_UA' => true, 'ru'    => true, 'rw_RW' => true,
		'rw'    => true, 'sa_IN' => true, 'sa'    => true, 'se_FI' => true, 'se_NO' => true,
		'se'    => true, 'sh_BA' => true, 'sh_CS' => true, 'sh_YU' => true, 'sh'    => true,
		'si_LK' => true, 'si'    => true, 'sid_ET'=> true, 'sid'   => true, 'sk_SK' => true,
		'sk'    => true, 'sl_SI' => true, 'sl'    => true, 'so_DJ' => true, 'so_ET' => true,
		'so_KE' => true, 'so_SO' => true, 'so'    => true, 'sq_AL' => true, 'sq'    => true,
		'sr_BA' => true, 'sr_CS' => true, 'sr_ME' => true, 'sr_RS' => true, 'sr_YU' => true,
		'sr'    => true, 'ss_SZ' => true, 'ss_ZA' => true, 'ss'    => true, 'st_LS' => true,
		'st_ZA' => true, 'st'    => true, 'sv_FI' => true, 'sv_SE' => true, 'sv'    => true,
		'sw_KE' => true, 'sw_TZ' => true, 'sw'    => true, 'syr_SY'=> true, 'syr'   => true,
		'ta_IN' => true, 'ta'    => true, 'te_IN' => true, 'te'    => true, 'tg_TJ' => true,
		'tg'    => true, 'th_TH' => true, 'th'    => true, 'ti_ER' => true, 'ti_ET' => true,
		'ti'    => true, 'tig_ER'=> true, 'tig'   => true, 'tl'    => true, 'tn_ZA' => true,
		'tn'    => true, 'to_TO' => true, 'to'    => true, 'tr_TR' => true, 'tr'    => true,
		'trv_TW'=> true, 'trv'   => true, 'ts_ZA' => true, 'ts'    => true, 'tt_RU' => true,
		'tt'    => true, 'ug_CN' => true, 'ug'    => true, 'uk_UA' => true, 'uk'    => true,
		'ur_IN' => true, 'ur_PK' => true, 'ur'    => true, 'uz_AF' => true, 'uz_UZ' => true,
		'uz'    => true, 've_ZA' => true, 've'    => true, 'vi_VN' => true, 'vi'    => true,
		'wal_ET'=> true, 'wal'   => true, 'wo_SN' => true, 'wo'    => true, 'xh_ZA' => true,
		'xh'    => true, 'yo_NG' => true, 'yo'    => true, 'zh_CN' => true, 'zh_HK' => true,
		'zh_MO' => true, 'zh_SG' => true, 'zh_TW' => true, 'zh'    => true, 'zu_ZA' => true,
		'zu'    => true
	);
	
	public function __construct($locale, $allowValidLocale = true)
	{
		$this->setAllowValidLocale($allowValidLocale);
		$this->setLocale($locale);
		
		$this->setLdmlReader(new LDMLReader($this));
	}
	
	/**
	 * Sets a new locale
	 *
	 * @param  string|Zend_Locale $locale (Optional) New locale to set
	 * @return void
	 */
	public function setLocale($locale)
	{
		if ($this->allowValidLocale) {
			if (false == $this->isValidLocale( (string) $locale) ) {
				throw new Exception\InvalidLocaleException(sprintf(
    				'The Locale "%s" not universe valid locale.', $locale
    			));
			}
		}
		
		$this->locale = (string) $locale;
		return $this;
	}
	
	public function setAllowValidLocale($bool = true) 
	{
		$this->allowValidLocale = (boolean) $bool; 
		return $this;
	}
	
	/**
	 * Returns a string representation of the object
	 *
	 * @return string
	 */
	public function toString()
	{
		return (string) $this->locale;
	}
	
	/**
	 * Returns a string representation of the object
	 * Alias for toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	
	/**
	 * Overloading: proxy to plugin helpers
	 *
	 * Proxies to the attached plugin manager to retrieve, return, and potentially
	 * execute helpers.
	 *
	 * * If the helper does not define __invoke, it will be returned
	 * * If the helper does define __invoke, it will be called as a functor
	 *
	 * @param  string $method
	 * @param  array $argv
	 * @return mixed
	 */
	public function __call($method, $argv)
	{
		$helper = $this->plugin($method);
		if (is_callable($helper)) {
			return call_user_func_array($helper, $argv);
		}
		return $helper;
	}
	
	/**
	 * Get plugin instance
	 *
	 * @param  string     $name Name of plugin to return
	 * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
	 * @return AbstractHelper
	 */
	public function plugin($name, array $options = null)
	{
		return $this->getPluginManager()->get($name, $options);
	}
	
	/**
	 * Set plugin manager instance
	 *
	 * @param  string|LocalePluginManager $helpers
	 * @throws Exception\InvalidArgumentException
	 */
	public function setPluginManager($helpers)
	{
		if (is_string($helpers)) {
			if (!class_exists($helpers)) {
				throw new Exception\InvalidArgumentException(sprintf(
					'Invalid helper helpers class provided (%s)',
					$helpers
				));
			}
			$helpers = new $helpers();
		}
		if (!$helpers instanceof LocalePluginManager) {
			throw new Exception\InvalidArgumentException(sprintf(
				'Helper helpers must extend Zend\View\HelperPluginManager; got type "%s" instead',
				(is_object($helpers) ? get_class($helpers) : gettype($helpers))
			));
		}
		$helpers->setLocale($this);
		$this->localePluginManager = $helpers;
	
		return $this;
	}
	
	/**
	 * Get plugin manager instance
	 *
	 * @return HelperPluginManager
	 */
	public function getPluginManager()
	{
		if (null === $this->localePluginManager) {
			$this->setPluginManager(new LocalePluginManager());
		}
		return $this->localePluginManager;
	}
	
	
	public function setLdmlReader(LDMLReader $reader)
	{
		if (! $this->isEqualTo($reader->getLocale())) {
			$reader->setLocale($this);
		}
		
		$this->ldmlReader = $reader;
		
		return $this;
	}
	
	public function getLdmlReader()
	{
		return $this->ldmlReader;
	}
	
	/**
	 * Checks if a locale identifier is a real locale or not
	 * Examples:
	 * "en_XX" refers to "en", which returns true
	 * "XX_yy" refers to "root", which returns false
	 *
	 * @param  string|Zend_Locale $locale     Locale to check for
	 * @param  boolean            $strict     (Optional) If true, no rerouting will be done when checking
	 * @return boolean If the locale is known dependend on the settings
	 */
	public function isValidLocale($locale = null, $strict = true)
	{
		$locale = (empty($locale)) ? (string) $this : $locale;
		
		if (! is_string($locale) ) {
			return false;
		}
		
		if (isset(self::$localeData[$locale])) {
			return true;
		} else if (!$strict) {
			if (strstr($locale,'_')) {
				$locale = substr($locale,0,strpos($locale,'_'));
			}
		}
	
		return isset(self::$localeData[$locale]);
	}
	
	/**
	 * Returns the language part of the locale
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		$locale = explode('_', $this->locale);
		return $locale[0];
	}
	
	/**
	 * Returns the region part of the locale if available
	 *
	 * @return string|false - Regionstring
	 */
	public function getRegion()
	{
		$locale = explode('_', $this->locale);
		if (isset($locale[1]) === true) {
			return $locale[1];
		}
	
		return false;
	}
	
	/**
	 * Returns true if both locales are equal
	 *
	 * @param  yimaLocali\Locale $object Locale to check for equality
	 * @return boolean
	 */
	public function isEqualTo(self $object)
	{
		if ($object->toString() === $this->toString()) {
			return true;
		}
	
		return false;
	}
	
}

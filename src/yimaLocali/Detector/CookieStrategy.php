<?php
namespace yimaLocali\Detector;

use yimaLocali\Detector\Feature\SetProgrammabilityLocaleInterface;

use Zend\Console\Console;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;

class CookieStrategy extends DetectorAbstract implements
	SetProgrammabilityLocaleInterface
{
    const COOKIE_NAME = 'yimalocali_locale';

    /**
     * Request Object
     *
     * @var \Zend\Http\Request
     */
    protected $request;
    
    /**
     * Response Object
     */
    protected $response;
    
    public function getLocale()
    {
    	if (Console::isConsole()) {
    		// not supported on console
    		return;
    	}

    	$request = $this->getRequest();
    	
    	$cookie = $request->getCookie();
    	if (!$cookie || !$cookie->offsetExists(self::COOKIE_NAME)) {
    		return;
    	}
    	
    	$locale = $cookie->offsetGet(self::COOKIE_NAME);
    	if ($this->isValidLocale($locale)) {
    		return $locale;
    	}
    	
    	return false;
    }
    
    // Implemented Features ........................................................................................
    
    /**
     * @param string $locale, null for proxy to resetLocale
     * 
     * @throws \Exception
     */
    public function setLocale($locale)
    {
    	if (Console::isConsole()) {
    		// not supported on console
    		return $this;
    	}
    	
    	$locale = (string) $locale;
    	if (false == $this->isValidLocale($locale) && !empty($locale)) {
    		throw new \Exception(sprintf(
    			'The Locale "%s" not supported and are invalid.', $locale
    		));
    	}
    	
    	$request = $this->getRequest();
    	$cookie  = $request->getCookie();
    	
    	// Omit Set-Cookie header when cookie is present
    	if ($cookie instanceof Cookie
    		&& $cookie->offsetExists(self::COOKIE_NAME)
    		&& $locale === $cookie->offsetGet(self::COOKIE_NAME)
    	) {
    		return;
    	}
    	
    	$response = $this->getResponse();
    	$cookies  = $response->getCookie();
    	
    	$setCookie = new SetCookie(self::COOKIE_NAME, $locale);
    	$response->getHeaders()->addHeader($setCookie);
    	
    	return $this;
    }
    
    public function resetLocale()
    {
    	return $this->setLocale('');
    }
    
    // Inside Class Usage ..........................................................................................
    
    public function getRequest()
    {
    	if ($this->request) {
    		return $this->request;
    	}
    	 
    	$serviceLocator = $this->getServiceLocator();
    	if ($serviceLocator->has('request')) {
    		$request = $serviceLocator->get('Application')->getRequest();
    	} else {
    		$request = new HttpRequest();
    	}
    	
    	if (!$request instanceof HttpRequest) {
    		throw new \Exception(sprintf(
    			'Request must be instance of "HttpRequest" but "%s" given',get_class($request)
    		));
    	}
    	 
    	$this->request = $request;
    	return $this->request;
    }
    
    public function getResponse()
    {
    	if ($this->response) {
    		return $this->response;
    	}
    
    	$serviceLocator = $this->getServiceLocator();
    	if (! $serviceLocator->has('response')) {
    		return;
    	}
    	
    	$response = $serviceLocator->get('Application')->getResponse();
    
    	$this->setResponse($response);
    	return $this->getResponse();
    }
    
    public function setResponse($response = null)
    {
    	# reset request to default
    	if ($response == null) {
    		$this->response = null;
    	}
     
    	if (! $response instanceof HttpResponse) {
    		throw new \Exception(sprintf(
    		'Request must be instance of "HttpResponse" but "%s" given',get_class($response)
    		));
    	}
     
    	$this->response = $response;
    	return $this;
    }
    
}
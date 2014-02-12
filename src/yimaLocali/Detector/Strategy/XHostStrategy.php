<?php
namespace SlmLocale\Strategy;

use SlmLocale\LocaleEvent;
use Zend\Http\PhpEnvironment\Response;

class HostStrategy extends AbstractStrategy
{
    const LOCALE_KEY           = ':locale';
    const REDIRECT_STATUS_CODE = 302;

    protected $domain;

    public function setOptions(array $options = array())
    {
        if (array_key_exists('domain', $options)) {
            $this->domain = $options['domain'];
        }
    }

    public function detect(LocaleEvent $event)
    {
        $request = $event->getRequest();
        $host    = $request->getUri()->getHost();

        $pattern = str_replace(self::LOCALE_KEY, '([a-zA-Z-_]+)', $this->domain);
        $pattern = sprintf('@%s@', $pattern);
        preg_match($pattern, $host, $matches);

        if (!array_key_exists(1, $matches)) {
            return;
        }
        $locale = $matches[1];

        if ($event->hasSupported()
            && ($supported = $event->getSupported())
            && !array_key_exists($locale, $supported)) {
            return;
        }

        return $locale;
    }

    public function found(LocaleEvent $event)
    {
        $uri     = $event->getRequest()->getUri();
        $locale  = $event->getLocale();

        if (null === $locale) {
            return;
        }

        $host = str_replace(self::LOCALE_KEY, $locale, $this->domain);

        if ($host === $uri->getHost()) {
            return;
        }

        /**
         * @todo Use factory or something? Port can be non-default, user/password can be set, query parameters are missing now
         */
        $location = $uri->getScheme() . '://' . $host . $uri->getPath();
        $response = $event->getResponse();
        $response->setStatusCode(self::REDIRECT_STATUS_CODE);
        $response->getHeaders()->addHeaderLine('Location', $location);

        $response->send();
    }
}
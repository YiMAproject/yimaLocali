<?php
namespace SlmLocale\Strategy;

use Locale;
use SlmLocale\LocaleEvent;
use Zend\Http\Request as HttpRequest;

class HttpAcceptLanguageStrategy extends AbstractStrategy
{
    public function detect(LocaleEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return;
        }

        $headers = $request->getHeaders();

        if ($lookup = $event->hasSupported()) {
            $supported = $event->getSupported();
        }

        if ($headers->has('Accept-Language')) {
            $locales = $headers->get('Accept-Language')->getPrioritized();

            foreach ($locales as $locale) {
                $locale = $locale->getLanguage();

                if (!$lookup) {
                    return $locale;
                }

                if ($match = Locale::lookup($supported, $locale)) {
                    return $locale;
                }
            }
        }
    }
}
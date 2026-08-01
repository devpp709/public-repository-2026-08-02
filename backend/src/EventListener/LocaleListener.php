<?php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST)]
class LocaleListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Проверяем заголовок X-Locale от мобильного приложения
        $locale = $request->headers->get('X-Locale');

        if (!$locale) {
            // Если нет заголовка, используем параметр запроса или куки
            $locale = $request->query->get('lang');
        }

        if (!$locale) {
            // Используем Accept-Language
            $locale = $request->getPreferredLanguage(['ru', 'en', 'kz']);
        }

        if ($locale && in_array($locale, ['ru', 'en', 'kz'])) {
            $request->setLocale($locale);
        }
    }
}

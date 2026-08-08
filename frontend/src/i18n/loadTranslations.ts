import i18n from './config';
import { translationService } from '../services/translationService';

const CACHE_KEY = 'translations_cache_v7';

interface TranslationCache {
    [locale: string]: any;
}

const getCache = (): TranslationCache => {
    if (typeof window === 'undefined') return {};

    const data = localStorage.getItem(CACHE_KEY);
    return data ? JSON.parse(data) : {};
};

const saveCache = (cache: TranslationCache) => {
    localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
};


export const loadTranslations = async (locale: string): Promise<boolean> => {
    try {

        const cache = getCache();

        // берем из localStorage
        if (cache[locale]) {
            console.log('Translations from cache:', locale);

            Object.keys(cache[locale]).forEach(namespace => {
                i18n.addResources(
                    locale,
                    namespace,
                    cache[locale][namespace]
                );
            });

            await i18n.changeLanguage(locale);

            return true;
        }


        // первый раз идем на Symfony
        console.log('Loading translations from API:', locale);

        const translations = await translationService.getTranslations(locale);


        if (translations && typeof translations === 'object') {

            const namespaces =
                translations.common ||
                translations.validation ||
                translations.car
                    ? translations
                    : {
                        common: translations
                    };


            Object.keys(namespaces).forEach(namespace => {

                i18n.addResources(
                    locale,
                    namespace,
                    namespaces[namespace]
                );

            });


            // сохраняем
            cache[locale] = namespaces;
            saveCache(cache);
        }


        return true;

    } catch (error) {
        console.error(error);
        throw error;
    }
};

export const preloadTranslations = async (
    locales: string[]
): Promise<void> => {

    await Promise.all(
        locales.map(locale => loadTranslations(locale))
    );

};

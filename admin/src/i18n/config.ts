import LanguageDetector from 'i18next-browser-languagedetector';
import i18n, { InitOptions } from 'i18next';
import { initReactI18next } from 'react-i18next';

const options: InitOptions = {
    lng: 'ru',
    fallbackLng: 'ru',
    debug: process.env.NODE_ENV === 'development',

    ns: [],
    defaultNS: 'admin',

    detection: {
        order: ['querystring', 'cookie', 'localStorage', 'navigator'],
        caches: ['localStorage', 'cookie'],
        lookupQuerystring: 'lang',
        lookupCookie: 'lang',
        lookupLocalStorage: 'lang',
    },

    interpolation: {
        escapeValue: false,
    },

    react: {
        useSuspense: false,
    }
};


i18n
    .use(LanguageDetector)
    .use(initReactI18next)
    .init(options);

i18n.on('missingKey', (lngs, namespace, key) => {
    // Сохраняем только язык и ключ
    if (typeof window !== 'undefined') {
        try {
            const missingKey = `${lngs}:${namespace}.${key}`;
            const stored = localStorage.getItem('missing_translate');
            const missing = stored ? JSON.parse(stored) : [];

            if (!missing.includes(missingKey)) {
                missing.push(missingKey);
                localStorage.setItem('missing_translate', JSON.stringify(missing));
                console.log('💾 Missing:', missingKey);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
});
export default i18n;
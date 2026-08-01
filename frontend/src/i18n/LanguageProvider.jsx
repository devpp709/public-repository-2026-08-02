import React, { createContext, useContext, useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import i18n from './config';
import { loadTranslations } from './loadTranslations';
import { translationService } from '../services/translationService';

const LanguageContext = createContext();

export const useLanguage = () => {
    const context = useContext(LanguageContext);
    if (!context) {
        throw new Error('useLanguage must be used within LanguageProvider');
    }
    return context;
};

export const LanguageProvider = ({ children }) => {
    const router = useRouter();
    const [currentLanguage, setCurrentLanguage] = useState('ru');
    const [availableLanguages, setAvailableLanguages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [translationsLoaded, setTranslationsLoaded] = useState(false);

    // Загрузка языков и переводов при инициализации
    useEffect(() => {
        const initializeLanguage = async () => {
            try {
                // Получаем язык из URL (для Next.js)
                const pathLocale = router.locale || 'ru';

                console.log('Initializing language:', pathLocale);

                // Загружаем доступные языки с бэкенда
                const languages = await translationService.getAvailableLanguages();
                setAvailableLanguages(languages);

                // Загружаем переводы для текущего языка
                await loadTranslations(pathLocale);

                setCurrentLanguage(pathLocale);
                i18n.changeLanguage(pathLocale);
                setTranslationsLoaded(true);
                setLoading(false);
            } catch (error) {
                console.error('Failed to initialize language:', error);
                setLoading(false);
            }
        };

        initializeLanguage();
    }, [router.locale]);

    // Функция смены языка
    // Функция смены языка
    const changeLanguage = async (newLocale) => {
        try {
            if (!newLocale) {
                console.error('changeLanguage: newLocale is undefined or null');
                return;
            }

            console.log('Changing language to:', newLocale);
            setLoading(true);

            await loadTranslations(newLocale);
            await i18n.changeLanguage(newLocale);
            localStorage.setItem('lang', newLocale);
            await router.push(router.asPath, router.asPath, { locale: newLocale });

            setCurrentLanguage(newLocale);
            setLoading(false);
        } catch (error) {
            console.error('Failed to change language:', error);
            setLoading(false);
        }
    };

    const value = {
        currentLanguage,
        availableLanguages,
        loading,
        translationsLoaded,
        changeLanguage,
        t: i18n.t.bind(i18n),
    };

    if (!translationsLoaded) {
        return null;
    }

    return (
        <LanguageContext.Provider value={value}>
            {children}
        </LanguageContext.Provider>
    );
};
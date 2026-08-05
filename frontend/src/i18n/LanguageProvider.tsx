// src/i18n/LanguageProvider.tsx
import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { useRouter } from 'next/router';
import i18n from './config';
import { loadTranslations } from './loadTranslations';
import { translationService } from '../services/translationService';

interface LanguageContextType {
    currentLanguage: string;
    availableLanguages: string[];
    loading: boolean;
    translationsLoaded: boolean;
    changeLanguage: (newLocale: string) => Promise<void>;
    t: (key: string, options?: any) => string;
}

interface LanguageProviderProps {
    children: ReactNode;
}

const LanguageContext = createContext<LanguageContextType | undefined>(undefined);

export const useLanguage = (): LanguageContextType => {
    const context = useContext(LanguageContext);
    if (!context) {
        throw new Error('useLanguage must be used within LanguageProvider');
    }
    return context;
};

export const LanguageProvider = ({ children }: LanguageProviderProps) => {
    const router = useRouter();
    const [currentLanguage, setCurrentLanguage] = useState<string>('ru');
    const [availableLanguages, setAvailableLanguages] = useState<string[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [translationsLoaded, setTranslationsLoaded] = useState<boolean>(false);

    // Загрузка языков и переводов при инициализации
    useEffect(() => {
        const initializeLanguage = async () => {
            try {
                // Получаем язык из URL (для Next.js)
                const pathLocale = router.locale || 'ru';
                await loadTranslations(pathLocale);

                setCurrentLanguage(pathLocale);
                i18n.changeLanguage(pathLocale);
                console.log('Initializing language:', pathLocale);

                // Загружаем доступные языки с бэкенда
                let languages;

                const cachedLanguages = localStorage.getItem('available_languages');

                if (cachedLanguages) {
                    languages = JSON.parse(cachedLanguages);
                } else {
                    languages = await translationService.getAvailableLanguages();

                    localStorage.setItem(
                        'available_languages',
                        JSON.stringify(languages)
                    );
                }

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
    const changeLanguage = async (newLocale: string): Promise<void> => {
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

    const value: LanguageContextType = {
        currentLanguage,
        availableLanguages,
        loading,
        translationsLoaded,
        changeLanguage,
        t: i18n.t.bind(i18n),
    };

    if (!translationsLoaded) {
        return (
            <div>
                Loading...
            </div>
        );
    }

    return (
        <LanguageContext.Provider value={value}>
            {children}
        </LanguageContext.Provider>
    );
};
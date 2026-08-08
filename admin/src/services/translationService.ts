import { api } from './api';


export interface TranslationResponse {
    [namespace: string]: Record<string, string>;
}

interface Language {
    code: string;
    name: string;
    nativeName: string;
}

export const translationService = {

    getTranslations: async (
        locale: string
    ): Promise<TranslationResponse> => {
        try {
            const response = await api.get<TranslationResponse>(
                `/api/v1/translations/language/${locale}/admin`
            );

            return response;

        } catch (error) {
            console.error('Error fetching translations:', error);
            throw error;
        }
    },


    getTranslation: async (
        locale: string,
        key: string,
        namespace = 'common'
    ): Promise<string> => {

        try {
            const translations =
                await translationService.getTranslations(locale);

            return translations[namespace]?.[key] ?? key;

        } catch (error) {
            console.error('Error fetching translation:', error);
            return key;
        }
    },


    updateTranslations: async (
        locale: string,
        translations: TranslationResponse
    ) => {

        return api.put(
            `/api/v1/translations/language/${locale}/admin`,
            translations
        );
    },


    async getAvailableLanguages() {

        const cached = localStorage.getItem('available_languages');

        if (cached) {
            return JSON.parse(cached);
        }

        const response = await fetch(
            `${import.meta.env.VITE_API_URL}/api/v1/translations/languages`
        );

        const data = await response.json();

        localStorage.setItem(
            'available_languages',
            JSON.stringify(data)
        );

        return data;
    }
};
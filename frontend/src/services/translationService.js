const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001';

export const translationService = {
    // Получить переводы для языка
    getTranslations: async (locale) => {
        try {
            const response = await fetch(`${API_URL}/api/translations/${locale}/common`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading translations:', error);
            return {}; // Возвращаем пустой объект при ошибке
        }
    },

    // Получить список доступных языков
    getAvailableLanguages: async () => {
        try {
            const response = await fetch(`${API_URL}/languages`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Если пришел массив строк - возвращаем как есть
            if (Array.isArray(data) && data.every(item => typeof item === 'string')) {
                return data;
            }

            // Если пришел массив объектов с полем code
            if (Array.isArray(data) && data.every(item => item.code)) {
                return data.map(item => item.code);
            }

            return ['ru'];
        } catch (error) {
            console.error('Error loading languages:', error);
            return ['ru'];
        }
    }
};
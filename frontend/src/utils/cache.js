export const translationCache = {
    get: (locale, namespace) => {
        const key = `translation_${locale}_${namespace}`;
        const cached = localStorage.getItem(key);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            // Кэш действителен 24 часа
            if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                return data;
            }
        }
        return null;
    },
    set: (locale, namespace, data) => {
        const key = `translation_${locale}_${namespace}`;
        localStorage.setItem(key, JSON.stringify({
            data,
            timestamp: Date.now()
        }));
    }
};
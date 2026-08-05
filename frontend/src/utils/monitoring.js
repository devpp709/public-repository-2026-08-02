export const logTranslationError = (error, locale, key) => {
    console.error(`Translation error [${locale}][${key}]:`, error);
    // Отправка в Sentry или другую систему мониторинга
};
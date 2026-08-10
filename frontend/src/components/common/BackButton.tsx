import { useRouter } from 'next/router';
import { useTranslation } from 'react-i18next';
import React, { ReactElement } from 'react';

export default function BackButton(): ReactElement {
    const router = useRouter();
    const { t } = useTranslation('common');

    const handleBack = (): void => {
        if (window.history.length > 1) {
            router.back();
            return;
        }

        void router.push('/');
    };

    return (
        <div className="site-navigation-controls">
            <div className="container">
                <button className="site-back-button" type="button" onClick={handleBack}>
                    <span className="site-back-button__icon" aria-hidden="true">←</span>
                    <span>{t('back', { defaultValue: 'Назад' })}</span>
                </button>
            </div>
        </div>
    );
}

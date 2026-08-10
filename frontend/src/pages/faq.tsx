import React, { ReactElement } from 'react';
import { useTranslation } from 'react-i18next';

const FAQ_ITEMS = [
    ['choose_a_car', 'choose_a_car_desc'],
    ['select_location', 'select_location_desc'],
    ['car_at_your_door', 'car_at_your_door_desc'],
    ['your_ride_your_rules', 'your_ride_your_rules_desc'],
] as const;

export default function FaqPage(): ReactElement {
    const { t } = useTranslation('common');

    return (
        <main id="content" className="site-content static-page">
            <section className="static-page-section">
                <div className="container">
                    <h1 className="static-page-title">{t('faq')}</h1>
                    <div className="faq-page-grid">
                        {FAQ_ITEMS.map(([titleKey, descriptionKey]) => (
                            <article className="faq-page-card" key={titleKey}>
                                <h2>{t(titleKey)}</h2>
                                <p>{t(descriptionKey)}</p>
                            </article>
                        ))}
                    </div>
                </div>
            </section>
        </main>
    );
}

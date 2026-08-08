import React, { ReactElement } from 'react';
import { useTranslation } from 'react-i18next';

export default function ContactPage(): ReactElement {
    const { t } = useTranslation('common');

    return (
        <main id="content" className="site-content static-page">
            <section className="static-page-section">
                <div className="container">
                    <h1 className="static-page-title">{t('contact_us')}</h1>
                    <div className="contact-page-card">
                        <div>
                            <h2>{t('call_us')}</h2>
                            <a href="tel:180065583203">1800-6558-3203</a>
                        </div>
                        <div>
                            <h2>{t('address')}</h2>
                            <p>04 Main Street west Peerl California 9017</p>
                        </div>
                        <div>
                            <h2>{t('working_hours')}</h2>
                            <p>8:00–18:00<br />{t('sunday_closed')}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    );
}

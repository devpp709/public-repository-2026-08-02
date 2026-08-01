import { useTranslation } from 'react-i18next';

export default function CTASection() {
    const { t } = useTranslation('common');

    return (
        <section className="elementor-section cta-section">
            <div className="elementor-background-overlay"></div>
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <p className="cta-text">{t('take_your_mobile')}</p>
                        <a className="elementor-button elementor-size-xl" href="/booking">{t('book_a_ride')}</a>
                    </div>
                </div>
            </div>
        </section>
    );
}
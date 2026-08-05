import { useTranslation } from 'react-i18next';

export default function AboutSection() {
    const { t } = useTranslation('common');

    return (
        <section className="elementor-section about-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <div className="about-grid">
                            <div className="about-content">
                                <h2 className="section-title">{t('about_company')}</h2>
                                <p className="about-tagline">{t('enjoy_your_ride')}</p>
                                <p>{t('about_company_desc')}</p>
                                <a className="elementor-button" href="#">{t('learn_more')}</a>
                            </div>
                            <div className="about-image">
                                <img src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2021/09/abs.jpg" alt={t('about_us')} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
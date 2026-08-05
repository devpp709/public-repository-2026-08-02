import { useTranslation } from 'react-i18next';

export default function AppDownloadSection() {
    const { t } = useTranslation('common');

    return (
        <section className="elementor-section app-download-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <img src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/001-1.png" alt={t('app')} className="app-image" />
                    </div>
                </div>
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('for_smooth_appearance')}</h2>
                        <p className="download-tagline">{t('download_app')}</p>
                        <p>{t('download_app_desc')}</p>
                        <div className="app-buttons">
                            <img src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/apple1.png" alt={t('app_store')} />
                            <img src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/google2.png" alt={t('google_play')} />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
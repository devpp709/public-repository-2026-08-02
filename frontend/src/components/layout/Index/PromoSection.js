import { useTranslation } from 'react-i18next';
import Link from 'next/link';

export default function PromoSection() {
    const { t } = useTranslation('common');

    return (
        <section className="elementor-section promo-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <div className="promo-grid">
                            <div className="promo-item promo-convertible">
                                <div className="elementor-background-overlay"></div>
                                <h2>{t('convertible_cars')}</h2>
                                <h2 className="price-tag">{t('per_day')}</h2>
                                <Link className="elementor-button" href="/cars/catalog">{t('book_now')}</Link>
                            </div>
                            <div className="promo-item promo-premium">
                                <div className="elementor-background-overlay"></div>
                                <h2>{t('all_new_models')}</h2>
                                <h2>{t('discover_our_premium_fleet')}</h2>
                                <Link className="elementor-button" href="/cars/catalog">{t('explore_now')}</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

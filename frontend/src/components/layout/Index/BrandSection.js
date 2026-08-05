import { useTranslation } from 'react-i18next';

export default function BrandSection() {
    const { t } = useTranslation('common');
    const brands = ['KIA', 'Audi', 'Cupra', 'Range Rover'];

    return (
        <section className="elementor-section brand-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('rent_your_favourite_brand')}</h2>
                        <p className="section-subtitle">{t('your_ride_your_rules_desc')}</p>
                        <div className="brand-grid">
                            {brands.map((brand, index) => (
                                <div key={index} className="brand-item">
                                    <div className="elementor-background-overlay"></div>
                                    <h2>{brand}</h2>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
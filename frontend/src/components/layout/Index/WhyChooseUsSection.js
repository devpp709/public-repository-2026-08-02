import { useTranslation } from 'react-i18next';

export default function WhyChooseUsSection() {
    const { t } = useTranslation('common');

    const features = [
        { title: t('airport_transfer'), desc: t('airport_transfer_desc') },
        { title: t('luxury_drive'), desc: t('luxury_drive_desc') },
        { title: t('pre_booking_discount'), desc: t('pre_booking_discount_desc') },
        { title: t('wedding_trips'), desc: t('wedding_trips_desc') }
    ];

    return (
        <section className="elementor-section why-choose-section">
            <div className="elementor-container">
                <div className="elementor-column image-column">
                    <div className="elementor-widget-wrap">
                        <img src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/789-1.png" alt={t('why_choose_us')} />
                    </div>
                </div>
                <div className="elementor-column content-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('why_choose_us')}</h2>
                        <p className="tagline">{t('customer_services')}</p>
                        <div className="features-grid">
                            {features.map((feature, index) => (
                                <div key={index} className="feature-item">
                                    <h3>{feature.title}</h3>
                                    <p>{feature.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
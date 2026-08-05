import { useTranslation } from 'react-i18next';

export default function ServicesSection() {
    const { t } = useTranslation('common');

    const services = [
        {
            icon: 'fas fa-car-alt',
            title: t('choose_a_car'),
            desc: t('choose_a_car_desc')
        },
        {
            icon: 'fas fa-map-marked-alt',
            title: t('select_location'),
            desc: t('select_location_desc')
        },
        {
            icon: 'fas fa-door-open',
            title: t('car_at_your_door'),
            desc: t('car_at_your_door_desc')
        }
    ];

    return (
        <section className="elementor-section services-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('easy_to_use_rental_services')}</h2>
                        <div className="services-grid">
                            {services.map((service, index) => (
                                <div key={index} className="service-item">
                                    <div className="elementor-icon-wrapper">
                                        <div className="elementor-icon">
                                            <i aria-hidden="true" className={service.icon}></i>
                                        </div>
                                    </div>
                                    <h2 className="elementor-heading-title">{service.title}</h2>
                                    <div className="elementor-divider">
                                        <span className="elementor-divider-separator"></span>
                                    </div>
                                    <p>{service.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
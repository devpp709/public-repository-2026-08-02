import { useTranslation } from 'react-i18next';

export default function FeatureSection() {
    const { t } = useTranslation('common');

    const features = [
        { icon: 'fas fa-car', title: t('free_cancellation'), desc: t('free_cancellation_desc') },
        { icon: 'fas fa-dollar-sign', title: t('affordable_pricing'), desc: t('affordable_pricing_desc') },
        { icon: 'fas fa-wrench', title: t('road_assistance'), desc: t('road_assistance_desc') },
        { icon: 'fas fa-car-side', title: t('wide_selections'), desc: t('wide_selections_desc') }
    ];

    return (
        <section className="elementor-section feature-section">
            <div className="elementor-container">
                {features.map((feature, index) => (
                    <div key={index} className="elementor-column">
                        <div className="elementor-widget-wrap">
                            <div className="elementor-icon-box-wrapper">
                                <div className="elementor-icon-box-icon">
                                    <span className="elementor-icon">
                                        <i aria-hidden="true" className={feature.icon}></i>
                                    </span>
                                </div>
                                <div className="elementor-icon-box-content">
                                    <h3>{feature.title}</h3>
                                    <p>{feature.desc}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    );
}
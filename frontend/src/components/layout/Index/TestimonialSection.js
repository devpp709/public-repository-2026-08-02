import { useTranslation } from 'react-i18next';

export default function TestimonialSection() {
    const { t } = useTranslation('common');

    const testimonials = [
        {
            name: t('maria'),
            text: 'Duis non quam et nisi tincidunt fermentum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.'
        },
        {
            name: t('william'),
            text: 'Praesent quis orci sit amet ante facilisis suscipit. Integer in eros molestie, ultricies arcu ac, cursus quam. Nulla facilisi. Ut egestas semper'
        },
        {
            name: t('emilia'),
            text: 'Duis non quam et nisi tincidunt fermentum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas'
        },
        {
            name: t('chapman'),
            text: 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam ullam, mauris nec feugiat'
        }
    ];

    return (
        <section className="elementor-section testimonial-section">
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('loved_by_renter')}</h2>
                        <p className="section-subtitle">{t('loved_by_renter_desc')}</p>
                        <div className="testimonial-grid">
                            {testimonials.map((testimonial, index) => (
                                <div key={index} className="testimonial-item">
                                    <p className="testimonial-text">"{testimonial.text}"</p>
                                    <h3 className="testimonial-name">{testimonial.name}</h3>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
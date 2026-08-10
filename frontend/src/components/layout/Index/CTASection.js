import { useTranslation } from 'react-i18next';
import Link from 'next/link';

export default function CTASection() {
    const { t } = useTranslation('common');

    return (
        <section className="elementor-section cta-section">
            <div className="elementor-background-overlay"></div>
            <div className="elementor-container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <p className="cta-text">{t('take_your_mobile')}</p>
                        <Link className="elementor-button elementor-size-xl" href="/cars/catalog">{t('book_a_ride')}</Link>
                    </div>
                </div>
            </div>
        </section>
    );
}

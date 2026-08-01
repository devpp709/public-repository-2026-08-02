import dynamic from 'next/dynamic';
import { useTranslation } from 'react-i18next';

// Динамический импорт с отключенным SSR
const LanguageSwitcher = dynamic(
    () => import('../../common/LanguageSwitcher').then(mod => mod.LanguageSwitcher),
    {
        ssr: false,
        loading: () => (
            <div style={{ display: 'flex', gap: '8px' }}>
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
            </div>
        )
    }
);

export default function Header() {
    const { t } = useTranslation('common');

    return (
        <header className="zita-site-header mhdrleft zta-fade">
            <div className="main-header mhdrleft inline right-menu linkeffect-1">
                <div className="main-header-bar two">
                    <div className="container">
                        <div className="main-header-container" style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            width: '100%'
                        }}>
                            <div className="main-header-col1" style={{ flexShrink: 0 }}>
                                <div className="zita-logo">
                                    <a href="/" className="custom-logo-link">
                                        <img
                                            fetchPriority="high"
                                            width="471"
                                            height="157"
                                            src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/logo3.png"
                                            className="custom-logo"
                                            alt={t('car_rental')}
                                        />
                                    </a>
                                </div>
                            </div>

                            <div style={{
                                display: 'flex',
                                justifyContent: 'center',
                                flex: 1,
                                margin: '0 20px'
                            }}>
                                <LanguageSwitcher />
                            </div>

                            <div className="main-header-col2" style={{ flexShrink: 0 }}>
                                <nav>
                                    <div className="sider main zita-menu-hide right">
                                        <div className="sider-inner">
                                            <ul id="zita-menu" className="zita-menu" style={{
                                                display: 'flex',
                                                alignItems: 'center',
                                                gap: '20px',
                                                listStyle: 'none',
                                                margin: 0,
                                                padding: 0
                                            }}>
                                                <li className="menu-item current-menu-item">
                                                    <a href="/"><span className="zita-menu-link">{t('home')}</span></a>
                                                </li>
                                                <li className="menu-item">
                                                    <a href="/about"><span className="zita-menu-link">{t('about_us')}</span></a>
                                                </li>
                                                <li className="menu-item">
                                                    <a href="/faq"><span className="zita-menu-link">{t('faq')}</span></a>
                                                </li>
                                                <li className="menu-item">
                                                    <a href="/contact"><span className="zita-menu-link">{t('contact_us')}</span></a>
                                                </li>
                                                <li>
                                                    <a className="main-header-btn" href="/booking" style={{
                                                        display: 'inline-block',
                                                        padding: '10px 24px',
                                                        background: '#0866c4',
                                                        color: '#fff',
                                                        borderRadius: '6px',
                                                        textDecoration: 'none',
                                                        fontWeight: '600'
                                                    }}>
                                                        {t('book_a_car')}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
}
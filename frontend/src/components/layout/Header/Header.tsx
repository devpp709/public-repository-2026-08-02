// src/components/layout/Header/Header.tsx
import dynamic from 'next/dynamic';
import { useTranslation } from 'react-i18next';
import React, { ReactElement } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/router';

// Динамический импорт с отключенным SSR
const LanguageSwitcher = dynamic(
    () => import('../../common/LanguageSwitcher').then(mod => mod.LanguageSwitcher),
    {
        ssr: false,
        loading: () => (
            <div className="header-language-options" aria-hidden="true">
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
                <div style={{ width: '40px', height: '40px', borderRadius: '50%', border: '2px solid #e5e7eb' }}></div>
            </div>
        )
    }
);

export default function Header(): ReactElement {
    const { t } = useTranslation('common');
    const router = useRouter();
    const currentPath = router.asPath.split(/[?#]/)[0].replace(/\/+$/, '') || '/';

    const isCurrentPath = (path: string): boolean => currentPath === path;
    const isCarsPath = currentPath === '/cars' || currentPath.startsWith('/cars/');
    const menuItemClass = (modifier: string, isActive: boolean): string =>
        `menu-item menu-item--${modifier}${isActive ? ' current-menu-item' : ''}`;

    return (
        <header className="zita-site-header mhdrleft zta-fade">
            <div className="main-header mhdrleft inline right-menu linkeffect-1">
                <div className="main-header-bar two">
                    <div className="container">
                        <div className="main-header-container">
                            <div className="main-header-col1">
                                <div className="zita-logo">
                                    <Link href="/" className="custom-logo-link">
                                        <img
                                            priority="high"
                                            width="471"
                                            height="157"
                                            src="https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/logo3.png"
                                            className="custom-logo"
                                            alt={t('car_rental')}
                                        />
                                    </Link>
                                </div>
                            </div>

                            <div className="main-header-language">
                                <LanguageSwitcher />
                            </div>

                            <div className="main-header-col2">
                                <nav aria-label={t('main_navigation', { defaultValue: 'Main navigation' })}>
                                    <div className="sider main zita-menu-hide right">
                                        <div className="sider-inner">
                                            <ul id="zita-menu" className="zita-menu">
                                                <li className={menuItemClass('home', isCurrentPath('/'))}>
                                                    <Link href="/" aria-current={isCurrentPath('/') ? 'page' : undefined}>
                                                        <span className="zita-menu-link">{t('home')}</span>
                                                    </Link>
                                                </li>
                                                <li className={menuItemClass('about', isCurrentPath('/about'))}>
                                                    <Link href="/about" aria-current={isCurrentPath('/about') ? 'page' : undefined}>
                                                        <span className="zita-menu-link">{t('about_us')}</span>
                                                    </Link>
                                                </li>
                                                <li className={menuItemClass('faq', isCurrentPath('/faq'))}>
                                                    <Link href="/faq" aria-current={isCurrentPath('/faq') ? 'page' : undefined}>
                                                        <span className="zita-menu-link">{t('faq')}</span>
                                                    </Link>
                                                </li>
                                                <li className={menuItemClass('contact', isCurrentPath('/contact'))}>
                                                    <Link href="/contact" aria-current={isCurrentPath('/contact') ? 'page' : undefined}>
                                                        <span className="zita-menu-link">{t('contact_us')}</span>
                                                    </Link>
                                                </li>
                                                <li className={menuItemClass('cta', isCarsPath)}>
                                                    <Link
                                                        className="main-header-btn"
                                                        href="/catalog"
                                                        aria-current={isCarsPath ? 'page' : undefined}
                                                    >
                                                        {t('book_a_car')}
                                                    </Link>
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

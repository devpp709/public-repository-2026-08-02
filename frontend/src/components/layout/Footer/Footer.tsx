// src/components/layout/Footer/Footer.tsx
import { useTranslation } from 'react-i18next';
import React, { ReactElement } from 'react';

export default function Footer(): ReactElement {
    const { t } = useTranslation('common');

    return (
        <footer id="zita-footer" className="zita-site-footer">
            <div className="footer-wrap widget-area">
                <div className="widget-footer">
                    <div className="widget-footer-bar ft-wgt-three">
                        <div className="container">
                            <div className="widget-footer-container">
                                <div className="widget-footer-col1">
                                    <h2>{t('about_us')}</h2>
                                    <p>{t('about_us_desc')}</p>
                                </div>
                                <div className="widget-footer-col2">
                                    <h2>{t('legal_policy')}</h2>
                                    <ul>
                                        <li><a href="#">{t('term_condition')}</a></li>
                                        <li><a href="#">{t('privacy_policy')}</a></li>
                                        <li><a href="#">{t('legal_notice')}</a></li>
                                        <li><a href="#">{t('accessibility')}</a></li>
                                    </ul>
                                </div>
                                <div className="widget-footer-col3">
                                    <h2>{t('contact_info')}</h2>
                                    <p>
                                        {t('call_us')} - {t('contact_phone')}<br />
                                        {t('contact_address')}<br />
                                        {t('working_hours')} - 8.00 - 18.00<br />
                                        {t('sunday_closed')}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bottom-footer">
                    <div className="bottom-footer-bar ft-btm-two">
                        <div className="container">
                            <div className="bottom-footer-container">
                                <div className="bottom-footer-col1">
                                    <p className="footer-copyright">
                                        {t('copyright')}
                                    </p>
                                </div>
                                <div className="bottom-footer-col2">
                                    <ul className="social-icon">
                                        <li><a href="#"><i className="fa fa-facebook"></i></a></li>
                                        <li><a href="#"><i className="fa fa-linkedin"></i></a></li>
                                        <li><a href="#"><i className="fa fa-pinterest"></i></a></li>
                                        <li><a href="#"><i className="fa fa-instagram"></i></a></li>
                                        <li><a href="#"><i className="fa fa-youtube-play"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}

import React, { useState, useEffect } from 'react';
import { useLanguage } from '../../i18n/LanguageProvider';

export const LanguageSwitcher = () => {
    const { currentLanguage, availableLanguages, changeLanguage, loading } = useLanguage();
    const [isMounted, setIsMounted] = useState(false);

    useEffect(() => {
        setIsMounted(true);
    }, []);

    const getFlag = (code) => {
        const flags = {
            ru: '🇷🇺',
            en: '🇬🇧',
            am: '🇦🇲'
        };
        return flags[code] || '🌐';
    };

    if (!isMounted || loading) {
        return (
            <div style={{
                display: 'flex',
                gap: '8px',
                alignItems: 'center'
            }}>
                <div style={{
                    width: '40px',
                    height: '40px',
                    borderRadius: '50%',
                    border: '2px solid #e5e7eb',
                    background: 'transparent'
                }}></div>
                <div style={{
                    width: '40px',
                    height: '40px',
                    borderRadius: '50%',
                    border: '2px solid #e5e7eb',
                    background: 'transparent'
                }}></div>
                <div style={{
                    width: '40px',
                    height: '40px',
                    borderRadius: '50%',
                    border: '2px solid #e5e7eb',
                    background: 'transparent'
                }}></div>
            </div>
        );
    }

    return (
        <div style={{
            display: 'flex',
            gap: '8px',
            alignItems: 'center'
        }}>
            {availableLanguages.map((code) => (
                <button
                    key={code}
                    onClick={() => changeLanguage(code)}
                    style={{
                        width: '40px',
                        height: '40px',
                        borderRadius: '50%',
                        border: currentLanguage === code ? '2.5px solid #0866c4' : '2px solid #e5e7eb',
                        background: currentLanguage === code ? '#eff6ff' : 'transparent',
                        cursor: 'pointer',
                        fontSize: '24px',
                        padding: 0,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        transition: 'all 0.2s ease',
                        opacity: currentLanguage === code ? 1 : 0.7,
                        transform: currentLanguage === code ? 'scale(1.1)' : 'scale(1)',
                        boxShadow: currentLanguage === code ? '0 2px 8px rgba(8,102,196,0.2)' : 'none'
                    }}
                    onMouseEnter={(e) => {
                        e.currentTarget.style.opacity = '1';
                        e.currentTarget.style.transform = 'scale(1.1)';
                        if (currentLanguage !== code) {
                            e.currentTarget.style.borderColor = '#0866c4';
                        }
                    }}
                    onMouseLeave={(e) => {
                        if (currentLanguage !== code) {
                            e.currentTarget.style.opacity = '0.7';
                            e.currentTarget.style.transform = 'scale(1)';
                            e.currentTarget.style.borderColor = '#e5e7eb';
                        } else {
                            e.currentTarget.style.opacity = '1';
                            e.currentTarget.style.transform = 'scale(1.1)';
                        }
                    }}
                    title={code.toUpperCase()}
                >
                    {getFlag(code)}
                </button>
            ))}
        </div>
    );
};
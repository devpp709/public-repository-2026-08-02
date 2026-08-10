// src/pages/_app.tsx
import '../../public/globals.css';
import React, { ReactElement } from 'react';
import type { AppProps } from 'next/app';

import '../i18n/config';
import { LanguageProvider } from '../i18n/LanguageProvider';
import { AuthProvider } from '../context/AuthContext';
import Header from '../components/layout/Header/Header';
import Footer from '../components/layout/Footer/Footer';
import BackButton from '../components/common/BackButton';

function App({ Component, pageProps }: AppProps): ReactElement {
    return (
        <AuthProvider>
            <LanguageProvider>
                <div className="zita-site">
                    <Header />
                    <BackButton />
                    <Component {...pageProps} />
                    <Footer />
                </div>
            </LanguageProvider>
        </AuthProvider>
    );
}

export default App;

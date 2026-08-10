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
import {useRouter} from "next/router";

function App({ Component, pageProps }: AppProps): ReactElement {
    const router = useRouter();
    const isHomePage = router.pathname === '/';
    return (
        <AuthProvider>
            <LanguageProvider>
                <div className="zita-site">
                    <Header />
                    {!isHomePage && <BackButton />}
                    <Component {...pageProps} />
                    <Footer />
                </div>
            </LanguageProvider>
        </AuthProvider>
    );
}

export default App;

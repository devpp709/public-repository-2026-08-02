// src/pages/_app.tsx
import '../../public/globals.css';
import React, { ReactElement } from 'react';
import type { AppProps } from 'next/app';

import '../i18n/config';
import { LanguageProvider } from '../i18n/LanguageProvider';
import { AuthProvider } from '../context/AuthContext';

function App({ Component, pageProps }: AppProps): ReactElement {
    return (
        <AuthProvider>
            <LanguageProvider>
                <Component {...pageProps} />
            </LanguageProvider>
        </AuthProvider>
    );
}

export default App;
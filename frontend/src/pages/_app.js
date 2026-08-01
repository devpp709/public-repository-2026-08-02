import '../../public/globals.css';
import React from 'react';
import { LanguageProvider } from '../i18n/LanguageProvider';
import Home from "../pages/index";

import '../i18n/config';

function App() {
    return (
        <LanguageProvider>
            <Home />
        </LanguageProvider>
    );
}

export default App;
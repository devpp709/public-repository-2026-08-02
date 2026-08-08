import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import "./index.css";
import "swiper/swiper-bundle.css";
import "flatpickr/dist/flatpickr.css";

import App from "./App";
import { AppWrapper } from "./components/common/PageMeta";
import { ThemeProvider } from "./context/ThemeContext";
import { AuthProvider } from "./context/AuthContext";
import { LanguageProvider } from "./i18n/LanguageProvider";

// Импорт конфигурации i18n
import "./i18n/config";

createRoot(document.getElementById("root")!).render(
    <StrictMode>
        <BrowserRouter>
            <LanguageProvider>
                <ThemeProvider>
                    <AuthProvider>
                        <AppWrapper>
                            <App />
                        </AppWrapper>
                    </AuthProvider>
                </ThemeProvider>
            </LanguageProvider>
        </BrowserRouter>
    </StrictMode>,
);
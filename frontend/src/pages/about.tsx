import React, { ReactElement } from 'react';
import AboutSection from '../components/layout/Index/AboutSection';

export default function AboutPage(): ReactElement {
    return (
        <main id="content" className="site-content static-page">
            <AboutSection />
        </main>
    );
}

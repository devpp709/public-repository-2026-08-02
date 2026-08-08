// frontend/src/pages/index.tsx
import HeroSection from "../components/layout/Index/HeroSection";
import ServicesSection from "../components/layout/Index/ServicesSection";
import CarListingSection from "../components/layout/Index/CarListingSection";
import BrandSection from "../components/layout/Index/BrandSection";
import FeatureSection from "../components/layout/Index/FeatureSection";
import PromoSection from "../components/layout/Index/PromoSection";
import TestimonialSection from "../components/layout/Index/TestimonialSection";
import CTASection from "../components/layout/Index/CTASection";
import AboutSection from "../components/layout/Index/AboutSection";
import AppDownloadSection from "../components/layout/Index/AppDownloadSection";
import WhyChooseUsSection from "../components/layout/Index/WhyChooseUsSection";
import React from "react";

export default function Home(): React.ReactElement {
    return (
        <main id="content" className="site-content">
            <HeroSection/>
            <ServicesSection/>
            <CarListingSection/>
            <BrandSection/>
            <FeatureSection/>
            <PromoSection/>
            <TestimonialSection/>
            <CTASection/>
            <AboutSection/>
            <AppDownloadSection/>
            <WhyChooseUsSection/>
        </main>
    );
}

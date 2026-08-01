import Header from "../components/layout/Header/Header";
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
import Footer from "../components/layout/Footer/Footer";
import React from "react";

export default function Home() {
    return (
        <div className="zita-site">
            <Header/>
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
            <Footer/>
        </div>
    );
}
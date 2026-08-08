// src/components/sections/CarListingSection.tsx
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { carService } from '../../../services/carService';
import { Car } from '../../../services/carService';
import CarCard from '../../cars/CarCard';

export default function CarListingSection() {
    const { t } = useTranslation('common');
    const [cars, setCars] = useState<Car[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadCars = async () => {
            try {
                setLoading(true);
                const data = await carService.getAllCars();
                setCars(data);
            } catch (err) {
                console.error('Error loading cars:', err);
                setError(err instanceof Error ? err.message : 'Unknown error');
            } finally {
                setLoading(false);
            }
        };

        loadCars();
    }, []);

    if (loading) {
        return (
            <section className="elementor-section car-listing-section">
                <div className="elementor-container container">
                    <div className="elementor-column">
                        <div className="elementor-widget-wrap" style={{ textAlign: 'center', padding: '50px 0' }}>
                            <h2>{t('your_ride_your_rules')}</h2>
                            <p>{t('loading')}...</p>
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    if (error) {
        return (
            <section className="elementor-section car-listing-section">
                <div className="elementor-container container">
                    <div className="elementor-column">
                        <div className="elementor-widget-wrap" style={{ textAlign: 'center', padding: '50px 0' }}>
                            <h2>{t('your_ride_your_rules')}</h2>
                            <p style={{ color: 'red' }}>{t('error')}: {error}</p>
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="elementor-section car-listing-section">
            <div className="elementor-container container">
                <div className="elementor-column">
                    <div className="elementor-widget-wrap">
                        <h2 className="section-title">{t('your_ride_your_rules')}</h2>
                        <p className="section-subtitle">{t('your_ride_your_rules_desc')}</p>
                        <div className="tf-car-archive-result">
                            <div className="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 grid-view">
                                {cars.map((car) => (
                                    <CarCard key={car.id} car={car} viewMode="grid" />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

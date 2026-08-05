// src/pages/catalog.tsx
import { useRouter } from 'next/router';
import React, { useCallback, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import Header from '../../components/layout/Header/Header';
import Footer from '../../components/layout/Footer/Footer';
import { useCarClasses } from '../../hooks/useCarClasses';
import { carService, Car } from '../../services/carService';
import SearchBox from "../../components/layout/Catalog/SearchBox";
import ArchiveHeader from "../../components/layout/Catalog/ArchiveHeader";
import CarListings from "../../components/layout/Catalog/CarListings";

export default function Catalog() {
    const router = useRouter();
    const { t } = useTranslation('common');
    const { carClasses } = useCarClasses();
    const [cars, setCars] = useState<Car[]>([]);
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [loading, setLoading] = useState(true);
    const [totalResults, setTotalResults] = useState(0);

    const getCarClassName = useCallback((value: string) => {
        const car = carClasses.find(c => c.value === value || c.id === value);
        return car ? car.name : value;
    }, [carClasses]);

    // Загрузка автомобилей
    const loadCars = useCallback(async () => {
        setLoading(true);
        try {
            const {
                startDate,
                endDate,
                pickupLocation,
                dropoffLocation,
                returnSameLocation,
                driverAge18to40,
                pickupCity,
                dropoffCity
            } = router.query;

            // Собираем параметры для API
            const params: any = {};

            if (startDate && typeof startDate === 'string') {
                params.start_date = startDate;
            }
            if (endDate && typeof endDate === 'string') {
                params.end_date = endDate;
            }
            if (pickupLocation && typeof pickupLocation === 'string') {
                params.pickup_location = pickupLocation;
            }
            if (dropoffLocation && typeof dropoffLocation === 'string') {
                params.dropoff_location = dropoffLocation;
            }
            if (returnSameLocation && typeof returnSameLocation === 'string') {
                params.return_same_location = returnSameLocation;
            }
            if (driverAge18to40 && typeof driverAge18to40 === 'string') {
                params.driver_age_18_40 = driverAge18to40;
            }

            // Если есть даты - запрашиваем доступные автомобили
            if (params.start_date && params.end_date) {
                const availableCars = await carService.getAvailableCars(params);
                setCars(availableCars);
                setTotalResults(availableCars.length);
            } else {
                // Если дат нет - показываем все доступные автомобили
                const allCars = await carService.getAllCars();
                setCars(allCars);
                setTotalResults(allCars.length);
            }
        } catch (error) {
            console.error('Error loading cars:', error);
            setCars([]);
            setTotalResults(0);
        } finally {
            setLoading(false);
        }
    }, [router.query]);

    useEffect(() => {
        if (router.isReady) {
            loadCars();
        }
    }, [router.isReady, router.query, loadCars]);

    return (
        <div className="zita-site">
            <Header />
            <main id="content" className="site-content">
                <div className="tf-container">
                    <div className="tf-container-inner">
                        <div className="tf-archive-car-details-warper">
                            <SearchBox />
                            <ArchiveHeader
                                totalResults={totalResults}
                                viewMode={viewMode}
                                setViewMode={setViewMode}
                            />
                            <CarListings
                                totalResults={totalResults}
                                cars={cars}
                                viewMode={viewMode}
                                loading={loading}
                                onCarsUpdate={setCars}
                            />
                        </div>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    );
}
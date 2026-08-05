// src/pages/cars/[id].tsx

import { useRouter } from 'next/router';
import React, { useEffect, useState } from 'react';
import CarDetail from '../../components/cars/CarDetail';
import { carService, Car } from '../../services/carService';
import Header from "../../components/layout/Header/Header";
import Footer from "../../components/layout/Footer/Footer";

export default function CarPage() {
    const router = useRouter();
    const { id } = router.query;
    const [car, setCar] = useState<Car | null>(null);
    const [loading, setLoading] = useState(true);
    const [notFound, setNotFound] = useState(false);

    useEffect(() => {
        if (id) {
            const loadCar = async () => {
                try {
                    const data = await carService.getCarById(Number(id), true);
                    if (data) {
                        setCar(data);
                    } else {
                        setNotFound(true);
                    }
                } catch (error) {
                    console.error('Error loading car:', error);
                    setNotFound(true);
                } finally {
                    setLoading(false);
                }
            };
            loadCar();
        }
    }, [id]);

    // Если машина не найдена - можно показать 404 или редирект
    if (notFound) {
        return (
            <div className="zita-site">
                <Header />
                <main id="content" className="site-content">
                    <div className="tf-container">
                        <div className="tf-container-inner">
                            <div className="tf-not-found" style={{ textAlign: 'center', padding: '60px 20px' }}>
                                <h1>Автомобиль не найден</h1>
                                <p>Извините, запрашиваемый автомобиль не существует или был удален.</p>
                                <a href="/cars" className="tf-btn tf-btn-primary" style={{ display: 'inline-block', marginTop: '20px' }}>
                                    Вернуться к списку
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
                <Footer />
            </div>
        );
    }

    return (
        <div className="zita-site">
            <Header />
            <main id="content" className="site-content">
                <CarDetail car={car} loading={loading} />
            </main>
            <Footer />
        </div>
    );
}
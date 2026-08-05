// src/components/cars/CarCard.tsx
import React from 'react';
import {useTranslation} from 'react-i18next';
import {Car} from '../../services/carService';
import CarSpecIcons from "../icons/CarSpecIcons";

interface CarCardProps {
    key?: number;
    car: Car;
    viewMode?: 'grid' | 'list';
    onDetailsClick?: (carId: number) => void;
}

export default function CarCard({car, viewMode = 'grid', onDetailsClick, key}: CarCardProps) {
    const {t} = useTranslation('common');

    const defaultImage = 'https://zitademo.wpzita.com/car-rental/wp-content/uploads/sites/92/2025/08/05-1-1.jpg';

    const handleDetailsClick = () => {
        if (onDetailsClick && car.id) {
            onDetailsClick(car.id);
        }
    };

    return (
        <div className={`tf-single-car-view ${viewMode === 'list' ? 'list-view-item' : ''}`}>
            <div className="tf-car-image">
                <img
                    decoding="async"
                    width="800"
                    height="533"
                    src={car.images?.[0]?.path || car.image || car.mainImage || car.img || defaultImage}
                    className="attachment-full size-full wp-post-image"
                    alt={car.fullName || car.name || car.brand || 'Car'}
                    loading="lazy"
                />
                <div className="tf-other-infos tf-flex">
                    <div className="tf-reviews-box">
                        <span>
                            {car.averageRating || car.rating || 0.0}
                            <i className="fa-solid fa-star"></i>
                            ({car.totalBookings || car.totalTrips || 0} {t('trips')})
                        </span>
                    </div>
                    <div className="tf-tags-box">
                        <ul>
                            <li>{car.status === 'available' ? t('available') : car.status || t('available')}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div className="tf-car-details">
                <div className="tf-car-content">
                    <h3 className="tf-mb-24">
                        <a href={`/cars/${car.id || 0}`} onClick={handleDetailsClick}>
                            {car.fullName || car.name || `${car.brand} ${car.model}` || 'Unknown Car'}
                        </a>
                    </h3>

                    {/* Используем CarSpecIcons */}
                    <CarSpecIcons
                        distance={car.mileage}
                        fuel={car.fuelType}
                        year={car.year}
                        transmission={car.transmission}
                        seats={car.seats}
                        bags={car.bags}
                        iconSize={18}
                        color="#566676"
                        className="tf-mb-24"
                    />
                </div>
                <div className="tf-booking-btn tf-flex tf-flex-space-bttn">
                    <div className="tf-price-info">
                        <h3>
                            <span className="woocommerce-Price-amount amount">
                                <bdi>
                                    <span className="woocommerce-Price-currencySymbol">$</span>
                                    {car.dailyPrice.toFixed(2)}
                                </bdi>
                            </span>
                            <small> / {t('day')}</small>
                        </h3>
                    </div>
                    <a className="view-more" href={`/cars/${car.id || 0}`} onClick={handleDetailsClick}>
                        {t('details')}
                    </a>
                </div>
            </div>
        </div>
    );
}
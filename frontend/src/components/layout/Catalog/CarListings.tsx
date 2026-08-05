import {useTranslation} from 'react-i18next';
import React, {useEffect, useState} from "react";
import {Car} from "../../../services/carService";
import {useRouter} from 'next/router';
import CarCard from '../../cars/CarCard';
import {extraService, ExtraService as ExtraServiceType} from "../../../services/extraService";
import {featureService, Feature as FeatureType} from "../../../services/featureService";
import { FeatureIcon, ExtraIcon } from '../../icons/FeatureIcons';

interface CarListingsProps {
    totalResults?: number,
    viewMode?: "grid" | "list",
    cars?: Car[],
    onCarsUpdate?: (cars: Car[]) => void,
    loading?: boolean
}

interface CarClass {
    id: number;
    name: string;
    description: string;
    icon: string;
    dailyRate: number;
    hourlyRate: number;
    carsCount: number;
}

interface FilterState {
    carClass: string;
    minMileage: string;
    maxMileage: string;
    status: string;
    minYear: string;
    maxYear: string;
    minPrice: string;
    maxPrice: string;
    fuelType: string;
    transmission: string;
    seats: string;
    extras: string[];
    features: string[];
}

export default function CarListings({
                                        totalResults = 0,
                                        viewMode = 'grid',
                                        cars = [],
                                        onCarsUpdate,
                                        loading = false
                                    }: CarListingsProps) {
    const {t} = useTranslation('common');
    const router = useRouter();
    const [localCars, setLocalCars] = useState<Car[]>(cars || []);
    const [filteredCars, setFilteredCars] = useState<Car[]>(cars || []);
    const [isFiltering, setIsFiltering] = useState(false);
    const [carClasses, setCarClasses] = useState<CarClass[]>([]);
    const [availableExtras, setAvailableExtras] = useState<ExtraServiceType[]>([]);
    const [availableFeatures, setAvailableFeatures] = useState<FeatureType[]>([]);
    const [extrasLoading, setExtrasLoading] = useState(false);
    const [featuresLoading, setFeaturesLoading] = useState(false);

    const [filters, setFilters] = useState<FilterState>({
        carClass: '',
        minMileage: '',
        maxMileage: '',
        status: '',
        minYear: '',
        maxYear: '',
        minPrice: '',
        maxPrice: '',
        fuelType: '',
        transmission: '',
        seats: '',
        extras: [],
        features: []
    });

    // Загрузка классов из localStorage
    useEffect(() => {
        const loadCarClasses = () => {
            try {
                const cached = localStorage.getItem('car_classes');
                if (cached) {
                    const classes = JSON.parse(cached);
                    setCarClasses(classes);
                }
            } catch (error) {
                console.error('Error loading car classes from localStorage:', error);
            }
        };

        loadCarClasses();
    }, []);

    // Загрузка доступных доп услуг
    useEffect(() => {
        const loadExtras = async () => {
            setExtrasLoading(true);
            try {
                const extras = await extraService.getAllServices(false, true);
                setAvailableExtras(extras);
            } catch (error) {
                console.error('Error loading extra services:', error);
            } finally {
                setExtrasLoading(false);
            }
        };

        loadExtras();
    }, []);

    // Загрузка доступных характеристик (комплектация)
    useEffect(() => {
        const loadFeatures = async () => {
            setFeaturesLoading(true);
            try {
                const features = await featureService.getAllFeatures(false);
                setAvailableFeatures(features);
            } catch (error) {
                console.error('Error loading features:', error);
            } finally {
                setFeaturesLoading(false);
            }
        };

        loadFeatures();
    }, []);

    useEffect(() => {
        if (cars) {
            setLocalCars(cars);
            setFilteredCars(cars);
        }
    }, [cars]);

    // Применение фильтров
    useEffect(() => {
        applyFilters();
    }, [filters, localCars]);

    const applyFilters = () => {
        setIsFiltering(true);
        let result = [...localCars];

        // Фильтр по классу (по названию или ID)
        if (filters.carClass) {
            const selectedClass = carClasses.find(c =>
                c.id === Number(filters.carClass) ||
                c.name.toLowerCase() === filters.carClass.toLowerCase()
            );

            if (selectedClass) {
                result = result.filter(car =>
                    car.carClass?.id === selectedClass.id ||
                    car.carClass?.name?.toLowerCase() === selectedClass.name.toLowerCase()
                );
            }
        }

        // Фильтр по пробегу
        if (filters.minMileage) {
            result = result.filter(car => car.mileage >= Number(filters.minMileage));
        }
        if (filters.maxMileage) {
            result = result.filter(car => car.mileage <= Number(filters.maxMileage));
        }

        // Фильтр по статусу
        if (filters.status) {
            result = result.filter(car => car.status === filters.status);
        }

        // Фильтр по году
        if (filters.minYear) {
            result = result.filter(car => car.year >= Number(filters.minYear));
        }
        if (filters.maxYear) {
            result = result.filter(car => car.year <= Number(filters.maxYear));
        }

        // Фильтр по цене
        if (filters.minPrice) {
            result = result.filter(car => car.dailyPrice >= Number(filters.minPrice));
        }
        if (filters.maxPrice) {
            result = result.filter(car => car.dailyPrice <= Number(filters.maxPrice));
        }

        // Фильтр по топливу
        if (filters.fuelType) {
            result = result.filter(car =>
                car.fuelType?.toLowerCase() === filters.fuelType.toLowerCase()
            );
        }

        // Фильтр по трансмиссии
        if (filters.transmission) {
            result = result.filter(car =>
                car.transmission?.toLowerCase() === filters.transmission.toLowerCase()
            );
        }

        // Фильтр по количеству мест
        if (filters.seats) {
            result = result.filter(car => car.seats === Number(filters.seats));
        }

        // Фильтр по доп услугам (экстра)
        if (filters.extras.length > 0) {
            result = result.filter(car => {
                return filters.extras.every(extraId => {
                    return car.features?.some(f =>
                        String(f.feature?.id) === extraId ||
                        f.feature?.name?.toLowerCase() === extraId.toLowerCase()
                    );
                });
            });
        }

        // Фильтр по характеристикам (комплектация)
        if (filters.features.length > 0) {
            result = result.filter(car => {
                return filters.features.every(featureId => {
                    return car.features?.some(f =>
                        String(f.feature?.id) === featureId ||
                        f.feature?.name?.toLowerCase() === featureId.toLowerCase()
                    );
                });
            });
        }

        setFilteredCars(result);
        setIsFiltering(false);
    };

    const handleFilterChange = (key: keyof FilterState, value: any) => {
        setFilters(prev => ({...prev, [key]: value}));
    };

    const handleExtraToggle = (extraId: string | number) => {
        setFilters(prev => {
            const extraIdStr = String(extraId);
            const current = prev.extras;
            const index = current.indexOf(extraIdStr);
            if (index > -1) {
                return {...prev, extras: current.filter(e => e !== extraIdStr)};
            } else {
                return {...prev, extras: [...current, extraIdStr]};
            }
        });
    };

    const handleFeatureToggle = (featureId: string | number) => {
        setFilters(prev => {
            const featureIdStr = String(featureId);
            const current = prev.features;
            const index = current.indexOf(featureIdStr);
            if (index > -1) {
                return {...prev, features: current.filter(f => f !== featureIdStr)};
            } else {
                return {...prev, features: [...current, featureIdStr]};
            }
        });
    };

    const resetFilters = () => {
        setFilters({
            carClass: '',
            minMileage: '',
            maxMileage: '',
            status: '',
            minYear: '',
            maxYear: '',
            minPrice: '',
            maxPrice: '',
            fuelType: '',
            transmission: '',
            seats: '',
            extras: [],
            features: []
        });
        setFilteredCars(localCars);
    };

    const displayCars = filteredCars.length > 0 ? filteredCars : localCars;

    return (
        <div className="tf-car-details-column">
            {/* Сайдбар с фильтрами */}
            <div className="tf-car-archive-sidebar">
                <div className="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                    <h4>{t('filter')}</h4>
                    <button className="filter-reset-btn" onClick={resetFilters}>{t('reset')}</button>
                </div>

                {/* Фильтры автомобилей */}
                <div className="tf-filter-section">
                    <h5 className="tf-filter-title">{t('car_filters')}</h5>

                    {/* Класс */}
                    <div className="tf-filter-group">
                        <label>{t('car_class')}</label>
                        <select
                            value={filters.carClass}
                            onChange={(e) => handleFilterChange('carClass', e.target.value)}
                            className="tf-filter-select"
                        >
                            <option value="">{t('all_classes')}</option>
                            {carClasses.map((carClass) => (
                                <option key={carClass.id} value={String(carClass.id)}>
                                    {carClass.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Пробег */}
                    <div className="tf-filter-group tf-range-group">
                        <label>{t('mileage')}</label>
                        <div className="tf-range-inputs">
                            <input
                                type="number"
                                placeholder={t('from')}
                                value={filters.minMileage}
                                onChange={(e) => handleFilterChange('minMileage', e.target.value)}
                                className="tf-filter-input"
                            />
                            <span>-</span>
                            <input
                                type="number"
                                placeholder={t('to')}
                                value={filters.maxMileage}
                                onChange={(e) => handleFilterChange('maxMileage', e.target.value)}
                                className="tf-filter-input"
                            />
                        </div>
                    </div>

                    {/* Состояние */}
                    <div className="tf-filter-group">
                        <label>{t('status')}</label>
                        <select
                            value={filters.status}
                            onChange={(e) => handleFilterChange('status', e.target.value)}
                            className="tf-filter-select"
                        >
                            <option value="">{t('all_statuses')}</option>
                            <option value="available">{t('available')}</option>
                            <option value="rented">{t('rented')}</option>
                            <option value="maintenance">{t('maintenance')}</option>
                            <option value="reserved">{t('reserved')}</option>
                        </select>
                    </div>

                    {/* Год */}
                    <div className="tf-filter-group tf-range-group">
                        <label>{t('year')}</label>
                        <div className="tf-range-inputs">
                            <input
                                type="number"
                                placeholder={t('from')}
                                value={filters.minYear}
                                onChange={(e) => handleFilterChange('minYear', e.target.value)}
                                className="tf-filter-input"
                            />
                            <span>-</span>
                            <input
                                type="number"
                                placeholder={t('to')}
                                value={filters.maxYear}
                                onChange={(e) => handleFilterChange('maxYear', e.target.value)}
                                className="tf-filter-input"
                            />
                        </div>
                    </div>

                    {/* Цена */}
                    <div className="tf-filter-group tf-range-group">
                        <label>{t('price_per_day')}</label>
                        <div className="tf-range-inputs">
                            <input
                                type="number"
                                placeholder={t('from')}
                                value={filters.minPrice}
                                onChange={(e) => handleFilterChange('minPrice', e.target.value)}
                                className="tf-filter-input"
                            />
                            <span>-</span>
                            <input
                                type="number"
                                placeholder={t('to')}
                                value={filters.maxPrice}
                                onChange={(e) => handleFilterChange('maxPrice', e.target.value)}
                                className="tf-filter-input"
                            />
                        </div>
                    </div>

                    {/* Тип топлива */}
                    <div className="tf-filter-group">
                        <label>{t('fuel_type')}</label>
                        <select
                            value={filters.fuelType}
                            onChange={(e) => handleFilterChange('fuelType', e.target.value)}
                            className="tf-filter-select"
                        >
                            <option value="">{t('all_fuel_types')}</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="lpg">LPG</option>
                        </select>
                    </div>

                    {/* Трансмиссия */}
                    <div className="tf-filter-group">
                        <label>{t('transmission')}</label>
                        <select
                            value={filters.transmission}
                            onChange={(e) => handleFilterChange('transmission', e.target.value)}
                            className="tf-filter-select"
                        >
                            <option value="">{t('all_transmissions')}</option>
                            <option value="auto">Automatic</option>
                            <option value="manual">Manual</option>
                            <option value="cvt">CVT</option>
                        </select>
                    </div>

                    {/* Количество мест */}
                    <div className="tf-filter-group">
                        <label>{t('seats')}</label>
                        <select
                            value={filters.seats}
                            onChange={(e) => handleFilterChange('seats', e.target.value)}
                            className="tf-filter-select"
                        >
                            <option value="">{t('all_seats')}</option>
                            <option value="2">2 {t('seats')}</option>
                            <option value="4">4 {t('seats')}</option>
                            <option value="5">5 {t('seats')}</option>
                            <option value="7">7 {t('seats')}</option>
                            <option value="8">8 {t('seats')}</option>
                            <option value="9">9 {t('seats')}</option>
                        </select>
                    </div>
                </div>

                {/* Блок: Комплектация (характеристики) */}
                <div className="tf-filter-section tf-features-section">
                    <h5 className="tf-filter-title">{t('equipment')}</h5>

                    {featuresLoading ? (
                        <div className="tf-loading-features">{t('loading')}</div>
                    ) : availableFeatures.length > 0 ? (
                        availableFeatures.map((feature) => (
                            <div key={feature.id} className="tf-feature-item">
                                <label className="tf-checkbox-label">
                                    <input
                                        type="checkbox"
                                        checked={filters.features.includes(String(feature.id))}
                                        onChange={() => handleFeatureToggle(feature.id)}
                                    />
                                    <span className="tf-checkbox-custom"></span>
                                    <span className="tf-feature-icon">
                                        <FeatureIcon
                                            name={feature.icon || feature.name}
                                            size={18}
                                            color="#566676"
                                        />
                                    </span>
                                    <span className="tf-feature-name">{feature.name}</span>
                                </label>
                            </div>
                        ))
                    ) : (
                        <div className="tf-no-features">{t('no_features_available')}</div>
                    )}
                </div>

                {/* Фильтры доп услуг */}
                <div className="tf-filter-section tf-extras-section">
                    <h5 className="tf-filter-title">{t('additional_services')}</h5>

                    {extrasLoading ? (
                        <div className="tf-loading-extras">{t('loading')}</div>
                    ) : availableExtras.length > 0 ? (
                        availableExtras.map((extra) => (
                            <div key={extra.id} className="tf-extra-item">
                                <label className="tf-checkbox-label">
                                    <input
                                        type="checkbox"
                                        checked={filters.extras.includes(String(extra.id))}
                                        onChange={() => handleExtraToggle(extra.id)}
                                    />
                                    <span className="tf-checkbox-custom"></span>
                                    <span className="tf-extra-icon">
                                        <ExtraIcon
                                            name={extra.icon || extra.name}
                                            size={18}
                                            color="#566676"
                                        />
                                    </span>
                                    <span className="tf-extra-description">{extra.name}</span>
                                </label>
                            </div>
                        ))
                    ) : (
                        <div className="tf-no-extras">{t('no_extras_available')}</div>
                    )}
                </div>
            </div>

            {/* Результаты */}
            <div className="tf-car-archive-result">
                <div className="tf-total-result-bar tf-mb-24">
                    <span className="tf-total-results">
                        <span>{filteredCars.length}</span> {t('cars_found')}
                    </span>
                </div>

                <div
                    className={`tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 ${viewMode === 'grid' ? 'grid-view' : 'list-view'}`}>
                    {loading || isFiltering ? (
                        <div className="loading-spinner">{t('loading')}</div>
                    ) : displayCars.length > 0 ? (
                        displayCars.map((car) => (
                            <CarCard key={car.id} car={car} viewMode={viewMode}/>
                        ))
                    ) : (
                        <div className="no-cars">{t('no_cars_available')}</div>
                    )}
                </div>
            </div>
        </div>
    );
}
// src/components/layout/Catalog/SearchBox.tsx
import BookingDateRangePicker from "../../ui/BookingDateRangePicker";
import { useTranslation } from 'react-i18next';
import { useState, useEffect } from "react";
import { useRouter } from 'next/router';
import { useLocations } from '../../../hooks/useLocations';
import { LocationDropdown } from '../../LocationDropdown';

interface LocationOption {
    id: number;
    name: string;
    address: string;
    city: string;
}

export default function SearchBox() {
    const { t } = useTranslation('common');
    const router = useRouter();
    const { query } = router;

    const {
        locations,
        isLoading: locationsLoading,
        getCities,
        getLocationsByCityId
    } = useLocations();

    // Инициализация из URL параметров
    const [rentalPeriod, setRentalPeriod] = useState<Date[]>(() => {
        if (query.startDate && query.endDate) {
            return [new Date(query.startDate as string), new Date(query.endDate as string)];
        }
        return [];
    });

    const [pickupLocation, setPickupLocation] = useState<number | null>(() => {
        return query.pickupLocation ? Number(query.pickupLocation) : null;
    });

    const [dropoffLocation, setDropoffLocation] = useState<number | null>(() => {
        return query.dropoffLocation ? Number(query.dropoffLocation) : null;
    });

    const [returnSameLocation, setReturnSameLocation] = useState<boolean>(() => {
        return query.returnSameLocation ? query.returnSameLocation === 'true' : true;
    });

    const [driverAge18to40, setDriverAge18to40] = useState<boolean>(() => {
        return query.driverAge18to40 ? query.driverAge18to40 === 'true' : true;
    });

    const [cities, setCities] = useState<Array<{id: number; name: string}>>([]);
    const [availableLocations, setAvailableLocations] = useState<LocationOption[]>([]);
    const [selectedCity, setSelectedCity] = useState<number | null>(() => {
        if (query.pickupCity) {
            const city = getCities().find((c: any) => c.name === query.pickupCity);
            return city ? city.id : null;
        }
        return null;
    });

    const isLoading = locationsLoading;

    useEffect(() => {
        const citiesData = getCities();
        setCities(citiesData.map((city: any) => ({
            id: city.id,
            name: city.name
        })));
    }, [getCities]);

    useEffect(() => {
        if (selectedCity) {
            const cityLocations = getLocationsByCityId(selectedCity);
            setAvailableLocations(cityLocations.map((loc: any) => ({
                id: loc.id,
                name: loc.name,
                address: loc.address || '',
                city: loc.city || ''
            })));
        } else {
            setAvailableLocations([]);
        }
    }, [selectedCity, getLocationsByCityId]);

    useEffect(() => {
        if (returnSameLocation && pickupLocation) {
            setDropoffLocation(pickupLocation);
        }
    }, [pickupLocation, returnSameLocation]);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();

        // Валидация
        if (!pickupLocation) {
            alert(t('please_select_pickup_location') || 'Пожалуйста, выберите точку выдачи');
            return;
        }

        if (!returnSameLocation && !dropoffLocation) {
            alert(t('please_select_dropoff_location') || 'Пожалуйста, выберите точку возврата');
            return;
        }

        if (rentalPeriod.length !== 2 || !rentalPeriod[0] || !rentalPeriod[1]) {
            alert(t('please_select_rental_period') || 'Пожалуйста, выберите период аренды');
            return;
        }

        if (rentalPeriod[0] >= rentalPeriod[1]) {
            alert(t('end_date_must_be_after_start') || 'Дата окончания должна быть позже даты начала');
            return;
        }

        // Формируем параметры для API
        const params = new URLSearchParams();

        // Даты (формат: YYYY-MM-DD)
        const startDate = rentalPeriod[0].toISOString().split('T')[0];
        const endDate = rentalPeriod[1].toISOString().split('T')[0];
        params.append('startDate', startDate);
        params.append('endDate', endDate);

        // Локации
        params.append('pickupLocation', String(pickupLocation));
        if (!returnSameLocation && dropoffLocation) {
            params.append('dropoffLocation', String(dropoffLocation));
        }

        // Дополнительные параметры
        params.append('returnSameLocation', String(returnSameLocation));
        params.append('driverAge18to40', String(driverAge18to40));

        // Получаем информацию о городе для отображения в URL
        const pickupCity = locations.find(group =>
            group.locations.some(location => location.id === pickupLocation)
        )?.city;
        if (pickupCity) {
            params.append('pickupCity', pickupCity);
        }

        // Переход на страницу каталога с параметрами
        router.push(`/cars/catalog?${params.toString()}`);
    };

    const handleReturnSameLocationChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const checked = e.target.checked;
        setReturnSameLocation(checked);

        if (checked && pickupLocation) {
            setDropoffLocation(pickupLocation);
        } else if (checked) {
            setDropoffLocation(null);
        }
    };

    if (isLoading) {
        return (
            <div className="tf-archive-search-box">
                <div className="tf-loading-locations">
                    <div className="spinner"></div>
                    <p>{t('loading') || 'Загрузка...'}</p>
                </div>
            </div>
        );
    }

    return (
        <div className="tf-archive-search-box">
            <form onSubmit={handleSearch}>
                <div className="tf-archive-search-box-wrapper">
                    {/* ТРИ БЛОКА В ОДНУ СТРОКУ */}
                    <div className="tf-search-row">
                        {/* Блок 1: Откуда */}
                        <div className="tf-search-block tf-search-block-location" style={{ position: 'relative', zIndex: 2 }}>
                            <LocationDropdown
                                label={t('pickup_location')}
                                locations={locations}
                                value={pickupLocation}
                                onChange={setPickupLocation}
                                placeholder={t('select_address')}
                                isDisabled={false}
                            />
                        </div>

                        {/* Блок 2: Куда */}
                        <div className="tf-search-block tf-search-block-location" style={{ position: 'relative', zIndex: 1 }}>
                            <LocationDropdown
                                label={t('dropoff_location')}
                                locations={locations}
                                value={dropoffLocation}
                                onChange={setDropoffLocation}
                                placeholder={t('select_address')}
                                isDisabled={returnSameLocation}
                            />
                        </div>

                        {/* Блок 3: Даты */}
                        <div className="tf-search-block tf-search-block-date" style={{position: 'relative', zIndex: 0}}>
                            <div className="tf-date-select-box tf-flex tf-flex-gap-8">
                                <div className="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn active">
                                    <div className="tf-select-date" style={{width: '100%'}}>
                                        <div className="tf-flex tf-flex-gap-4">
                                            <div className="icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z"
                                                        stroke="#566676" strokeWidth="1.5" strokeLinecap="round"
                                                        strokeLinejoin="round"/>
                                                </svg>
                                            </div>
                                            <div className="info-select" style={{width: '100%'}}>
                                                <h5>{t('rental_period')}</h5>
                                                <BookingDateRangePicker
                                                    value={rentalPeriod}
                                                    onChange={setRentalPeriod}
                                                    placeholder={t('select_rental_period')}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Чекбоксы и кнопка поиска */}
                    <div className="tf-driver-location-box tf-flex tf-flex-space-bttn tf-flex-align-center">
                        <div className="tf-driver-location">
                            <ul>
                                <li>
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="same_location"
                                            checked={returnSameLocation}
                                            onChange={handleReturnSameLocationChange}
                                        />
                                        <span className="tf-checkmark" aria-hidden="true"></span>
                                        <span className="tf-checkbox-label">{t('return_same_location')}</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="driver_age"
                                            checked={driverAge18to40}
                                            onChange={(e) => setDriverAge18to40(e.target.checked)}
                                        />
                                        <span className="tf-checkmark" aria-hidden="true"></span>
                                        <span className="tf-checkbox-label">{t('driver_age_18_40')}</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div className="tf-submit-button">
                            <button type="submit" className="tf-filter-cars">
                                {t('search')} <i className="ri-search-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    );
}

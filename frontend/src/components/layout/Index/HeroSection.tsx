// src/components/layout/Index/HeroSection.tsx
import { useRouter } from 'next/router';
import { useState, useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useCarClasses } from '../../../hooks/useCarClasses';
import BookingDateRangePicker from "../../ui/BookingDateRangePicker";

export default function HeroSection() {
    const router = useRouter();
    const { t } = useTranslation('common');
    const { carClasses, isLoading } = useCarClasses();

    const [carClass, setCarClass] = useState('');
    const [isCarClassOpen, setIsCarClassOpen] = useState(false);
    const [rentalPeriod, setRentalPeriod] = useState<Date[]>([]);
    const carClassRef = useRef(null);

    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (carClassRef.current && !carClassRef.current.contains(event.target)) {
                setIsCarClassOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const getCarClassLabel = (value: string) => {
        const car = carClasses.find(c => c.value === value || c.id === value);
        return car ? car.name : t('select_car_class');
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        console.log({
            carClass,
            pickupDateTime: rentalPeriod[0]?.toISOString(),
            dropoffDateTime: rentalPeriod[1]?.toISOString()
        });

        // Собираем параметры для URL
        const params = new URLSearchParams();

        if (carClass) {
            params.append('carClass', carClass);
        }

        if (rentalPeriod[0]) {
            params.append('pickup', rentalPeriod[0].toISOString());
        }

        if (rentalPeriod[1]) {
            params.append('dropoff', rentalPeriod[1].toISOString());
        }

        // Перенаправляем на страницу каталога
        const url = `/cars/catalog?${params.toString()}`;
        console.log('Redirecting to:', url);

        router.push(url);
    };;

    return (
        <section
            className="elementor-section hero-section"
            style={{
                backgroundImage: `url('/images/000-1.jpg')`,
                backgroundSize: 'cover',
                backgroundPosition: 'center center',
                backgroundRepeat: 'no-repeat',
                minHeight: '600px',
                display: 'flex',
                alignItems: 'center',
                position: 'relative'
            }}
        >
            <div className="elementor-background-overlay" style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                background: 'rgba(0, 0, 0, 0.5)',
                zIndex: 1
            }}></div>

            <div className="elementor-container" style={{
                position: 'relative',
                zIndex: 2,
                width: '100%',
                maxWidth: '1300px',
                margin: '0 auto',
                padding: '0 15px'
            }}>
                <div className="elementor-column" style={{ width: '100%' }}>
                    <div className="elementor-widget-wrap">
                        <h2 className="hero-title" style={{
                            color: '#fff',
                            fontSize: '3rem',
                            fontWeight: 700,
                            marginBottom: '30px',
                            textAlign: 'center',
                            textShadow: '0 2px 8px rgba(0,0,0,0.3)'
                        }}>
                            {t('drive_your_way')}
                        </h2>

                        <div className="elementor-element elementor-element-e305550 elementor-widget__width-initial compact-search elementor-widget elementor-widget-tourfic-search">
                            <div className="elementor-widget-container widget-area">
                                <div className="tf_tf_booking-widget-wrap" data-fullwidth="true">
                                    <div className="tf_custom-container">
                                        <div className="tf_custom-inner">
                                            <div id="tf-booking-search-tabs" className="default-form tf-search-tabs__design--1">
                                                <div className="tf-booking-form-tab design-1"></div>
                                                <div className="tf-booking-forms-wrapper">
                                                    <div id="tf-car-booking-form" className="tf-tabcontent" style={{ display: 'block' }}>
                                                        <form
                                                            className="tf_booking-widget default-form"
                                                            id="tf_car_booking"
                                                            method="get"
                                                            autoComplete="off"
                                                            onSubmit={handleSubmit}
                                                        >
                                                            <div className="tf_homepage-booking">
                                                                {/* Car Class Dropdown */}
                                                                <div className="tf_destination-wrap" ref={carClassRef}>
                                                                    <div className="tf_input-inner">
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row info-select">
                                                                                <span className="tf-label">{t('car_class')}:</span>
                                                                                <div className="tf_form-inner">
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fas fa-car"></i>
                                                                                    </div>
                                                                                    <div
                                                                                        className="selected-car-class"
                                                                                        onClick={() => !isLoading && setIsCarClassOpen(!isCarClassOpen)}
                                                                                        style={{
                                                                                            cursor: isLoading ? 'default' : 'pointer'
                                                                                        }}
                                                                                    >
                                                                                        <div className="text" style={{
                                                                                            color: carClass ? '#333' : '#999'
                                                                                        }}>
                                                                                            {isLoading ? t('loading') : (carClass ? getCarClassLabel(carClass) : t('select_car_class'))}
                                                                                        </div>
                                                                                        <div className="icon">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path>
                                                                                            </svg>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input
                                                                                        type="hidden"
                                                                                        name="car-class"
                                                                                        value={carClass}
                                                                                    />
                                                                                    {isCarClassOpen && !isLoading && (
                                                                                        <div className="tf-select-time" style={{
                                                                                            position: 'absolute',
                                                                                            top: '100%',
                                                                                            left: 0,
                                                                                            right: 0,
                                                                                            background: '#fff',
                                                                                            border: '1px solid #ddd',
                                                                                            borderRadius: '4px',
                                                                                            maxHeight: '200px',
                                                                                            overflowY: 'auto',
                                                                                            zIndex: 1000,
                                                                                            marginTop: '4px',
                                                                                            boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
                                                                                        }}>
                                                                                            <ul className="time-options-list" style={{
                                                                                                listStyle: 'none',
                                                                                                padding: '0',
                                                                                                margin: '0'
                                                                                            }}>
                                                                                                {carClasses.map((car) => (
                                                                                                    <li
                                                                                                        key={car.id}
                                                                                                        onClick={() => {
                                                                                                            setCarClass(car.value || car.id);
                                                                                                            setIsCarClassOpen(false);
                                                                                                        }}
                                                                                                        style={{
                                                                                                            padding: '8px 15px',
                                                                                                            cursor: 'pointer',
                                                                                                            fontSize: '14px',
                                                                                                            background: (carClass === car.value || carClass === car.id) ? '#0866c4' : 'transparent',
                                                                                                            color: (carClass === car.value || carClass === car.id) ? '#fff' : '#333'
                                                                                                        }}
                                                                                                    >
                                                                                                        {car.name}
                                                                                                    </li>
                                                                                                ))}
                                                                                            </ul>
                                                                                        </div>
                                                                                    )}
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Rental Period */}
                                                                <div className="tf_destination-wrap">
                                                                    <div className="tf_input-inner">
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row info-select">
                                                                                <span className="tf-label">{t('rental_period')}:</span>
                                                                                <div className="tf_form-inner">
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fa-solid fa-calendar-days"></i>
                                                                                    </div>
                                                                                    <BookingDateRangePicker
                                                                                        value={rentalPeriod}
                                                                                        onChange={setRentalPeriod}
                                                                                        placeholder={t('select_rental_period')}
                                                                                    />
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div className="tf_submit-wrap">
                                                                    <button className="tf_btn tf-submit" type="submit">
                                                                        {t('search')}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

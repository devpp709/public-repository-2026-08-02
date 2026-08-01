import { useState, useRef, useEffect } from 'react';
import { useTranslation } from 'react-i18next';

export default function HeroSection() {
    const { t } = useTranslation('common');

    const [pickupTime, setPickupTime] = useState('10:00 AM');
    const [dropoffTime, setDropoffTime] = useState('10:00 AM');
    const [pickupDate, setPickupDate] = useState('2026/07/31');
    const [dropoffDate, setDropoffDate] = useState('2026/08/01');
    const [isPickupTimeOpen, setIsPickupTimeOpen] = useState(false);
    const [isDropoffTimeOpen, setIsDropoffTimeOpen] = useState(false);
    const [pickupLocation, setPickupLocation] = useState('');
    const [dropoffLocation, setDropoffLocation] = useState('');
    const pickupTimeRef = useRef(null);
    const dropoffTimeRef = useRef(null);

    const timeOptions = [
        '12:00 AM', '12:30 AM', '1:00 AM', '1:30 AM', '2:00 AM', '2:30 AM',
        '3:00 AM', '3:30 AM', '4:00 AM', '4:30 AM', '5:00 AM', '5:30 AM',
        '6:00 AM', '6:30 AM', '7:00 AM', '7:30 AM', '8:00 AM', '8:30 AM',
        '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM',
        '12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM',
        '3:00 PM', '3:30 PM', '4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM',
        '6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM', '8:00 PM', '8:30 PM',
        '9:00 PM', '9:30 PM', '10:00 PM', '10:30 PM', '11:00 PM', '11:30 PM'
    ];

    useEffect(() => {
        function handleClickOutside(event) {
            if (pickupTimeRef.current && !pickupTimeRef.current.contains(event.target)) {
                setIsPickupTimeOpen(false);
            }
            if (dropoffTimeRef.current && !dropoffTimeRef.current.contains(event.target)) {
                setIsDropoffTimeOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        console.log({
            pickup: formData.get('pickup-name'),
            dropoff: formData.get('dropoff-name'),
            pickupDate: formData.get('pickup-date'),
            dropoffDate: formData.get('dropoff-date'),
            pickupTime: formData.get('pickup-time'),
            dropoffTime: formData.get('dropoff-time'),
            type: formData.get('type')
        });
    };

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

                        <div className="elementor-element elementor-element-e305550 elementor-widget__width-initial compact-search elementor-widget elementor-widget-tourfic-search" style={{
                            maxWidth: '900px',
                            margin: '0 auto'
                        }}>
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
                                                            action="/tf-search/"
                                                            onSubmit={handleSubmit}
                                                            style={{
                                                                padding: '0',
                                                                background: 'none',
                                                                border: 'none'
                                                            }}
                                                        >
                                                            <div className="tf_homepage-booking" style={{
                                                                display: 'table',
                                                                boxShadow: '0 2px 8px rgba(0, 0, 0, 0.15)',
                                                                borderRadius: '8px',
                                                                position: 'relative',
                                                                border: '1px solid #ddd',
                                                                justifyContent: 'space-between',
                                                                alignItems: 'center',
                                                                width: '100%',
                                                                fontSize: '0.875rem',
                                                                height: '55px',
                                                                margin: '24px 0 16px',
                                                                background: '#fff',
                                                                tableLayout: 'fixed'
                                                            }}>
                                                                {/* Pickup Location */}
                                                                <div className="tf_destination-wrap" style={{
                                                                    borderRight: '1px solid #D1D7EE',
                                                                    borderLeft: '0',
                                                                    display: 'table-cell',
                                                                    verticalAlign: 'middle',
                                                                    minWidth: '150px',
                                                                    width: '25%',
                                                                    padding: '0 15px'
                                                                }}>
                                                                    <div className="tf_input-inner" style={{ padding: '0' }}>
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row" style={{ margin: 0, display: 'block' }}>
                                                                                <span className="tf-label" style={{ display: 'none' }}>{t('enter_pickup_location')}</span>
                                                                                <div className="tf_form-inner" style={{
                                                                                    padding: '0',
                                                                                    display: 'flex',
                                                                                    alignItems: 'center',
                                                                                    position: 'relative'
                                                                                }}>
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fas fa-search"></i>
                                                                                    </div>
                                                                                    <input
                                                                                        type="text"
                                                                                        name="pickup-name"
                                                                                        id="tf_pickup_location"
                                                                                        className=""
                                                                                        placeholder={t('enter_pickup_location')}
                                                                                        value={pickupLocation}
                                                                                        onChange={(e) => setPickupLocation(e.target.value)}
                                                                                        style={{
                                                                                            border: '0px solid',
                                                                                            width: '100%',
                                                                                            background: 'transparent',
                                                                                            boxShadow: 'none',
                                                                                            height: '45px',
                                                                                            margin: '0px',
                                                                                            padding: '6px 5px',
                                                                                            fontSize: '14px',
                                                                                            borderRadius: '6px',
                                                                                            outline: 'none'
                                                                                        }}
                                                                                    />
                                                                                    <input type="hidden" name="pickup" className="tf-place-input" />
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Dropoff Location */}
                                                                <div className="tf_destination-wrap" style={{
                                                                    borderRight: '1px solid #D1D7EE',
                                                                    borderLeft: '0',
                                                                    display: 'table-cell',
                                                                    verticalAlign: 'middle',
                                                                    minWidth: '150px',
                                                                    width: '25%',
                                                                    padding: '0 15px'
                                                                }}>
                                                                    <div className="tf_input-inner" style={{ padding: '0' }}>
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row" style={{ margin: 0, display: 'block' }}>
                                                                                <span className="tf-label" style={{ display: 'none' }}>{t('enter_dropoff_location')}</span>
                                                                                <div className="tf_form-inner" style={{
                                                                                    padding: '0',
                                                                                    display: 'flex',
                                                                                    alignItems: 'center',
                                                                                    position: 'relative'
                                                                                }}>
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fas fa-search"></i>
                                                                                    </div>
                                                                                    <input
                                                                                        type="text"
                                                                                        name="dropoff-name"
                                                                                        id="tf_dropoff_location"
                                                                                        className=""
                                                                                        placeholder={t('enter_dropoff_location')}
                                                                                        value={dropoffLocation}
                                                                                        onChange={(e) => setDropoffLocation(e.target.value)}
                                                                                        style={{
                                                                                            border: '0px solid',
                                                                                            width: '100%',
                                                                                            background: 'transparent',
                                                                                            boxShadow: 'none',
                                                                                            height: '45px',
                                                                                            margin: '0px',
                                                                                            padding: '6px 5px',
                                                                                            fontSize: '14px',
                                                                                            borderRadius: '6px',
                                                                                            outline: 'none'
                                                                                        }}
                                                                                    />
                                                                                    <input type="hidden" name="dropoff" className="tf-place-input" />
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Pickup Date */}
                                                                <div className="tf_destination-wrap" style={{
                                                                    borderRight: '1px solid #D1D7EE',
                                                                    borderLeft: '0',
                                                                    display: 'table-cell',
                                                                    verticalAlign: 'middle',
                                                                    minWidth: '150px',
                                                                    width: '25%',
                                                                    padding: '0 15px'
                                                                }}>
                                                                    <div className="tf_input-inner" style={{ padding: '0' }}>
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row" style={{ margin: 0, display: 'block' }}>
                                                                                <span className="tf-label" style={{ display: 'none' }}>{t('date')}</span>
                                                                                <div className="tf_form-inner" style={{
                                                                                    padding: '0',
                                                                                    display: 'flex',
                                                                                    alignItems: 'center',
                                                                                    position: 'relative'
                                                                                }}>
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fa-solid fa-calendar-days"></i>
                                                                                    </div>
                                                                                    <input
                                                                                        type="text"
                                                                                        name="pickup-date"
                                                                                        className="tf_pickup_date flatpickr-input"
                                                                                        placeholder={t('date')}
                                                                                        value={pickupDate}
                                                                                        onChange={(e) => setPickupDate(e.target.value)}
                                                                                        style={{
                                                                                            border: '0px solid',
                                                                                            width: '100%',
                                                                                            background: 'transparent',
                                                                                            boxShadow: 'none',
                                                                                            height: '45px',
                                                                                            margin: '0px',
                                                                                            padding: '6px 5px',
                                                                                            fontSize: '14px',
                                                                                            borderRadius: '6px',
                                                                                            outline: 'none'
                                                                                        }}
                                                                                    />
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Pickup Time */}
                                                                <div className="tf_destination-wrap" style={{
                                                                    borderRight: '1px solid #D1D7EE',
                                                                    borderLeft: '0',
                                                                    display: 'table-cell',
                                                                    verticalAlign: 'middle',
                                                                    minWidth: '150px',
                                                                    width: '25%',
                                                                    padding: '0 15px',
                                                                    position: 'relative'
                                                                }} ref={pickupTimeRef}>
                                                                    <div className="tf_input-inner" style={{ padding: '0' }}>
                                                                        <div className="tf_form-row">
                                                                            <label className="tf_label-row info-select" style={{ margin: 0, display: 'block' }}>
                                                                                <span className="tf-label" style={{ display: 'none' }}>{t('time')}</span>
                                                                                <div className="tf_form-inner" style={{
                                                                                    padding: '0',
                                                                                    display: 'flex',
                                                                                    alignItems: 'center',
                                                                                    position: 'relative'
                                                                                }}>
                                                                                    <div className="tf-search-form-field-icon">
                                                                                        <i className="fa-regular fa-clock"></i>
                                                                                    </div>
                                                                                    <div
                                                                                        className="selected-pickup-time"
                                                                                        style={{
                                                                                            display: 'flex',
                                                                                            alignItems: 'center',
                                                                                            justifyContent: 'space-between',
                                                                                            width: '100%',
                                                                                            cursor: 'pointer'
                                                                                        }}
                                                                                        onClick={() => setIsPickupTimeOpen(!isPickupTimeOpen)}
                                                                                    >
                                                                                        <div className="text" style={{
                                                                                            fontSize: '14px',
                                                                                            color: '#333'
                                                                                        }}>
                                                                                            {pickupTime}
                                                                                        </div>
                                                                                        <div className="icon">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                                                <path d="M5 7.5L10 12.5L15 7.5" stroke="#566676" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path>
                                                                                            </svg>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="pickup-time" className="tf_pickup_time" id="tf_pickup_time" value={pickupTime} />

                                                                                    {isPickupTimeOpen && (
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
                                                                                            <ul className="time-options-list tf-pickup-time" style={{
                                                                                                listStyle: 'none',
                                                                                                padding: '0',
                                                                                                margin: '0'
                                                                                            }}>
                                                                                                {timeOptions.map((time) => (
                                                                                                    <li
                                                                                                        key={time}
                                                                                                        value={time}
                                                                                                        style={{
                                                                                                            padding: '8px 15px',
                                                                                                            cursor: 'pointer',
                                                                                                            fontSize: '14px',
                                                                                                            background: pickupTime === time ? '#0866c4' : 'transparent',
                                                                                                            color: pickupTime === time ? '#fff' : '#333'
                                                                                                        }}
                                                                                                        onMouseEnter={(e) => {
                                                                                                            if (pickupTime !== time) {
                                                                                                                e.target.style.background = '#f0f0f0';
                                                                                                            }
                                                                                                        }}
                                                                                                        onMouseLeave={(e) => {
                                                                                                            if (pickupTime !== time) {
                                                                                                                e.target.style.background = 'transparent';
                                                                                                            }
                                                                                                        }}
                                                                                                        onClick={() => {
                                                                                                            setPickupTime(time);
                                                                                                            setIsPickupTimeOpen(false);
                                                                                                        }}
                                                                                                    >
                                                                                                        {time}
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
                                                            </div>

                                                            <div className="tf_submit-wrap" style={{
                                                                textAlign: 'center',
                                                                marginTop: '0'
                                                            }}>
                                                                <input type="hidden" name="type" value="tf_carrental" className="tf-post-type" />
                                                                <button
                                                                    className="tf_btn tf-submit"
                                                                    type="submit"
                                                                    style={{
                                                                        background: '#0866c4',
                                                                        color: '#fff',
                                                                        border: 'none',
                                                                        padding: '12px 48px',
                                                                        borderRadius: '6px',
                                                                        fontSize: '16px',
                                                                        fontWeight: 600,
                                                                        cursor: 'pointer',
                                                                        transition: 'background 0.3s',
                                                                        height: '48px'
                                                                    }}
                                                                    onMouseEnter={(e) => e.target.style.background = '#0550a0'}
                                                                    onMouseLeave={(e) => e.target.style.background = '#0866c4'}
                                                                >
                                                                    {t('search')}
                                                                </button>
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
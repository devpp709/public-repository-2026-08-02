import { useState, useRef, useEffect } from "react";

export const LocationDropdown = ({
                                     label,
                                     locations,
                                     value,
                                     onChange,
                                     placeholder,
                                     isDisabled = false
                                 }: {
    label: string;
    locations: {
        city: string;
        locations: {
            id: number;
            name: string;
            address: string;
        }[];
    }[];
    value: number | null;
    onChange: (id: number) => void;
    placeholder: string;
    isDisabled?: boolean;
}) => {
    const [open, setOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Закрываем список при клике вне компонента
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    // Закрываем список при disabled
    useEffect(() => {
        if (isDisabled) {
            setOpen(false);
        }
    }, [isDisabled]);

    const selected = locations
        .flatMap(city =>
            city.locations.map(location => ({
                ...location,
                city: city.city
            }))
        )
        .find(item => item.id === value);

    return (
        <div className="tf-location-dropdown" ref={dropdownRef}>
            <div className="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn active" style={{
                opacity: isDisabled ? 0.6 : 1,
                background: isDisabled ? '#f5f5f5' : '#f8f9fc',
                cursor: isDisabled ? 'not-allowed' : 'pointer'
            }}>
                <div className="tf-select-date" style={{ width: '100%' }}>
                    <div className="tf-flex tf-flex-gap-4">
                        <div className="icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 10C11.3807 10 12.5 8.88071 12.5 7.5C12.5 6.11929 11.3807 5 10 5C8.61929 5 7.5 6.11929 7.5 7.5C7.5 8.88071 8.61929 10 10 10Z" stroke="#566676" strokeWidth="1.5"/>
                                <path d="M15.8334 7.5C15.8334 12.0833 10 17.5 10 17.5C10 17.5 4.16669 12.0833 4.16669 7.5C4.16669 3.27208 7.7631 0 10 0C12.2369 0 15.8334 3.27208 15.8334 7.5Z" stroke="#566676" strokeWidth="1.5"/>
                            </svg>
                        </div>
                        <div className="info-select" style={{ width: '100%' }}>
                            <h5 style={{
                                fontSize: '11px',
                                fontWeight: 600,
                                color: isDisabled ? '#999' : '#6b7a8f',
                                margin: '0 0 2px 0',
                                textTransform: 'uppercase',
                                letterSpacing: '0.5px'
                            }}>
                                {label}
                            </h5>
                            <div
                                className="tf-location-input"
                                onClick={() => {
                                    if (!isDisabled) {
                                        setOpen(!open);
                                    }
                                }}
                                style={{
                                    border: 'none',
                                    background: 'transparent',
                                    padding: '4px 0',
                                    fontSize: '14px',
                                    color: isDisabled ? '#999' : '#1a2332',
                                    width: '100%',
                                    outline: 'none',
                                    cursor: isDisabled ? 'not-allowed' : 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    minHeight: '28px'
                                }}
                            >
                                {selected
                                    ? `${selected.city}, ${selected.name}`
                                    : <span style={{ color: '#a0aec0' }}>{placeholder}</span>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {open && !isDisabled && (
                <div className="tf-location-list">
                    {locations.map(city => (
                        <div key={city.city}>
                            <div className="tf-location-city">
                                {city.city}
                            </div>

                            {city.locations.map(location => (
                                <div
                                    key={location.id}
                                    className="tf-location-address"
                                    onClick={() => {
                                        onChange(location.id);
                                        setOpen(false);
                                    }}
                                >
                                    {location.address}
                                </div>
                            ))}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
};
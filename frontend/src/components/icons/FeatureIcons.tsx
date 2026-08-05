// src/components/icons/FeatureIcons.tsx

import React from 'react';

interface IconProps {
    size?: number;
    color?: string;
    className?: string;
}

// ============ ХАРАКТЕРИСТИКИ (FEATURES) ============

export const IconABS: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M4 6L8 12L12 6L16 12L20 6" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M4 18H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="8" cy="6" r="1.5" fill={color}/>
        <circle cx="16" cy="6" r="1.5" fill={color}/>
        <circle cx="12" cy="12" r="1.5" fill={color}/>
    </svg>
);

export const IconESP: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L15 8H9L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M12 22L9 16H15L12 22Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M2 12L8 9V15L2 12Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M22 12L16 9V15L22 12Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <circle cx="12" cy="12" r="3" stroke={color} strokeWidth="2"/>
    </svg>
);

export const IconBackCamera: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="2" y="4" width="20" height="16" rx="2" stroke={color} strokeWidth="2"/>
        <circle cx="12" cy="12" r="4" stroke={color} strokeWidth="2"/>
        <circle cx="12" cy="12" r="1.5" fill={color}/>
        <path d="M19 8L21 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M5 8L3 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M19 16L21 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M5 16L3 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconParkingSensors: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="3" y="8" width="18" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <circle cx="8" cy="14" r="1.5" fill={color}/>
        <circle cx="12" cy="14" r="1.5" fill={color}/>
        <circle cx="16" cy="14" r="1.5" fill={color}/>
        <path d="M3 8L1 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M21 8L23 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconAirbags: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
        <circle cx="12" cy="12" r="4" stroke={color} strokeWidth="2"/>
        <path d="M12 3V6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 18V21" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M3 12H6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18 12H21" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconBluetooth: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M6 8L18 16L12 20V4L18 8L6 16" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M12 12L18 16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 12L18 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconUSB: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="8" y="4" width="8" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M12 16V20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="20" r="2" stroke={color} strokeWidth="2"/>
        <path d="M10 8H14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M10 11H14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconAudio: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="2" y="4" width="20" height="16" rx="2" stroke={color} strokeWidth="2"/>
        <circle cx="9" cy="12" r="3" stroke={color} strokeWidth="2"/>
        <circle cx="16" cy="10" r="2" stroke={color} strokeWidth="2"/>
        <circle cx="16" cy="14" r="1.5" fill={color}/>
        <path d="M12 9V15" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconKeylessGo: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
        <path d="M12 7V12L15 14" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M4 12H2" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M22 12H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconRainSensor: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2C8 2 5 5 5 9C5 13 8 16 12 16C16 16 19 13 19 9C19 5 16 2 12 2Z" stroke={color} strokeWidth="2"/>
        <path d="M12 16V20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 20H16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 22H18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconLightSensor: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="5" stroke={color} strokeWidth="2"/>
        <path d="M12 2V4" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 20V22" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M4 12H2" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M22 12H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M5.64 5.64L7.05 7.05" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16.95 16.95L18.36 18.36" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18.36 5.64L16.95 7.05" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M7.05 16.95L5.64 18.36" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconPushStart: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
        <path d="M12 7V12L15 15" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <circle cx="12" cy="12" r="2" fill={color}/>
    </svg>
);

export const IconClimateControl: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="4" y="6" width="16" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M8 10V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 10V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 8L4 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18 8L20 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 16L4 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18 16L20 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconLeatherSeats: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="3" y="8" width="18" height="10" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M3 8L8 4H16L21 8" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M8 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconCruiseControl: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
        <path d="M12 4V7" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 17V20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M4 12H7" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M17 12H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 8L10 10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M14 14L16 16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 16L10 14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M14 10L16 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconSunroof: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="3" y="6" width="18" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M9 6V18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M15 6V18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 6V18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 9H9" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M15 9H18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconGPS: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L8 10H16L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M12 22L8 14H16L12 22Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M2 12L10 8V16L2 12Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M22 12L14 8V16L22 12Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
    </svg>
);

export const IconHeatedSteering: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
        <path d="M12 4C12 4 14 6 14 9C14 12 12 14 12 14" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M12 20C12 20 10 18 10 15C10 12 12 10 12 10" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M18 8C18 8 16 10 16 13C16 16 18 18 18 18" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M6 8C6 8 8 10 8 13C8 16 6 18 6 18" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
    </svg>
);

export const IconHeatedSeats: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="4" y="12" width="16" height="8" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M8 20V22" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 20V22" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 20V22" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 12L6 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 12L18 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 12L12 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconElectricSeats: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="3" y="8" width="18" height="10" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M8 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 18V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 10L10 12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 10L14 12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 8V10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

// ============ ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ (EXTRAS) ============

export const IconWifi: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M5 12C7.5 9.5 16.5 9.5 19 12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 15C10 13.5 14 13.5 16 15" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="18" r="1.5" fill={color}/>
        <path d="M12 6C8.5 6 5.5 7.5 3 10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconDriver: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="8" r="4" stroke={color} strokeWidth="2"/>
        <path d="M6 20V18C6 15.8 7.8 14 10 14H14C16.2 14 18 15.8 18 18V20" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <circle cx="12" cy="20" r="2" stroke={color} strokeWidth="2"/>
    </svg>
);

export const IconOneWay: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 4L4 12L12 20" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M20 4L12 12L20 20" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M4 12H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconDelivery: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="2" y="8" width="16" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M18 12H22V16H18" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <circle cx="6" cy="18" r="2" stroke={color} strokeWidth="2"/>
        <circle cx="18" cy="18" r="2" stroke={color} strokeWidth="2"/>
        <path d="M8 12H14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 15H12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconCarWash: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M6 4L8 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18 4L16 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <rect x="4" y="8" width="16" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <circle cx="8" cy="18" r="2" stroke={color} strokeWidth="2"/>
        <circle cx="16" cy="18" r="2" stroke={color} strokeWidth="2"/>
        <path d="M8 12H16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 15H12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconFullTank: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M4 12H18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M4 8H16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M4 16H12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <rect x="2" y="4" width="16" height="16" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M22 8L20 12L22 16" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
    </svg>
);

export const IconRoofRack: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="3" y="6" width="18" height="4" rx="1" stroke={color} strokeWidth="2"/>
        <rect x="6" y="10" width="12" height="8" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M10 4V6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M14 4V6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconBooster: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="6" y="10" width="12" height="10" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M9 10L10 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M15 10L14 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="9" cy="14" r="1.5" fill={color}/>
        <circle cx="15" cy="14" r="1.5" fill={color}/>
    </svg>
);

export const IconChildSeat: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <rect x="5" y="8" width="14" height="12" rx="1" stroke={color} strokeWidth="2"/>
        <path d="M9 8L8 4" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M15 8L16 4" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="9" cy="12" r="1.5" fill={color}/>
        <circle cx="15" cy="12" r="1.5" fill={color}/>
        <path d="M12 12V16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconWinterTires: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="8" stroke={color} strokeWidth="2"/>
        <path d="M12 4V6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 18V20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M4 12H6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M18 12H20" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 6L7.5 7.5" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16.5 16.5L18 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 18L7.5 16.5" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16.5 7.5L18 6" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 8V12L14 14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconChains: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <circle cx="12" cy="12" r="8" stroke={color} strokeWidth="2"/>
        <path d="M6 8L8 10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 14L18 16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M6 16L8 14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 8L18 10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M8 6L6 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M10 18L8 16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M16 6L14 8" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M14 18L16 16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconInsurance: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L3 6V12C3 16 12 22 12 22C12 22 21 16 21 12V6L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M12 8V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="17" r="1.5" fill={color}/>
        <path d="M8 10L16 10" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconInsuranceCASCO: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L3 6V12C3 16 12 22 12 22C12 22 21 16 21 12V6L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M8 10H16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 8V14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="17" r="1.5" fill={color}/>
        <path d="M12 14L16 18" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconInsuranceOSAGO: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L3 6V12C3 16 12 22 12 22C12 22 21 16 21 12V6L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M8 12H16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="15" r="1.5" fill={color}/>
        <path d="M12 8V12" stroke={color} strokeWidth="2" strokeLinecap="round"/>
    </svg>
);

export const IconInsuranceTheft: React.FC<IconProps> = ({ size = 20, color = "#566676", className = "" }) => (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
        <path d="M12 2L3 6V12C3 16 12 22 12 22C12 22 21 16 21 12V6L12 2Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        <path d="M9 10L15 14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M15 10L9 14" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <path d="M12 14V16" stroke={color} strokeWidth="2" strokeLinecap="round"/>
        <circle cx="12" cy="18" r="1.5" fill={color}/>
    </svg>
);

// ============ MAP ИКОНОК ============

export const FEATURE_ICON_MAP: Record<string, React.FC<IconProps>> = {
    'abs': IconABS,
    'esp': IconESP,
    'back_camera': IconBackCamera,
    'parking_sensors': IconParkingSensors,
    'airbags': IconAirbags,
    'bluetooth': IconBluetooth,
    'usb': IconUSB,
    'audio': IconAudio,
    'keyless_go': IconKeylessGo,
    'rain_sensor': IconRainSensor,
    'light_sensor': IconLightSensor,
    'push_start': IconPushStart,
    'climate_control': IconClimateControl,
    'leather_seats': IconLeatherSeats,
    'cruise_control': IconCruiseControl,
    'sunroof': IconSunroof,
    'gps': IconGPS,
    'heated_steering': IconHeatedSteering,
    'heated_seats': IconHeatedSeats,
    'electric_seats': IconElectricSeats,
};

export const EXTRA_ICON_MAP: Record<string, React.FC<IconProps>> = {
    'wifi': IconWifi,
    'driver': IconDriver,
    'one_way': IconOneWay,
    'delivery': IconDelivery,
    'car_wash': IconCarWash,
    'full_tank': IconFullTank,
    'roof_rack': IconRoofRack,
    'booster': IconBooster,
    'child_seat': IconChildSeat,
    'winter_tires': IconWinterTires,
    'chains': IconChains,
    'insurance': IconInsurance,
    'casco': IconInsuranceCASCO,
    'osago': IconInsuranceOSAGO,
    'theft': IconInsuranceTheft,
};

// ============ КОМПОНЕНТ ДЛЯ ПОЛУЧЕНИЯ ИКОНКИ ============

interface FeatureIconProps extends IconProps {
    name: string;
}

export const FeatureIcon: React.FC<FeatureIconProps> = ({ name, size = 20, color = "#566676", className = "" }) => {
    const IconComponent = FEATURE_ICON_MAP[name.toLowerCase()];
    if (!IconComponent) {
        // Иконка по умолчанию
        return (
            <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
                <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
                <path d="M12 8V12L14 14" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
        );
    }
    return <IconComponent size={size} color={color} className={className} />;
};

export const ExtraIcon: React.FC<FeatureIconProps> = ({ name, size = 20, color = "#566676", className = "" }) => {
    const IconComponent = EXTRA_ICON_MAP[name.toLowerCase()];
    if (!IconComponent) {
        // Иконка по умолчанию
        return (
            <svg width={size} height={size} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={className}>
                <circle cx="12" cy="12" r="9" stroke={color} strokeWidth="2"/>
                <path d="M12 8V12L14 14" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
        );
    }
    return <IconComponent size={size} color={color} className={className} />;
};

// ============ ЭКСПОРТ ============

export default {
    FEATURE_ICON_MAP,
    EXTRA_ICON_MAP,
    FeatureIcon,
    ExtraIcon,
    // Features
    IconABS,
    IconESP,
    IconBackCamera,
    IconParkingSensors,
    IconAirbags,
    IconBluetooth,
    IconUSB,
    IconAudio,
    IconKeylessGo,
    IconRainSensor,
    IconLightSensor,
    IconPushStart,
    IconClimateControl,
    IconLeatherSeats,
    IconCruiseControl,
    IconSunroof,
    IconGPS,
    IconHeatedSteering,
    IconHeatedSeats,
    IconElectricSeats,
    // Extras
    IconWifi,
    IconDriver,
    IconOneWay,
    IconDelivery,
    IconCarWash,
    IconFullTank,
    IconRoofRack,
    IconBooster,
    IconChildSeat,
    IconWinterTires,
    IconChains,
    IconInsurance,
    IconInsuranceCASCO,
    IconInsuranceOSAGO,
    IconInsuranceTheft,
};
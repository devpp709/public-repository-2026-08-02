import {useTranslation} from 'react-i18next';
import React from "react";

interface ArchiveHeaderProps {
    totalResults?: number,
    viewMode?: "grid" | "list",
    setViewMode?: (mode: "grid" | "list") => void;
}

export default function ArchiveHeader({totalResults, viewMode, setViewMode}: ArchiveHeaderProps) {
    const {t} = useTranslation('common');

    const handleViewModeChange = (mode: "grid" | "list") => {
        if (setViewMode) {
            setViewMode(mode);
        }
    };

    return (
        <div className="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
            <div className="tf-archive-view">
                <ul className="tf-flex tf-flex-gap-16">
                    <li className={viewMode === 'grid' ? 'active' : ''} data-view="grid"
                        onClick={() => handleViewModeChange('grid')}>
                        <i className="ri-layout-grid-line"></i>
                    </li>
                    <li className={viewMode === 'list' ? 'active' : ''} data-view="list"
                        onClick={() => handleViewModeChange('list')}>
                        <i className="ri-list-check"></i>
                    </li>
                </ul>
            </div>
            <div className="tf-total-result-bar">
                <span>{t('total_results')}</span>
                <div className="tf-total-results">
                    <span>{totalResults}</span>
                </div>
            </div>
        </div>
    );
}

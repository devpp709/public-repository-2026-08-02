// ============ КОНФИГУРАЦИЯ КАТЕГОРИЙ ============

// Для ExtraService
export const EXTRA_CATEGORY_CONFIG = {
  Insurance: { label: 'Страхование', icon: '📋' },
  Equipment: { label: 'Оборудование', icon: '🔧' },
  Comfort: { label: 'Комфорт', icon: '💺' },
  Safety: { label: 'Безопасность', icon: '🛡️' },
  Additional: { label: 'Дополнительно', icon: '➕' },
} as const;

// Для Feature
export const FEATURE_CATEGORY_CONFIG = {
  safety: { label: 'Безопасность', icon: '🛡️' },
  comfort: { label: 'Комфорт', icon: '💺' },
  technology: { label: 'Технологии', icon: '💻' },
  exterior: { label: 'Экстерьер', icon: '🚗' },
  interior: { label: 'Интерьер', icon: '🪑' },
  performance: { label: 'Производительность', icon: '⚡' },
} as const;

export type ExtraCategoryCode = keyof typeof EXTRA_CATEGORY_CONFIG;
export type FeatureCategoryCode = keyof typeof FEATURE_CATEGORY_CONFIG;

// ============ УТИЛИТЫ ДЛЯ EXTRA SERVICE ============

export const getExtraCategoryLabel = (code?: string | null): string => {
  if (!code) return 'Другое';
  return EXTRA_CATEGORY_CONFIG[code as ExtraCategoryCode]?.label || code;
};

export const getExtraCategoryIcon = (code?: string | null): string => {
  if (!code) return '📋';
  return EXTRA_CATEGORY_CONFIG[code as ExtraCategoryCode]?.icon || '📋';
};

export const getAllExtraCategories = () => {
  return Object.entries(EXTRA_CATEGORY_CONFIG).map(([code, config]) => ({
    value: code,
    label: config.label,
    code: code
  }));
};

// ============ УТИЛИТЫ ДЛЯ FEATURE ============

export const getFeatureCategoryLabel = (code?: string | null): string => {
  if (!code) return 'Другое';
  return FEATURE_CATEGORY_CONFIG[code as FeatureCategoryCode]?.label || code;
};

export const getFeatureCategoryIcon = (code?: string | null): string => {
  if (!code) return '📋';
  return FEATURE_CATEGORY_CONFIG[code as FeatureCategoryCode]?.icon || '📋';
};

export const getAllFeatureCategories = () => {
  return Object.entries(FEATURE_CATEGORY_CONFIG).map(([code, config]) => ({
    value: code,
    label: config.label,
    code: code
  }));
};

// ============ СТАРЫЕ ФУНКЦИИ (для обратной совместимости) ============

/**
 * @deprecated Используйте getExtraCategoryLabel или getFeatureCategoryLabel
 */
export const getCategoryLabel = (category: string | undefined | null): string => {
  return getExtraCategoryLabel(category);
};

/**
 * @deprecated Используйте getFeatureCategoryLabel
 */
export const getFeatureCategoryLabelOld = (category: string | undefined | null): string => {
  const labels: Record<string, string> = {
    'Safety': 'Безопасность',
    'Comfort': 'Комфорт',
    'Technology': 'Технологии',
    'Exterior': 'Экстерьер',
    'Interior': 'Интерьер',
    'Performance': 'Производительность',
  };

  if (!category) return 'Другое';
  return labels[category] || category;
};

/**
 * @deprecated Используйте getFeatureCategoryIcon
 */
export const getFeatureCategoryColor = (category: string | undefined): string => {
  const colors: Record<string, string> = {
    'Safety': '#EF4444',
    'Comfort': '#10B981',
    'Technology': '#3B82F6',
    'Exterior': '#F59E0B',
    'Interior': '#8B5CF6',
    'Performance': '#EC4899',
  };

  return category ? colors[category] || '#6B7280' : '#6B7280';
};

// ============ ОСТАЛЬНЫЕ УТИЛИТЫ ============

export const formatDate = (date: Date | string | number): string => {
  return new Date(date).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

export const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB'
  }).format(amount);
};

export const generateId = (): string => {
  return Date.now().toString(36) + Math.random().toString(36).substring(2);
};

export const formatPrice = (price: number | undefined | null, currency: string = '₽'): string => {
  if (price === undefined || price === null) return '0 ₽';
  return new Intl.NumberFormat('ru-RU').format(price) + ' ' + currency;
};

export const getStatusColor = (isActive: boolean): 'success' | 'error' | 'warning' => {
  return isActive ? 'success' : 'error';
};

export const getStatusText = (isActive: boolean): string => {
  return isActive ? 'Активна' : 'Неактивна';
};
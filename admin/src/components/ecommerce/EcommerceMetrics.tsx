import React, { useState, useEffect } from 'react';
import {
  ArrowDownIcon,
  ArrowUpIcon,
  BoxIconLine,
  GroupIcon,
} from "../../icons";
import Badge from "../ui/badge/Badge";
import { useLanguage } from "../../i18n/LanguageProvider";
import { useCustomersStatistics } from "../../hooks/useCustomers";
import { useBookingsStatistics } from "../../hooks/useBookings";

export default function EcommerceMetrics() {
  const { t } = useLanguage();
  const [period, setPeriod] = useState<'week' | 'month'>('week');

  // Используем хук для получения статистики клиентов
  const {
    data: customersData,
    loading: customersLoading,
    error: customersError,
    refetch: refetchCustomers
  } = useCustomersStatistics({
    days: period === 'week' ? 7 : 30,
    autoFetch: true
  });

  const {
    data: bookingsData,
    loading: bookingsLoading,
    error: bookingsError,
    refetch: refetchBookings
  } = useBookingsStatistics({
    days: period === 'week' ? 7 : 30,
    autoFetch: true
  });

  // Получаем данные из ответа
  const totalCustomers = customersData?.data?.totalCustomers ?? 0;
  const growthPercentage = customersData?.data?.growthPercentage ?? 0;
  const trend = customersData?.data?.trend ?? 'stable';
  const newCustomers = customersData?.data?.newCustomers ?? 0;

  const customerGrowthPercentage =
      customersData?.data?.growthPercentage ?? 0;

  const customerTrend =
      customersData?.data?.trend ?? 'stable';

  const totalBookings = bookingsData?.data?.totalOrders ?? 0;
  const newBookings = bookingsData?.data?.newOrders ?? 0;
  const bookingGrowthPercentage = bookingsData?.data?.growthPercentage ?? 0;
  const bookingTrend = bookingsData?.data?.trend ?? 'stable';

  // Форматируем число с разделителями тысяч
  const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('ru-RU').format(num);
  };

  // Определяем цвет Badge в зависимости от тренда
  const getBadgeColor = (trend: 'up' | 'down' | 'stable'): 'success' | 'error' | 'warning' => {
    if (trend === 'up') return 'success';
    if (trend === 'down') return 'error';
    return 'warning';
  };

  // Определяем иконку в зависимости от тренда
  const getTrendIcon = (trend: 'up' | 'down' | 'stable') => {
    if (trend === 'up') return <ArrowUpIcon />;
    if (trend === 'down') return <ArrowDownIcon />;
    return null; // или можно вернуть иконку для стабильности
  };

  // Форматируем процент для отображения
  const formatPercentage = (value: number): string => {
    const sign = value > 0 ? '+' : '';
    return `${sign}${value.toFixed(2)}%`;
  };

  // Состояние загрузки для отображения скелетона
  if (customersLoading || bookingsLoading) {
    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
          {/* Skeleton для первой карточки */}
          <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 animate-pulse">
            <div className="flex items-center justify-center w-12 h-12 bg-gray-200 rounded-xl dark:bg-gray-700"></div>
            <div className="flex items-end justify-between mt-5">
              <div className="flex-1">
                <div className="h-4 bg-gray-200 rounded w-20 dark:bg-gray-700"></div>
                <div className="h-8 bg-gray-200 rounded w-24 mt-2 dark:bg-gray-700"></div>
              </div>
              <div className="h-6 bg-gray-200 rounded w-16 dark:bg-gray-700"></div>
            </div>
          </div>

          {/* Skeleton для второй карточки */}
          <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 animate-pulse">
            <div className="flex items-center justify-center w-12 h-12 bg-gray-200 rounded-xl dark:bg-gray-700"></div>
            <div className="flex items-end justify-between mt-5">
              <div className="flex-1">
                <div className="h-4 bg-gray-200 rounded w-20 dark:bg-gray-700"></div>
                <div className="h-8 bg-gray-200 rounded w-24 mt-2 dark:bg-gray-700"></div>
              </div>
              <div className="h-6 bg-gray-200 rounded w-16 dark:bg-gray-700"></div>
            </div>
          </div>
        </div>
    );
  }

  // Обработка ошибок
  if (customersError || bookingsError) {
    return (
        <div className="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-900 dark:bg-red-950/20">
          <div className="text-sm text-red-600 dark:text-red-400">
            {customersError || bookingsError}
          </div>

          <button
              onClick={() => {
                refetchCustomers();
                refetchBookings();
              }}
              className="mt-2 text-sm text-red-600 hover:text-red-800 dark:text-red-400"
          >
            {t('retry') || 'Повторить'}
          </button>
        </div>
    );
  }

  return (
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
        {/* <!-- Metric Item: Customers --> */}
        <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
              <GroupIcon className="text-gray-800 size-6 dark:text-white/90" />
            </div>

            {/* Селектор периода для клиентов */}
            <div className="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
              <button
                  onClick={() => setPeriod('week')}
                  className={`px-2 py-1 text-xs rounded-md transition-colors ${
                      period === 'week'
                          ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm'
                          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  }`}
              >
                {t('week') || 'Неделя'}
              </button>
              <button
                  onClick={() => setPeriod('month')}
                  className={`px-2 py-1 text-xs rounded-md transition-colors ${
                      period === 'month'
                          ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm'
                          : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                  }`}
              >
                {t('month') || 'Месяц'}
              </button>
            </div>
          </div>

          <div className="flex items-end justify-between mt-5">
            <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
              {t('customers')}
            </span>
              <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                {formatNumber(totalCustomers)}
              </h4>
              <div className="flex items-center gap-2 mt-1">
              <span className="text-xs text-gray-400 dark:text-gray-500">
                {t('new')}: {formatNumber(newCustomers)}
              </span>
              </div>
            </div>

            {trend !== 'stable' && (
                <Badge color={getBadgeColor(trend)}>
                  {getTrendIcon(trend)}
                  {formatPercentage(growthPercentage)}
                </Badge>
            )}

            {trend === 'stable' && (
                <Badge color="warning">
                  <span className="text-sm">→</span>
                  0.00%
                </Badge>
            )}
          </div>
        </div>
        {/* <!-- Metric Item End --> */}

        {/* <!-- Metric Item: Orders --> */}
        {/* Metric Item: Orders */}
        <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
          <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
            <BoxIconLine className="text-gray-800 size-6 dark:text-white/90" />
          </div>

          <div className="flex items-end justify-between mt-5">
            <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
                {t('orders')}
            </span>

              <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                {formatNumber(totalBookings)}
              </h4>

              <div className="flex items-center gap-2 mt-1">
                <span className="text-xs text-gray-400 dark:text-gray-500">
                    {t('new')}: {formatNumber(newBookings)}
                </span>
              </div>
            </div>

            {bookingTrend !== 'stable' && (
                <Badge
                    color={getBadgeColor(bookingTrend)}
                >
                  {bookingTrend === 'up' && (
                      <ArrowUpIcon />
                  )}

                  {bookingTrend === 'down' && (
                      <ArrowDownIcon />
                  )}

                  {formatPercentage(
                      bookingGrowthPercentage
                  )}
                </Badge>
            )}

            {bookingTrend === 'stable' && (
                <Badge color="warning">
                  <span className="text-sm">→</span>
                  0.00%
                </Badge>
            )}
          </div>
        </div>
        {/* <!-- Metric Item End --> */}
      </div>
  );
}
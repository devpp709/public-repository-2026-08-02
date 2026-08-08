import Chart from "react-apexcharts";
import { ApexOptions } from "apexcharts";
import { Dropdown } from "../ui/dropdown/Dropdown";
import { DropdownItem } from "../ui/dropdown/DropdownItem";
import { MoreDotIcon } from "../../icons";
import { useState } from "react";
import { useLanguage } from "../../i18n/LanguageProvider";
import { useBookingsMonthlyStatistics } from "../../hooks/useBookings";

export default function MonthlySalesChart() {
  const { t } = useLanguage();
  const currentYear = new Date().getFullYear();
  const years = [currentYear, currentYear - 1, currentYear - 2];
  const [selectedYear, setSelectedYear] = useState(currentYear);


  const options: ApexOptions = {
    colors: ["#465fff"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "bar",
      height: 180,
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "39%",
        borderRadius: 5,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 4,
      colors: ["transparent"],
    },
    xaxis: {
      categories: [
        t('jan'),
        t('feb'),
        t('mar'),
        t('apr'),
        t('may'),
        t('jun'),
        t('jul'),
        t('aug'),
        t('sep'),
        t('oct'),
        t('nov'),
        t('dec'),
      ],
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Outfit",
    },
    yaxis: {
      title: {
        text: undefined,
      },
    },
    grid: {
      yaxis: {
        lines: {
          show: true,
        },
      },
    },
    fill: {
      opacity: 1,
    },
    tooltip: {
      x: {
        show: false,
      },
      y: {
        formatter: (val: number) => `${val}`,
      },
    },
  };

  const [isOpen, setIsOpen] = useState(false);

  const {
    data: monthlyData,
    loading,
    error,
  } = useBookingsMonthlyStatistics(selectedYear);

  const series = [
    {
      name: t('orders'),
      data: monthlyData?.data ?? Array(12).fill(0),
    },
  ];


  function toggleDropdown() {
    setIsOpen(!isOpen);
  }

  function closeDropdown() {
    setIsOpen(false);
  }

  return (
      <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
        <div className="flex items-center justify-between">
          <h3 className="text-lg font-semibold text-gray-800 dark:text-white/90">
            {t('bookings_per_month')}
          </h3>
          <div className="relative inline-block">
            <button className="dropdown-toggle" onClick={toggleDropdown}>
              <MoreDotIcon className="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 size-6" />
            </button>
            <Dropdown
                isOpen={isOpen}
                onClose={closeDropdown}
                className="w-40 p-2"
            >
              {years.map((year) => (
                  <DropdownItem
                      key={year}
                      onItemClick={() => {
                        setSelectedYear(year);
                        closeDropdown();
                      }}
                      className={`flex w-full font-normal text-left rounded-lg ${
                          selectedYear === year
                              ? 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300'
                              : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300'
                      }`}
                  >
                    {year}
                  </DropdownItem>
              ))}
            </Dropdown>
          </div>
        </div>

        <div className="max-w-full overflow-x-auto custom-scrollbar">
          <div className="-ml-5 min-w-[650px] xl:min-w-full pl-2">
            <Chart options={options} series={series} type="bar" height={180} />
          </div>
        </div>
      </div>
  );
}
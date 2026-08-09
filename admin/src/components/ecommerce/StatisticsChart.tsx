import { useEffect, useRef, useState } from "react";
import Chart from "react-apexcharts";
import { ApexOptions } from "apexcharts";
import flatpickr from "flatpickr";
import { useLanguage } from "../../i18n/LanguageProvider";
import { useBookingsChartStatistics } from "../../hooks/useBookings";

type Period = "month" | "quarter" | "year";

export default function StatisticsChart() {
  const { t } = useLanguage();
  const datePickerRef = useRef<HTMLInputElement>(null);

  const [period, setPeriod] = useState<Period>("month");
  const [customStart, setCustomStart] = useState<string | null>(null);
  const [customEnd, setCustomEnd] = useState<string | null>(null);

  const {
    data,
    loading,
    error,
  } = useBookingsChartStatistics({
    period,
    start: customStart,
    end: customEnd,
  });

  useEffect(() => {
    if (!datePickerRef.current) {
      return;
    }

    const today = new Date();

    const fp = flatpickr(datePickerRef.current, {
      mode: "range",
      static: true,
      monthSelectorType: "static",
      dateFormat: "d M Y",
      defaultDate: [today],
      clickOpens: true,

      prevArrow:
          '<svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M12.5 15L7.5 10L12.5 5" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

      nextArrow:
          '<svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7.5 15L12.5 10L7.5 5" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

      onChange: (selectedDates) => {
        if (selectedDates.length !== 2) {
          return;
        }

        const [start, end] = selectedDates;

        const formatDate = (date: Date) =>
            date.toISOString().split("T")[0];

        setCustomStart(formatDate(start));
        setCustomEnd(formatDate(end));
        setPeriod("custom");
      },
    });

    return () => {
      fp.destroy();
    };
  }, []);


  console.log('===================================');
  console.log(data);
  const categories =
      period === "month"
          ? Array.from(
              {
                length:
                    data?.find(item => item.name === "fact")?.data.length ?? 0,
              },
              (_, index) => String(index + 1)
          )
          : period === "quarter"
              ? ["Q1", "Q2", "Q3", "Q4"]
              : period === "year"
                  ? [
                    t("jan"),
                    t("feb"),
                    t("mar"),
                    t("apr"),
                    t("may"),
                    t("jun"),
                    t("jul"),
                    t("aug"),
                    t("sep"),
                    t("oct"),
                    t("nov"),
                    t("dec"),
                  ]
                  : Array.from(
                      {
                        length:
                            data?.find(item => item.name === "fact")?.data.length ?? 0,
                      },
                      (_, index) => String(index + 1)
                  );

  const series = (data ?? []).map(item => ({
    ...item,
    name: t(item.name),
  }));

  const options: ApexOptions = {
    legend: {
      show: false,
    },

    colors: ["#465FFF", "#9CB9FF"],

    chart: {
      fontFamily: "Outfit, sans-serif",
      height: 310,
      type: "line",
      toolbar: {
        show: false,
      },
    },

    stroke: {
      curve: "straight",
      width: [2, 2],
    },

    fill: {
      type: "gradient",
      gradient: {
        opacityFrom: 0.55,
        opacityTo: 0,
      },
    },

    markers: {
      size: 0,
      strokeColors: "#fff",
      strokeWidth: 2,
      hover: {
        size: 6,
      },
    },

    grid: {
      xaxis: {
        lines: {
          show: false,
        },
      },
      yaxis: {
        lines: {
          show: true,
        },
      },
    },

    dataLabels: {
      enabled: false,
    },

    tooltip: {
      enabled: true,
    },

    xaxis: {
      type: "category",
      categories,

      axisBorder: {
        show: false,
      },

      axisTicks: {
        show: false,
      },

      tooltip: {
        enabled: false,
      },
    },

    yaxis: {
      labels: {
        style: {
          fontSize: "12px",
          colors: ["#6B7280"],
        },
      },

      title: {
        text: "",
        style: {
          fontSize: "0px",
        },
      },
    },
  };

  return (
      <div className="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">

        <div className="flex flex-col gap-5 mb-5 sm:flex-row sm:justify-between">

          <div>
            <h3 className="text-lg font-semibold text-gray-800 dark:text-white/90">
              {t("statistics")}
            </h3>
          </div>

          <div className="flex items-center gap-2">

            <button
                onClick={() => setPeriod("month")}
                className={`px-3 py-2 text-sm rounded-lg ${
                    period === "month"
                        ? "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white"
                        : "text-gray-500"
                }`}
            >
              {t("month") || "Месяц"}
            </button>

            <button
                onClick={() => setPeriod("quarter")}
                className={`px-3 py-2 text-sm rounded-lg ${
                    period === "quarter"
                        ? "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white"
                        : "text-gray-500"
                }`}
            >
              {t("quarter") || "Квартал"}
            </button>

            <button
                onClick={() => setPeriod("year")}
                className={`px-3 py-2 text-sm rounded-lg ${
                    period === "year"
                        ? "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white"
                        : "text-gray-500"
                }`}
            >
              {t("year") || "Год"}
            </button>

            <input
                ref={datePickerRef}
                className="h-10 w-10 lg:w-40 lg:h-auto lg:pl-10 lg:pr-3 lg:py-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-transparent lg:text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-800 dark:lg:text-gray-300 cursor-pointer"
                placeholder={t("select_date_range")}
            />

          </div>
        </div>

        {loading && (
            <div className="flex items-center justify-center h-[310px]">
                    <span className="text-gray-500">
                        {t("loading") || "Загрузка..."}
                    </span>
            </div>
        )}

        {error && (
            <div className="flex items-center justify-center h-[310px] text-red-500">
              {error}
            </div>
        )}

        {!loading && !error && (
            <div className="max-w-full overflow-x-auto custom-scrollbar">
              <div className="min-w-[1000px] xl:min-w-full">
                <Chart
                    options={options}
                    series={series}
                    type="area"
                    height={310}
                />
              </div>
            </div>
        )}
      </div>
  );
}
import {useEffect, useState} from "react";
import {Dropdown} from "../ui/dropdown/Dropdown";
import {DropdownItem} from "../ui/dropdown/DropdownItem";
import {MoreDotIcon} from "../../icons";
import CountryMap from "./CountryMap";
import {useLanguage} from "../../i18n/LanguageProvider";
import {useBookingsRegionStatistics} from "../../hooks/useBookings";

interface RegionStatistics {
    code: string;
    name: string;
    orders: number;
}

export default function DemographicCard() {
    const {t} = useLanguage();
    const [isOpen, setIsOpen] = useState(false);
    const [regionData, setRegionData] = useState<RegionStatistics[]>([]);

    const {
        data,
        loading,
        error,
    } = useBookingsRegionStatistics();

    useEffect(() => {
        if (data && typeof data === "object") {
            const regions = Object.entries(data).map(([code, region]) => ({
                code,
                name: region.name,
                orders: region.orders,
            }));

            setRegionData(regions);
        }
    }, [data]);

    const totalOrders = regionData.reduce(
        (sum, region) => sum + region.orders,
        0
    );

    function toggleDropdown() {
        setIsOpen(!isOpen);
    }

  function closeDropdown() {
    setIsOpen(false);
  }
  return (
      <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div className="flex justify-between">
          <div>
            <h3 className="text-lg font-semibold text-gray-800 dark:text-white/90">
              {t('customers_demographic')}
            </h3>
            <p className="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
              {t('number_of_customer_based_on_country')}
            </p>
          </div>
          <div className="relative inline-block">
            <button className="dropdown-toggle" onClick={toggleDropdown}>
              <MoreDotIcon className="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 size-6" />
            </button>
            <Dropdown
                isOpen={isOpen}
                onClose={closeDropdown}
                className="w-40 p-2"
            >
              <DropdownItem
                  onItemClick={closeDropdown}
                  className="flex w-full font-normal text-left text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
              >
                {t('view_more')}
              </DropdownItem>
              <DropdownItem
                  onItemClick={closeDropdown}
                  className="flex w-full font-normal text-left text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
              >
                {t('delete')}
              </DropdownItem>
            </Dropdown>
          </div>
        </div>
        <div className="px-4 py-6 my-6 overflow-hidden border border-gary-200 rounded-2xl dark:border-gray-800 sm:px-6">
          <div
              id="mapOne"
              className="mapOne map-btn -mx-4 -my-6 h-[212px] w-[252px] 2xsm:w-[307px] xsm:w-[358px] sm:-mx-6 md:w-[668px] lg:w-[634px] xl:w-[393px] 2xl:w-[554px]"
          >
              <CountryMap
                  regionData={regionData}
              />
          </div>
        </div>

        <div className="space-y-5">
            {regionData
                .filter((region) => region.orders > 0)
                .map((region) => {
                    const percent =
                        totalOrders > 0
                            ? Math.round(
                                (region.orders / totalOrders) * 100
                            )
                            : 0;

                    return (
                        <div
                            key={region.code}
                            className="flex items-center justify-between"
                        >
                            <div className="flex items-center gap-3">
                                <div className="items-center w-full rounded-full max-w-8">
                                    <div
                                        className="flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-500 dark:bg-brand-500/10"
                                    >
                                        {region.code.replace("AM", "")}
                                    </div>
                                </div>

                                <div>
                                    <p className="font-semibold text-gray-800 text-theme-sm dark:text-white/90">
                                        {region.name}
                                    </p>

                                    <span className="block text-gray-500 text-theme-xs dark:text-gray-400">
                            {region.orders} {t("customers")}
                        </span>
                                </div>
                            </div>

                            <div className="flex w-full max-w-[140px] items-center gap-3">
                                <div className="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800">
                                    <div
                                        className="absolute left-0 top-0 flex h-full items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
                                        style={{
                                            width: `${percent}%`,
                                        }}
                                    />
                                </div>

                                <p className="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                    {percent}%
                                </p>
                            </div>
                        </div>
                    );
                })}


        </div>
      </div>
  );
}
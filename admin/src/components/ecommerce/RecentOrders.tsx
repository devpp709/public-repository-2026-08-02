import {
  Table,
  TableBody,
  TableCell,
  TableHeader,
  TableRow,
} from "../ui/table";
import Badge from "../ui/badge/Badge";
import { useLanguage } from "../../i18n/LanguageProvider";
import { useLatestBookings } from "../../hooks/useBookings";

export default function RecentOrders() {
  const { t } = useLanguage();

  const {
    data: bookings,
    loading,
    error,
  } = useLatestBookings(5);

  const getStatusColor = (
      status: string
  ): "success" | "warning" | "error" | "info" => {
    switch (status) {
      case "confirmed":
        return "success";

      case "pending":
        return "warning";

      case "cancelled":
        return "error";

      case "in_progress":
        return "info";

      default:
        return "info";
    }
  };

  const getStatusLabel = (status: string): string => {
    const translations: Record<string, string> = {
      confirmed: t("confirmed"),
      pending: t("pending"),
      cancelled: t("canceled"),
      in_progress: t("in_progress"),
      completed: t("completed"),
    };

    return translations[status] ?? status;
  };

  const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString();
  };

  return (
      <div>
        <div className="flex items-center justify-between mb-5">
          <h3 className="text-lg font-semibold text-gray-800 dark:text-white/90">
            {t("recent_orders")}
          </h3>

          <div className="flex items-center gap-3">
            <button
                className="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            >
              <svg
                  className="stroke-current fill-white dark:fill-gray-800"
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
              >
                <path
                    d="M2.29004 5.90393H17.7067"
                    stroke=""
                    strokeWidth="1.5"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                />
                <path
                    d="M17.7075 14.0961H2.29085"
                    stroke=""
                    strokeWidth="1.5"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                />
                <path
                    d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                    fill=""
                    stroke=""
                    strokeWidth="1.5"
                />
                <path
                    d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                    fill=""
                    stroke=""
                    strokeWidth="1.5"
                />
              </svg>

              {t("filter")}
            </button>

            <button
                className="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            >
              {t("see_all")}
            </button>
          </div>
        </div>

        <div className="max-w-full overflow-x-auto">
          <Table>
            <TableHeader className="border-gray-100 dark:border-gray-800 border-y">
              <TableRow>
                <TableCell
                    isHeader
                    className="py-3 font-medium text-gray-500 text-start text-theme-xs dark:text-gray-400"
                >
                  {t("car")}
                </TableCell>

                <TableCell
                    isHeader
                    className="py-3 font-medium text-gray-500 text-start text-theme-xs dark:text-gray-400"
                >
                  {t("order_date")}
                </TableCell>

                <TableCell
                    isHeader
                    className="py-3 font-medium text-gray-500 text-start text-theme-xs dark:text-gray-400"
                >
                  {t("days")}
                </TableCell>

                <TableCell
                    isHeader
                    className="py-3 font-medium text-gray-500 text-start text-theme-xs dark:text-gray-400"
                >
                  {t("price")}
                </TableCell>

                <TableCell
                    isHeader
                    className="py-3 font-medium text-gray-500 text-start text-theme-xs dark:text-gray-400"
                >
                  {t("status")}
                </TableCell>
              </TableRow>
            </TableHeader>

            <TableBody className="divide-y divide-gray-100 dark:divide-gray-800">
              {loading && (
                  <TableRow>
                    <TableCell
                        colSpan={5}
                        className="py-8 text-center text-gray-500"
                    >
                      {t("loading")}...
                    </TableCell>
                  </TableRow>
              )}

              {!loading && error && (
                  <TableRow>
                    <TableCell
                        colSpan={5}
                        className="py-8 text-center text-red-500"
                    >
                      {error}
                    </TableCell>
                  </TableRow>
              )}

              {!loading && !error && bookings.length === 0 && (
                  <TableRow>
                    <TableCell
                        colSpan={5}
                        className="py-8 text-center text-gray-500"
                    >
                      {t("no_orders")}
                    </TableCell>
                  </TableRow>
              )}

              {!loading &&
                  !error &&
                  bookings.map((booking) => (
                      <TableRow key={booking.id}>
                        <TableCell className="py-3">
                          <div className="flex items-center gap-3">
                            <div className="h-[50px] w-[70px] overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800">
                              {booking.car.image ? (
                                  <img
                                      src={booking.car.image}
                                      className="h-[50px] w-[70px] object-cover"
                                      alt={booking.car.name}
                                  />
                              ) : (
                                  <div className="flex h-full items-center justify-center text-xs text-gray-400">
                                    —
                                  </div>
                              )}
                            </div>

                            <div>
                              <p className="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                {booking.car.name}
                              </p>

                              <span className="text-gray-500 text-theme-xs dark:text-gray-400">
                                                    {booking.car.licensePlate}
                                                </span>
                            </div>
                          </div>
                        </TableCell>

                        <TableCell className="py-3 text-gray-500 text-theme-sm dark:text-gray-400">
                          {formatDate(booking.orderDate)}
                        </TableCell>

                        <TableCell className="py-3 text-gray-500 text-theme-sm dark:text-gray-400">
                          {booking.days}
                        </TableCell>

                        <TableCell className="py-3 text-gray-500 text-theme-sm dark:text-gray-400">
                          {booking.totalPrice}
                        </TableCell>

                        <TableCell className="py-3">
                          <Badge
                              size="sm"
                              color={getStatusColor(
                                  booking.status
                              )}
                          >
                            {getStatusLabel(
                                booking.status
                            )}
                          </Badge>
                        </TableCell>
                      </TableRow>
                  ))}
            </TableBody>
          </Table>
        </div>
      </div>
  );
}
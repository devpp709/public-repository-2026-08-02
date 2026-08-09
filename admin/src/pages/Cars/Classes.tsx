import PageMeta from "../../components/common/PageMeta";
import { useCarClasses } from "../../hooks/useCarClasses";

export default function Classes() {
    const { classes, loading, error } = useCarClasses();

    return (
        <>
            <PageMeta
                title="Классы автомобилей"
                description="Классы автомобилей"
            />

            <div>
                <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Классы автомобилей
                </h1>

                {loading && (
                    <div className="mt-6 text-gray-500">
                        Загрузка...
                    </div>
                )}

                {error && (
                    <div className="mt-6 text-red-500">
                        {error}
                    </div>
                )}

                {!loading && !error && (
                    <div className="mt-6 space-y-3">
                        {classes.map((item) => (
                            <div
                                key={item.id}
                                className="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                            >
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h2 className="font-medium text-gray-800 dark:text-white">
                                            {item.name}
                                        </h2>

                                        {item.description && (
                                            <p className="mt-1 text-sm text-gray-500">
                                                {item.description}
                                            </p>
                                        )}
                                    </div>

                                    <div className="text-right text-sm text-gray-500">
                                        <div>
                                            Автомобилей: {item.carsCount}
                                        </div>

                                        {item.dailyRate !== null && (
                                            <div>
                                                {item.dailyRate} / день
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
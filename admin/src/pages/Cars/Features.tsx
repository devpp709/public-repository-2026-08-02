import { useFeatures } from '../../hooks/useFeatures';

export default function Features() {
    const {
        features,
        loading,
        error,
    } = useFeatures();

    if (loading) {
        return <div>Загрузка...</div>;
    }

    if (error) {
        return <div className="text-red-500">{error}</div>;
    }

    return (
        <div>
            <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                Комплектации автомобилей
            </h1>

            <div className="mt-6">
                {features.map((feature) => (
                    <div key={feature.id}>
                        {feature.name}
                    </div>
                ))}
            </div>
        </div>
    );
}
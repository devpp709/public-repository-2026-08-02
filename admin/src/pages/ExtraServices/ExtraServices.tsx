import { useExtraServices } from '../../hooks/useExtraServices';

export default function ExtraServices() {
    const {
        extraServices,
        loading,
        error,
    } = useExtraServices();

    if (loading) {
        return <div>Загрузка...</div>;
    }

    if (error) {
        return <div className="text-red-500">{error}</div>;
    }

    return (
        <div>
            <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                Дополнительные услуги
            </h1>

            <div className="mt-6">
                {extraServices.map((service) => (
                    <div key={service.id}>
                        {service.name}
                    </div>
                ))}
            </div>
        </div>
    );
}
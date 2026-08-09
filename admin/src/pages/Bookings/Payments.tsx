import PageMeta from "../../components/common/PageMeta";

export default function Payments() {
    return (
        <>
            <PageMeta title="Платежи" description="Платежи по бронированиям" />
            <div>
                <h1 className="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Платежи
                </h1>
            </div>
        </>
    );
}
import { HelmetProvider, Helmet } from "react-helmet-async";
import { useLanguage } from "../../i18n/LanguageProvider";

const PageMeta = ({
                      title,
                      description,
                  }: {
    title: string;
    description: string;
}) => {
    const { t } = useLanguage();

    return (
        <Helmet>
            <title>{t(title)}</title>
            <meta name="description" content={t(description)} />
        </Helmet>
    );
};

export const AppWrapper = ({ children }: { children: React.ReactNode }) => (
    <HelmetProvider>{children}</HelmetProvider>
);

export default PageMeta;
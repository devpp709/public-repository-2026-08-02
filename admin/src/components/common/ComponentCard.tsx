import { useLanguage } from "../../i18n/LanguageProvider";
import {JSX} from "react";

interface ComponentCardProps {
  title: string;
  children: React.ReactNode;
  className?: string;
  desc?: string;
}

const ComponentCard: ({title, children, className, desc}: {
  title: any;
  children: any;
  className?: any;
  desc?: any
}) => JSX.Element = ({
                                                       title,
                                                       children,
                                                       className = "",
                                                       desc = "",
                                                     }) => {
  const { t } = useLanguage();

  return (
      <div
          className={`rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] ${className}`}
      >
        <div className="px-6 py-5">
          <h3 className="text-base font-medium text-gray-800 dark:text-white/90">
            {t(title)}
          </h3>
          {desc && (
              <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {t(desc)}
              </p>
          )}
        </div>

        <div className="p-4 border-t border-gray-100 dark:border-gray-800 sm:p-6">
          <div className="space-y-6">{children}</div>
        </div>
      </div>
  );
};

export default ComponentCard;
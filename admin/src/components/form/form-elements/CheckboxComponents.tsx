import { useState } from "react";
import ComponentCard from "../../common/ComponentCard";
import Checkbox from "../input/Checkbox";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function CheckboxComponents() {
  const { t } = useLanguage();
  const [isChecked, setIsChecked] = useState(false);
  const [isCheckedTwo, setIsCheckedTwo] = useState(true);
  const [isCheckedDisabled, setIsCheckedDisabled] = useState(false);

  return (
      <ComponentCard title="checkbox">
        <div className="flex items-center gap-4">
          <div className="flex items-center gap-3">
            <Checkbox checked={isChecked} onChange={setIsChecked} />
            <span className="block text-sm font-medium text-gray-700 dark:text-gray-400">
            {t('default')}
          </span>
          </div>
          <div className="flex items-center gap-3">
            <Checkbox
                checked={isCheckedTwo}
                onChange={setIsCheckedTwo}
                label={t('checked')}
            />
          </div>
          <div className="flex items-center gap-3">
            <Checkbox
                checked={isCheckedDisabled}
                onChange={setIsCheckedDisabled}
                disabled
                label={t('disabled')}
            />
          </div>
        </div>
      </ComponentCard>
  );
}
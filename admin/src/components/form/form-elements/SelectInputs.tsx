import { useState } from "react";
import ComponentCard from "../../common/ComponentCard";
import Label from "../Label";
import Select from "../Select";
import MultiSelect from "../MultiSelect";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function SelectInputs() {
  const { t } = useLanguage();
  const options = [
    { value: "marketing", label: "Marketing" },
    { value: "template", label: "Template" },
    { value: "development", label: "Development" },
  ];
  const handleSelectChange = (value: string) => {
    console.log("Selected value:", value);
  };
  const [selectedValues, setSelectedValues] = useState<string[]>([]);

  const multiOptions = [
    { value: "1", text: "Option 1", selected: false },
    { value: "2", text: "Option 2", selected: false },
    { value: "3", text: "Option 3", selected: false },
    { value: "4", text: "Option 4", selected: false },
    { value: "5", text: "Option 5", selected: false },
  ];
  return (
      <ComponentCard title="select_inputs">
        <div className="space-y-6">
          <div>
            <Label>{t('select_input')}</Label>
            <Select
                options={options}
                placeholder={t('select_option')}
                onChange={handleSelectChange}
                className="dark:bg-dark-900"
            />
          </div>
          <div>
            <MultiSelect
                label={t('multiple_select_options')}
                options={multiOptions}
                defaultSelected={["1", "3"]}
                onChange={(values) => setSelectedValues(values)}
            />
            <p className="sr-only">
              {t('selected_values')}: {selectedValues.join(", ")}
            </p>
          </div>
        </div>
      </ComponentCard>
  );
}
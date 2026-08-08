import ComponentCard from "../../common/ComponentCard";
import Label from "../Label";
import Input from "../input/InputField";
import { EnvelopeIcon } from "../../../icons";
import PhoneInput from "../group-input/PhoneInput";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function InputGroup() {
  const { t } = useLanguage();
  const countries = [
    { code: "US", label: "+1" },
    { code: "GB", label: "+44" },
    { code: "CA", label: "+1" },
    { code: "AU", label: "+61" },
  ];
  const handlePhoneNumberChange = (phoneNumber: string) => {
    console.log("Updated phone number:", phoneNumber);
  };
  return (
      <ComponentCard title="input_group">
        <div className="space-y-6">
          <div>
            <Label>{t('email')}</Label>
            <div className="relative">
              <Input
                  placeholder="info@gmail.com"
                  type="text"
                  className="pl-[62px]"
              />
              <span className="absolute left-0 top-1/2 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
              <EnvelopeIcon className="size-6" />
            </span>
            </div>
          </div>
          <div>
            <Label>{t('phone')}</Label>
            <PhoneInput
                selectPosition="start"
                countries={countries}
                placeholder="+1 (555) 000-0000"
                onChange={handlePhoneNumberChange}
            />
          </div>
          <div>
            <Label>{t('phone')}</Label>
            <PhoneInput
                selectPosition="end"
                countries={countries}
                placeholder="+1 (555) 000-0000"
                onChange={handlePhoneNumberChange}
            />
          </div>
        </div>
      </ComponentCard>
  );
}
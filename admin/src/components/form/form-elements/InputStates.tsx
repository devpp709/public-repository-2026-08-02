import { useState } from "react";
import ComponentCard from "../../common/ComponentCard";
import Input from "../input/InputField";
import Label from "../Label";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function InputStates() {
  const { t } = useLanguage();
  const [email, setEmail] = useState("");
  const [emailTwo, setEmailTwo] = useState("");
  const [error, setError] = useState(false);

  const validateEmail = (value: string) => {
    const isValidEmail =
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    setError(!isValidEmail);
    return isValidEmail;
  };

  const handleEmailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setEmail(value);
    validateEmail(value);
  };
  const handleEmailTwoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setEmailTwo(value);
    validateEmail(value);
  };
  return (
      <ComponentCard
          title="input_states"
          desc="input_states_desc"
      >
        <div className="space-y-5 sm:space-y-6">
          {/* Error Input */}
          <div>
            <Label>{t('email')}</Label>
            <Input
                type="email"
                value={email}
                error={error}
                onChange={handleEmailChange}
                placeholder={t('enter_email')}
                hint={error ? t('invalid_email') : ""}
            />
          </div>

          {/* Success Input */}
          <div>
            <Label>{t('email')}</Label>
            <Input
                type="email"
                value={emailTwo}
                success={!error}
                onChange={handleEmailTwoChange}
                placeholder={t('enter_email')}
                hint={!error ? t('success_message') : ""}
            />
          </div>

          {/* Disabled Input */}
          <div>
            <Label>{t('email')}</Label>
            <Input
                type="text"
                value="disabled@example.com"
                disabled={true}
                placeholder={t('disabled_email')}
            />
          </div>
        </div>
      </ComponentCard>
  );
}
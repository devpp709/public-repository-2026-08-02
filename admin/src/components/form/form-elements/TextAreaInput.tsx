import { useState } from "react";
import ComponentCard from "../../common/ComponentCard";
import TextArea from "../input/TextArea";
import Label from "../Label";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function TextAreaInput() {
  const { t } = useLanguage();
  const [message, setMessage] = useState("");
  const [messageTwo, setMessageTwo] = useState("");
  return (
      <ComponentCard title="textarea_input_field">
        <div className="space-y-6">
          {/* Default TextArea */}
          <div>
            <Label>{t('description')}</Label>
            <TextArea
                value={message}
                onChange={(value) => setMessage(value)}
                rows={6}
            />
          </div>

          {/* Disabled TextArea */}
          <div>
            <Label>{t('description')}</Label>
            <TextArea rows={6} disabled />
          </div>

          {/* Error TextArea */}
          <div>
            <Label>{t('description')}</Label>
            <TextArea
                rows={6}
                value={messageTwo}
                error
                onChange={(value) => setMessageTwo(value)}
                hint={t('enter_valid_message')}
            />
          </div>
        </div>
      </ComponentCard>
  );
}
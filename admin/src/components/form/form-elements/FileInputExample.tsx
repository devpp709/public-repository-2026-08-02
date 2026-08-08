import ComponentCard from "../../common/ComponentCard";
import FileInput from "../input/FileInput";
import Label from "../Label";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function FileInputExample() {
  const { t } = useLanguage();

  const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      console.log("Selected file:", file.name);
    }
  };

  return (
      <ComponentCard title="file_input">
        <div>
          <Label>{t('upload_file')}</Label>
          <FileInput onChange={handleFileChange} className="custom-class" />
        </div>
      </ComponentCard>
  );
}
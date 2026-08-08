import ComponentCard from "../../common/ComponentCard";
import Switch from "../switch/Switch";
import { useLanguage } from "../../../i18n/LanguageProvider";

export default function ToggleSwitch() {
    const { t } = useLanguage();

    const handleSwitchChange = (checked: boolean) => {
        console.log("Switch is now:", checked ? "ON" : "OFF");
    };
    return (
        <ComponentCard title="toggle_switch_input">
            <div className="flex gap-4">
                <Switch
                    label={t('default')}
                    defaultChecked={true}
                    onChange={handleSwitchChange}
                />
                <Switch
                    label={t('checked')}
                    defaultChecked={true}
                    onChange={handleSwitchChange}
                />
                <Switch label={t('disabled')} disabled={true} />
            </div>
            <div className="flex gap-4">
                <Switch
                    label={t('default')}
                    defaultChecked={true}
                    onChange={handleSwitchChange}
                    color="gray"
                />
                <Switch
                    label={t('checked')}
                    defaultChecked={true}
                    onChange={handleSwitchChange}
                    color="gray"
                />
                <Switch label={t('disabled')} disabled={true} color="gray" />
            </div>
        </ComponentCard>
    );
}
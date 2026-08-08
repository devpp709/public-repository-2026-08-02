import { VectorMap } from "@react-jvectormap/core";
import { worldMill } from "@react-jvectormap/world";
import { useLanguage } from "../../i18n/LanguageProvider";

interface CountryMapProps {
  mapColor?: string;
}

const CountryMap: React.FC<CountryMapProps> = ({ mapColor }) => {
  const { t } = useLanguage();

  return (
      <VectorMap
          map={worldMill}
          backgroundColor="transparent"
          markerStyle={{
            initial: {
              fill: "#465FFF",
              r: 4,
            } as any,
          }}
          markersSelectable={true}
          markers={[
            {
              latLng: [37.2580397, -104.657039],
              name: t('usa'),
              style: {
                fill: "#465FFF",
                borderWidth: 1,
                borderColor: "white",
                stroke: "#383f47",
              },
            },
            {
              latLng: [20.7504374, 73.7276105],
              name: t('india'),
              style: { fill: "#465FFF", borderWidth: 1, borderColor: "white" },
            },
            {
              latLng: [53.613, -11.6368],
              name: t('united_kingdom'),
              style: { fill: "#465FFF", borderWidth: 1, borderColor: "white" },
            },
            {
              latLng: [-25.0304388, 115.2092761],
              name: t('sweden'),
              style: {
                fill: "#465FFF",
                borderWidth: 1,
                borderColor: "white",
                strokeOpacity: 0,
              },
            },
          ]}
          zoomOnScroll={false}
          zoomMax={12}
          zoomMin={1}
          zoomAnimate={true}
          zoomStep={1.5}
          regionStyle={{
            initial: {
              fill: mapColor || "#D0D5DD",
              fillOpacity: 1,
              fontFamily: "Outfit",
              stroke: "none",
              strokeWidth: 0,
              strokeOpacity: 0,
            },
            hover: {
              fillOpacity: 0.7,
              cursor: "pointer",
              fill: "#465fff",
              stroke: "none",
            },
            selected: {
              fill: "#465FFF",
            },
            selectedHover: {},
          }}
          regionLabelStyle={{
            initial: {
              fill: "#35373e",
              fontWeight: 500,
              fontSize: "13px",
              stroke: "none",
            },
            hover: {},
            selected: {},
            selectedHover: {},
          }}
      />
  );
};

export default CountryMap;
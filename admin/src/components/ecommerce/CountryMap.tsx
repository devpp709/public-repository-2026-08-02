import { useRef, useState } from "react";
import armeniaMill from "../../maps/armeniaMill.json";

interface RegionStatistics {
    code: string;
    name: string;
    orders: number;
}

interface CountryMapProps {
    mapColor?: string;
    regionData: RegionStatistics[];
}

const CountryMap: React.FC<CountryMapProps> = ({
                                                   mapColor = "#D0D5DD",
                                                   regionData,
                                               }) => {
    const [scale, setScale] = useState(1);
    const [position, setPosition] = useState({
        x: 0,
        y: 0,
    });

    const [hoveredRegion, setHoveredRegion] = useState<string | null>(null);

    const dragging = useRef(false);

    const dragStart = useRef({
        x: 0,
        y: 0,
    });

    const positionStart = useRef({
        x: 0,
        y: 0,
    });

    const regionByCode = Object.fromEntries(
        regionData.map((region) => [
            region.code,
            region,
        ])
    );

    const handleMouseDown = (
        event: React.MouseEvent
    ) => {
        dragging.current = true;

        dragStart.current = {
            x: event.clientX,
            y: event.clientY,
        };

        positionStart.current = {
            ...position,
        };
    };

    const handleMouseMove = (
        event: React.MouseEvent
    ) => {
        if (!dragging.current) {
            return;
        }

        const dx =
            event.clientX -
            dragStart.current.x;

        const dy =
            event.clientY -
            dragStart.current.y;

        setPosition({
            x:
                positionStart.current.x +
                dx,
            y:
                positionStart.current.y +
                dy,
        });
    };

    const handleMouseUp = () => {
        dragging.current = false;
    };

    const zoomIn = () => {
        setScale((value) =>
            Math.min(value * 1.5, 6)
        );
    };

    const zoomOut = () => {
        setScale((value) =>
            Math.max(value / 1.5, 0.5)
        );
    };

    return (
        <div
            style={{
                position: "relative",
                width: "100%",
                height: "300px",
                overflow: "hidden",
            }}
        >
            <div
                style={{
                    position: "absolute",
                    top: 10,
                    left: 10,
                    zIndex: 10,
                    display: "flex",
                    flexDirection: "column",
                    gap: 4,
                }}
            >
                <button
                    type="button"
                    onClick={zoomIn}
                    style={{
                        width: 32,
                        height: 32,
                        cursor: "pointer",
                        position: "relative",
                    }}
                >
                    +
                </button>

                <button
                    type="button"
                    onClick={zoomOut}
                    style={{
                        width: 32,
                        height: 32,
                        cursor: "pointer",
                        position: "relative",
                    }}
                >
                    −
                </button>
            </div>

            {hoveredRegion && (
                <div
                    style={{
                        position: "absolute",
                        top: 10,
                        left: 50,
                        zIndex: 10,
                        background: "#fff",
                        border: "1px solid #ddd",
                        borderRadius: 6,
                        padding: "8px 12px",
                        pointerEvents: "none",
                        boxShadow:
                            "0 2px 8px rgba(0,0,0,0.15)",
                    }}
                >
                    <strong>
                        {regionByCode[
                                hoveredRegion
                                ]?.name ||
                            hoveredRegion}
                    </strong>

                    <div>
                        Заказов:{" "}
                        {regionByCode[
                            hoveredRegion
                            ]?.orders ?? 0}
                    </div>
                </div>
            )}

            <svg
                viewBox="0 0 500 500"
                width="100%"
                height="100%"
                style={{
                    cursor: dragging.current
                        ? "grabbing"
                        : "grab",
                    userSelect: "none",
                }}
                onMouseDown={handleMouseDown}
                onMouseMove={handleMouseMove}
                onMouseUp={handleMouseUp}
                onMouseLeave={handleMouseUp}
            >
                <g
                    transform={`
                        translate(${250 + position.x} ${250 + position.y})
                        scale(${scale})
                        translate(-250 -250)
                    `}
                >
                    {Object.entries(
                        armeniaMill.content.paths
                    ).map(([id, region]) => {
                        const isHovered =
                            hoveredRegion === id;

                        return (
                            <path
                                key={id}
                                d={region.path}
                                fill={
                                    isHovered
                                        ? "#465FFF"
                                        : mapColor
                                }
                                fillOpacity={
                                    isHovered
                                        ? 0.85
                                        : 1
                                }
                                stroke="#FFFFFF"
                                strokeWidth={1}
                                vectorEffect="non-scaling-stroke"
                                style={{
                                    cursor: "pointer",
                                    transition:
                                        "fill 0.15s ease",
                                }}
                                onMouseEnter={(
                                    event
                                ) => {
                                    event.stopPropagation();
                                    setHoveredRegion(id);
                                }}
                                onMouseLeave={() => {
                                    setHoveredRegion(
                                        null
                                    );
                                }}
                            />
                        );
                    })}
                </g>
            </svg>
        </div>
    );
};

export default CountryMap;
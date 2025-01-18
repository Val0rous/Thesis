import View from "@/frontend/utils/views.js";
import maxValues from "@/frontend/utils/maxValues.json";
import Colors from "@/frontend/utils/colors.js";

const shades = ["50", "100", "200", "300", "400", "500", "600", "700", "800", "900"];
const lineShades = ["A100", "A200", "A400", "A700"];

/**
 * Generates map options with the appropriate color and shade.
 *
 * @param {View} view - The type of view (e.g., Crowding, Attendance).
 * @param {number} value - The numeric value to determine the shade.
 * @param {boolean} isPolyline - Polygon if false (default), Polyline if true
 * @returns {{ color: string, fillColor: string, fillOpacity: number }} - The map options.
 */
const mapOptionsFactory = (view, value, isPolyline = false) => {
  const shade = Colors[shadeFactory(view, value, isPolyline)];

  if (isPolyline) {
    return {
      color: shade,
      fillColor: shade,
      fillOpacity: 1,
      weight: weightFactory(view, value),
    }
  }

  return {
    color: shade,
    fillColor: shade,
    fillOpacity: 0.5,
  }
}

export default mapOptionsFactory;

const colorMapping = {
  [View.Crowding]: "Orange",
  [View.Attendance]: "Red",
  [View.Movements]: "DeepPurple",
  [View.Medians]: "Purple",
}

const colorFactory = (view) => {
  if (!colorMapping[view]) {
    throw new Error(`Unhandled view type: ${view}`);
  }
  return colorMapping[view];
}

const shadeFactory = (view, value, isPolyline = false) => {
  if (view === View.Areas) {
    return "Blue600";
  }
  const maxValue = maxValues[view];
  if (value === 0) {
    return "Gray500";
  }
  if (value >= maxValue) {
    return colorFactory(view) + "900";
  }

  const normalizedValue = value / maxValue; // Scale to [0, 1]
  const pseudoLogValue = Math.sqrt(normalizedValue);  // Adjust for smaller differences at low values

  // // Linear
  // const interval = maxValue / (shades.length);
  // const index = Math.floor(value / interval);

  if (isPolyline) {
    const index = Math.floor(pseudoLogValue * (lineShades.length));
    return colorFactory(view) + lineShades[index];
  }

  const index = Math.floor(pseudoLogValue * (shades.length)); // Map to shade index
  return colorFactory(view) + shades[index];
}

const weightFactory = (view, value) => {
  const maxValue = maxValues[view];
  const normalizedValue = value / maxValue;
  const pseudoLogValue = Math.sqrt(normalizedValue);
  return (Math.floor(pseudoLogValue * (lineShades.length)) + 1) * 1.5;
}
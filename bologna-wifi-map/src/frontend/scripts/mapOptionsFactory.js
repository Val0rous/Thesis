import View from "@/frontend/utils/views.js";
import maxValues from "@/frontend/utils/maxValues.json";
import Colors from "@/frontend/utils/colors.js";

const shades = ["50", "100", "200", "300", "400", "500", "600", "700", "800", "900"];

/**
 * Generates map options with the appropriate color and shade.
 *
 * @param {View} view - The type of view (e.g., Crowding, Attendance).
 * @param {number} value - The numeric value to determine the shade.
 * @returns {{ color: string, fillColor: string, fillOpacity: number }} - The map options.
 */
const mapOptionsFactory = (view, value) => {
  const shade = Colors[shadeFactory(view, value)];
  return {
    color: shade,
    fillColor: shade,
    fillOpacity: 0.5,
  }
}

export default mapOptionsFactory;

const colorMapping = {
  [View.Crowding]: "DeepOrange",
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

const shadeFactory = (view, value) => {
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
  const index = Math.floor(pseudoLogValue * (shades.length - 1)); // Map to shade index

  // // Linear
  // const interval = maxValue / (shades.length);
  // const index = Math.floor(value / interval);

  return colorFactory(view) + shades[index];
}
import View from "@/frontend/utils/views.js";
import maxValues from "@/frontend/utils/maxValues.json";
import Colors from "@/frontend/utils/colors.js";

const shades = ["50", "100", "200", "300", "400", "500", "600", "700", "800", "900"];

/**
 *
 * @param {"Crowding" | "Attendance" | "Movements" | "Medians"} view
 * @param {number} value
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

const colorFactory = (view) => {
  switch (view) {
    case View.Crowding:
      return "Amber";
    case View.Attendance:
      return "Red";
    case View.Movements:
      return "DeepPurple";
    case View.Medians:
      return "Purple";
    default:
      throw new Error(`Unhandled view type: ${view}`);
  }
}

const shadeFactory = (view, value) => {
  const maxValue = maxValues[view];
  if (value === 0) {
    return "Gray500";
  }
  if (value >= maxValue) {
    return colorFactory(view) + "900";
  }
  const interval = maxValue / (shades.length - 1);
  const index = Math.floor(value / interval);

  return colorFactory(view) + shades[index];
}
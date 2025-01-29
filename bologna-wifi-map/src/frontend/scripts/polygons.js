import L from "leaflet";
import mapOptionsFactory from "@/frontend/scripts/mapOptionsFactory.js";
import View from "@/frontend/utils/views.js";
import maxValues from "@/frontend/utils/maxValues.json";
import {fetchAreas, fetchCrowdingAttendance, fetchMovements} from "@/frontend/scripts/api.js";

/**
 * Setup polygons for the first time.
 * @param props
 * @param data
 * @param map
 */
export const setupPolygons = (props, data, map) => {
  if (data.areas.value.length > 0) {
    data.areas.value.forEach((area) => {
      const polygon = L.polygon(area.coordinates, mapOptionsFactory(props.view, NaN)).addTo(map.value);
      data.polygons.value[area.zone_id] = polygon;

      //  TODO: console.log(getViewData(props.view))

      let popupContent = `<b>${area.zone_name}</b></br>`

      // if (props.view === View.Crowding || props.view === View.Attendance) {
      //   popupContent += `Value: ${getViewData(props.view)[date.value][area.zone_id][hour.value]} / ${maxValues[props.view]}`
      // }

      polygon.bindPopup(popupContent)
      // Add mouseover event to change style on hover
      // polygon.on("mouseover", () => {
      //   if (clickedPolygon.value !== polygon) {
      //     polygon.setStyle(hoverOptions);
      //   }
      // });

      // Add mouseout event to reset style when hover ends
      // polygon.on("mouseout", () => {
      //   if (clickedPolygon.value !== polygon) {
      //     polygon.setStyle(mapOptionsFactory(props.view, 2000));
      //   }
      // });

      // Add click event to zoom in on the polygon
      polygon.on("click", () => {
        // if (clickedPolygon.value) {
        //   clickedPolygon.value.setStyle(defaultOptions);
        // }
        data.clickedPolygon.value = polygon;
        // polygon.setStyle(clickOptions);
        // Get the bounds of the polygon and zoom to it
        const bounds = polygon.getBounds();
        // Calculate area of the bounds (approximation based on lat/lng difference)
        const area = Math.abs(
          (bounds.getNorthEast().lat - bounds.getSouthWest().lat) *
          (bounds.getNorthEast().lng - bounds.getSouthWest().lng)
        );
        console.log(area * 1000000);
        // console.log(getViewData(props.view)[date.value][area.zone_id][hour.value])

        // Determine the zoom level based on the area
        let targetZoom;
        if (area < 0.000001) {
          targetZoom = 19; // Very small polygons -> High zoom level
          console.log("High zoom")
        } else if (area < 0.00001) {
          targetZoom = 17; // Medium polygons -> Moderate zoom level
          console.log("Moderate zoom")
        } else {
          targetZoom = 15; // Large polygons -> Lower zoom level
          console.log("Low zoom")
        }

        map.value.fitBounds(bounds, {
          padding: [20, 20], // Add padding for a better view
          maxZoom: targetZoom, // Maximum zoom level
        });
      });
    })
  }
};

const getNewData = async (props, data) => {
  // await fetchAreas(data.areas);
  await fetchCrowdingAttendance(props.date, data.crowding, data.attendance);
  await fetchMovements(props.date, data.movements, data.medians);
}

const getViewData = (view, data) => {
  switch (view) {
    case View.Crowding:
      return data.crowding;
    case View.Attendance:
      return data.attendance;
    case View.Movements:
      return data.movements;
    case View.Medians:
      return data.medians;
    default:
      return;
  }
}

export const updatePolygons = async (props, data, map, newView, newDate, newHour, oldDate) => {
  if (newDate !== oldDate) {
    await getNewData(props, data);
  }
  const viewData = getViewData(newView, data);
  if (newView === View.Crowding || newView === View.Attendance) {
    console.log(viewData);
    data.polylines.value.forEach((polyline) => {
      polyline.remove();
    })
    data.polylines.value = [];
    data.areas.value.forEach((area) => {
      const polygon = data.polygons.value[area.zone_id];
      let popupContent = `<b>${area.zone_name}</b></br>`;
      const dailyValues = viewData[props.date][area.zone_id];
      const value = (dailyValues !== undefined) ? dailyValues[props.hour] : NaN;
      popupContent += `Value: ${value} / ${maxValues[newView]}`;
      polygon.setStyle(mapOptionsFactory(newView, value));
      polygon.setPopupContent(popupContent);
      // console.log(area, viewData[date.value][area.zone_id][hour.value]);
    });
  } else if (newView === View.Movements || newView === View.Medians) {
    data.areas.value.forEach((area) => {
      const polygon = data.polygons.value[area.zone_id];
      let popupContent = `<b>${area.zone_name}</b></br>`;
      polygon.setPopupContent(popupContent);
    })
    data.polylines.value.forEach((polyline) => {
      polyline.remove();
    })
    data.polylines.value = [];
    const list = viewData[props.date];
    data.areas.value.forEach((area) => {
      if (list[area.zone_id] !== undefined) {
        let isNonZero = false;
        for (const zoneIdTo in list[area.zone_id]) {
          /** @type {Area} */
          const areaTo = data.areas.value.find((it) => it.zone_id === zoneIdTo);
          const value = list[area.zone_id][zoneIdTo][props.hour];
          if (value === 0) {
            continue;
          }
          isNonZero = true;
          const polyline = L.polyline([
            [area.latitude, area.longitude],
            [areaTo.latitude, areaTo.longitude]
          ], mapOptionsFactory(newView, value, true));
          polyline.addTo(map.value);
          const popupLabel = (newView === View.Medians) ? "Median Movements: " : "Movements: ";
          const fromLabel = "From: " + area.zone_name;
          const toLabel = "To: " + areaTo.zone_name;
          const popupContent = `<b>${fromLabel}</b><br/><b>${toLabel}</b><br/>${popupLabel} ${value.toString()}`;
          polyline.bindPopup(popupContent);
          data.polylines.value.push(polyline);
          // const polygon = polygons.value[zoneIdTo];
          // polygon.setStyle(mapOptionsFactory(newValue, 2000));
        }
        const polygonValue = isNonZero ? 2000 : 0;
        const polygon = data.polygons.value[area.zone_id];
        polygon.setStyle(mapOptionsFactory(newView, polygonValue));
      } else {
        const polygon = data.polygons.value[area.zone_id];
        let popupContent = `<b>${area.zone_name}</b></br>`;
        polygon.setStyle(mapOptionsFactory(newView, 0));
        polygon.setPopupContent(popupContent);
      }
    });

    // for (const zoneIdFrom in list) {
    //   const subList = list[zoneIdFrom];
    //   const polygon = polygons.value[zoneIdFrom];
    //   polygon.setStyle(mapOptionsFactory(newValue, 2000));
    //   for (const zoneIdTo in subList) {
    //     const value = subList[zoneIdTo][hour.value];
    //   }
    // }

  } else {
    data.polylines.value.forEach((polyline) => {
      polyline.remove();
    })
    data.polylines.value = [];
    data.areas.value.forEach((area) => {
      const polygon = data.polygons.value[area.zone_id];
      let popupContent = `<b>${area.zone_name}</b></br>`;
      polygon.setStyle(mapOptionsFactory(newView, NaN));
      polygon.setPopupContent(popupContent);
    });
  }
};
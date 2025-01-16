import {addObjectToKey} from "@/frontend/utils/utils.js";

const baseURL = `${window.location.protocol}//${window.location.hostname}`;

/**
 * Fetch all areas from server
 * @param {Ref<Area>} areas
 * @returns {Promise<void>}
 */
export const fetchAreas = async (areas) => {
  try {
    const url = `${baseURL}/backend/api/areas.php`;
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      }
    });
    const data = await response.json();
    if (data.success) {
      areas.value = data.results;
    } else {
      console.error("Failed to fetch areas: ", data.message);
    }
  } catch (error) {
    console.error("Failed to fetch areas: an error occurred.\n");
  }
}

/**
 * Fetch all crowding and attendance from server for a specific day
 * @param {string} date
 * @param {Ref<Crowding[]>} crowding
 * @param {Ref<Attendance[]>} attendance
 * @returns {Promise<void>}
 */
export const fetchCrowdingAttendance = async (date, crowding, attendance) => {
  try {
    const url = `${baseURL}/backend/api/crowding_attendance.php`;
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({date}),
    });
    const data = await response.json();
    if (data.success) {
      /** @type {CrowdingAttendance} */
      let results = data.results;
      addObjectToKey(crowding, date, results.crowding);
      addObjectToKey(attendance, date, results.attendance);
    } else {
      console.error("Failed to fetch crowding and attendance: ", data.message);
    }
  } catch (error) {
    console.error("Failed to fetch crowding and attendance: an error occurred.\n");
  }
}
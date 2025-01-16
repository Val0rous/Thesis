/**
 * @template T
 * @typedef {import('vue').Ref<T>} Ref
 */

/**
 * @typedef {Object} Area
 * @property {string} zone_id - ID of the area
 * @property {string} zone_name - Name of the area
 * @property {number} latitude - Latitude of the area
 * @property {number} longitude - Longitude of the area
 * @property {[number, number][]} coordinates - Coordinates of the area polygon
 */

/**
 * @typedef {Object} CrowdingAttendance
 * @property {Crowding} crowding - Crowding data, indexed by area
 * @property {Attendance} attendance - Attendance data, indexed by area
 */

/**
 * @typedef {Object.<string, HourlyData>} Crowding
 * - Keys are area IDs
 * - Values are arrays of hourly data, of size 24
 */

/**
 * @typedef {Object.<string, HourlyData>} Attendance
 * - Keys are area IDs
 * - Values are arrays of hourly data, of size 24
 */

/**
 * @typedef {number[]} HourlyData
 * - An array containing 24 integers, one for each hour
 */
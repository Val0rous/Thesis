export function addObjectToKey(targetObject, key, newObject) {
  // Ensure the key exists in the target object
  if (!targetObject[key]) {
    targetObject[key] = [];
  }
  // Merge the newObject into the existing object at the key
  Object.assign(targetObject[key], newObject);
}

export function getInitialDate() {
  const today = new Date();
  const threeDaysAgo = new Date();
  threeDaysAgo.setDate(today.getDate() - 3);
  return threeDaysAgo.toISOString().split('T')[0];
}

/**
 *
 * @param {String} dateString
 * @param {Number} offset
 */
export function calculateDate(dateString, offset) {
  const date = new Date(dateString);
  date.setDate(date.getDate() + offset);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
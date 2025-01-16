export function addObjectToKey(targetObject, key, newObject) {
  // Ensure the key exists in the target object
  if (!targetObject[key]) {
    targetObject[key] = [];
  }
  // Merge the newObject into the existing object at the key
  Object.assign(targetObject[key], newObject);
}
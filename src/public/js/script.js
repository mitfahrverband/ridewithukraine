let $ = (selector) => {
  let result = [window];
  if (selector !== undefined) {
    result = document.querySelectorAll(selector);
    if (result.length === 0) return null;
  }
  if (result.length === 1) {
    result[0].forEach = (cb) => cb(result[0]);
    return result[0];
  }
  return result;
}

let $ = (selector) => {
  let result = [window];
  if (selector !== undefined) {
    result = document.querySelectorAll(selector);
    if (result.length === 0) return null;
  }
  result.toggleClass = className => {
    result.forEach(r => r.classList.toggle(className));
  };
  result.addClass = className => {
    result.forEach(r => r.classList.add(className));
  };
  result.removeClass = className => {
    result.forEach(r => r.classList.remove(className));
  };
  result.onClick = cb => {
    result.forEach(r => r.onclick = cb);
  };
  return result;
}

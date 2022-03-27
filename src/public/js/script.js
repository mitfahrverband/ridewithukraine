let $ = (selector) => {
    let result = document.querySelectorAll(selector);
    if (result.length === 0) return null;
    result.forEach((r) => {
        r.onClick = (callback) => r.addEventListener('click', callback);
    });
    if (result.length === 1) return result.item(0);
    return result;
}

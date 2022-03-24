let $ = (selector) => {
    let result = document.querySelectorAll(selector);
    if (result.length === 0) return null;
    if (result.length === 1) return result.item(0);
    return result;
}

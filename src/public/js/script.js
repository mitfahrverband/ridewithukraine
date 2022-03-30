let $ = (selector) => {
    let result = [window];
    if (selector !== undefined) {
        result = document.querySelectorAll(selector);
        if (result.length === 0) return null;
    }
    result.forEach((r) => {
        r.onClick = (cb) => r.addEventListener('click', cb);
        r.onLoad = (cb) => r.addEventListener('load', cb);
    });
    if (result.length === 1) return result[0];
    return result;
}

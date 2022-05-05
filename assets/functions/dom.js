/**
 * @param {string} selector
 * @param parent
 * @return {HTMLElement}
 */
export function $(selector, parent = document) {
    return parent.querySelector(selector);
}

/**
 * @param {string} selector
 * @param parent
 * @return {HTMLElement[]}
 */
export function $$(selector, parent = document) {
    return [...parent.querySelectorAll(selector)]
}

/**
 * Transform une chaîne en élément DOM
 * @param {string} str
 * @return {DocumentFragment}
 */
export function strToDom(str) {
    return document.createRange().createContextualFragment(str).firstChild;
}
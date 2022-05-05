import {$$} from "../functions/dom";

export function bindActivePage() {
    const menuElements = $$('[aria-current]')

    menuElements.forEach(el => {
        el.classList.add("active")
    })
}
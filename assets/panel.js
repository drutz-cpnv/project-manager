import {bindActivePage} from "./modules/menu";
import './styles/app.scss';
import {Tooltip} from 'bootstrap'

document.documentElement.addEventListener("turbo:load", evt => {
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl)
    })

})
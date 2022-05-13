import feather from "feather-icons";
import {bindActivePage} from "./modules/menu";
import Quill from "quill/quill";
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import './styles/app.scss';
import 'bootstrap/dist/js/bootstrap.min'
import {Tooltip} from 'bootstrap'

document.documentElement.addEventListener("turbo:load", evt => {
    bindActivePage()
    feather.replace()

    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl)
    })

})
import feather from "feather-icons";
import {bindActivePage} from "./modules/menu";
import Quill from "quill/quill";
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import './styles/app.scss';
import 'bootstrap/dist/js/bootstrap.min'

document.documentElement.addEventListener("turbo:load", evt => {
    bindActivePage()
    feather.replace()

})
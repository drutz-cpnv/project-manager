import feather from "feather-icons";
import {bindActivePage} from "./modules/menu";

document.documentElement.addEventListener("turbo:load", evt => {
    bindActivePage()
    feather.replace()
})
import feather from "feather-icons";
import {bindActivePage} from "./modules/menu";
import {$} from "./functions/dom";
import mandate_nav from "./modules/mandate_nav";

document.documentElement.addEventListener("turbo:load", evt => {
    bindActivePage()
    feather.replace()
    if($("[data-mandate-show]")) {
        mandate_nav()
    }
})
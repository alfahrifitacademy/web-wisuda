"use strict";

import initFilter from "./modules/filter";
import {drawTeamPopup} from "./modules/modal";

document.addEventListener('DOMContentLoaded', () => {
    drawTeamPopup();
    initFilter('.list_courses', '.list_tags-tag', {
        itemSelector: '.list_courses-card'
    });
})
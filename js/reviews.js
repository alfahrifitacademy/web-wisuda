"use strict";

import {initSwiperSlider} from "./modules/slider";

document.addEventListener('DOMContentLoaded', () => {
    initSwiperSlider('.reviews_slider', {
        effect: 'fade',
        fadeEffect: {
            crossFade: true,
        },
        autoplay: true,
        speed: 1500
    })
})
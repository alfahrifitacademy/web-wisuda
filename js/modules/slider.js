"use strict";

import Swiper, {Navigation, Pagination, Autoplay, EffectFade} from 'swiper';

Swiper.use([Navigation, Pagination, Autoplay, EffectFade]);

// basic swiper initialization
export function initSwiperSlider(container, options) {
    const containerEL = document.querySelector(container);
    if (containerEL) {
        const swiper = new Swiper(container, {
            disableOnInteraction: true,
            pauseOnMouseEnter: true,
            loop: true,
            keyboard: {
                enabled: true,
                onlyInViewport: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            ...options
        });
    }
}
"use strict";

import simpleParallax from 'simple-parallax-js';

export function setParallax() {
    const targetElems = document.querySelectorAll('.parallax')
    new simpleParallax(targetElems, {scale: 1.1});
}

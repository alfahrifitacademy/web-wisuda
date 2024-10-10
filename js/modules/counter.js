"use strict";

import { CountUp } from 'countup.js';
import { inViewport } from './helpers';


function animateNumber(numSelector = '.countNum', animationDuration = 1.5, startVal = 0) {
    const numArr = document.querySelectorAll(numSelector);
    for (let i = 0; i < numArr.length; i++) {
        let num = numArr[i];
        let value = +num.dataset.value;
        let options = {
            prefix: num.dataset.prefix ? num.dataset.prefix : '',
            suffix: num.dataset.suffix ? num.dataset.suffix : '',
            separator: num.dataset.separator ? num.dataset.separator : '',
            duration: animationDuration,
            startVal: startVal
        };
        let animatedNum = new CountUp(num, value, options);

        function triggerAnimation() {
            if (!inViewport(num)) {
                animatedNum.start();
            }
        }

        triggerAnimation();

        window.addEventListener('scroll', triggerAnimation);
    }
}

export default animateNumber;
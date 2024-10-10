'use strict';

import Shuffle from 'shufflejs';

function initFilter(container, trigger, options) {
    const containerEl = document.querySelector(container);

    if (containerEl) {
        const shuffleInstance = new Shuffle(containerEl, {...options});
        const triggers = document.querySelectorAll(trigger);

        triggers.forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                for (let i = 0; i < triggers.length; i++) {
                    triggers[i].classList.remove('current');
                }
                this.classList.add('current');
                const filter = this.dataset.target;
                shuffleInstance.filter(filter);
            })
        })
    }
}

export default initFilter;
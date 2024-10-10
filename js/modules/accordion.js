'use strict';

function drawAccordion() {
    const accordionCards = document.querySelectorAll('.faq_accordion .accordion-collapse');
    const icons = document.querySelectorAll('.faq_accordion-item_header .icon');

    accordionCards.forEach((el, i) => {
        el.addEventListener('show.bs.collapse', () => {
            icons[i].classList.add('transform');
        })
        el.addEventListener('hide.bs.collapse', () => {
            icons[i].classList.remove('transform')
        })
    })

}

export default drawAccordion;
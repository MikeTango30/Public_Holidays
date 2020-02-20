'use strict';

(function () {
    // suggestion from stack overflow to reload page on navigating back in browser
    window.addEventListener( "pageshow", function ( event ) {
        var historyTraversal = event.persisted ||
            ( typeof window.performance != "undefined" &&
                window.performance.navigation.type === 2 );
        if ( historyTraversal ) {
            // Handle page restore.
            window.location.reload();
        }
    });
    // toggle regions when country has them
    let country = document.querySelector('#country-choice');
    country.addEventListener('input', function () {
        let options = document.querySelector('#countries').childNodes;
        options.forEach(element => {
            if (element.value === country.value) {
                const region = document.querySelector('.regions-' + element.dataset.countryCode);
                const regions = document.querySelectorAll('.regions');
                regions.forEach(region => {
                    if (!region.classList.contains('d-none')) {
                        region.classList.toggle('d-none');
                    }
                });
                if (region) {

                    region.classList.toggle('d-none');
                }
            }
        })
    });
})();
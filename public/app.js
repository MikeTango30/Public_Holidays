'use strict';

(function () {


    document.addEventListener('DOMContentLoaded', function () {
        let countries = document.querySelectorAll('[data-country]');
        // for (let country of countries.dataset.countries) {
        const entryIds =
            Array.from(countries).map(
                item => item.dataset.country
            );
        console.log(entryIds)
        // }
    });

    let country = document.querySelector('#country-choice');
    country.addEventListener('input', function () {
        let opts = document.querySelector('#countries').childNodes;
        opts.forEach(element => {
            if (element.value === country.value) {
                console.log(country.value);
            }
        }
    )
    });


    class Region {
        constructor(region) {
            this.region = region;
        }

        showRegion() {
            return this.region;
        }
    }

    class RegionSelect {
        constructor(regionList) {
            this.form = document.querySelector('.holidays');

            this.formGroup = document.createElement('div');
            this.formGroup.classList.add('form-group');

            this.label = document.createElement('label');
            this.label.setAttribute('for', 'region-choice');
            this.label.textContent = 'Choose Region:'

            this.select = document.createElement('select');
            this.select.classList.add('form-control');
            this.select.setAttribute('list', 'countries');
            this.select.setAttribute('id', 'region-choice');
            this.select.setAttribute('name', 'region-choice');
            for (let region of regionList) {
                this.option = document.createElement('option');
                this.option.setAttribute('value', region["regionCode"]);
                this.option.textContent = region["regionName"];

                this.select.appendChild(this.option);
            }

            this.formGroup.appendChild(this.label);
            this.formGroup.appendChild(this.select);

            this.form.appendChild(this.formGroup);

        }
    }
})();

// <div class="form-group">
//     <label for="country-choice">Choose country:</label>
//     <select class="form-control" list="countries" id="country-choice" name="country-choice">
//         <option value="{{ country.country }}">
// </div>
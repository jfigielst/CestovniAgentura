document.addEventListener("DOMContentLoaded", function () {
    
    // Inicializujeme všechny komponenty na stránce
    document.querySelectorAll('.vyber-komponenta').forEach(function(komponenta) {
        var prefix = komponenta.getAttribute('data-prefix');
        var isMulti = komponenta.getAttribute('data-multi') === 'true';
        var defaultText = komponenta.getAttribute('data-default') || 'Vyberte';
        var pluralText = 'položek'; // Zjednodušeno pro univerzálnost
        
        setupModalLogic(komponenta, prefix, isMulti, defaultText, pluralText);
    });

    function setupModalLogic(komponenta, prefix, isMulti, defaultText, pluralText) {
        var trigger = document.getElementById(prefix + '-trigger');
        var modal = document.getElementById(prefix + '-modal');
        if (!trigger || !modal) return;

        var closeBtn = modal.querySelector('.dest-modal__close');
        var confirmBtn = document.getElementById(prefix + '-confirm-btn');
        var search = document.getElementById(prefix + '-search');
        var triggerText = document.getElementById(prefix + '-trigger-text');

        trigger.addEventListener('click', function() {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        var zavriModal = function() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            aktualizujZobrazenyText();
        };

        if (closeBtn) closeBtn.addEventListener('click', zavriModal);
        if (confirmBtn) confirmBtn.addEventListener('click', zavriModal);
        modal.addEventListener('click', function(e) {
            if(e.target === modal) zavriModal();
        });

        if (search) {
            search.addEventListener('input', function(e) {
                var term = e.target.value.toLowerCase();
                modal.querySelectorAll('.dest-state-group').forEach(function(group) {
                    var stateName = group.querySelector('.stat-name').textContent.toLowerCase();
                    var cities = group.querySelectorAll('.city-cb');
                    var matchFound = stateName.includes(term);

                    cities.forEach(function(city) {
                        if (city.textContent.toLowerCase().includes(term)) {
                            matchFound = true;
                            city.style.display = '';
                        } else {
                            if (!stateName.includes(term)) {
                                city.style.display = 'none';
                            } else {
                                city.style.display = '';
                            }
                        }
                    });

                    if (matchFound) {
                        group.style.display = '';
                    } else {
                        group.style.display = 'none';
                    }
                });
            });
        }

        modal.querySelectorAll('.dest-expand-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var citiesContainer = btn.parentElement.nextElementSibling;
                citiesContainer.classList.toggle('open');
                btn.textContent = citiesContainer.classList.contains('open') ? '^' : 'v';
            });
        });

        if (isMulti) {
            modal.querySelectorAll('.cb-stat').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var checked = this.checked;
                    var group = this.closest('.dest-state-group');
                    group.querySelectorAll('.cb-mesto').forEach(function(cityCb) {
                        cityCb.checked = checked;
                    });
                });
            });

            modal.querySelectorAll('.cb-mesto').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var group = this.closest('.dest-state-group');
                    var total = group.querySelectorAll('.cb-mesto').length;
                    var checked = group.querySelectorAll('.cb-mesto:checked').length;
                    var stateCb = group.querySelector('.cb-stat');
                    
                    if (checked === 0) {
                        stateCb.checked = false;
                        stateCb.indeterminate = false;
                    } else if (checked === total) {
                        stateCb.checked = true;
                        stateCb.indeterminate = false;
                    } else {
                        stateCb.checked = false;
                        stateCb.indeterminate = true;
                    }
                });
            });
        } else {
            // Radio chování - po výběru rovnou zavřeme modal a aktualizujeme text
            modal.querySelectorAll('.cb-mesto').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    aktualizujZobrazenyText();
                    zavriModal();
                });
            });
        }

        function aktualizujZobrazenyText() {
            var selectedCities = modal.querySelectorAll('.cb-mesto:checked');
            if (selectedCities.length === 0) {
                triggerText.textContent = defaultText;
            } else if (selectedCities.length === 1) {
                triggerText.textContent = selectedCities[0].getAttribute('data-nazev');
            } else {
                triggerText.textContent = "Vybráno " + selectedCities.length + " " + pluralText;
            }
        }
        
        // Inicializace textu po načtení
        aktualizujZobrazenyText();
    }
});

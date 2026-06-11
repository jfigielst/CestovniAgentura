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
            
            // UX Improvement: Auto-expand the state group of the currently selected city/airport
            var checkedInput = modal.querySelector('.cb-mesto:checked');
            console.log('[VyberDestinace] Open modal prefix:', prefix, 'checkedInput:', checkedInput);
            if (checkedInput) {
                var group = checkedInput.closest('.dest-state-group');
                console.log('[VyberDestinace] Found group:', group);
                if (group) {
                    var citiesContainer = group.querySelector('.dest-cities');
                    if (citiesContainer) {
                        citiesContainer.classList.add('open');
                        console.log('[VyberDestinace] Expanded cities container for:', group.getAttribute('data-stat'));
                    }
                    var btn = group.querySelector('.dest-expand-btn');
                    if (btn) {
                        btn.textContent = '^';
                    }
                }
            }
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
                        // UX Improvement: Auto-expand matching groups when searching
                        if (term.length > 0) {
                            var citiesContainer = group.querySelector('.dest-cities');
                            if (citiesContainer) {
                                citiesContainer.classList.add('open');
                            }
                            var btn = group.querySelector('.dest-expand-btn');
                            if (btn) {
                                btn.textContent = '^';
                            }
                        }
                    } else {
                        group.style.display = 'none';
                    }

                    // Revert to collapsed if search is cleared (except for the one containing checked option)
                    if (term.length === 0) {
                        var citiesContainer = group.querySelector('.dest-cities');
                        var isCheckedInside = group.querySelector('.cb-mesto:checked');
                        if (citiesContainer && !isCheckedInside) {
                            citiesContainer.classList.remove('open');
                            var btn = group.querySelector('.dest-expand-btn');
                            if (btn) {
                                btn.textContent = 'v';
                            }
                        }
                    }
                });
            });
        }

        // UX Improvement: Allow clicking on the entire state header to expand/collapse
        modal.querySelectorAll('.dest-state-header').forEach(function(header) {
            header.addEventListener('click', function(e) {
                // Ignore clicks on checkmark containers unless they click the name text itself
                if (e.target.closest('.checkbox-container') && !e.target.classList.contains('stat-name')) {
                    return;
                }
                var btn = header.querySelector('.dest-expand-btn');
                var citiesContainer = header.nextElementSibling;
                if (citiesContainer && citiesContainer.classList.contains('dest-cities')) {
                    citiesContainer.classList.toggle('open');
                    if (btn) {
                        btn.textContent = citiesContainer.classList.contains('open') ? '^' : 'v';
                    }
                }
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

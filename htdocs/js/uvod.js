document.addEventListener("DOMContentLoaded", function () {
    var filtrDestinace = document.getElementById("filtr-destinace");
    var filtrCena = document.getElementById("filtr-cena");
    var filtrDelka = document.getElementById("filtr-delka");
    var karty = document.querySelectorAll("[data-zajezd]");
    var detail = document.getElementById("detail-zajezdu");

    // Načtení parametrů z URL a nastavení filtrů
    var urlParams = new URLSearchParams(window.location.search);
    var urlDest = urlParams.get('destinace');
    var urlStat = urlParams.get('stat');
    var vybranyStat = urlStat || "";

    if (urlDest && filtrDestinace) {
        filtrDestinace.value = urlDest;
    }

    function filtrujZajezdy() {
        var dest = filtrDestinace ? filtrDestinace.value : "";
        var cena = filtrCena ? filtrCena.value : "";
        var delka = filtrDelka ? filtrDelka.value : "";

        karty.forEach(function (karta) {
            var zobrazit = true;
            if (dest && karta.dataset.destinace !== dest) zobrazit = false;
            // Pokud filtrujeme podle státu (a není vybrána konkrétní destinace)
            if (!dest && vybranyStat && karta.dataset.stat !== vybranyStat) zobrazit = false;
            if (cena && karta.dataset.cena !== cena) zobrazit = false;
            if (delka && karta.dataset.delka !== delka) zobrazit = false;
            karta.classList.toggle("skryte", !zobrazit);
        });
    }

    if (filtrDestinace) {
        filtrDestinace.addEventListener("change", function() {
            vybranyStat = ""; // zrušíme filtr na stát při ruční změně destinace
            filtrujZajezdy();
        });
    }
    if (filtrCena) filtrCena.addEventListener("change", filtrujZajezdy);
    if (filtrDelka) filtrDelka.addEventListener("change", filtrujZajezdy);

    // Spustíme filtrování při načtení, pokud jsou předány parametry
    if (urlDest || urlStat) {
        filtrujZajezdy();
    }

    document.querySelectorAll("[data-detail-id]").forEach(function (tlacitko) {
        tlacitko.addEventListener("click", function (e) {
            e.preventDefault();
            var id = tlacitko.getAttribute("data-detail-id");
            var sablona = document.getElementById("detail-" + id);
            if (!detail || !sablona) return;
            detail.innerHTML = sablona.innerHTML;
            detail.classList.add("detail-zajezdu--viditelny");
            detail.scrollIntoView({ behavior: "smooth", block: "nearest" });
        });
    });

    // Counter logika
    var dropdown = document.querySelector('.counter-dropdown');
    var toggle = document.querySelector('.counter-dropdown__toggle');
    var textSpan = document.getElementById('pocet-osob-text');
    var dospeliInput = document.getElementById('hledat-dospeli');
    var detiInput = document.getElementById('hledat-deti');

    if(toggle) {
        toggle.addEventListener('click', function() {
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if(!dropdown.contains(e.target)) dropdown.classList.remove('open');
        });
    }

    function updateCounterText() {
        var d = parseInt(dospeliInput.value);
        var c = parseInt(detiInput.value);
        var dText = d === 1 ? '1 dospělý' : (d < 5 ? d + ' dospělí' : d + ' dospělých');
        var cText = c === 1 ? '1 dítě' : (c > 1 && c < 5 ? c + ' děti' : c + ' dětí');
        textSpan.textContent = dText + (c > 0 ? ', ' + cText : '');
    }

    document.querySelectorAll('.btn-plus').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var targetId = 'hledat-' + e.target.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if(parseInt(input.value) < parseInt(input.max)) {
                input.value = parseInt(input.value) + 1;
                updateCounterText();
            }
        });
    });

    document.querySelectorAll('.btn-minus').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var targetId = 'hledat-' + e.target.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if(parseInt(input.value) > parseInt(input.min)) {
                input.value = parseInt(input.value) - 1;
                updateCounterText();
            }
        });
    });

    // Modal logika pro destinace a letiste
    function setupModalLogic(prefix, defaultText, pluralText) {
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
    }

    setupModalLogic('dest', 'Všechny destinace', 'destinací');
    setupModalLogic('letiste', 'Jakékoliv letiště', 'letišť');



    var formRegistrace = document.getElementById("form-registrace-uvod");
    if (formRegistrace) {
        formRegistrace.addEventListener("submit", function (e) {
            var heslo = formRegistrace.querySelector('[name="heslo"]');
            var potvrzeni = formRegistrace.querySelector('[name="heslo_potvrzeni"]');
            if (heslo && potvrzeni && heslo.value !== potvrzeni.value) {
                e.preventDefault();
                alert("Hesla se neshodují.");
            }
        });
    }

    var formRezervace = document.getElementById("form-rezervace");
    if (formRezervace) {
        formRezervace.addEventListener("submit", function (e) {
            e.preventDefault();
            alert("Rezervace byla odeslána. Brzy vás budeme kontaktovat.");
            formRezervace.reset();
        });
    }
});

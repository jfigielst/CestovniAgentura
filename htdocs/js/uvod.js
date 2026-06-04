document.addEventListener("DOMContentLoaded", function () {
    var filtrDestinace = document.getElementById("filtr-destinace");
    // Získáme formulářové prvky z nových komponent (pokud neexistují selecty)
    var filtrCenaSelect = document.getElementById("filtr-cena");
    var filtrDelkaSelect = document.getElementById("filtr-delka");
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
        var vybranaCena = document.querySelector('input[name="filtr-cena"]:checked');
        var cena = filtrCenaSelect ? filtrCenaSelect.value : (vybranaCena ? vybranaCena.value : "");
        
        var vybranaDelka = document.querySelector('input[name="filtr-delka"]:checked');
        var delka = filtrDelkaSelect ? filtrDelkaSelect.value : (vybranaDelka ? vybranaDelka.value : "");
        
        // Získání vybraných destinací z nového modálu (pokud jsme na stránce zajezdy)
        var vybraneDestinace = [];
        var modalCheckboxu = document.querySelectorAll('#filtr-dest-modal .cb-mesto:checked');
        modalCheckboxu.forEach(function(cb) {
            vybraneDestinace.push(cb.value);
        });

        karty.forEach(function (karta) {
            var zobrazit = true;
            
            // Filtrování podle vícero destinací
            if (vybraneDestinace.length > 0) {
                if (!vybraneDestinace.includes(karta.dataset.destinace)) {
                    zobrazit = false;
                }
            } else if (vybranyStat && karta.dataset.stat !== vybranyStat) {
                // Ponecháme funkčnost starého URL filtru přes stát, pokud nejsou naklikané destinace
                zobrazit = false;
            }

            if (cena && karta.dataset.cena !== cena) zobrazit = false;
            if (delka && karta.dataset.delka !== delka) zobrazit = false;
            karta.classList.toggle("skryte", !zobrazit);
        });
    }

    // Při změně checkboxu v novém modálu zavolat filtr
    document.querySelectorAll('#filtr-dest-modal .cb-mesto, #filtr-dest-modal .cb-stat').forEach(function(el) {
        el.addEventListener("change", function() {
            vybranyStat = ""; // zrušíme statický filtr na stát
            filtrujZajezdy();
        });
    });
    
    // Pro staré selecty
    if (filtrCenaSelect) filtrCenaSelect.addEventListener("change", filtrujZajezdy);
    if (filtrDelkaSelect) filtrDelkaSelect.addEventListener("change", filtrujZajezdy);
    
    // Pro nové radio inputy
    document.querySelectorAll('input[name="filtr-cena"], input[name="filtr-delka"]').forEach(function(el) {
        el.addEventListener("change", filtrujZajezdy);
    });

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

    // Modal logika pro destinace a letiste byla presunuta do vyber_destinace.js



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

// ---------------------------------------------------------
// HUVUDSCRIPT FÖR FRONTEND-LOGIK
// ---------------------------------------------------------
// Detta script körs i webbläsaren (klienten).
// Det hanterar Bootstrap-komponenter och förbättrar användarupplevelsen (UX).

console.log("App loaded"); // Debug: Bekräftar att scriptet laddats korrekt i konsolen.

// Vi väntar på att hela HTML-strukturen (DOM) ska vara laddad innan vi kör koden.
// Detta förhindrar fel där scriptet försöker hitta element som inte ritats ut än.
document.addEventListener("DOMContentLoaded", function () {
  // ---------------------------------------------------------
  // 1. INITIERA BOOTSTRAP KOMPONENTER (VISUELLT)
  // ---------------------------------------------------------
  // Bootstrap 5 använder JavaScript för interaktiva element som Popovers och Tooltips.
  // Dessa måste aktiveras manuellt.

  // A. POPOVERS (Används t.ex. för att visa info om Badges i Flöde B)
  // Vi hämtar alla element som har attributet 'data-bs-toggle="popover"'
  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  // Vi loopar igenom dem och skapar en ny Bootstrap Popover-instans för varje.
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // B. TOOLTIPS (Små hjälprutor vid hover)
  // Samma logik här. Används ofta i Admin-gränssnittet (Flöde C) för knappar.
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // ---------------------------------------------------------
  // 2. UX: BEHÅLL SCROLL-POSITION (STATE MANAGEMENT)
  // ---------------------------------------------------------
  // Eftersom PHP är "stateless" och laddar om hela sidan vid formulärskick (POST)
  // eller filtrering (GET), tappar användaren sin plats på sidan.
  // Denna kod sparar scroll-positionen i webbläsarens minne (SessionStorage)
  // och återställer den efter omladdning. Detta är kritiskt för FLÖDE C (Admin-listor).

  // A. Återställ position OMEDELBART utan animation
  // När sidan laddas om, kollar vi om det finns en sparad position.
  const scrollPos = sessionStorage.getItem("scrollPosition");
  if (scrollPos) {
    // UX-TRICK: Tvinga webbläsaren att ignorera "smooth scroll" i CSS för detta hopp.
    // Annars ser användaren hur sidan åker ner, vilket kan göra en yr.
    document.documentElement.style.scrollBehavior = "auto";

    // Hoppa direkt till den sparade pixeln.
    window.scrollTo(0, parseInt(scrollPos));

    // Rensa minnet så vi inte fastnar här för alltid.
    sessionStorage.removeItem("scrollPosition");

    // Återställ smooth scroll (om du använder det i CSS) efter en kort stund.
    // Detta gör att manuell scrollning känns mjuk igen efter hoppet.
    setTimeout(() => {
      document.documentElement.style.scrollBehavior = "";
    }, 100);
  }

  // B. Spara position vid FORMULÄR-skick
  // När man sparar en uppgift eller användare (Flöde A, C) vill man vara kvar på samma ställe.
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function () {
      // Spara nuvarande Y-position (pixlar från toppen)
      sessionStorage.setItem("scrollPosition", window.scrollY);
    });
  });

  // C. Spara position vid FILTER-KNAPPAR
  // När man klickar på filter i Dashboard (Flöde D) eller Admin (Flöde C).
  const filterBtns = document.querySelectorAll(".btn-filter");
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      sessionStorage.setItem("scrollPosition", window.scrollY);
    });
  });
});

console.log("App loaded");

document.addEventListener("DOMContentLoaded", function () {
  // --- 1. INITIERA BOOTSTRAP KOMPONENTER ---
  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // --- 2. UX: BEHÅLL SCROLL-POSITION (SNABBARE VERSION) ---

  // A. Återställ position OMEDELBART utan animation
  const scrollPos = sessionStorage.getItem("scrollPosition");
  if (scrollPos) {
    // Tvinga webbläsaren att ignorera "smooth scroll" i CSS för detta hopp
    document.documentElement.style.scrollBehavior = "auto";

    window.scrollTo(0, parseInt(scrollPos));

    sessionStorage.removeItem("scrollPosition");

    // Återställ smooth scroll (om du använder det i CSS) efter en kort stund
    setTimeout(() => {
      document.documentElement.style.scrollBehavior = "";
    }, 100);
  }

  // B. Spara position vid FORMULÄR-skick
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function () {
      sessionStorage.setItem("scrollPosition", window.scrollY);
    });
  });

  // C. Spara position vid FILTER-KNAPPAR
  const filterBtns = document.querySelectorAll(".btn-filter");
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      sessionStorage.setItem("scrollPosition", window.scrollY);
    });
  });
});

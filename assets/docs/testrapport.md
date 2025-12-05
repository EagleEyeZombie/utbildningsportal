# 📋 Testrapport: Utbildningsportal (Gamification)

**Projekt:** Utbildningsportal (Yrkesprov 2025)  
**Version:** 1.0.0 (MVP)  
**Datum:** 2025-11-14 (Senast uppdaterad: 2025-12-04)  
**Testare:** Fredrich  
**Miljö:** Laragon (Lokal server), Chrome

---

## **1. Funktionstester (Use-Case)**

Syftet är att verifiera att alla funktioner fungerar enligt kravspecifikationen.

### **1.1 Autentisering & Användare**

| ID   | Testbeskrivning                               | Förväntat Resultat                                        | Faktiskt Resultat                                                                                               | Status                  |
| :--- | :-------------------------------------------- | :-------------------------------------------------------- | :-------------------------------------------------------------------------------------------------------------- | :---------------------- |
| F1.1 | **Registrering:** Skapa nytt elevkonto.       | Kontot skapas, lösenordet hashas, omdirigeras till login. | Kontot skapas, lösenordet hashas, omdirigeras till login med texten: "Ditt konto är nu skapat! Logga in nedan." | ✅ Godkänd (19.11.2025) |
| F1.2 | **Registrering:** Ange upptaget användarnamn. | Felmeddelande på svenska visas.                           | Felmeddelande på svenska visas: "Användarnamnet är upptaget".                                                   | ✅ Godkänd (19.11.2025) |
| F1.3 | **Registrering:** Ange upptagen e-post.       | Felmeddelande visas på svenska ("E-post upptagen").       | Felmeddelande visas på svenska: "E-postadressen används redan".                                                 | ✅ Godkänd (19.11.2025) |
| F1.4 | **Login:** Logga in med Användarnamn.         | Inloggning lyckas, omdirigeras till Dashboard.            | Fungerar korrekt.                                                                                               | ✅ Godkänd (18.11.2025) |
| F1.5 | **Login:** Logga in med E-post.               | Inloggning lyckas, omdirigeras till Dashboard.            | Fungerar korrekt.                                                                                               | ✅ Godkänd (18.11.2025) |
| F1.6 | **Login:** Felaktigt lösenord.                | Felmeddelande visas, ingen inloggning.                    | Felmeddelande visas: "Felaktigt användarnamn/e-post eller lösenord".                                            | ✅ Godkänd (18.11.2025) |
| F1.7 | **Logout:** Klicka på "Logga ut".             | Sessionen avslutas, skickas till Login-sidan.             | Fungerar korrekt.                                                                                               | ✅ Godkänd (18.11.2025) |

### **1.2 Elev Dashboard & Progression**

| ID    | Testbeskrivning                                       | Förväntat Resultat                                          | Faktiskt Resultat                                                                                                      | Status                  |
| :---- | :---------------------------------------------------- | :---------------------------------------------------------- | :--------------------------------------------------------------------------------------------------------------------- | :---------------------- |
| F2.1  | **Visning:** Välkomstpanel & Stats.                   | Namn, XP och Level visas korrekt.                           | Namn, XP och Level visas korrekt och ändras enligt konfigurering.                                                      | ✅ Godkänd (20.11.2025) |
| F2.2  | **Listning:** Visa uppgifter.                         | Alla uppgifter visas som kort.                              | Alla uppgifter syns.                                                                                                   | ✅ Godkänd (19.11.2025) |
| F2.3  | **Filter:** Filtrera på "Spelsätt".                   | Endast valda uppgifter visas.                               | Endast valt spelsätts uppgift visas.                                                                                   | ✅ Godkänd (03.12.2025) |
| F2.4  | **Filter:** Filtrera på "Genre".                      | Endast valda uppgifter visas.                               | Endast vald genre visas.                                                                                               | ✅ Godkänd (03.12.2025) |
| F2.5  | **Progression:** Låsta kapitel.                       | Kapitel med högre nivå än upplåst ska vara gråa/oklickbara. | Kapitel med högre nivå än upplåsta visas inte eller är gråa/oklickbara.                                                | ✅ Godkänd (03.12.2025) |
| F2.6  | **Progression:** Badges.                              | Badges visas.                                               | Badges visas.                                                                                                          | ✅ Godkänd (02.12.2025) |
| F2.7  | **Temahantering:** Byt tema (t.ex. Pixel).            | Utseende ändras direkt, valet sparas.                       | Temat byttes och sparades korrekt i databasen.                                                                         | ✅ Godkänd (02.12.2025) |
| F2.7  | **Läsbarhet i Teman:** Kontrollera text i alla teman. | Texten anpassas för att synas mot ljus/mörk bakgrund.       | Texten är tydlig i teman: Fantasy, Retro, Cyberpunk, Pixel, Ocean och Rainbow. (Viss justering gjord för Pink/Nature). | ✅ Godkänd (02.12.2025) |
| F2.8  | **XP-Vinst:** Eleven klarar en uppgift.               | XP ökar korrekt (inkl. multiplikator) i Hero-boxen.         | XP läggs till korrekt i databasen och visas i Hero-boxen.                                                              | ✅ Godkänd (02.12.2025) |
| F2.9  | **XP-Förlust:** Eleven misslyckas (< 70%).            | Ingen XP delas ut.                                          | Ingen XP delas ut vid < 70% rätt.                                                                                      | ✅ Godkänd (02.12.2025) |
| F2.10 | **Level Up:** Eleven passerar nivågräns.              | "Level Up"-meddelande visas, mätaren uppdateras.            | Eleven får meddelande vid Level Up.                                                                                    | ✅ Godkänd (02.12.2025) |
| F2.11 | **Badges (Nykomling):** Ny elev loggar in.            | Badgen "Nykomling" delas ut direkt.                         | Nykomling-badgen delas ut direkt vid första inloggningen.                                                              | ✅ Godkänd (02.12.2025) |
| F2.12 | **Badges (Progress):** Låst badge med räknare.        | Mätaren visar korrekt framsteg (t.ex. "3 / 5").             | Badges.php visar alla badges och vad som krävs.                                                                        | ✅ Godkänd (02.12.2025) |
| F2.13 | **XP to next level:** Progressbar för nästa nivå.     | Stapel visar XP kvar till nästa nivå.                       | Grön stapel under namnet visar % och exakt XP kvar. Anpassas efter DB.                                                 | ✅ Godkänd (02.12.2025) |
| F2.14 | **Badges (Upplåsning):** Uppfyll kravet.              | Badgen ändras till "Upplåst".                               | Badges låses upp korrekt och visas på badges.php.                                                                      | ✅ Godkänd (03.12.2025) |
| F2.15 | **Popovers:** Klicka på badge i Dashboard.            | Info-ruta visas (även på mobil).                            | Popovers visas vid klick på både mobil och dator.                                                                      | ✅ Godkänd (03.12.2025) |

### **1.3 Genomförande av Uppgifter (Quiz)**

| ID   | Testbeskrivning                                      | Förväntat Resultat                    | Faktiskt Resultat                                       | Status                  |
| :--- | :--------------------------------------------------- | :------------------------------------ | :------------------------------------------------------ | :---------------------- |
| F3.1 | **Starta uppgift:** Alla typer.                      | "Wizard" startar, texten visas först. | Flödet fungerar.                                        | ✅ Godkänd (03.12.2025) |
| F3.2 | **Läst text:** Flerval och Sant/Falskt.              | Visar frågor och påstående.           | Visas korrekt. CSS fixad för textfärg på ljus bakgrund. | ✅ Godkänd (03.12.2025) |
| F3.3 | **Drag-and-Drop:** Sortering, Para ihop, Textluckor. | Går att dra och byta plats.           | Sortering fungerar smidigt.                             | ✅ Godkänd (03.12.2025) |
| F3.4 | **Rättning:** < 70% rätt.                            | "Försök igen", inga XP.               | Rättar korrekt.                                         | ✅ Godkänd (03.12.2025) |
| F3.5 | **Rättning:** > 70% rätt.                            | "Bra jobbat", XP sparas.              | Rättar korrekt.                                         | ✅ Godkänd (03.12.2025) |
| F3.6 | **Uppdatering:** Dashboard efter klarad.             | Kortet får grön ram, badge "Klarad".  | Status uppdateras direkt.                               | ✅ Godkänd (03.12.2025) |

### **1.4 Adminpanel**

| ID   | Testbeskrivning                                  | Förväntat Resultat                       | Faktiskt Resultat                                                    | Status                  |
| :--- | :----------------------------------------------- | :--------------------------------------- | :------------------------------------------------------------------- | :---------------------- |
| F4.1 | **CRUD:** Skapa uppgift (Alla typer).            | Sparas med korrekt JSON.                 | Uppgifter skapas korrekt och syns i listan.                          | ✅ Godkänd (03.12.2025) |
| F4.2 | **CRUD:** Redigera uppgift.                      | Formuläret fylls i, ändringar sparas.    | Redigering fungerar.                                                 | ✅ Godkänd (03.12.2025) |
| F4.3 | **CRUD:** Ta bort uppgift.                       | Uppgift + resultat raderas.              | Radering fungerar.                                                   | ✅ Godkänd (03.12.2025) |
| F4.4 | **Filter:** Admin-filter.                        | Filtrera på Lärare, Typ, Nivå, Klass.    | Filtrering fungerar.                                                 | ✅ Godkänd (03.12.2025) |
| F4.5 | **Klasshantering:** Skapa klass & koppla lärare. | Klassen skapas och syns med rätt lärare. | Klassen skapades korrekt.                                            | ✅ Godkänd (02.12.2025) |
| F4.6 | **Elevkoppling:** Lägg till elev i klass.        | Eleven kopplas till klassen.             | Eleven flyttades korrekt.                                            | ✅ Godkänd (02.12.2025) |
| F4.7 | **UX Scroll:** Behåll position vid spara.        | Sidan hoppar inte till toppen.           | Positionen behålls (State Preservation).                             | ✅ Godkänd (02.12.2025) |
| F4.8 | **Validering:** Spara utan titel/frågor.         | Felmeddelande på svenska.                | JavaScript fångar 'invalid'-eventet och visar svenskt felmeddelande. | ✅ Godkänd (02.12.2025) |

---

## **2. Responsivitetstestning**

_Testat via Chrome DevTools samt fysisk mobil._

| Sida                    | Mobil (375x667)                                     | Surfplatta (768x1024)              | Desktop (1920x1080)             | Status     |
| :---------------------- | :-------------------------------------------------- | :--------------------------------- | :------------------------------ | :--------- |
| **index.php**           | Anpassade padding/text. Ingen scroll i sidled.      | Responsiva klasser för marginaler. | Centrerad och snygg.            | ✅ Godkänd |
| **login.php**           | Staplas snyggt.                                     | OK.                                | Centrerat kort.                 | ✅ Godkänd |
| **admin_dashboard.php** | OK.                                                 | OK.                                | OK.                             | ✅ Godkänd |
| **user-management.php** | Rubrik/knappar justerade. Dolda kolumner för plats. | Text och val syns korrekt.         | Rensade containers, full bredd. | ✅ Godkänd |
| **admin_tasks.php**     | Card-view för tabell (inga sidscroll).              | Tabellen ryms bra.                 | Full överblick.                 | ✅ Godkänd |
| **admin_classes.php**   | Staplad header, dolt ikon, Card-view.               | OK.                                | OK.                             | ✅ Godkänd |
| **dashboard.php**       | 1 kolumn.                                           | 2 kolumner.                        | 3 kolumner (justerat).          | ✅ Godkänd |
| **badges.php**          | OK.                                                 | OK.                                | OK.                             | ✅ Godkänd |
| **task_view.php**       | Minskad padding för mer textutrymme.                | OK.                                | OK.                             | ✅ Godkänd |
| **task_submit.php**     | Textfärg och knappar justerade.                     | OK.                                | OK.                             | ✅ Godkänd |
| **403.php**             | OK.                                                 | OK.                                | OK.                             | ✅ Godkänd |

---

## **3. Säkerhetstestning**

| ID   | Testbeskrivning     | Metod                                 | Resultat                                                     | Status     |
| :--- | :------------------ | :------------------------------------ | :----------------------------------------------------------- | :--------- |
| S1.1 | **SQL Injection**   | Injicera `' OR 1=1 --` i login.       | "Felaktigt användarnamn". (PDO Prepared Statements skyddar). | ✅ Säker   |
| S1.2 | **XSS**             | Skriv `<script>` i namnfält.          | Koden visas som text (Escaped).                              | ✅ Säker   |
| S1.3 | **CSRF**            | Skicka formulär utan token.           | "Ogiltig token" (Funktionen hanterar null säkert).           | ✅ Säker   |
| S1.4 | **Behörighet**      | Gå till admin_dashboard.php som elev. | Omdirigerad till 403.php.                                    | ✅ Säker   |
| S1.5 | **URL Hackning**    | Försök nå låst nivå via URL.          | "Låst"-skärm visas.                                          | ✅ Säker   |
| S1.6 | **Säkerhet (403)**  | Nå admin-sida manuellt.               | Omdirigering till 403 fungerade. (Header-fix införd).        | ✅ Godkänd |
| S1.7 | **Session Timeout** | Inaktiv i 60 minuter.                 | Utloggning sker automatiskt.                                 | ✅ Godkänd |

---

## **4. Avvikelser & Förbättringsförslag (Bug Report)**

| ID    | Typ       | Beskrivning                                 | Prioritet | Status      | Åtgärd / Kommentar                                               |
| :---- | :-------- | :------------------------------------------ | :-------- | :---------- | :--------------------------------------------------------------- |
| B1    | Bug       | **Raderad Lärare:** Uppgifter tappar ägare. | Låg       | **Godkänd** | Filter för "Saknar lärare" och möjlighet att byta ägare tillagt. |
| B2    | UX        | **Bekräftelse:** Saknas vid "Slutför".      | Medel     | **Godkänd** | "Är du säker?"-ruta tillagd.                                     |
| B3    | UX        | **Scroll på mobil:** Drag-and-drop krockar. | Låg       | **Godkänd** | Infört "Drag Handles" (handtag) för att flytta objekt.           |
| B4    | Design    | **Admin-tabell:** Svårläst på mobil.        | Låg       | **Godkänd** | Omvandlat till "Card View" på mobil.                             |
| B5    | UX/Design | **Ocean Animation:** Psykedelisk effekt.    | Låg       | **Stängd**  | Beslut: Tog bort animation för statisk gradient.                 |
| B6    | Feature   | **Sortering mobil:** Krock med scroll.      | Medel     | **Godkänd** | Löst via Drag Handles.                                           |
| B7    | Innehåll  | **Tomma nivåer:** Flödet stannar.           | Låg       | **Öppen**   | Dashboard söker automatiskt nästa nivå, men innehåll krävs.      |
| F1    | Feature   | **Gamification:** Ljud/Konfetti.            | Medel     | **Backlog** | Framtida utveckling.                                             |
| F2    | Feature   | **Bilder:** Uppladdning till uppgifter.     | Medel     | **Backlog** | Framtida utveckling.                                             |
| Extra | Bug       | **Labels:** Saknade ID-kopplingar.          | Hög       | **Godkänd** | Alla formulär genomgångna och fixade.                            |

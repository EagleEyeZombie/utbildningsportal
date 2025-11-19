# **📋 Testrapport: Utbildningsportal (Gamification)**

Projekt: Utbildningsportal (Yrkesprov 2025\)  
Version: 1.1.0 (Post-MVP Bugfix)  
Datum: 2025-11-19  
Testare: Fredrich  
Miljö: Laragon (Lokal server), Chrome

## **1\. Funktionstester (Use-Case)**

Syftet är att verifiera att alla funktioner fungerar enligt kravspecifikationen.

### **1.1 Autentisering & Användare**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status och datum |
| :---- | :---- | :---- | :---- | :---- |
| F1.1.0 | **Registrering:** Skapa nytt elevkonto. | Kontot skapas, lösenordet hashas, omdirigeras till login. | Kontot skapas, lösenordet hashas, omdirigeras **inte** till login. | ❌ Ej godkänd, 18.11.2025 |
| F1.1.1 | **Registrering:** Skapa nytt elevkonto (Omtest). | Kontot skapas, lösenordet hashas, omdirigeras till login. | Kontot skapas, lösenordet hashas, omdirigeras till login med texten: "Ditt konto är nu skapat\! Logga in nedan." | ✅ Godkänd, 19.11.2025 |
| F1.2.0 | **Registrering:** Ange upptaget användarnamn. | Felmeddelande på svenska visas. | "Username already exists." (Engelska). | ❌ Ej godkänd, 18.11.2025 |
| F1.2.1 | **Registrering:** Ange upptaget användarnamn (Omtest). | Felmeddelande på svenska visas. | Felmeddelande på svenska visas: "Användarnamnet är redan upptaget." | ✅ Godkänd, 19.11.2025 |
| F1.3.0 | **Registrering:** Ange upptagen e-post. | Felmeddelande visas på svenska ("E-post upptagen"). | "Email already exists." (Engelska). | ❌ Ej godkänd, 18.11.2025 |
| F1.3.1 | **Registrering:** Ange upptagen e-post (Omtest). | Felmeddelande visas på svenska. | Felmeddelande på svenska visas: "E-postadressen används redan." | ✅ Godkänd, 19.11.2025 |
| F1.4 | **Login:** Logga in med Användarnamn. | Inloggning lyckas, omdirigeras till Dashboard. | Fungerar korrekt. | ✅ Godkänd, 18.11.2025 |
| F1.5 | **Login:** Logga in med E-post. | Inloggning lyckas, omdirigeras till Dashboard. | Fungerar korrekt. | ✅ Godkänd, 18.11.2025 |
| F1.6 | **Login:** Felaktigt lösenord. | Felmeddelande visas, ingen inloggning. | Felmeddelande visas: "Felaktigt användarnamn/e-post eller lösenord." | ✅ Godkänd, 18.11.2025 |
| F1.7 | **Logout:** Klicka på "Logga ut". | Sessionen avslutas, skickas till Login-sidan. | Fungerar korrekt. | ✅ Godkänd, 18.11.2025 |

### **1.2 Elev Dashboard & Progression**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status och datum |
| :---- | :---- | :---- | :---- | :---- |
| F2.1.0 | **Visning:** Välkomstpanel & Stats. | Namn, XP och Level visas korrekt. | Namn och XP visas korrekt men Level ändras inte. | ❌ Ej godkänd, 19.11.2025 |
| F2.1.1 | **Visning:** Välkomstpanel & Stats. | Namn, XP och Level visas korrekt. | *(Ej utförd)* |  |
| F2.2.0 | **Listning:** Visa uppgifter. | Alla uppgifter visas som kort. | Alla uppgifter syns. | ✅ Godkänd, 19.11.2025 |
| F2.3.0 | **Filter:** Filtrera på "Flervalsfrågor". | Endast Flervals-uppgifter visas. | Det finns ingen filtrering. | ❌ Ej godkänd, 19.11.2025 |
| F2.3.1 | **Filter:** Filtrera på "Flervalsfrågor". | Endast Flervals-uppgifter visas. | *(Ej utförd)* |  |
| F2.4.0 | **Filter:** Filtrera på "Sortering". | Endast Sorterings-uppgifter visas. | Det finns ingen filtrering. | ❌ Ej godkänd, 19.11.2025 |
| F2.4.1 | **Filter:** Filtrera på "Sortering". | Endast Sorterings-uppgifter visas. | *(Ej utförd)* |  |
| F2.5.0 | **Progression:** Låsta kapitel. | Kapitel med högre nivå än upplåst ska vara gråa/oklickbara. | Funktionen finns inte. | ❌ Ej godkänd, 19.11.2025 |
| F2.5.1 | **Progression:** Låsta kapitel. | Kapitel med högre nivå än upplåst ska vara gråa/oklickbara. | *(Ej utförd)* |  |

### **1.3 Genomförande av Uppgifter (Quiz)**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status och datum |
| :---- | :---- | :---- | :---- | :---- |
| F3.1 | **Flerval:** Starta uppgift. | "Wizard" startar, texten visas först. | Flödet fungerar. | ✅ Godkänd, 19.11.2025 |
| F3.2 | **Sant/Falskt:** Starta uppgift. | Visar påstående och Sant/Falskt-knappar. | Visas korrekt. | ✅ Godkänd, 19.11.2025 |
| F3.3 | **Sortering:** Drag-and-Drop. | Går att dra meningar och byta plats på dem. | Sortering fungerar smidigt. | ✅ Godkänd, 19.11.2025 |
| F3.4 | **Rättning:** \< 70% rätt. | Resultatsida visar "Försök igen", inga XP delas ut. | Rättar korrekt. | ✅ Godkänd, 19.11.2025 |
| F3.5 | **Rättning:** \> 70% rätt. | Resultatsida visar "Bra jobbat", XP sparas i DB. | Rättar korrekt. | ✅ Godkänd, 19.11.2025 |
| F3.6 | **Uppdatering:** Dashboard efter klarad uppgift. | Kortet får grön ram, badge "Klarad". | Status uppdateras direkt. | ✅ Godkänd, 19.11.2025 |

### **1.4 Adminpanel**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status och datum |
| :---- | :---- | :---- | :---- | :---- |
| F4.1 | **Åtkomst:** Elev försöker nå admin. | Omdirigeras till dashboard.php. | Omdirigering fungerar. | ✅ Godkänd, 19.11.2025 |
| F4.2 | **CRUD:** Skapa uppgift (Alla typer). | Uppgiften sparas med korrekt JSON. | Uppgifter skapas korrekt. | ✅ Godkänd, 19.11.2025 |
| F4.3 | **CRUD:** Redigera uppgift. | Formuläret fylls i, ändringar sparas. | Redigering fungerar. | ✅ Godkänd, 19.11.2025 |
| F4.4 | **CRUD:** Ta bort uppgift. | Uppgift \+ resultat raderas (Cascade). | Radering fungerar. | ✅ Godkänd, 19.11.2025 |
| F4.5 | **Filter:** Admin-filter. | Kan filtrera på Lärare, Typ, Nivå, Klass. | Filtrering fungerar. | ✅ Godkänd, 19.11.2025 |

## **2\. Responsivitetstestning**

Testat via Chrome DevTools samt fysisk mobil.

| Sida | Mobil (375x667) | Surfplatta (768x1024) | Desktop (1920x1080) | Status | Datum |
| :---- | :---- | :---- | :---- | :---- | :---- |
| **Login** | Staplas snyggt. | OK. | Centrerat kort. | ✅ Godkänd | 19.11.2025 |
| **Dashboard** | 1 kolumn. | 2 kolumner. | 3 kolumner. | ✅ Godkänd | 19.11.2025 |
| **Uppgift** | Drag-and-drop fungerar med touch. | OK. | OK. | ✅ Godkänd | 19.11.2025 |
| **Admin** | Tabellen får scrollbar i sidled. | OK. | OK. | ⚠️ Godkänd med anmärkning | 19.11.2025 |

## **3\. Säkerhetstestning**

| ID | Testbeskrivning | Metod | Resultat | Status | Datum |
| :---- | :---- | :---- | :---- | :---- | :---- |
| S1 | **SQL Injection** | Injicera ' OR 1=1 \-- i login. | Misslyckades. | ✅ Säker | 19.11.2025 |
| S2 | **XSS** | Skriv \<script\> i namnfält. | Koden visas som text. | ✅ Säker | 19.11.2025 |
| S3 | **CSRF** | Skicka formulär utan token. | "Ogiltig token". | ✅ Säker | 19.11.2025 |
| S4 | **Behörighet** | Gå till admin\_dashboard.php som elev. | Omdirigerad. | ✅ Säker | 19.11.2025 |

## **4\. Avvikelser & Förbättringsförslag (Bug Report)**

Här listas kända problem och förslag för framtida versioner (v1.1).

| ID | Typ | Beskrivning | Prioritet | Status |
| :---- | :---- | :---- | :---- | :---- |
| B1 | **Bug** | **Raderad Lärare:** Om en lärare raderas, sätts t\_teacher\_fk till NULL på deras uppgifter. Dessa uppgifter syns då inte i filtret "Visa Bara Mina" för någon admin. | Låg | Öppen |
| B2 | **UX** | **Bekräftelse:** Det saknas en "Är du säker?"-dialogruta när eleven klickar på "Slutför och Rätta". | Medel | Öppen |
| B3 | **UX** | **Scroll på mobil:** Vid långa sorteringsövningar på mobil måste man scrolla mycket, och drag-funktionen kan ibland krocka med scrollen. | Låg | Öppen |
| B4 | **Design** | **Admin-tabell:** På mycket små skärmar (iPhone SE) kan admin-tabellen bli lite svårläst även med scroll. Vissa kolumner borde kanske döljas. | Låg | Öppen |
| F1 | **Feature** | **Gamification:** Lägga till ljud-effekter eller konfetti-animation när man klarar en nivå. | Medel | Backlog |
| F2 | **Feature** | **Bilder:** Möjlighet för lärare att ladda upp en bild till varje uppgift (t.ex. en karta för sagan). | Medel | Backlog |


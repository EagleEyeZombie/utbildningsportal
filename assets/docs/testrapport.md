# **Testrapport: Utbildningsportal (Gamification)**

Version: 1.0.0 (MVP)  
Datum: 2025-11-14  
Testare: Fredrich

## **1\. Funktionstester (Use-Case)**

### **1.1 Autentisering & Användare**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status |
| :---- | :---- | :---- | :---- | :---- |
| F1.1 | **Registrering:** Skapa nytt elevkonto. | Kontot skapas, lösenordet hashas, omdirigeras till login. | Fungerar som förväntat. | ✅ Godkänd |
| F1.2 | **Registrering:** Ange upptagen e-post. | Felmeddelande visas ("E-post upptagen"). | Felmeddelande visas korrekt. | ✅ Godkänd |
| F1.3 | **Login:** Logga in med Användarnamn. | Inloggning lyckas, omdirigeras till Dashboard. | Fungerar korrekt. | ✅ Godkänd |
| F1.4 | **Login:** Logga in med E-post. | Inloggning lyckas, omdirigeras till Dashboard. | Fungerar korrekt. | ✅ Godkänd |
| F1.5 | **Login:** Felaktigt lösenord. | Felmeddelande visas, ingen inloggning. | Felmeddelande visas. | ✅ Godkänd |
| F1.6 | **Logout:** Klicka på "Logga ut". | Sessionen avslutas, skickas till Login-sidan. | Fungerar korrekt. | ✅ Godkänd |

### **1.2 Elev Dashboard & Progression**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status |
| :---- | :---- | :---- | :---- | :---- |
| F2.1 | **Visning:** Välkomstpanel & Stats. | Namn, XP och Level visas korrekt. | Visas korrekt. | ✅ Godkänd |
| F2.2 | **Listning:** Visa uppgifter. | Alla uppgifter visas som kort. | Alla uppgifter syns. | ✅ Godkänd |
| F2.3 | **Filter:** Filtrera på "Flervalsfrågor". | Endast Flervals-uppgifter visas. | Filtrering fungerar. | ✅ Godkänd |
| F2.4 | **Filter:** Filtrera på "Sortering". | Endast Sorterings-uppgifter visas. | Filtrering fungerar. | ✅ Godkänd |
| F2.5 | **Progression:** Låsta kapitel. | Kapitel med högre nivå än upplåst ska vara gråa/oklickbara. | Lås-ikon visas, knappen inaktiv. | ✅ Godkänd |

### **1.3 Genomförande av Uppgifter (Quiz)**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status |
| :---- | :---- | :---- | :---- | :---- |
| F3.1 | **Flerval:** Starta uppgift. | "Wizard" startar, texten visas först. | Flödet fungerar. | ✅ Godkänd |
| F3.2 | **Sant/Falskt:** Starta uppgift. | Visar påstående och Sant/Falskt-knappar. | Visas korrekt. | ✅ Godkänd |
| F3.3 | **Sortering:** Drag-and-Drop. | Går att dra meningar och byta plats på dem. | Sortering fungerar smidigt. | ✅ Godkänd |
| F3.4 | **Rättning:** \< 70% rätt. | "Försök igen", inga XP delas ut. | Rättar korrekt. | ✅ Godkänd |
| F3.5 | **Rättning:** \> 70% rätt. | "Bra jobbat", XP sparas i DB. | Rättar korrekt. | ✅ Godkänd |
| F3.6 | **Uppdatering:** Dashboard efter klarad uppgift. | Kortet får grön ram, badge "Klarad". | Status uppdateras direkt. | ✅ Godkänd |

### **1.4 Adminpanel**

| ID | Testbeskrivning | Förväntat Resultat | Faktiskt Resultat | Status |
| :---- | :---- | :---- | :---- | :---- |
| F4.1 | **Åtkomst:** Elev försöker nå admin. | Omdirigeras till dashboard.php. | Omdirigering fungerar. | ✅ Godkänd |
| F4.2 | **CRUD:** Skapa uppgift (Alla typer). | Uppgiften sparas med korrekt JSON. | Uppgifter skapas korrekt. | ✅ Godkänd |
| F4.3 | **CRUD:** Redigera uppgift. | Formuläret fylls i, ändringar sparas. | Redigering fungerar. | ✅ Godkänd |
| F4.4 | **CRUD:** Ta bort uppgift. | Uppgift \+ resultat raderas (Cascade). | Radering fungerar. | ✅ Godkänd |
| F4.5 | **Filter:** Admin-filter. | Kan filtrera på Lärare, Typ, Nivå, Klass. | Filtrering fungerar. | ✅ Godkänd |

## **2\. Responsivitetstestning**

Testat via Chrome DevTools samt fysisk mobil.

| Sida | Mobil (375x667) | Surfplatta (768x1024) | Desktop (1920x1080) | Status |
| :---- | :---- | :---- | :---- | :---- |
| **Login** | Staplas snyggt. | OK. | Centrerat kort. | ✅ Godkänd |
| **Dashboard** | 1 kolumn. | 2 kolumner. | 3 kolumner. | ✅ Godkänd |
| **Uppgift** | Drag-and-drop fungerar med touch. | OK. | OK. | ✅ Godkänd |
| **Admin** | Tabellen får scrollbar i sidled. | OK. | OK. | ⚠️ Godkänd med anmärkning |

## **3\. Säkerhetstestning**

| ID | Testbeskrivning | Metod | Resultat | Status |
| :---- | :---- | :---- | :---- | :---- |
| S1 | **SQL Injection** | Injicera ' OR 1=1 \-- i login. | Misslyckades. | ✅ Säker |
| S2 | **XSS** | Skriv \<script\> i namnfält. | Koden visas som text. | ✅ Säker |
| S3 | **CSRF** | Skicka formulär utan token. | "Ogiltig token". | ✅ Säker |
| S4 | **Behörighet** | Gå till admin\_dashboard.php som elev. | Omdirigerad. | ✅ Säker |

## **4\. Avvikelser & Förbättringsförslag (Bug Report)**

Här listas kända problem och förslag för framtida versioner (v1.1).

| ID | Typ | Beskrivning | Prioritet | Status |
| :---- | :---- | :---- | :---- | :---- |
| B1 | **Bug** | **Raderad Lärare:** Om en lärare raderas, sätts t\_teacher\_fk till NULL på deras uppgifter. Dessa uppgifter syns då inte i filtret "Visa Bara Mina" för någon admin, och kan bli svåra att hitta. | Låg | Öppen |
| B2 | **UX** | **Bekräftelse:** Det saknas en "Är du säker?"-dialogruta när eleven klickar på "Slutför och Rätta". Man kan råka klicka av misstag innan man sorterat klart. | Medel | Öppen |
| B3 | **UX** | **Scroll på mobil:** Vid långa sorteringsövningar på mobil måste man scrolla mycket, och drag-funktionen kan ibland krocka med scrollen. | Låg | Öppen |
| B4 | **Design** | **Admin-tabell:** På mycket små skärmar (iPhone SE) kan admin-tabellen bli lite svårläst även med scroll. Vissa kolumner borde kanske döljas. | Låg | Öppen |
| F1 | **Feature** | **Gamification:** Lägga till ljud-effekter eller konfetti-animation när man klarar en nivå för att öka belöningskänslan. | Medel | Backlog |
| F2 | **Feature** | **Bilder:** Möjlighet för lärare att ladda upp en bild till varje uppgift (t.ex. en karta för sagan). | Medel | Backlog |


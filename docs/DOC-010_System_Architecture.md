# DOC-010: System Architecture Document
**Project:** SmartQueue Health & MedReminder (SQHM)  
**Target:** Ethiopian Healthcare Facilities (Tertiary, Secondary, and Primary Health Centers)  
**Architecture Pattern:** Modular Monolith with Offline Edge Synchronization  

---

## 1. Architectural Philosophy
SmartQueue Health is designed as a **Modular Monolith** in Laravel (PHP 8.4) with clean domain boundaries, rather than dozens of distributed microservices. This avoids high operational overhead in resource-constrained hospital server environments while allowing modular scaling and independent service extraction when required.

### Key Layers:
1. **Presentation Layer:**
   - **Vue 3 Single Page Application (SPA):** For hospital staff (Reception, Triage Nurses, Doctors, Pharmacists, Lab Technicians, Cashiers, Medical Directors).
   - **Public TV Waiting Display:** Web Audio chime synthesizer and text-to-speech voice announcements for calling patients to consulting rooms and counters.
   - **Flutter Mobile Application:** Cross-platform Android/iOS client for patients and community health extension workers, equipped with local SQLite caching.
   - **SMS Service Gateway:** Accessible via 2G/feature phones for patients without smartphones.

2. **Application & Business Layer (Laravel 11/12):**
   - **Authentication & RBAC:** Laravel Sanctum token-based authentication with role-based policies (Doctor, Nurse, Pharmacist, Lab Tech, Cashier, Receptionist, Admin, Patient).
   - **Queue Engine Service:** Ticket sequence generation, dynamic estimated wait-time calculation, and queue status lifecycle management.
   - **EMR Clinical Service:** Unique Ethiopian Medical Record Number (MRN) generation, duplicate detection, vital signs recording, and SOAP clinical consultation notes.
   - **MedReminder Service:** Automatic daily dose timetable derivation from prescriptions, dose logging, and SMS reminder dispatch.
   - **Ethiopian Payment Service:** Telebirr, CBE Birr, Cash, and CBHI insurance claim processing.
   - **AI Safety Service:** Triage guardrails, multilingual NLP (English & Amharic), and red-flag emergency escalation.

3. **Persistence & Infrastructure Layer:**
   - **Relational Database:** SQLite (local edge / development) and PostgreSQL (centralized cloud / cluster).
   - **Cache & Queue Processing:** Redis for queue broadcasting and job dispatching.
   - **Offline Edge Storage:** SQLite embedded engine inside Flutter mobile devices.

4. **National Interoperability Layer:**
   - **Master Facility Register (MFR):** Verification of Ethiopian healthcare institutions against national codes (e.g. `MFR-AA-001`).
   - **DHIS2 Interoperability:** Automated aggregation and export of monthly OPD, Emergency, and mortality indicators.
   - **NDHx HL7 FHIR Adapters:** Bundles encounters, patients, and diagnoses into standard FHIR JSON for the Ethiopian National Digital Health Exchange.

---

## 2. Multi-Hospital Logical Isolation
Each participating hospital operates as a logically isolated facility:
- Dedicated departments (`facility_id`)
- Dedicated employee roster (`employees`)
- Local department queue tickets and room numbers
- Independent inventory stock batches and price lists
- Patient records are identified globally via unique MRN and Fayda National ID, with access restricted by facility authorization policies.

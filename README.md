# SmartQueue Health & MedReminder (SQHM)
### Integrated Digital Healthcare and Hospital Operations Platform
**Target Country:** Ethiopia 🇪🇹 • Scalable Across Africa 🌍  
**Repository:** [https://github.com/elsadmasu-code/SmartQueue-Health](https://github.com/elsadmasu-code/SmartQueue-Health)

---

## 🏥 Platform Overview
**SmartQueue Health & MedReminder (SQHM)** is an enterprise healthcare platform engineered to transform manual, paper-based hospital workflows into a unified, digital ecosystem while maintaining robust operations in environments with limited internet connectivity.

### Key Capabilities
- **Smart Queue Management:** Color-coded triage (Red Emergency, Yellow Urgent, Green Regular), dynamic wait-time estimation, and audible public TV calling screens.
- **Electronic Medical Records (EMR):** Ethiopian MRN generator, SOAP clinical notes, ICD-10 diagnostic coding, and longitudinal patient health histories.
- **MedReminder & Adherence Engine:** Prescription-linked daily dose timetables, SMS alerts via Ethio Telecom gateway, and patient compliance tracking.
- **Central Pharmacy & Inventory:** Stock batches, expiry monitoring, e-prescriptions, and automated inventory deduction.
- **Pathology Laboratory & Radiology:** Specimen barcode tracking, verified diagnostic results, and medical imaging reports.
- **Cashier & Billing Desk:** Invoices and multi-channel payment settlements via **Telebirr**, **CBE Birr**, Cash, and **Ethiopian CBHI** (Community Based Health Insurance).
- **Inpatient & Ward Bed Matrix:** Real-time bed occupancy visualization across Male Medical, Female Surgical, and ICU wards.
- **AI Health Assistant & Triage Navigator:** Multilingual (English & Amharic አማርኛ) with automatic emergency escalation to emergency hotlines (907 / 939 / 911).
- **National Interoperability:** Aligned with Ethiopian Master Facility Register (MFR), DHIS2 monthly indicator exports, and HL7 FHIR encounter bundles.
- **Offline-First Flutter Mobile App:** SQLite local database caching with automated batch synchronization.

---

## 🏛 System Architecture

```
                               ┌────────────────────────────────────────┐
                               │   Patients & Healthcare Stakeholders   │
                               └───────────────────┬────────────────────┘
                                                   │
                ┌──────────────────────────────────┼──────────────────────────────────┐
                │                                  │                                  │
     ┌──────────▼──────────┐            ┌──────────▼──────────┐            ┌──────────▼──────────┐
     │   Vue 3 Web SPA     │            │ Flutter Mobile App  │            │   Public TV Screen  │
     │ Hospital Operations │            │  Online / Offline   │            │ Queue Calling Board │
     └──────────┬──────────┘            └──────────┬──────────┘            └──────────┬──────────┘
                │                                  │                                  │
                └──────────────────────────────────┼──────────────────────────────────┘
                                                   │ HTTPS / REST
                                        ┌──────────▼──────────┐
                                        │ Laravel REST API    │
                                        │ Domain Services     │
                                        └──────────┬──────────┘
                                                   │
        ┌───────────────────┬──────────────────────┼──────────────────────┬───────────────────┐
        │                   │                      │                      │                   │
┌───────▼────────┐  ┌───────▼────────┐     ┌───────▼────────┐     ┌───────▼────────┐  ┌───────▼────────┐
│   PostgreSQL/  │  │  Redis Cache & │     │  Ethio Telecom │     │   Telebirr &   │  │ Ethiopian MFR  │
│ SQLite Storage │  │  Queue Worker  │     │   SMS Gateway  │     │    CBE Birr    │  │ & DHIS2 Interop│
└────────────────┘  └────────────────┘     └────────────────┘     └────────────────┘  └────────────────┘
```

---

## 📂 Project Directory Structure

```text
SmartQueue-Health/
├── backend/                  # Laravel 11/12 REST API Core
│   ├── app/
│   │   ├── Http/Controllers/Api/  # Auth, Queue, EMR, Pharmacy, Lab, Billing, Interop, AI
│   │   ├── Models/                # 24+ Eloquent Models with relationships
│   │   └── Services/              # QueueService, EmrService, MedReminderService, PaymentService, etc.
│   ├── database/
│   │   ├── migrations/            # 13 Schema migrations for hospital operations
│   │   └── seeders/               # Ethiopian demo data (Tikur Anbessa Hospital, staff, patients)
│   ├── routes/api.php             # 54 REST API endpoints
│   └── Dockerfile
├── frontend/                 # Vue 3 + Tailwind CSS Single Page Application
│   ├── src/
│   │   ├── views/                 # Dashboard, QueueBoard, Reception, Triage, Doctor, Pharmacy, etc.
│   │   ├── locales/               # English (en.js) and Amharic (am.js) dictionaries
│   │   ├── api/client.js          # Axios API client with token interceptor
│   │   └── App.vue                # Main layout with navigation bar and language toggle
│   ├── index.html
│   ├── vite.config.js
│   └── Dockerfile
├── mobile/                   # Flutter Cross-Platform Mobile Client
│   ├── lib/
│   │   ├── screens/               # Home, QueueTracker, MedReminder, Chatbot, OfflineSync
│   │   ├── services/              # Offline SQLite Database & Sync Engine
│   │   ├── models/                # Dart entity models
│   │   └── localization/          # Bilingual Ethiopian strings
│   └── pubspec.yaml
├── docs/                     # Formal Documentation Register (DOC-001 to DOC-112)
├── docker-compose.yml        # Multi-container deployment orchestrator
└── README.md
```

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- **PHP 8.2+** with `pdo_sqlite` or `pdo_pgsql`
- **Node.js 18+** & npm
- **Composer 2+**
- (Optional) **Flutter SDK 3+** for mobile app development
- (Optional) **Docker & Docker Compose**

---

### 2. Backend Setup (Laravel REST API)
```bash
cd backend

# 1. Environment configuration
copy .env.example .env

# 2. Database migration & demo seeding
php -d extension=pdo_sqlite -d extension=sqlite3 artisan migrate:fresh --seed

# 3. Start API server
php -d extension=pdo_sqlite -d extension=sqlite3 artisan serve --port=8000
```
> The API will be accessible at: `http://127.0.0.1:8000/api`  
> Health check: `http://127.0.0.1:8000/api/health`

#### Pre-seeded Demo Credentials:
| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@smartqueue.et` | `password123` |
| **Doctor** | `doctor@smartqueue.et` | `password123` |
| **Nurse** | `nurse@smartqueue.et` | `password123` |
| **Pharmacist** | `pharmacist@smartqueue.et` | `password123` |
| **Lab Technician** | `lab@smartqueue.et` | `password123` |
| **Cashier** | `cashier@smartqueue.et` | `password123` |
| **Patient** | `patient@smartqueue.et` | `password123` |

---

### 3. Frontend Setup (Vue 3 Portal)
```bash
cd frontend

# 1. Install dependencies
npm install

# 2. Start Vite development server
npm run dev
# or build for production
npm run build
```
> The web application will launch at: `http://localhost:5173`

---

### 4. Flutter Mobile App Setup
```bash
cd mobile

# 1. Get dependencies
flutter pub get

# 2. Run on connected Android / iOS device or emulator
flutter run
```

---

### 5. Running with Docker Compose
To launch the complete platform (Backend API + Vue 3 Web Portal):
```bash
docker-compose up --build -d
```
- Web Portal: `http://localhost:5173`
- Backend API: `http://localhost:8000/api`

---

## 📡 Key REST API Endpoints

| Category | Method | Endpoint | Description |
|---|---|---|---|
| **System** | `GET` | `/api/health` | System health status & timestamp |
| **Auth** | `POST` | `/api/auth/login` | Login with email/phone & token issuance |
| **Queue** | `GET` | `/api/facilities/{id}/queue/board` | Live public waiting monitor & called tickets |
| **Queue** | `POST` | `/api/queue/issue` | Issue queue ticket with triage classification |
| **Queue** | `POST` | `/api/queue/{id}/call` | Call ticket to room or counter |
| **EMR** | `GET` | `/api/patients` | Search patients by MRN, Fayda ID, or phone |
| **EMR** | `POST` | `/api/encounters` | Record clinical encounter, vitals, & SOAP notes |
| **Pharmacy** | `GET` | `/api/pharmacy/prescriptions`| List prescriptions & dispensing queue |
| **Pharmacy** | `POST` | `/api/pharmacy/prescriptions/{id}/dispense` | Fulfill prescription & adjust stock batches |
| **MedReminder** | `GET` | `/api/patients/{id}/med-reminders` | Active medication timetables & adherence % |
| **MedReminder** | `POST` | `/api/med-reminders/log-dose` | Record dose taken / missed |
| **Billing** | `POST` | `/api/billing/invoices/{id}/pay` | Settle bill via Telebirr, CBE Birr, or Cash |
| **AI Navigator**| `POST` | `/api/chatbot/message` | Multilingual healthcare AI with triage safety |
| **Offline Sync**| `POST` | `/api/sync/offline-batch` | Batch process offline-queued mobile transactions |
| **Interop** | `GET` | `/api/interop/mfr/verify` | Verify facility against Ethiopian MFR register |
| **Interop** | `GET` | `/api/facilities/{id}/interop/dhis2` | Generate DHIS2 monthly indicator reports |

---

## 📜 Compliance & Documentation
Detailed architectural blueprints, business rules (BR-001 to BR-015), functional specifications, and data migration plans are cataloged in [`docs/DOC_REGISTER.md`](docs/DOC_REGISTER.md).

Developed for the advancement of digital health infrastructure across Ethiopia and Africa.

# DOC-035: REST API Reference & Specification
**Base URL:** `http://127.0.0.1:8000/api`  
**Authentication:** Bearer Token (`Authorization: Bearer <token>`) via Laravel Sanctum  
**Content-Type:** `application/json`  

---

## 1. System & Authentication

### `GET /api/health`
- **Description:** System health check and server time.
- **Response:** `200 OK`
  ```json
  {
    "status": "healthy",
    "system": "SmartQueue Health & MedReminder",
    "version": "1.0.0",
    "time": "2026-11-17T08:00:00Z"
  }
  ```

### `POST /api/auth/login`
- **Request Body:**
  ```json
  { "email": "doctor@smartqueue.et", "password": "password123" }
  ```
- **Response:** `200 OK` (returns `user` object and `token`).

---

## 2. Queue Operations

### `GET /api/facilities/{id}/queue/board`
- **Description:** Live public queue monitor data for TV screens.
- **Response:** `200 OK`
  - `called_now`: Array of tickets currently called to rooms.
  - `waiting`: Array of waiting tickets with estimated wait times.

### `POST /api/queue/issue`
- **Request Body:**
  ```json
  {
    "facility_id": 1,
    "department_id": 1,
    "patient_id": 1,
    "queue_type": "walk_in",
    "priority_level": "normal",
    "triage_category": "green"
  }
  ```
- **Response:** `201 Created` with issued `ticket_number` (e.g. `OPD-005`).

### `POST /api/queue/{id}/call`
- **Request Body:** `{ "room_or_counter": "OPD Room 104" }`
- **Response:** `200 OK` (ticket status updated to `called`).

---

## 3. Patients & EMR

### `GET /api/patients?search=Abebe`
- **Description:** Search patients by name, phone, Fayda National ID, or MRN.

### `POST /api/encounters`
- **Request Body:**
  ```json
  {
    "facility_id": 1,
    "patient_id": 1,
    "department_id": 1,
    "encounter_type": "opd",
    "chief_complaint": "Follow up on blood pressure",
    "vital_signs": {
      "bp_systolic": 130,
      "bp_diastolic": 85,
      "heart_rate": 72,
      "temperature": 36.6,
      "spo2": 98
    },
    "clinical_note": {
      "subjective": "Feeling well",
      "objective": "Clear lungs, no edema",
      "assessment": "Controlled hypertension",
      "plan": "Continue current dosage"
    }
  }
  ```

---

## 4. Pharmacy & MedReminder

### `POST /api/pharmacy/prescriptions`
- **Description:** Transmits e-prescription and automatically creates patient MedReminder schedules.

### `POST /api/med-reminders/log-dose`
- **Request Body:**
  ```json
  {
    "medication_schedule_id": 1,
    "patient_id": 1,
    "status": "taken",
    "patient_notes": "Taken with breakfast"
  }
  ```

---

## 5. Billing & Payments

### `POST /api/billing/invoices/{id}/pay`
- **Request Body:**
  ```json
  {
    "amount": 280.00,
    "payment_method": "telebirr",
    "customer_phone": "+251911223344"
  }
  ```
- **Response:** `200 OK` with transaction reference (e.g. `TB-XXXXXXXX`).

---

## 6. AI Healthcare Chatbot

### `POST /api/chatbot/message`
- **Request Body:**
  ```json
  { "message": "When is my next medication due?", "language": "en" }
  ```
- **Emergency Safeguard:** Automatically escalates red-flag symptoms (e.g. "severe chest pain") to immediate emergency triage.

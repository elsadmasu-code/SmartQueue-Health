# DOC-011: Core Database Design & Entity Dictionary
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**DBMS:** PostgreSQL / SQLite (Edge Offline)  
**ORM:** Laravel Eloquent  

---

## 1. Major Domain Entities

### A. Facilities & Organization
- `facilities`: Primary healthcare institutions cataloged under the Ethiopian Master Facility Register (MFR).
  - Attributes: `id`, `mfr_code`, `name`, `name_amharic`, `type`, `tier`, `region`, `city`, `subcity_woreda`, `kebele`, `phone`, `email`, `offline_sync_enabled`, `settings`.
- `departments`: Functional hospital service units.
  - Attributes: `id`, `facility_id`, `name`, `name_amharic`, `code` (`OPD`, `EMG`, `LAB`, `PHARM`, `RAD`, `INPAT`, `CASH`), `location_floor_or_room`, `is_active`.
- `employees`: Healthcare professionals, physicians, nurses, pharmacists, and administrative staff.
  - Attributes: `id`, `facility_id`, `department_id`, `user_id`, `employee_code`, `first_name`, `last_name`, `role`, `specialization`, `license_number`, `room_number`, `shift`, `is_on_duty`.

### B. Patient Identity & Medical Records
- `patients`: Longitudinal patient demographic profile.
  - Attributes: `id`, `user_id`, `registered_facility_id`, `mrn` (e.g. `SQH-AA-2026-00001`), `national_id` (Fayda ID), `cbhi_number` (Community Based Health Insurance), `first_name`, `father_name`, `grandfather_name`, `amharic_full_name`, `date_of_birth`, `age_years`, `gender`, `blood_group`, `phone_number`, `allergies` (JSON), `chronic_conditions` (JSON), `preferred_language`.
- `encounters`: Clinical outpatient, emergency, or inpatient visits.
  - Attributes: `id`, `facility_id`, `patient_id`, `doctor_id`, `department_id`, `queue_ticket_id`, `encounter_type`, `chief_complaint`, `triage_category`, `vital_signs` (JSON: `bp_systolic`, `bp_diastolic`, `heart_rate`, `temperature`, `spo2`, `respiratory_rate`, `weight_kg`, `height_cm`, `bmi`), `status`.
- `clinical_notes`: Physician consultation notes using the SOAP standard.
  - Attributes: `id`, `encounter_id`, `patient_id`, `doctor_id`, `subjective`, `objective`, `assessment`, `plan`, `icd10_codes` (JSON), `follow_up_date`, `is_signed`, `signed_at`.

### C. Smart Queue Management
- `queue_tickets`: Digital service ticket for each department visit.
  - Attributes: `id`, `facility_id`, `department_id`, `patient_id`, `doctor_id`, `ticket_number` (e.g. `OPD-001`, `LAB-005`), `queue_type` (`walk_in`, `appointment`, `emergency`, `follow_up`), `priority_level` (`normal`, `urgent`, `emergency`), `triage_category` (`red`, `yellow`, `green`, `blue`), `status` (`waiting`, `called`, `in_service`, `completed`, `skipped`, `cancelled`), `counter_or_room`, `estimated_wait_minutes`, `called_at`, `service_started_at`, `service_ended_at`.
- `queue_events`: Event transition audit trail for wait-time analytics.
  - Attributes: `id`, `queue_ticket_id`, `from_status`, `to_status`, `action_by`, `room_or_counter`, `occurred_at`.

### D. Pharmacy, Prescriptions & MedReminder
- `medications`: Hospital essential drug formulary list based on Ethiopian EPSS catalogs.
  - Attributes: `id`, `code`, `generic_name`, `brand_name`, `category`, `dosage_form`, `strength`, `unit_price`, `reorder_level`.
- `stock_batches`: Batch and expiration tracking per facility.
  - Attributes: `id`, `facility_id`, `medication_id`, `batch_number`, `quantity_in_stock`, `expiry_date`, `unit_price`, `supplier`.
- `prescriptions` & `prescription_items`: Digital prescriptions with dosage, frequency, and instructions.
- `medication_schedules`: MedReminder daily timetable slots.
  - Attributes: `id`, `patient_id`, `prescription_item_id`, `medicine_name`, `dosage`, `scheduled_time` (`08:00`, `14:00`, `20:00`), `frequency`, `instructions`, `sms_reminder_enabled`, `is_active`.
- `medication_logs`: Intake compliance history.
  - Attributes: `id`, `medication_schedule_id`, `patient_id`, `scheduled_at`, `taken_at`, `status` (`taken`, `missed`, `skipped`), `patient_notes`.

### E. Financial & Payment Settlements
- `invoices` & `invoice_items`: Itemized service bills for consultations, lab tests, medications, and ward admissions.
- `payments`: Digital and cash transaction records.
  - Attributes: `id`, `invoice_id`, `facility_id`, `patient_id`, `cashier_id`, `amount`, `payment_method` (`telebirr`, `cbe_birr`, `cash`, `insurance`), `transaction_reference`, `receipt_number`, `status`.
- `insurance_claims`: Verification and claims for Ethiopian Community Based Health Insurance (CBHI).

### F. Offline Synchronization & Interoperability
- `offline_sync_records`: Staged transactions queued from mobile apps.
  - Attributes: `id`, `device_id`, `facility_id`, `entity_type`, `client_uuid`, `server_record_id`, `sync_status`, `payload`, `client_timestamp`, `server_synced_at`.
- `interoperability_records`: Outbound data sync logs for MFR, DHIS2, and NDHx.

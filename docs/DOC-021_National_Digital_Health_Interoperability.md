# DOC-021: National Digital Health Interoperability Specification
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Authority:** Federal Ministry of Health (MOH), Ethiopia  
**Standards:** HL7 FHIR R4, ICD-10, LOINC, National Health Data Dictionary (NHDD)  

---

## 1. Master Facility Register (MFR) Integration
- **Endpoint:** `GET /api/interop/mfr/verify?mfr_code=MFR-AA-001`
- **Verification Logic:**
  - Queries facility profile against the national registry (55,530+ registered facilities across Ethiopia).
  - Validates operational status, regional ownership, and hospital accreditation tier (Tertiary Specialized, General Hospital, Primary Hospital, Health Center).

---

## 2. DHIS2 Monthly Aggregate Reporting
- **Endpoint:** `GET /api/facilities/{id}/interop/dhis2?year=2026&month=11`
- **Output:** Structured DHIS2 JSON indicators for automated morbidity, mortality, and throughput reporting:
  ```json
  {
    "orgUnit": "MFR-AA-001",
    "period": "202611",
    "dataValues": [
      { "dataElement": "OPD_ATTENDANCE_TOTAL", "value": 1420 },
      { "dataElement": "EMERGENCY_ATTENDANCE_TOTAL", "value": 310 },
      { "dataElement": "SMARTQUEUE_TICKETS_ISSUED", "value": 2450 },
      { "dataElement": "AVG_WAITING_TIME_MINUTES", "value": 13.8 }
    ]
  }
  ```

---

## 3. National Digital Health Exchange (NDHx) & HL7 FHIR
- **Endpoint:** `GET /api/encounters/{id}/interop/fhir`
- **Format:** HL7 FHIR `Bundle` with `Patient` and `Encounter` resources containing:
  - Patient identifiers (`urn:ethiopia:moh:mrn`, `urn:ethiopia:fayda:national_id`)
  - Encounter classification (`opd`, `emergency`, `inpatient`)
  - Diagnoses coded with ICD-10 (`E11.9`, `I10`, etc.)
  - Facility identifier linked to official MFR code.

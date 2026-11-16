# SmartQueue Health & MedReminder (SQHM) — Master Documentation Register (DOC-001 – DOC-112)

## Overview & Scope
SmartQueue Health & MedReminder (SQHM) is an enterprise integrated digital healthcare and hospital management platform designed for Ethiopia and the African continent, supporting both connected and limited-connectivity / offline environments.

---

### PART I: PROJECT FOUNDATION (DOC-001 – DOC-004)
- **DOC-001: Project Charter**
  - **Vision:** Accessible, intelligent, secure, and integrated digital healthcare ecosystem that replaces manual hospital operations across Ethiopia.
  - **Core Objectives:** Digitize paper registers, reduce hospital waiting queues, automate MedReminder schedules, and support Ethiopian digital health interoperability.
- **DOC-002: Problem Statement**
  - Targets 16 manual hospital activities: handwritten registers, physical patient cards, manual queue numbers, paper prescriptions, lab result slips, physical receipts, etc.
- **DOC-003: Project Scope**
  - Full outpatient, emergency, inpatient, nursing, pharmacy, laboratory, radiology, cashier/billing, and MedReminder operations.
  - Out of scope: Autonomous AI diagnosis without certified clinical review.
- **DOC-004: Target Stakeholders & Role Matrix**
  - Super Administrator, Ministry/Oversight, Hospital Administrator, Medical Director, Doctor, Nurse, Pharmacist, Lab Technician, Radiologist, Cashier, Patient.

---

### PART II: ETHIOPIAN HEALTHCARE CONTEXT & REGULATION (DOC-005 – DOC-006)
- **DOC-005: National Healthcare Context & Existing Digital Systems**
  - Integrated with the **Ethiopian Master Facility Register (MFR)** (55,530+ facilities cataloged).
  - Interoperability with **DHIS2** (District Health Information Software), **NDHx** (National Digital Health Exchange), **eAPTS** (Pharmaceutical Tracking), and **eCHIS**.
- **DOC-006: Regulatory & Standards Compliance**
  - Compliant with **Health Service Administration and Regulation Proclamation No. 1362/2024**.
  - Ethiopia National Digital Health Blueprint 2021–2030 & Digital Health Strategy 2025–2030.
  - Ethiopian Hospital Standards **ES 7191:2025** and National Health Data Dictionary (NHDD).

---

### PART III: COMPLETE HOSPITAL WORKFLOW DIGITIZATION (DOC-007)
Covers all 35 hospital departments and end-to-end patient care pathways:
1. Reception & Registration (Digital Patient ID & MRN Generation)
2. Medical Records Department (Longitudinal Electronic Medical Records)
3. Outpatient Department (OPD consultations & SOAP notes)
4. Emergency Department (Red/Yellow triage severity & immediate direct clinical routing)
5. Inpatient Wards (Bed occupancy matrix, ward transfers, and admission/discharge)
6. Nursing & Triage (Vital signs monitoring: BP, HR, Temp, SpO2, BMI)
7. Doctor Services (Diagnosis with ICD-10, clinical notes, digital prescriptions)
8. Central Pathology Laboratory (Barcode specimen tracking, validation, test reports)
9. Radiology & Diagnostic Imaging (X-Ray, Ultrasound, CT Scan, MRI reports)
10. Central Pharmacy (Electronic prescriptions, stock batch tracking, dispensing)
11. Cashier & Finance (Telebirr, CBE Birr, Cash, Chapa, and official receipts)
12. Insurance & Claims (Ethiopian Community Based Health Insurance CBHI integration)
13–35. Specialized units: Maternity, Pediatrics, Surgery, ICU, Blood Bank, Maintenance, Ambulance Dispatch, Security, Quality Assurance.

---

### PART IV: SOFTWARE REQUIREMENTS SPECIFICATION (DOC-008 – DOC-009)
- **FR-001 to FR-011:** User Authentication, Patient Management, Smart Queue Engine, Appointments, EMR, MedReminder, Laboratory, Pharmacy, Payments, AI Assistant, Analytics.
- **NFR-001 to NFR-015:** 99.5% availability, <2s API latency, <5s queue broadcast, role-based security, SQLite offline transaction sync.

---

### PART V: SYSTEM ARCHITECTURE & DATABASE DESIGN (DOC-010 – DOC-017)
- **DOC-010: Proposed System Architecture:** Modular Monolith in Laravel 11/12 REST API + Vue 3 Tailwind SPA + Flutter cross-platform mobile client.
- **DOC-011: Core Database Design:** 24+ normalized entities across identity, facilities, patients, queues, encounters, prescriptions, lab, billing, and logs.
- **DOC-012: Patient Identity:** Separation of Identity, Hospital Encounter, and Longitudinal Medical Record.
- **DOC-013: Offline & SMS Architecture:** SQLite local mobile caching + Ethio Telecom SMS gateway.
- **DOC-014: AI Healthcare Chatbot:** Clinically guardrailed assistant with emergency escalation to 907.
- **DOC-015: Smart Queue Specification:** Dynamic waiting time algorithms and calling chimes.
- **DOC-016: Payment & Financial Management:** Telebirr, CBE Birr, Cash, CBHI claim processing.
- **DOC-017: Hospital Executive Dashboard:** KPI metrics, bed occupancy, revenue, stock alerts.

---

### MASTER DOCUMENT REGISTER INDEX (DOC-018 – DOC-112)
| Document ID | Title |
|---|---|
| DOC-018 | Use Case Specification |
| DOC-019 | Business Rules Specification (BR-001 to BR-015) |
| DOC-020 | Security and Access Control Specification |
| DOC-021 | National Interoperability Specification (MFR, DHIS2, NDHx) |
| DOC-022 | Software Testing Plan |
| DOC-023 | Deployment Plan |
| DOC-024 | Implementation Roadmap (Phases 1-12) |
| DOC-025 | Data Migration and Paper Digitization Plan |
| DOC-026 | Risk Management Plan |
| DOC-027 | User Acceptance and Success Criteria |
| DOC-028 | Software Requirements Specification (SRS) |
| DOC-029 | Software Design Description (SDD) |
| DOC-030 | System Architecture Document |
| DOC-031 | UI/UX Design Specification |
| DOC-032 | Website Requirements |
| DOC-033 | Flutter Mobile Application Requirements |
| DOC-034 | SMS Gateway Specification |
| DOC-035 | API Documentation |
| DOC-036 | Authentication Specification |
| DOC-037 | Role and Permission Matrix |
| DOC-038 | Hospital Administration Module |
| DOC-039 | Patient Registration Module |
| DOC-040 | Electronic Medical Records Module |
| DOC-041 | OPD Management Module |
| DOC-042 | Emergency Management Module |
| DOC-043 | Inpatient Management Module |
| DOC-044 | Nursing Management Module |
| DOC-045 | Doctor Management Module |
| DOC-046 | Appointment Module |
| DOC-047 | Queue Management Module |
| DOC-048 | Laboratory Management Module |
| DOC-049 | Radiology Management Module |
| DOC-050 | Pharmacy Management Module |
| DOC-051 | Medication Reminder Module |
| DOC-052 | Billing and Cashier Module |
| DOC-053 | Insurance Management Module |
| DOC-054 | Referral Management Module |
| DOC-055 | Discharge Management Module |
| DOC-056 | Maternity Management Module |
| DOC-057 | Pediatrics Management Module |
| DOC-058 | Surgery Management Module |
| DOC-059 | ICU Management Module |
| DOC-060 | Blood Bank Management Module |
| DOC-061 | Dental Management Module |
| DOC-062 | Physiotherapy Management Module |
| DOC-063 | Medical Inventory Module |
| DOC-064 | Procurement Management Module |
| DOC-065 | Human Resource Management Module |
| DOC-066 | Attendance and Shift Management |
| DOC-067 | Medical Equipment Management |
| DOC-068 | Ambulance Management |
| DOC-069 | Mortuary Management |
| DOC-070 | Infection Prevention Module |
| DOC-071 | Quality Assurance Module |
| DOC-072 | Hospital Administration Dashboard |
| DOC-073 | AI Chatbot Specification |
| DOC-074 | AI Integration Architecture |
| DOC-075 | Patient Notification Specification |
| DOC-076 | Payment Gateway Integration (Telebirr & CBE Birr) |
| DOC-077 | Offline Synchronization Specification |
| DOC-078 | Multilingual Support Specification (English & Amharic) |
| DOC-079 | Data Privacy and Protection Plan |
| DOC-080 | Cybersecurity Plan |
| DOC-081 | Audit and Logging Specification |
| DOC-082 | Backup and Disaster Recovery Plan |
| DOC-083 | Database Implementation Specification |
| DOC-084 | API Integration Specification |
| DOC-085 | National Health System Integration Plan |
| DOC-086 | Facility Registry Integration |
| DOC-087 | Clinical Terminology and Data Dictionary |
| DOC-088 | Data Migration and Verification |
| DOC-089 | Unit Testing Plan |
| DOC-090 | Integration Testing Plan |
| DOC-091 | System Testing Plan |
| DOC-092 | Performance Testing Plan |
| DOC-093 | Security Testing Plan |
| DOC-094 | Mobile Application Testing Plan |
| DOC-095 | User Acceptance Testing |
| DOC-096 | Clinical Safety Validation |
| DOC-097 | Deployment and Release Management |
| DOC-098 | Cloud Infrastructure Specification |
| DOC-099 | DevOps and CI/CD Specification |
| DOC-100 | Monitoring and Maintenance Plan |
| DOC-101 | User Training Manual |
| DOC-102 | Patient User Manual |
| DOC-103 | Hospital Administrator Manual |
| DOC-104 | Doctor and Nurse Manual |
| DOC-105 | Laboratory and Pharmacy Manual |
| DOC-106 | System Administrator Manual |
| DOC-107 | Technical Support Manual |
| DOC-108 | Project Budget and Cost Analysis |
| DOC-109 | Project Schedule and Resource Plan |
| DOC-110 | Pilot Implementation and Evaluation |
| DOC-111 | Final Project Report |
| DOC-112 | Project Approval and Handover Document |

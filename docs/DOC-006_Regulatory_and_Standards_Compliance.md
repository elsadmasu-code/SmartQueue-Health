# DOC-006: Regulatory & Standards Compliance Analysis
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Jurisdiction:** Federal Democratic Republic of Ethiopia  
**Primary Legal Reference:** Health Service Administration and Regulation Proclamation No. 1362/2024  
**Accreditation Reference:** Ethiopian Hospital Standards ES 7191:2025  

---

## 1. Statutory Legal Framework

### 1.1 Health Service Administration and Regulation Proclamation No. 1362/2024
Proclamation No. 1362/2024 governs health service institutions, health professionals, and the handling of clinical data across Ethiopia:
- **Article on Health Information Confidentiality:** Mandates that clinical records, diagnoses, and patient identities remain confidential. Access is restricted strictly to authorized healthcare personnel directly involved in patient care.
- **Article on Digital Health Information Systems:** Authorizes the deployment of electronic health systems, provided they maintain complete data integrity, electronic audit trails, and interoperability with national health reporting channels.
- **Article on Professional Documentation:** Prescriptions and medical certificates must bear verified professional credentials, timestamps, and traceable electronic signatures.

### 1.2 Personal Data Protection & Cybersecurity
In accordance with Ethiopian cybersecurity standards established by the Information Network Security Administration (INSA):
- Patient medical records are classified as sensitive personal data.
- Encryption in transit (TLS 1.3) and encryption at rest (AES-256) are mandatory.
- Medical records must be hosted within Ethiopian sovereign boundaries or approved national cloud infrastructure.

---

## 2. Standards Compliance Matrix

| Standard / Policy | Regulatory Body | SQHM Implementation |
|---|---|---|
| **ES 7191:2025** (Hospital Accreditation Standard) | Ethiopian Standards Institute & MOH | Structured EMR layout, SOAP clinical notes, ICD-10 coding, and emergency triage prioritization. |
| **National Digital Health Blueprint 2021–2030** | Ministry of Health (MOH) | Multi-facility support, MFR integration, and offline-first mobile sync for rural health facilities. |
| **National Health Data Dictionary (NHDD)** | MOH Data Harmonization Team | Standardized clinical terms, department codes, and unit pricing formats. |
| **National Health Data Access & Sharing Guideline** | Ethiopian Public Health Institute (EPHI) | Role-based data separation ensuring cashiers cannot view clinical notes and doctors cannot view unrelated financial ledgers. |
| **Ethical AI & Clinical Safety Guidelines** | Ministry of Innovation & Technology (MInT) & MOH | AI chatbot enforces strict clinical guardrails: zero autonomous prescribing, immediate escalation of acute symptoms to 907. |

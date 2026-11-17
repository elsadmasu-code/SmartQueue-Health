# DOC-020: Security, Authentication & Access Control Specification
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Security Standard:** Role-Based Access Control (RBAC) & Facility Tenancy Isolation  

---

## 1. Role-Based Access Control (RBAC) Matrix

The system enforces granular principle-of-least-privilege permissions across 11 primary roles:

| Module / Resource | Super Admin | Medical Director | Doctor | Nurse | Pharmacist | Lab Tech | Radiologist | Cashier | Receptionist | Patient |
|---|---|---|---|---|---|---|---|---|---|---|
| **Facility Settings** | Full | View | - | - | - | - | - | - | - | - |
| **Queue Issuance** | Full | View | View | View | View | View | View | View | Full | View Own |
| **Queue Calling** | Full | Full | Full | Full | Full | Full | Full | Full | - | - |
| **Patient Registration** | Full | View | View | View | - | - | - | - | Full | Self |
| **Vitals Recording** | Full | Full | Full | Full | - | - | - | - | - | View Own |
| **Consultation SOAP Notes**| Full | Full | Full | Read-Only| - | - | - | - | - | View Own |
| **Diagnostic Lab Orders** | Full | Full | Full | Read-Only| - | Full (Results)| - | - | - | View Results|
| **Imaging Orders** | Full | Full | Full | Read-Only| - | - | Full (Reports)| - | - | View Reports|
| **Prescription Creation** | Full | Full | Full | - | - | - | - | - | - | View Own |
| **Medication Dispensing** | Full | Full | - | - | Full | - | - | - | - | View Own |
| **MedReminder Schedules** | Full | Full | Full | View | View | - | - | - | - | Full (Take/Skip)|
| **Billing & Invoices** | Full | Full | - | - | View Total| - | - | Full | View Total | View Own |
| **Payment Reconciliation**| Full | Full | - | - | - | - | - | Full | - | View Receipts |
| **National Interoperability**| Full | Full | View | - | - | - | - | - | - | - |

---

## 2. Department Data Isolation
1. **Clinical / Financial Separation:** Cashiers and billing clerks have zero read or write access to confidential doctor consultation notes (`clinical_notes.subjective` / `clinical_notes.objective`).
2. **Prescription Integrity:** Prescriptions are immutable once signed by a physician. Dispensing pharmacists can record actual dispensed quantities and counseling notes, but cannot alter the doctor's prescribed dosage without a documented clinical amendment.
3. **Cross-Facility Firewalls:** Employees of Hospital A cannot query patient encounters created at Hospital B unless explicit authorized referral consent is established through the National Digital Health Exchange (NDHx).

---

## 3. Cryptography & Session Management
- **In-Transit Protection:** Mandatory HTTPS with TLS 1.3 encryption.
- **At-Rest Protection:** Database storage encrypted with AES-256. Passwords hashed using Bcrypt with cost factor 12.
- **Session Expiry:** Clinical sessions auto-expire after 15 minutes of inactivity to prevent unauthorized access at shared nursing stations or consultation desks.
- **Audit Logging:** Every view, export, amendment, and deletion of a medical record is permanently recorded in `audit_logs` with actor ID, IP address, timestamp, and action description.

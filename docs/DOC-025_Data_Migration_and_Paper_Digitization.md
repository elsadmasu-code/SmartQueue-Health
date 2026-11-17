# DOC-025: Data Migration & Historical Paper Record Digitization Plan
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Objective:** Seamless migration of Ethiopian paper hospital registers into electronic SQHM databases without disrupting active clinical services.  

---

## 1. Paper Registers Targeted for Digitization

| Existing Physical Register | Department | Digital Equivalent in SQHM | Priority |
|---|---|---|---|
| **Patient Registration Book** | Reception | `patients` table (Unique MRN, Fayda ID, phone) | Phase 1 (Immediate) |
| **OPD Attendance Ledger** | Outpatient | `encounters` & `queue_tickets` | Phase 1 (Immediate) |
| **Pharmacy Bin & Stock Cards** | Pharmacy | `medications` & `stock_batches` (Expiry, Batch #) | Phase 1 (Immediate) |
| **Cashier Receipt Counterfoils** | Cashier | `invoices` & `payments` | Phase 2 |
| **Ward Admission & Discharge Book**| Inpatient | `admissions` & `beds` matrix | Phase 2 |
| **Laboratory Test Request & Logbooks**| Pathology | `lab_orders` & `lab_results` | Phase 2 |
| **Historical Paper Patient Folders** | Medical Records | Scanned PDF document attachments with OCR index | Phase 3 (Progressive) |

---

## 2. Nine-Step Migration Process

```
[1. Inventory Paper Records]
            │
            ▼
[2. Department Classification]
            │
            ▼
[3. Deduplication & Data Cleansing]
            │
            ▼
[4. Standard Digital Template Mapping]
            │
            ▼
[5. High-Resolution Batch Scanning]
            │
            ▼
[6. Structured Data Entry & Double-Keying]
            │
            ▼
[7. Verification & Clinical Approval]
            │
            ▼
[8. Linkage to Global Patient MRN]
            │
            ▼
[9. Immutable Audit Trail Archival]
```

### Key Principles:
1. **Provenance Retention:** Digitized records must permanently store the original physical register name, page number, and entry date.
2. **Dual-Entry Verification:** Critical demographic data (Full Name, Date of Birth, Allergies) requires two-operator verification before final commit.
3. **Legal Preservation:** In accordance with Proclamation No. 1362/2024, scanning a paper record does not permit physical destruction until the statutory institutional retention period has lapsed.

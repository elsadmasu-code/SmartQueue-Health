# DOC-016: Ethiopian Payment & Financial Management Specification
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Currencies:** Ethiopian Birr (ETB)  

---

## 1. Supported Payment Channels

### 1.1 Telebirr Integration (Ethio Telecom)
- **Direct Merchant API:** Integrates via Telebirr Merchant Payment API (RSA-encrypted JSON payloads).
- **In-App QR / Push USSD:** Generates Telebirr payment order reference (e.g. `TB-9821876402`).
- **Callback Webhook:** Confirms payment status asynchronously; verifies cryptographic signature and marks invoice `payment_status = 'paid'`.

### 1.2 CBE Birr Integration (Commercial Bank of Ethiopia)
- **Merchant Shortcode / Account:** Supports CBE Birr USSD and API settlements.
- **Reference Tracking:** Validates CBE transaction IDs against customer phone numbers.

### 1.3 Ethiopian Community Based Health Insurance (CBHI)
- **Roster Verification:** Validates patient `cbhi_number` and Kebele membership against the national CBHI registry.
- **Claim Processing:** Generates structured insurance claims linked to diagnostic codes and hospital price schedules (`approval_code: CBHI-APV-XXXXXXXX`).
- **Zero Out-of-Pocket Balance:** Covered services auto-reconcile to 0.00 ETB for eligible beneficiaries.

### 1.4 Cash Settlements
- Cashier records cash tendered, generates system receipt numbers (`REC-YYYYMMDD-XXXX`), and logs cashier shift totals.

---

## 2. Financial Safety & Reconciliation
1. Confirmation must originate from verified provider webhook or cashier sign-off, never unverified client-side inputs.
2. Every payment generates an audit trail linking `invoice_id`, `patient_id`, `cashier_id`, and `transaction_reference`.

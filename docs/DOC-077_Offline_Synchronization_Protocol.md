# DOC-077: Offline Synchronization Protocol & Conflict Resolution
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Applicability:** Mobile App (Flutter) & Hospital Local Edge Nodes  

---

## 1. Synchronization Topology

```
                  ┌──────────────────────────────────────────────┐
                  │ Central Cloud / National Health Server       │
                  │ (PostgreSQL, Master Data, DHIS2, MFR)        │
                  └──────────────────────▲───────────────────────┘
                                         │
                        HTTPS Batch Sync │ (When WAN Available)
                                         │
                  ┌──────────────────────▼───────────────────────┐
                  │ Hospital Local Edge Gateway (LAN Server)     │
                  │ (Local SQLite / PostgreSQL Instance)         │
                  └──────────────────────▲───────────────────────┘
                                         │
                         Wi-Fi / Hotspot │ (Local Network)
                                         │
                  ┌──────────────────────▼───────────────────────┐
                  │ Flutter Mobile Clients (Physicians/Patients) │
                  │ (Embedded SQLite DB & Sync Queue)            │
                  └──────────────────────────────────────────────┘
```

---

## 2. Deterministic Conflict Resolution Algorithm
When multiple offline devices modify records simultaneously, SQHM applies the following rules:

1. **Clinical Provenance (Physician Authority):**
   - If a clinical note or prescription was updated by a licensed physician, client-side edits by non-clinical users cannot overwrite the physician's signed record.
2. **Append-Only Event Streams:**
   - Vital signs, medication intake logs (`taken`, `missed`), and queue transitions are stored as immutable chronological events rather than state overwrites. Both events are preserved with their respective device timestamps.
3. **Queue Ticket State Precedence:**
   - Server state always takes precedence over cached device state for queue calling and room assignment to prevent calling two patients into the same room simultaneously.
4. **Inventory Stock Balance Deductions:**
   - If an offline mobile clinic dispenses medication from local stock, synchronization creates an inventory debit transaction tagged with `offline_dispensed = true`. If centralized stock drops below zero, a warning flag is raised for supervisor reconciliation without blocking patient treatment.

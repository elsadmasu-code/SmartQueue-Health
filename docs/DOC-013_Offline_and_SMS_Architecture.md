# DOC-013: Offline-First & SMS Architecture Specification
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Target:** Low-connectivity regions in Ethiopia  

---

## 1. Offline-First Mobile Synchronization

### 1.1 Mobile Local SQLite Storage
The Flutter client embeds a local SQLite database (`sqflite`) containing:
1. `cached_tickets`: The patient's active and historical queue tickets.
2. `cached_medications`: The patient's prescribed medication schedules and instructions.
3. `sync_queue`: Staged actions performed while disconnected (e.g., dose intake logged, offline patient registration, emergency vitals recorded by community health workers).

### 1.2 Batch Synchronization Protocol
When internet connectivity is restored:
1. The Flutter client gathers all rows from `sync_queue` where `status = 'pending'`.
2. Packages them into a JSON payload with unique client UUIDs and timestamps:
   ```json
   {
     "device_id": "MOBILE-DEVICE-UUID",
     "facility_id": 1,
     "records": [
       {
         "client_uuid": "e81c1920-5cf4-4b51-93e1-314285b01851",
         "entity_type": "medication_taken",
         "payload": {
           "schedule_id": 1,
           "status": "taken",
           "notes": "Taken on time"
         },
         "client_timestamp": "2026-11-16T14:05:00Z"
       }
     ]
   }
   ```
3. Posts the batch to `POST /api/sync/offline-batch`.
4. The server validates each transaction, generates server IDs, checks for concurrent record conflicts, and returns individual synchronization statuses.
5. Synced items are marked `status = 'synced'` locally on the mobile device.

---

## 2. Ethio Telecom & Safaricom ET SMS Architecture

### 2.1 SMS Outbound Notifications
For patients without smartphones, notifications are dispatched as localized SMS text messages:
- **Appointment Confirmations:** Date, time, doctor name, and hospital room.
- **Queue Alerts:** Sent when a ticket is within 3 turns of being called (`"Your ticket OPD-001 is next. Please proceed to OPD Room 104"`).
- **MedReminder Alarms:** Daily dosage reminders in Amharic or English (`"የመድኃኒት ማስታወሻ: የ Metformin HCl 500mg የሚወስዱበት ሰዓት ደርሷል"`).

### 2.2 SMS Inbound Commands
Patients can send short SMS commands to the hospital shortcode (e.g. `8911`):
| Command | Response / Action |
|---|---|
| `QUEUE` | Returns live status of active ticket registered under patient phone number |
| `REMINDER` | Summarizes today's medication schedule |
| `CANCEL` | Cancels an upcoming appointment |
| `HELP` | Returns available commands in Amharic & English |

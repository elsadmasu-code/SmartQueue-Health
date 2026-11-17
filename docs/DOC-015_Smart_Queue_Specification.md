# DOC-015: Smart Queue Specification & Algorithms
**Platform:** SmartQueue Health & MedReminder (SQHM)  
**Module:** Core Hospital Queue Management Engine  

---

## 1. Ticket Numbering Scheme
Tickets are formatted with a department prefix and a 3-digit daily sequential counter:
- `OPD-001` : Outpatient Department
- `EMG-001` : Emergency & Urgent Triage
- `LAB-001` : Central Pathology Laboratory
- `PHARM-001` : Main Dispensary Pharmacy
- `RAD-001` : Radiology & Diagnostic Imaging
- `CASH-001` : Cashier & Billing Desk

---

## 2. Dynamic Waiting Time Formula
The estimated waiting time ($W$) in minutes is calculated using:
$$W = \max\left(0, \; \frac{N \times M}{D \times P}\right)$$
Where:
- $N$ = Number of patients currently waiting ahead in the department queue.
- $M$ = Average historical consultation/service duration (default: 12 minutes for OPD, 4 minutes for Cashier, 6 minutes for Pharmacy).
- $D$ = Number of healthcare professionals currently on duty in the department ($\max(1, D)$).
- $P$ = Priority weighting multiplier:
  - **Emergency (Red):** $W = 0$ (Immediate priority bypass).
  - **Urgent (Yellow):** Multiplier $P = 2.0$ (Halves effective wait estimate).
  - **Normal (Green):** Multiplier $P = 1.0$ (Standard first-in, first-out sequence).

---

## 3. Queue Ticket Lifecycle State Machine
```
   [Registered]
        │
        ▼
   [Checked In] ──► [Waiting] ──► [Called] ──► [In Service] ──► [Completed]
                        │            │               │
                        ├────────────┼───────────────┴──► [Skipped / Cancelled]
                        │            │
                        ▼            ▼
                   [Transferred to another Dept (e.g. Lab / Pharmacy)]
```

---

## 4. Public Monitor Calling & Audio Announcer
- **Audio Chime:** Dual-tone oscillator (587.33 Hz ➔ 880.00 Hz) played via HTML5 Web Audio API before ticket call.
- **Voice Announcement:** Browser SpeechSynthesis speech synthesizer speaks:
  - English: *"Attention please. Ticket O P D 0 0 1, please proceed to O P D Room 104."*
  - Amharic: *"ማሳሰቢያ: የቲኬት ቁጥር ኦ ፒ ዲ 0 0 1፣ እባክዎ ወደ ኦ ፒ ዲ ክፍል 104 ይግቡ::"*

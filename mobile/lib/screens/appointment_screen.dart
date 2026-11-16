import 'package:flutter/material.dart';
import '../localization/app_strings.dart';

class AppointmentScreen extends StatefulWidget {
  final String currentLang;
  const AppointmentScreen({Key? key, required this.currentLang}) : super(key: key);

  @override
  State<AppointmentScreen> createState() => _AppointmentScreenState();
}

class _AppointmentScreenState extends State<AppointmentScreen> {
  final List<Map<String, dynamic>> appointments = [
    {
      'date': '2026-11-30',
      'time': '09:30 - 10:00 AM',
      'doctor': 'Dr. Almaz Tefera',
      'specialty': 'Internal Medicine',
      'department': 'Outpatient (OPD)',
      'status': 'Confirmed',
      'reason': '2-week diabetes & hypertension blood pressure check',
    },
  ];

  DateTime selectedDate = DateTime.now().add(const Duration(days: 7));
  String selectedTimeSlot = '10:00 - 10:30 AM';
  String selectedDepartment = 'Outpatient Department (OPD)';

  void _bookAppointment() {
    setState(() {
      appointments.add({
        'date': '${selectedDate.year}-${selectedDate.month.toString().padLeft(2, '0')}-${selectedDate.day.toString().padLeft(2, '0')}',
        'time': selectedTimeSlot,
        'doctor': 'Dr. Almaz Tefera',
        'specialty': 'Internal Medicine',
        'department': selectedDepartment,
        'status': 'Confirmed',
        'reason': 'Consultation follow-up',
      });
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Appointment booked successfully! SMS reminder confirmation dispatched.'),
        backgroundColor: Color(0xFF009B48),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('appointments', lang: widget.currentLang)),
        backgroundColor: const Color(0xFF1D4ED8),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Upcoming Appointments Header
            const Text(
              'Your Scheduled Appointments',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 12),

            ...appointments.map((apt) => Card(
              margin: const EdgeInsets.only(bottom: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(apt['date'], style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8))),
                        Chip(
                          label: Text(apt['status'], style: const TextStyle(fontSize: 10, color: Colors.green, fontWeight: FontWeight.bold)),
                          backgroundColor: const Color(0xFFE8F5E9),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(apt['doctor'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    Text('${apt['specialty']} • ${apt['department']}', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(8)),
                      child: Text('⏰ ${apt['time']} • Reason: ${apt['reason']}', style: const TextStyle(fontSize: 11, color: Colors.black87)),
                    ),
                  ],
                ),
              ),
            )),

            const SizedBox(height: 24),

            // Book New Appointment Card
            Card(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
              elevation: 2,
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    const Text('Book Doctor Appointment', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 14),

                    // Department
                    const Text('Department', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)),
                    const SizedBox(height: 4),
                    DropdownButtonFormField<String>(
                      value: selectedDepartment,
                      items: [
                        'Outpatient Department (OPD)',
                        'Emergency & Triage (EMG)',
                        'Dental Clinic',
                        'Physiotherapy Unit',
                      ].map((dept) => DropdownMenuItem(value: dept, child: Text(dept, style: const TextStyle(fontSize: 13)))).toList(),
                      onChanged: (val) {
                        if (val != null) setState(() => selectedDepartment = val);
                      },
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: const Color(0xFFF8FAFC),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Time Slot
                    const Text('Available Time Slot', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)),
                    const SizedBox(height: 4),
                    DropdownButtonFormField<String>(
                      value: selectedTimeSlot,
                      items: [
                        '09:00 - 09:30 AM',
                        '09:30 - 10:00 AM',
                        '10:00 - 10:30 AM',
                        '11:00 - 11:30 AM',
                        '02:00 - 02:30 PM',
                      ].map((slot) => DropdownMenuItem(value: slot, child: Text(slot, style: const TextStyle(fontSize: 13)))).toList(),
                      onChanged: (val) {
                        if (val != null) setState(() => selectedTimeSlot = val);
                      },
                      decoration: InputDecoration(
                        filled: true,
                        fillColor: const Color(0xFFF8FAFC),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      ),
                    ),
                    const SizedBox(height: 20),

                    ElevatedButton(
                      onPressed: _bookAppointment,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1D4ED8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                      child: const Text('Confirm Appointment Booking', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 14)),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

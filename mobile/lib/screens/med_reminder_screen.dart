import 'package:flutter/material.dart';
import '../localization/app_strings.dart';
import '../models/medication_schedule.dart';

class MedReminderScreen extends StatefulWidget {
  final String currentLang;
  const MedReminderScreen({Key? key, required this.currentLang}) : super(key: key);

  @override
  State<MedReminderScreen> createState() => _MedReminderScreenState();
}

class _MedReminderScreenState extends State<MedReminderScreen> {
  List<MedicationScheduleModel> medications = [
    MedicationScheduleModel(
      id: 1,
      medicineName: 'Metformin HCl 500mg',
      dosage: '1 Tablet (500mg)',
      scheduledTime: '08:00',
      frequency: 'Twice daily with meals',
      instructions: 'Take morning with breakfast',
      isTaken: true,
      takenAt: DateTime.now().subtract(const Duration(hours: 2)),
    ),
    MedicationScheduleModel(
      id: 2,
      medicineName: 'Amlodipine Besylate 5mg',
      dosage: '1 Tablet (5mg)',
      scheduledTime: '08:00',
      frequency: 'Once daily morning',
      instructions: 'Take every morning with water',
      isTaken: true,
      takenAt: DateTime.now().subtract(const Duration(hours: 2)),
    ),
    MedicationScheduleModel(
      id: 3,
      medicineName: 'Metformin HCl 500mg',
      dosage: '1 Tablet (500mg)',
      scheduledTime: '20:00',
      frequency: 'Twice daily with meals',
      instructions: 'Take evening with dinner',
      isTaken: false,
    ),
  ];

  void _markDoseTaken(int index) {
    setState(() {
      medications[index] = MedicationScheduleModel(
        id: medications[index].id,
        medicineName: medications[index].medicineName,
        dosage: medications[index].dosage,
        scheduledTime: medications[index].scheduledTime,
        frequency: medications[index].frequency,
        instructions: medications[index].instructions,
        isTaken: true,
        takenAt: DateTime.now(),
      );
    });

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Dose recorded for ${medications[index].medicineName}! Adherence updated.'),
        backgroundColor: const Color(0xFF009B48),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('med_reminder', lang: widget.currentLang)),
        backgroundColor: const Color(0xFF1D4ED8),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Adherence Gauge Card
            Container(
              padding: const EdgeInsets.all(20.0),
              decoration: BoxDecoration(
                color: const Color(0xFF0F172A),
                borderRadius: BorderRadius.circular(20.0),
              ),
              child: Row(
                children: [
                  Container(
                    width: 70,
                    height: 70,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: const Color(0xFF009B48), width: 6),
                    ),
                    child: const Center(
                      child: Text('94%', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: const [
                        Text('Monthly Adherence Rate', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold)),
                        SizedBox(height: 4),
                        Text('Excellent Compliance', style: TextStyle(color: Color(0xFF009B48), fontSize: 16, fontWeight: FontWeight.w900)),
                        SizedBox(height: 2),
                        Text('32 of 34 prescribed doses logged on time', style: TextStyle(color: Colors.white54, fontSize: 11)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Today's Timetable
            Text(
              AppStrings.get('today_medications', lang: widget.currentLang),
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.black87),
            ),
            const SizedBox(height: 12),

            ...List.generate(medications.length, (idx) {
              final med = medications[idx];
              return Card(
                margin: const EdgeInsets.only(bottom: 12),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.blue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          med.scheduledTime,
                          style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8), fontSize: 14),
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(med.medicineName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                            const SizedBox(height: 2),
                            Text('${med.dosage} • ${med.frequency}', style: const TextStyle(color: Colors.black54, fontSize: 12)),
                            const SizedBox(height: 2),
                            Text(med.instructions, style: const TextStyle(color: Colors.black38, fontSize: 11)),
                          ],
                        ),
                      ),
                      med.isTaken
                          ? const Chip(
                              label: Text('Taken', style: TextStyle(fontSize: 11, color: Colors.green)),
                              backgroundColor: Color(0xFFE8F5E9),
                            )
                          : ElevatedButton(
                              onPressed: () => _markDoseTaken(idx),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF009B48),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                              ),
                              child: const Text('Take', style: TextStyle(fontSize: 12, color: Colors.white, fontWeight: FontWeight.bold)),
                            ),
                    ],
                  ),
                ),
              );
            }),
          ],
        ),
      ),
    );
  }
}

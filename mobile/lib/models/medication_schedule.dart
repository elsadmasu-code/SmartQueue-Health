class MedicationScheduleModel {
  final int id;
  final String medicineName;
  final String dosage;
  final String scheduledTime; // e.g. "08:00"
  final String frequency;
  final String instructions;
  final bool isTaken;
  final DateTime? takenAt;

  MedicationScheduleModel({
    required this.id,
    required this.medicineName,
    required this.dosage,
    required this.scheduledTime,
    required this.frequency,
    required this.instructions,
    this.isTaken = false,
    this.takenAt,
  });

  factory MedicationScheduleModel.fromJson(Map<String, dynamic> json) {
    return MedicationScheduleModel(
      id: json['id'] ?? 0,
      medicineName: json['medicine_name'] ?? '',
      dosage: json['dosage'] ?? '',
      scheduledTime: json['scheduled_time'] ?? '08:00',
      frequency: json['frequency'] ?? '',
      instructions: json['instructions'] ?? '',
      isTaken: json['is_taken'] ?? false,
      takenAt: json['taken_at'] != null ? DateTime.tryParse(json['taken_at']) : null,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'medicine_name': medicineName,
      'dosage': dosage,
      'scheduled_time': scheduledTime,
      'frequency': frequency,
      'instructions': instructions,
      'is_taken': isTaken ? 1 : 0,
      'taken_at': takenAt?.toIso8601String(),
    };
  }
}

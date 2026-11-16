import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/queue_ticket.dart';
import '../models/medication_schedule.dart';

class ApiService {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android emulator localhost / local network IP

  static Future<Map<String, dynamic>> checkQueueStatus(int facilityId, {int? ticketId}) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/facilities/$facilityId/queue/board')).timeout(const Duration(seconds: 4));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (e) {
      // Offline fallback
    }
    return {
      'status': 'offline',
      'waiting_count': 14,
      'avg_wait_minutes': 12,
    };
  }

  static Future<List<MedicationScheduleModel>> fetchMedSchedules(int patientId) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/patients/$patientId/med-reminders')).timeout(const Duration(seconds: 4));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final list = data['schedules'] as List? ?? [];
        return list.map((item) => MedicationScheduleModel.fromJson(item)).toList();
      }
    } catch (e) {
      // Offline fallback
    }

    return [
      MedicationScheduleModel(
        id: 1,
        medicineName: 'Metformin HCl 500mg',
        dosage: '1 Tablet (500mg)',
        scheduledTime: '08:00',
        frequency: 'Twice daily with meals',
        instructions: 'Take morning with breakfast',
        isTaken: true,
      ),
      MedicationScheduleModel(
        id: 2,
        medicineName: 'Amlodipine Besylate 5mg',
        dosage: '1 Tablet (5mg)',
        scheduledTime: '08:00',
        frequency: 'Once daily morning',
        instructions: 'Take every morning with glass of water',
        isTaken: true,
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
  }

  static Future<Map<String, dynamic>> askAiChatbot(String message, String lang) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/chatbot/message'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'message': message, 'language': lang}),
      ).timeout(const Duration(seconds: 5));

      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (e) {
      // Offline response
    }

    if (lang == 'am') {
      return {
        'response': 'የተመላላሽ ሰልፍ ቲኬትዎ OPD-001 ነው:: ወደ OPD Room 104 ተጠርተዋል:: እባክዎ መድሃኒትዎን በሰዓቱ ይውሰዱ::',
        'is_emergency': false,
      };
    }
    return {
      'response': 'Your queue ticket OPD-001 is called at OPD Room 104 (Dr. Almaz Tefera). Next dose due at 20:00.',
      'is_emergency': false,
    };
  }

  static Future<bool> pushOfflineSyncBatch(String deviceId, int facilityId, List<Map<String, dynamic>> records) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/sync/offline-batch'),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          'device_id': deviceId,
          'facility_id': facilityId,
          'records': records,
        }),
      ).timeout(const Duration(seconds: 8));

      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}

class QueueTicketModel {
  final int id;
  final String ticketNumber;
  final String departmentName;
  final String status; // waiting, called, in_service, completed
  final String? counterOrRoom;
  final int estimatedWaitMinutes;
  final String priorityLevel;
  final String triageCategory;
  final DateTime issuedAt;

  QueueTicketModel({
    required this.id,
    required this.ticketNumber,
    required this.departmentName,
    required this.status,
    this.counterOrRoom,
    required this.estimatedWaitMinutes,
    required this.priorityLevel,
    required this.triageCategory,
    required this.issuedAt,
  });

  factory QueueTicketModel.fromJson(Map<String, dynamic> json) {
    return QueueTicketModel(
      id: json['id'] ?? 0,
      ticketNumber: json['ticket_number'] ?? '',
      departmentName: json['department']?['name'] ?? 'General OPD',
      status: json['status'] ?? 'waiting',
      counterOrRoom: json['counter_or_room'],
      estimatedWaitMinutes: json['estimated_wait_minutes'] ?? 15,
      priorityLevel: json['priority_level'] ?? 'normal',
      triageCategory: json['triage_category'] ?? 'green',
      issuedAt: DateTime.tryParse(json['issued_at'] ?? '') ?? DateTime.now(),
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'ticket_number': ticketNumber,
      'department_name': departmentName,
      'status': status,
      'counter_or_room': counterOrRoom,
      'estimated_wait_minutes': estimatedWaitMinutes,
      'priority_level': priorityLevel,
      'triage_category': triageCategory,
      'issued_at': issuedAt.toIso8601String(),
    };
  }
}

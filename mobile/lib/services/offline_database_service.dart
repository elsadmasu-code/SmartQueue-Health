import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class OfflineDatabaseService {
  static final OfflineDatabaseService instance = OfflineDatabaseService._init();
  static Database? _database;

  OfflineDatabaseService._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('sqhm_offline.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, filePath);

    return await openDatabase(
      path,
      version: 1,
      onCreate: _createDB,
    );
  }

  Future<void> _createDB(Database db, int version) async {
    // Local cached queue tickets
    await db.execute('''
      CREATE TABLE cached_tickets (
        id INTEGER PRIMARY KEY,
        ticket_number TEXT,
        department_name TEXT,
        status TEXT,
        counter_or_room TEXT,
        estimated_wait_minutes INTEGER,
        priority_level TEXT,
        triage_category TEXT,
        issued_at TEXT
      )
    ''');

    // Local cached medication schedules
    await db.execute('''
      CREATE TABLE cached_medications (
        id INTEGER PRIMARY KEY,
        medicine_name TEXT,
        dosage TEXT,
        scheduled_time TEXT,
        frequency TEXT,
        instructions TEXT,
        is_taken INTEGER,
        taken_at TEXT
      )
    ''');

    // Offline pending transaction sync queue
    await db.execute('''
      CREATE TABLE sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        client_uuid TEXT UNIQUE,
        entity_type TEXT,
        payload TEXT,
        timestamp TEXT,
        status TEXT
      )
    ''');
  }

  Future<void> enqueueOfflineAction(String entityType, String payloadJson, String uuid) async {
    final db = await instance.database;
    await db.insert('sync_queue', {
      'client_uuid': uuid,
      'entity_type': entityType,
      'payload': payloadJson,
      'timestamp': DateTime.now().toIso8601String(),
      'status': 'pending',
    });
  }

  Future<List<Map<String, dynamic>>> getPendingSyncActions() async {
    final db = await instance.database;
    return await db.query('sync_queue', where: 'status = ?', whereArgs: ['pending']);
  }

  Future<void> markActionSynced(String uuid) async {
    final db = await instance.database;
    await db.update('sync_queue', {'status': 'synced'}, where: 'client_uuid = ?', whereArgs: [uuid]);
  }
}

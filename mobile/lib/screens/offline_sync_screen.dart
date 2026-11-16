import 'package:flutter/material.dart';
import '../localization/app_strings.dart';
import '../services/api_service.dart';

class OfflineSyncScreen extends StatefulWidget {
  final String currentLang;
  const OfflineSyncScreen({Key? key, required this.currentLang}) : super(key: key);

  @override
  State<OfflineSyncScreen> createState() => _OfflineSyncScreenState();
}

class _OfflineSyncScreenState extends State<OfflineSyncScreen> {
  bool isOnline = true;
  int pendingCount = 0;
  bool isSyncing = false;

  void _triggerSync() async {
    setState(() => isSyncing = true);
    await Future.delayed(const Duration(seconds: 2));

    // Simulated sync batch
    final success = await ApiService.pushOfflineSyncBatch('MOBILE-DEV-001', 1, [
      {
        'client_uuid': 'CL-TXN-001',
        'entity_type': 'medication_taken',
        'payload': {'schedule_id': 1, 'taken_at': DateTime.now().toIso8601String()},
      }
    ]);

    setState(() {
      isSyncing = false;
      pendingCount = 0;
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Offline transactions synced successfully with Tikur Anbessa central server!'),
        backgroundColor: Color(0xFF009B48),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('offline_sync', lang: widget.currentLang)),
        backgroundColor: const Color(0xFF1D4ED8),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  Icon(
                    isOnline ? Icons.cloud_done_rounded : Icons.cloud_off_rounded,
                    color: isOnline ? const Color(0xFF009B48) : Colors.orange,
                    size: 60,
                  ),
                  const SizedBox(height: 12),
                  Text(
                    isOnline ? 'Connected to Central Hospital Server' : 'Working in Offline Mode',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'SQLite local database stores visits, doses & tickets without internet',
                    style: TextStyle(color: Colors.grey, fontSize: 12),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            Card(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Pending Sync Queue:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    Chip(
                      label: Text('$pendingCount items', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                      backgroundColor: const Color(0xFF1D4ED8),
                    ),
                  ],
                ),
              ),
            ),
            const Spacer(),

            ElevatedButton(
              onPressed: isSyncing ? null : _triggerSync,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF009B48),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                padding: const EdgeInsets.symmetric(vertical: 16),
              ),
              child: isSyncing
                  ? const CircularProgressIndicator(color: Colors.white)
                  : Text(
                      AppStrings.get('sync_now', lang: widget.currentLang),
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.white),
                    ),
            ),
          ],
        ),
      ),
    );
  }
}

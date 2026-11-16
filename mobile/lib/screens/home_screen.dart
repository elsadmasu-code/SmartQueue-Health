import 'package:flutter/material.dart';
import '../localization/app_strings.dart';
import 'queue_tracker_screen.dart';
import 'med_reminder_screen.dart';
import 'chatbot_screen.dart';
import 'offline_sync_screen.dart';

class HomeScreen extends StatefulWidget {
  final String currentLang;
  final Function(String) onLanguageChange;

  const HomeScreen({Key? key, required this.currentLang, required this.onLanguageChange}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  Widget build(BuildContext context) {
    final lang = widget.currentLang;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 8,
                  height: 8,
                  decoration: const BoxDecoration(shape: BoxShape.circle, color: Color(0xFF009B48)),
                ),
                const SizedBox(width: 6),
                Text(
                  AppStrings.get('app_title', lang: lang),
                  style: const TextStyle(color: Color(0xFF0F172A), fontWeight: FontWeight.w900, fontSize: 18),
                ),
              ],
            ),
            const Text(
              'Tikur Anbessa Specialized Hospital',
              style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.normal),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () {
              widget.onLanguageChange(lang == 'en' ? 'am' : 'en');
            },
            child: Text(
              lang == 'en' ? 'አማርኛ' : 'English',
              style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
            ),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Patient Welcome Banner
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF1D4ED8), Color(0xFF1E3A8A)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Welcome Back,', style: TextStyle(color: Colors.white70, fontSize: 12)),
                  const SizedBox(height: 2),
                  const Text(
                    'Abebe Kebede Wolde (አበበ)',
                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 20),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'MRN: SQH-AA-2026-00001 • CBHI Member',
                    style: TextStyle(color: Colors.white60, fontSize: 11, fontFamily: 'monospace'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Live Queue Highlight Card
            InkWell(
              onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => QueueTrackerScreen(currentLang: lang)));
              },
              child: Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.blue.shade100, width: 1.5),
                  boxShadow: [
                    BoxShadow(color: Colors.blue.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFF6FF),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Text('📢', style: TextStyle(fontSize: 24)),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: const [
                              Text('Active Queue: ', style: TextStyle(color: Colors.grey, fontSize: 12)),
                              Text('OPD-001', style: TextStyle(fontWeight: FontWeight.w900, color: Color(0xFF1D4ED8), fontSize: 14)),
                            ],
                          ),
                          const SizedBox(height: 2),
                          const Text('CALLED: Proceed to OPD Room 104', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.black87)),
                        ],
                      ),
                    ),
                    const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Next MedReminder Banner
            InkWell(
              onTap: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => MedReminderScreen(currentLang: lang)));
              },
              child: Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.emerald.shade100, width: 1.5),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE8F5E9),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Text('⏰', style: TextStyle(fontSize: 24)),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: const [
                          Text('Next Dose Reminder: 20:00 (Evening)', style: TextStyle(color: Colors.grey, fontSize: 12)),
                          SizedBox(height: 2),
                          Text('Metformin HCl 500mg (1 Tab with dinner)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.black87)),
                        ],
                      ),
                    ),
                    const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Modules 2x2 Grid
            const Text('Healthcare Services', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            const SizedBox(height: 12),

            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisSpacing: 14,
              mainAxisSpacing: 14,
              children: [
                _buildGridItem(
                  icon: '🎫',
                  title: AppStrings.get('queue_tracker', lang: lang),
                  subtitle: 'Live ticket caller',
                  color: const Color(0xFFEFF6FF),
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => QueueTrackerScreen(currentLang: lang))),
                ),
                _buildGridItem(
                  icon: '💊',
                  title: AppStrings.get('med_reminder', lang: lang),
                  subtitle: 'Daily dose alarms',
                  color: const Color(0xFFECFDF5),
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => MedReminderScreen(currentLang: lang))),
                ),
                _buildGridItem(
                  icon: '🤖',
                  title: AppStrings.get('ai_navigator', lang: lang),
                  subtitle: 'Multilingual chat',
                  color: const Color(0xFFF5F3FF),
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ChatbotScreen(currentLang: lang))),
                ),
                _buildGridItem(
                  icon: '🔄',
                  title: AppStrings.get('offline_sync', lang: lang),
                  subtitle: 'SQLite sync engine',
                  color: const Color(0xFFFFFBEB),
                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => OfflineSyncScreen(currentLang: lang))),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildGridItem({required String icon, required String title, required String subtitle, required Color color, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.black.withOpacity(0.04)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(icon, style: const TextStyle(fontSize: 28)),
            const SizedBox(height: 8),
            Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Colors.black87)),
            const SizedBox(height: 2),
            Text(subtitle, style: const TextStyle(fontSize: 10, color: Colors.black45)),
          ],
        ),
      ),
    );
  }
}

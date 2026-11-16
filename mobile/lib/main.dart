import 'package:flutter/material.dart';
import 'screens/home_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const SmartQueueApp());
}

class SmartQueueApp extends StatefulWidget {
  const SmartQueueApp({Key? key}) : super(key: key);

  @override
  State<SmartQueueApp> createState() => _SmartQueueAppState();
}

class _SmartQueueAppState extends State<SmartQueueApp> {
  String _currentLang = 'en';

  void _changeLanguage(String lang) {
    setState(() {
      _currentLang = lang;
    });
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SmartQueue Health & MedReminder',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF1D4ED8),
          primary: const Color(0xFF1D4ED8),
        ),
        fontFamily: 'Roboto',
        useMaterial3: true,
      ),
      home: HomeScreen(
        currentLang: _currentLang,
        onLanguageChange: _changeLanguage,
      ),
    );
  }
}

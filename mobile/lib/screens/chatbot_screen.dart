import 'package:flutter/material.dart';
import '../localization/app_strings.dart';
import '../services/api_service.dart';

class ChatbotScreen extends StatefulWidget {
  final String currentLang;
  const ChatbotScreen({Key? key, required this.currentLang}) : super(key: key);

  @override
  State<ChatbotScreen> createState() => _ChatbotScreenState();
}

class _ChatbotScreenState extends State<ChatbotScreen> {
  final TextEditingController _controller = TextEditingController();
  final List<Map<String, dynamic>> _messages = [
    {
      'is_user': false,
      'text': 'Welcome to SmartQueue Health AI Assistant! You can ask about your queue ticket, medication timetable, or hospital directions.',
      'is_emergency': false,
    }
  ];

  void _sendMessage() async {
    final text = _controller.text.trim();
    if (text.isEmpty) return;

    setState(() {
      _messages.add({'is_user': true, 'text': text, 'is_emergency': false});
    });
    _controller.clear();

    final res = await ApiService.askAiChatbot(text, widget.currentLang);

    setState(() {
      _messages.add({
        'is_user': false,
        'text': res['response'] ?? 'I have recorded your query.',
        'is_emergency': res['is_emergency'] ?? false,
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.get('ai_navigator', lang: widget.currentLang)),
        backgroundColor: const Color(0xFF1D4ED8),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Emergency Clinical Guardrail Banner
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            color: const Color(0xFFFEF3C7),
            child: Row(
              children: const [
                Icon(Icons.warning_amber_rounded, color: Color(0xFFB45309), size: 20),
                SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'Clinical Safety: In case of acute severe pain or emergency, call 907 immediately.',
                    style: TextStyle(color: Color(0xFF92400E), fontSize: 11, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          ),

          // Chat message list
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _messages.length,
              itemBuilder: (context, index) {
                final msg = _messages[index];
                final isUser = msg['is_user'] == true;
                final isEmergency = msg['is_emergency'] == true;

                return Align(
                  alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(14),
                    constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.8),
                    decoration: BoxDecoration(
                      color: isUser
                          ? const Color(0xFF1D4ED8)
                          : (isEmergency ? const Color(0xFFFFE4E6) : const Color(0xFFF1F5F9)),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Text(
                      msg['text'] ?? '',
                      style: TextStyle(
                        color: isUser
                            ? Colors.white
                            : (isEmergency ? const Color(0xFF9F1239) : const Color(0xFF1E293B)),
                        fontSize: 13,
                        fontWeight: isEmergency ? FontWeight.bold : FontWeight.normal,
                      ),
                    ),
                  ),
                );
              },
            ),
          ),

          // Message input bar
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: Colors.grey.shade200)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _controller,
                    onSubmitted: (_) => _sendMessage(),
                    decoration: InputDecoration(
                      hintText: widget.currentLang == 'am' ? 'ጥያቄዎን እዚህ ይጻፉ...' : 'Ask question...',
                      hintStyle: const TextStyle(fontSize: 13),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(24), borderSide: BorderSide.none),
                      filled: true,
                      fillColor: const Color(0xFFF1F5F9),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                IconButton(
                  onPressed: _sendMessage,
                  icon: const Icon(Icons.send_rounded, color: Color(0xFF1D4ED8)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

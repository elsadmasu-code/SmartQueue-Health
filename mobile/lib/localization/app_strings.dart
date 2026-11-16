class AppStrings {
  static const Map<String, Map<String, String>> localizedValues = {
    'en': {
      'app_title': 'SmartQueue Health',
      'tagline': 'MedReminder & Hospital Access',
      'queue_tracker': 'Queue Tracker',
      'med_reminder': 'MedReminder',
      'appointments': 'Appointments',
      'ai_navigator': 'AI Health Assistant',
      'offline_sync': 'Offline Sync Status',
      'ticket_number': 'Ticket Number',
      'estimated_wait': 'Estimated Wait Time',
      'now_serving': 'Now Serving',
      'waiting': 'Waiting in Queue',
      'called_to': 'Proceed to Room',
      'today_medications': 'Today\'s Prescribed Medications',
      'take_dose': 'Mark Dose Taken',
      'adherence_rate': 'Medication Adherence',
      'sync_now': 'Synchronize Offline Data',
      'emergency_hotline': 'Emergency Hotline: 907',
    },
    'am': {
      'app_title': 'ስማርት ሰልፍ ጤና',
      'tagline': 'የመድኃኒት ማስታወሻ እና የሆስፒታል አገልግሎት',
      'queue_tracker': 'የሰልፍ ተራ መከታተያ',
      'med_reminder': 'የመድኃኒት ማስታወሻ',
      'appointments': 'የቀጠሮ መመዝገቢያ',
      'ai_navigator': 'የ AI ጤና ረዳት',
      'offline_sync': 'ከመስመር ውጭ ማመሳሰያ',
      'ticket_number': 'የቲኬት ቁጥር',
      'estimated_wait': 'ግምታዊ የጥበቃ ጊዜ',
      'now_serving': 'አሁን የተጠራው',
      'waiting': 'በተራ የሚጠብቅ',
      'called_to': 'ወደ ክፍል ይግቡ',
      'today_medications': 'የዛሬ የታዘዙ መድሃኒቶች',
      'take_dose': 'መድኃኒት ወስጃለሁ',
      'adherence_rate': 'የመድኃኒት አወሳሰድ ትክክለኛነት',
      'sync_now': 'መረጃዎችን አመሳስል',
      'emergency_hotline': 'የድንገተኛ ጥሪ: 907',
    },
  };

  static String get(String key, {String lang = 'en'}) {
    return localizedValues[lang]?[key] ?? localizedValues['en']?[key] ?? key;
  }
}

<?php

return [
    'numbers' => '[0-9]+',

    'words' => '(?:صفر|واحد|اثنين|ثلاثة|أربعة|خمسة|ستة|سبعة|ثمانية|تسعة|عشرة|أحد عشر|اثني عشر|ثلاثة عشر|أربعة عشر|خمسة عشر|ستة عشر|سبعة عشر|ثمانية عشر|تسعة عشر|عشرون|ثلاثون|أربعون|خمسون|ستون|سبعون|ثمانون|تسعون|مائة|مائتين|ثلاثمائة|أربعمائة|خمسمائة|ستمائة|سبعمائة|ثمانمائة|تسعمائة|ألف|ألفين)+',
    // // English digits
    // 'digits' => '/\b\d+\b/',

    // // English number words (basic)
    // 'english_words' => '/\b(one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety|hundred|thousand)\b/i',

    // // Arabic digits
    // 'arabic_digits' => '/\b[\x{0660}-\x{0669}]+\b/u',

    // // Arabic number words
    // 'arabic_words' => '/\b(صفر|واحد|اثنان|ثلاثة|أربعة|خمسة|ستة|سبعة|ثمانية|تسعة|عشرة|أحد عشر|اثنا عشر|ثلاثة عشر|أربعة عشر|خمسة عشر|ستة عشر|سبعة عشر|ثمانية عشر|تسعة عشر|عشرون|ثلاثون|أربعون|خمسون|ستون|سبعون|ثمانون|تسعون|مائة|ألف)\b/u',

    // // Chinese digits (simplified)
    // 'chinese_digits' => '/\b(零|一|二|三|四|五|六|七|八|九|十|百|千|万|亿)\b/u',

    // // Korean digits
    // 'korean_digits' => '/\b(공|일|이|삼|사|오|육|칠|팔|구|십|백|천|만|억)\b/u',

    // // Japanese digits and words
    // 'japanese_digits' => '/\b(零|一|二|三|四|五|六|七|八|九|十|百|千|万|億|兆)\b/u',

    // // Russian digits (written as words)
    // 'russian_words' => '/\b(ноль|один|два|три|четыре|пять|шесть|семь|восемь|девять|десять|одиннадцать|двенадцать|тринадцать|четырнадцать|пятнадцать|шестнадцать|семнадцать|восемнадцать|девятнадцать|двадцать|тридцать|сорок|пятьдесят|шестьдесят|семьдесят|восемьдесят|девяносто|сто|тысяча)\b/u',

    // // Hindi digits
    // 'hindi_digits' => '/\b[\x{0966}-\x{096F}]+\b/u',

    // // Thai digits
    // 'thai_digits' => '/\b[\x{0E50}-\x{0E59}]+\b/u',

    // // Greek digits (written as words)
    // 'greek_words' => '/\b(μηδέν|ένα|δύο|τρία|τέσσερα|πέντε|έξι|επτά|οκτώ|εννέα|δέκα|έντεκα|δώδεκα|δεκατρία|δεκατέσσερα|δεκαπέντε|δεκαέξι|δεκαεπτά|δεκαοκτώ|δεκαεννέα|είκοσι|τριάντα|σαράντα|πενήντα|εξήντα|εβδομήντα|ογδόντα|ενενήντα|εκατό|χίλια)\b/u',

    // // Turkish digits (written as words)
    // 'turkish_words' => '/\b(sıfır|bir|iki|üç|dört|beş|altı|yedi|sekiz|dokuz|on|onbir|oniki|onüç|ondört|onbeş|onalti|onyedi|onsekiz|ondokuz|yirmi|otuz|kırk|elli|altmış|yetmiş|seksen|doksan|yüz|bin)\b/u',

    // // Persian (Farsi) digits
    // 'persian_digits' => '/\b[\x{06F0}-\x{06F9}]+\b/u',

    // // Hebrew digits
    // 'hebrew_digits' => '/\b(א|ב|ג|ד|ה|ו|ז|ח|ט|י|כ|ל|מ|נ|ס|ע|פ|צ|ק|ר|ש|ת)\b/u',

    // // Bengali digits
    // 'bengali_digits' => '/\b[\x{09E6}-\x{09EF}]+\b/u',

    // // Tamil digits
    // 'tamil_digits' => '/\b[\x{0BE6}-\x{0BEF}]+\b/u',

    // // Gujarati digits
    // 'gujarati_digits' => '/\b[\x{0A66}-\x{0A6F}]+\b/u',

    // // Kannada digits
    // 'kannada_digits' => '/\b[\x{0CE6}-\x{0CEF}]+\b/u',

    // // Malayalam digits
    // 'malayalam_digits' => '/\b[\x{0D66}-\x{0D6F}]+\b/u',

    // // Urdu digits
    // 'urdu_digits' => '/\b[\x{06F0}-\x{06F9}]+\b/u',

    // // Armenian digits
    // 'armenian_digits' => '/\b[\x{0531}-\x{053A}\x{0561}-\x{056A}]+\b/u',

    // // Georgian digits
    // 'georgian_digits' => '/\b[\x{10D0}-\x{10D6}]+\b/u',

    // // Mongolian digits
    // 'mongolian_digits' => '/\b[\x{1810}-\x{1819}]+\b/u',

    // // Tibetan digits
    // 'tibetan_digits' => '/\b[\x{0F20}-\x{0F29}]+\b/u',

    // // Lao digits
    // 'lao_digits' => '/\b[\x{0E50}-\x{0E59}]+\b/u',

    // // Khmer digits
    // 'khmer_digits' => '/\b[\x{17E0}-\x{17E9}]+\b/u',

    // // Sinhala digits
    // 'sinhala_digits' => '/\b[\x{0D66}-\x{0D6F}]+\b/u',

    // // Burmese (Myanmar) digits
    // 'burmese_digits' => '/\b[\x{1040}-\x{1049}]+\b/u',

    // // Nepali digits
    // 'nepali_digits' => '/\b[\x{0966}-\x{096F}]+\b/u',

    // // Telugu digits
    // 'telugu_digits' => '/\b[\x{0C66}-\x{0C6F}]+\b/u',

    // // Mixed format: number words combined with digits (English)
    // 'mixed_format' => '/\b(one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety|hundred|thousand)\s*\d*\b/i',
];

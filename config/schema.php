<?php
function install_schema(PDO $pdo) {
    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user',
        profile_picture TEXT DEFAULT NULL,
        phone TEXT DEFAULT NULL,
        location TEXT DEFAULT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE crops (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        image TEXT DEFAULT NULL,
        soil_type TEXT NOT NULL,
        min_temp REAL NOT NULL,
        max_temp REAL NOT NULL,
        min_humidity REAL NOT NULL,
        max_humidity REAL NOT NULL,
        min_moisture REAL NOT NULL,
        max_moisture REAL NOT NULL,
        season TEXT DEFAULT 'Any',
        description TEXT DEFAULT '',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    // Dynamic extra fields admins can add/remove for the recommendation form
    $pdo->exec("CREATE TABLE recommendation_fields (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        field_key TEXT NOT NULL UNIQUE,
        label TEXT NOT NULL,
        field_type TEXT NOT NULL DEFAULT 'number',
        unit TEXT DEFAULT '',
        min_value REAL DEFAULT NULL,
        max_value REAL DEFAULT NULL,
        options TEXT DEFAULT NULL,
        is_core INTEGER NOT NULL DEFAULT 0,
        active INTEGER NOT NULL DEFAULT 1,
        sort_order INTEGER NOT NULL DEFAULT 0
    )");

    $pdo->exec("CREATE TABLE recommendation_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        input_data TEXT NOT NULL,
        recommended_crop_id INTEGER,
        match_score REAL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(recommended_crop_id) REFERENCES crops(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE TABLE iot_data (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        temperature REAL NOT NULL,
        humidity REAL NOT NULL,
        moisture REAL NOT NULL,
        soil_type TEXT NOT NULL,
        recorded_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        image TEXT DEFAULT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE site_settings (
        setting_key TEXT PRIMARY KEY,
        setting_value TEXT
    )");

    // ---- Seed data ----
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $userPass  = password_hash('user123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")
        ->execute(['Site Admin', 'admin@cropsys.local', $adminPass, 'admin']);
    $pdo->prepare("INSERT INTO users (name,email,password,role,location) VALUES (?,?,?,?,?)")
        ->execute(['Demo Farmer', 'farmer@cropsys.local', $userPass, 'user', 'Kathmandu, Nepal']);

    $fields = [
        ['soil_type', 'Soil Type', 'select', '', null, null, 'Loamy,Sandy,Clay,Silty,Peaty,Saline,Black,Red', 1, 1, 1],
        ['temperature', 'Temperature', 'number', '°C', -10, 55, null, 1, 1, 2],
        ['humidity', 'Humidity', 'number', '%', 0, 100, null, 1, 1, 3],
        ['moisture', 'Soil Moisture', 'number', '%', 0, 100, null, 1, 1, 4],
    ];
    $stmt = $pdo->prepare("INSERT INTO recommendation_fields (field_key,label,field_type,unit,min_value,max_value,options,is_core,active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
    foreach ($fields as $f) $stmt->execute($f);

    $crops = [
        ['Rice', 'rice,paddy,field', 'Clay', 20, 37, 60, 90, 60, 100, 'Monsoon', 'Thrives in waterlogged clay soils with high humidity — a staple of monsoon farming.'],
        ['Wheat', 'wheat,field,golden', 'Loamy', 10, 25, 30, 60, 30, 55, 'Winter', 'A cool-season cereal that prefers well-drained loamy soil and moderate moisture.'],
        ['Maize', 'maize,corn,field', 'Loamy', 18, 32, 40, 70, 40, 65, 'Summer', 'Corn grows fast in warm weather with fertile loamy soil and steady moisture.'],
        ['Cotton', 'cotton,plant,field', 'Black', 21, 35, 40, 65, 35, 60, 'Summer', 'Black (regur) soil retains the moisture cotton needs through hot, dry spells.'],
        ['Sugarcane', 'sugarcane,field', 'Loamy', 20, 38, 60, 85, 60, 90, 'Any', 'A thirsty crop that rewards deep loamy soil, warmth, and abundant water.'],
        ['Barley', 'barley,grain,field', 'Sandy', 8, 22, 25, 55, 25, 50, 'Winter', 'Tolerant of drier, sandy soils and cooler temperatures better than most cereals.'],
        ['Millet', 'millet,grain,field', 'Sandy', 22, 40, 20, 50, 15, 40, 'Summer', 'A hardy, drought-resistant grain suited to sandy soils and low moisture.'],
        ['Soybean', 'soybean,legume,field', 'Loamy', 18, 30, 45, 70, 45, 65, 'Monsoon', 'A nitrogen-fixing legume that favors loamy soil and moderate rainfall.'],
        ['Potato', 'potato,tuber,farm', 'Sandy', 10, 25, 50, 80, 50, 75, 'Winter', 'Tubers form best in loose, well-aerated sandy-loam soil with cool nights.'],
        ['Tea', 'tea,plantation,green', 'Red', 15, 30, 70, 95, 60, 85, 'Any', 'High humidity and acidic red soils on hillsides give tea its character.'],
        ['Groundnut', 'peanut,groundnut,field', 'Sandy', 20, 35, 40, 65, 35, 55, 'Summer', 'Sandy soil lets the pods develop easily underground in warm conditions.'],
        ['Mustard', 'mustard,yellow,field', 'Loamy', 10, 25, 30, 55, 25, 45, 'Winter', 'A cool-weather oilseed crop that does well in fertile loamy soil.'],
        ['Jute', 'jute,fiber,field', 'Silty', 24, 37, 70, 90, 65, 90, 'Monsoon', 'Silty riverbank soils and monsoon humidity make ideal jute-growing land.'],
        ['Coconut', 'coconut,palm,tropical', 'Sandy', 20, 35, 60, 90, 50, 80, 'Any', 'Coastal sandy soils with high humidity suit this tropical palm well.'],
        ['Chickpea', 'chickpea,legume,field', 'Black', 15, 30, 30, 55, 25, 45, 'Winter', 'Black soil’s water retention helps chickpeas survive dry winter spells.'],
    ];
    $stmt = $pdo->prepare("INSERT INTO crops (name,image,soil_type,min_temp,max_temp,min_humidity,max_humidity,min_moisture,max_moisture,season,description) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    foreach ($crops as $c) {
        $img = 'https://loremflickr.com/500/350/' . urlencode($c[1]) . '?lock=' . crc32($c[0]);
        $stmt->execute([$c[0], $img, $c[2], $c[3], $c[4], $c[5], $c[6], $c[7], $c[8], $c[9], $c[10]]);
    }

    $articles = [
        ['Precision Farming: Letting Sensors Do the Guesswork', 'Modern farms increasingly rely on soil and climate sensors to decide when to water, fertilize, or plant. Real-time data reduces waste and raises yields by replacing intuition with measurement.', 'https://loremflickr.com/600/400/sensor,farm,technology?lock=1'],
        ['Reading Your Soil: Why Type Matters More Than You Think', 'Clay, sandy, loamy, and silty soils each hold water and nutrients differently. Matching a crop to its preferred soil type is one of the highest-leverage decisions a grower can make.', 'https://loremflickr.com/600/400/soil,farmland?lock=2'],
        ['Humidity, Moisture, and the Difference Between Them', 'Air humidity and soil moisture are often confused, but crops respond to each differently. Understanding both helps prevent fungal disease and drought stress alike.', 'https://loremflickr.com/600/400/greenhouse,plants?lock=3'],
        ['Crop Rotation in the Age of Smart Recommendations', 'Rule-based recommendation systems can suggest a strong crop for today\'s conditions, but rotating crops season to season still protects long-term soil health.', 'https://loremflickr.com/600/400/harvest,tractor?lock=4'],
    ];
    $stmt = $pdo->prepare("INSERT INTO articles (title,content,image) VALUES (?,?,?)");
    foreach ($articles as $a) $stmt->execute($a);

    $settings = [
        ['site_name', 'AgroSense'],
        ['tagline', 'Know your soil. Grow the right crop.'],
        ['about_us', "AgroSense is a rule-based crop recommendation platform built for farmers and agronomists. By combining live IoT sensor readings for temperature, humidity, and soil moisture with a transparent, editable rule engine, AgroSense suggests crops suited to your exact field conditions — no black-box machine learning required. Administrators can add crops, tune the rules, and manage the recommendation criteria at any time."],
        ['contact_email', 'support@agrosense.local'],
        ['contact_phone', '+977-1-4000000'],
        ['contact_address', 'Kathmandu, Bagmati Province, Nepal'],
    ];
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?)");
    foreach ($settings as $s) $stmt->execute($s);
}

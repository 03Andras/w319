<?php
/**
 * Application Configuration Constants
 */

// Directory paths
define('DATA_DIR', __DIR__ . '/../../data');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');
define('AUDIT_LOG_FILE', DATA_DIR . '/audit_log.json');
define('SESSIONS_FILE', DATA_DIR . '/sessions.json');

// Default settings
define('DEFAULT_SEATS', 7);
define('DEFAULT_ADMIN_PASSWORD', 'Jablko123');
define('DEFAULT_PIN_CODE', '147258369');

// Default team members
define('DEFAULT_TEAM', [
    "Eva Mészáros", "Viera Krajníková", "Nikola Oslanská", "Soňa Žáková", "Roman Blažek",
    "Ján Tóth", "Ivo Novysedlák", "Kristína Jablonská", "Zuzana Špalková", "Roman Šajbidor",
    "Margaréta Cifrová", "Dávid Jablonický", "Peter Marko", "Michal Michalec", "Ľubica Hadbavná"
]);

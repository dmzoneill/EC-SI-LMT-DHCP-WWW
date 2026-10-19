<?php

require_once __DIR__ . '/includes/db_row.php';
require_once __DIR__ . '/includes/db_class.php';

if (!class_exists('DBRow', false)) {
    class_alias('Intel\\Lmt\\DBRow', 'DBRow');
}

if (!class_exists('DB', false)) {
    class_alias('Intel\\Lmt\\DB', 'DB');
}

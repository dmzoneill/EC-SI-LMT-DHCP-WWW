<?php

require_once __DIR__ . '/includes/leases_class.php';

if (!class_exists('Leases', false)) {
    class_alias('Intel\\Lmt\\Leases', 'Leases');
}

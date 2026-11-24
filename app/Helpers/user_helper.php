<?php

use CodeIgniter\I18n\Time;

/**
 * Ambil URL foto user dengan fallback ke default.
 */
function user_photo(?string $photo): string
{
    $file = $photo ?: 'default.png';
    $path = FCPATH . 'uploads/users/' . $file;

    // Jika file tidak ada, pakai default
    if (!is_file($path)) {
        $file = 'default.png';
    }

    return base_url('uploads/users/' . $file);
}

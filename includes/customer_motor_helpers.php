<?php

function normalize_motor_text($value)
{
    $value = trim((string) $value);

    return preg_replace('/\s+/', ' ', $value);
}

function normalize_motor_plate_number($plateNumber)
{
    $plateNumber = strtoupper((string) $plateNumber);
    $plateNumber = preg_replace('/[^A-Z0-9]+/', ' ', $plateNumber);

    return normalize_motor_text($plateNumber);
}

function validate_customer_motor_input(array $input, $currentYear = null)
{
    $currentYear = $currentYear === null ? (int) date('Y') : (int) $currentYear;

    $data = [
        'brand' => normalize_motor_text($input['brand'] ?? ''),
        'model' => normalize_motor_text($input['model'] ?? ''),
        'plate_number' => normalize_motor_plate_number($input['plate_number'] ?? ''),
        'production_year' => null,
        'color' => normalize_motor_text($input['color'] ?? ''),
    ];
    $errors = [];

    if ($data['brand'] === '') {
        $errors['brand'] = 'Merk motor wajib diisi.';
    } elseif (strlen($data['brand']) > 50) {
        $errors['brand'] = 'Merk motor maksimal 50 karakter.';
    }

    if ($data['model'] === '') {
        $errors['model'] = 'Model motor wajib diisi.';
    } elseif (strlen($data['model']) > 50) {
        $errors['model'] = 'Model motor maksimal 50 karakter.';
    }

    if ($data['plate_number'] === '') {
        $errors['plate_number'] = 'Nomor plat wajib diisi.';
    } elseif (strlen($data['plate_number']) > 15) {
        $errors['plate_number'] = 'Nomor plat maksimal 15 karakter.';
    } elseif (!preg_match('/^[A-Z0-9 ]+$/', $data['plate_number']) || !preg_match('/\d/', $data['plate_number'])) {
        $errors['plate_number'] = 'Nomor plat hanya boleh berisi huruf, angka, dan spasi.';
    }

    $year = normalize_motor_text($input['production_year'] ?? '');
    if ($year !== '') {
        if (!ctype_digit($year)) {
            $errors['production_year'] = 'Tahun produksi harus berupa angka.';
        } else {
            $data['production_year'] = (int) $year;
            if ($data['production_year'] < 1900 || $data['production_year'] > $currentYear) {
                $errors['production_year'] = 'Tahun produksi harus di antara 1900 dan ' . $currentYear . '.';
            }
        }
    }

    if (strlen($data['color']) > 30) {
        $errors['color'] = 'Warna maksimal 30 karakter.';
    }

    return [
        'data' => $data,
        'errors' => $errors,
    ];
}

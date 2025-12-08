<?php

function normalizeImageUrl($imgPath) {
    if (empty($imgPath)) return '';
    $imgPath = str_replace('\\', '/', $imgPath);

    // If it's already an absolute URL, return as-is
    if (filter_var($imgPath, FILTER_VALIDATE_URL)) return $imgPath;

    // Remove absolute API host prefixes like https://host/api/ or http://host/api/ (for cases where host was included without valid scheme)
    $imgPath = preg_replace('#^https?://[^/]+/api/#i', '', $imgPath);

    // Remove leading /api/ or api/ if present
    $imgPath = preg_replace('#^/api/#i', '', $imgPath);
    $imgPath = preg_replace('#^api/#i', '', $imgPath);

    $imgPath = ltrim($imgPath, '/');
    if (strpos($imgPath, 'uploads/') === 0) return API_BASE_URL . '/' . $imgPath;
    return API_BASE_URL . '/uploads/' . $imgPath;
}

?>
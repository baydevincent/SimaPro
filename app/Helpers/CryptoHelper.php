<?php

if (!function_exists('encryptAjaxHeaders')) {
    /**
     * Encrypt AJAX headers for security
     */
    function encryptAjaxHeaders($data)
    {
        return encrypt(json_encode($data));
    }
}

if (!function_exists('decryptAjaxHeaders')) {
    /**
     * Decrypt AJAX headers
     */
    function decryptAjaxHeaders($encrypted)
    {
        try {
            return json_decode(decrypt($encrypted), true);
        } catch (\Exception $e) {
            return null;
        }
    }
}

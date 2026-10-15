<?php
namespace App\Middleware;

use Exception;

class SanitizeMiddleware
{
    public static function sanitize(mixed $dataPassed, string $variableType): mixed
    {
        $sanitized_input = $dataPassed;
        switch ($variableType) {
            case 'string':
                $sanitized_input = strip_tags($sanitized_input);
                $sanitized_input = trim($sanitized_input);
                return $sanitized_input = (string) htmlspecialchars($sanitized_input, ENT_QUOTES | ENT_SUBSTITUTE);
            case 'int':
                $sanitized_input = filter_var($sanitized_input, FILTER_SANITIZE_NUMBER_INT);
                return (int) $sanitized_input;
            case 'float':
                $sanitized_input = filter_var($sanitized_input, FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
                // FILTER_FLAG_ALLOW_FRACTION — allow the decimal separator (dot or locale decimal) so "12.34" keeps the dot.
                // FILTER_FLAG_ALLOW_THOUSAND — allow thousands separators (commas or locale) so "1,234.56" keeps the comma.
                return (float) $sanitized_input;
            case 'email':
                $sanitized_input = filter_var($sanitized_input, FILTER_SANITIZE_EMAIL);
                $sanitized_input = trim($sanitized_input);
                return $sanitized_input = (string) htmlspecialchars($sanitized_input, ENT_QUOTES | ENT_SUBSTITUTE);
            case 'url':
                $sanitized_input = filter_var($sanitized_input, FILTER_SANITIZE_URL);
                $sanitized_input = trim($sanitized_input);
                return $sanitized_input = (string) htmlspecialchars($sanitized_input, ENT_QUOTES | ENT_SUBSTITUTE);
            default:
                throw new Exception('Must select type');
        }
    }
}

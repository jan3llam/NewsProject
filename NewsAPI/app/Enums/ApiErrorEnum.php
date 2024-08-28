<?php

namespace App\Enums;

enum ApiErrorEnum: string
{
    case BAD_REQUEST = 'Bad request';
    case UNAUTHORIZED = 'Unauthorized access';
    case FORBIDDEN = 'Forbidden access';
    case NOCONTENT = 'No content retrieved';
    case NOT_FOUND = 'Resource not found';
    case UNPROCESSABLE_CONTENT = "Validation error";
    case INTERNAL_SERVER_ERROR = 'Internal server error';
    case GENERAL_ERROR = 'An unexpected error occurred';
}

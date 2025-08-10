<?php

namespace oihana\http;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Enumeration of standard HTTP header names (request and response).
 *
 * This class provides a centralized, type-safe list of common HTTP header
 * names, preserving the exact wire-format casing defined by the relevant
 * RFCs (notably RFC 7230–7235, RFC 9110–9112) and de-facto standards.
 *
 * Usage examples:
 * - Access a header name: Headers::CONTENT_TYPE
 * - Validate/inspect available names with ConstantsTrait utilities:
 *   - Headers::enums() returns all header values
 *   - Headers::includes('Content-Type') checks existence
 *   - Headers::getConstant('Content-Type') returns the constant name
 *
 * Notes:
 * - Only widely used standardized headers are listed. Vendor-specific
 *   or application-specific X- headers are intentionally omitted.
 * - Values are case-insensitive per RFC, but this list keeps canonical casing.
 */
class HttpHeaders
{
    use ConstantsTrait ;

    // CORS (Cross-Origin Resource Sharing)
    const string ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials' ;
    const string ACCESS_CONTROL_ALLOW_HEADERS     = 'Access-Control-Allow-Headers' ;
    const string ACCESS_CONTROL_ALLOW_METHODS     = 'Access-Control-Allow-Methods' ;
    const string ACCESS_CONTROL_ALLOW_ORIGIN      = 'Access-Control-Allow-Origin'  ;
    const string ACCESS_CONTROL_EXPOSE_HEADERS    = 'Access-Control-Expose-Headers' ;
    const string ACCESS_CONTROL_MAX_AGE           = 'Access-Control-Max-Age'       ;
    const string ACCESS_CONTROL_REQUEST_HEADERS   = 'Access-Control-Request-Headers' ;
    const string ACCESS_CONTROL_REQUEST_METHOD    = 'Access-Control-Request-Method' ;

    // Content negotiation (request)
    const string ACCEPT                           = 'Accept' ;
    const string ACCEPT_CHARSET                   = 'Accept-Charset' ;
    const string ACCEPT_ENCODING                  = 'Accept-Encoding' ;
    const string ACCEPT_LANGUAGE                  = 'Accept-Language' ;

    // Caching
    const string AGE                              = 'Age' ;
    const string CACHE_CONTROL                    = 'Cache-Control' ;
    const string EXPIRES                          = 'Expires' ;
    const string PRAGMA                           = 'Pragma' ;
    const string WARNING                          = 'Warning' ;

    // Conditional requests
    const string ETAG                             = 'ETag' ;
    const string IF_MATCH                         = 'If-Match' ;
    const string IF_NONE_MATCH                    = 'If-None-Match' ;
    const string IF_MODIFIED_SINCE                = 'If-Modified-Since' ;
    const string IF_UNMODIFIED_SINCE              = 'If-Unmodified-Since' ;
    const string IF_RANGE                         = 'If-Range' ;

    // Content/representation metadata
    const string CONTENT_DISPOSITION              = 'Content-Disposition' ;
    const string CONTENT_ENCODING                 = 'Content-Encoding' ;
    const string CONTENT_LANGUAGE                 = 'Content-Language' ;
    const string CONTENT_LENGTH                   = 'Content-Length' ;
    const string CONTENT_LOCATION                 = 'Content-Location' ;
    const string CONTENT_RANGE                    = 'Content-Range' ;
    const string CONTENT_TYPE                     = 'Content-Type' ;
    const string LAST_MODIFIED                    = 'Last-Modified' ;
    const string VARY                             = 'Vary' ;

    // Authentication & Authorization
    const string AUTHORIZATION                    = 'Authorization' ;
    const string PROXY_AUTHENTICATE               = 'Proxy-Authenticate' ;
    const string PROXY_AUTHORIZATION              = 'Proxy-Authorization' ;
    const string WWW_AUTHENTICATE                 = 'WWW-Authenticate' ;

    // Cookies
    const string COOKIE                           = 'Cookie' ;
    const string SET_COOKIE                       = 'Set-Cookie' ;

    // Range requests
    const string ACCEPT_RANGES                    = 'Accept-Ranges' ;
    const string RANGE                            = 'Range' ;
    const string RETRY_AFTER                      = 'Retry-After' ;

    // Message routing and networking
    const string CONNECTION                       = 'Connection' ;
    const string DATE                             = 'Date' ;
    const string FORWARDED                        = 'Forwarded' ;
    const string HOST                             = 'Host' ;
    const string KEEP_ALIVE                       = 'Keep-Alive' ;
    const string LINK                             = 'Link' ;
    const string LOCATION                         = 'Location' ;
    const string SERVER                           = 'Server' ;
    const string TE                               = 'TE' ;
    const string TRAILER                          = 'Trailer' ;
    const string TRANSFER_ENCODING                = 'Transfer-Encoding' ;
    const string UPGRADE                          = 'Upgrade' ;
    const string VIA                              = 'Via' ;

    // Request context
    const string DNT                              = 'DNT' ;
    const string ORIGIN                           = 'Origin' ;
    const string REFERER                          = 'Referer' ;
    const string USER_AGENT                       = 'User-Agent' ;
    const string UPGRADE_INSECURE_REQUESTS        = 'Upgrade-Insecure-Requests' ;

    // Security related response headers (commonly used)
    const string STRICT_TRANSPORT_SECURITY        = 'Strict-Transport-Security' ;
    const string X_CONTENT_TYPE_OPTIONS           = 'X-Content-Type-Options' ;
    const string X_FRAME_OPTIONS                  = 'X-Frame-Options' ;
    const string X_XSS_PROTECTION                 = 'X-XSS-Protection' ;
}
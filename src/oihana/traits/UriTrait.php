<?php

namespace oihana\traits;

use InvalidArgumentException;
use oihana\enums\Char;

/**
 * Trait UriTrait
 *
 * Provides a utility method to construct a full URL by merging a base URI with additional parameters.
 * Useful in routing, HTTP clients, or services needing dynamic URL generation.
 *
 * Features:
 * - Parses the input URI using `parse_url()`.
 * - Merges query parameters or other components (e.g., path, fragment) with provided values.
 * - Rebuilds and returns the final URL string with scheme, user info, host, port, path, query, and fragment.
 *
 * Dependencies:
 * - `oihana\enums\Char` for constants such as AMPERSAND and EMPTY.
 *
 * Method:
 * - `buildUri(string $uri, array $params = []): string`
 *   Merges query or fragment parts based on provided `$params`.
 *
 * Example usage:
 * ```php
 * use oihana\traits\UriTrait;
 *
 * class MyService {
 *     use UriTrait;
 * }
 *
 * $service = new MyService();
 * echo $service->buildUri('https://example.com/api', [
 *     'query' => ['page' => 2, 'limit' => 10],
 *     'fragment' => ['section' => 'comments']
 * ]);
 * // Output: https://example.com/api?page=2&limit=10#section=comments
 * ```
 *
 * Note:
 * This method expects `$params` to be an associative array where keys match valid components
 * from `parse_url()` such as `query`, `fragment`, etc. Each value should be an array compatible with `http_build_query()`.
 */
trait UriTrait
{
    /**
     * Builds a complete URI by merging query and fragment parameters into the provided URI.
     *
     * This method parses the given URI, merges existing query parameters with additional ones,
     * and optionally builds or replaces the fragment part of the URI. It reconstructs and returns
     * the resulting URI as a string.
     *
     * Options can include:
     * - `query` (array): An associative array of query parameters to add or override.
     * - `fragment` (array): An associative array to construct the URI fragment (after the '#' symbol).
     *
     * All other components of the URI (scheme, user, password, host, port, path) are preserved.
     *
     * @param string $uri The original URI to modify. Must be a valid URL string.
     * @param array $options Optional array of parameters to merge into the URI.
     *  - $query : Query parameters to merge with existing ones.
     *  - $fragment : Fragment parameters to build a new fragment string.
     *
     * @throws InvalidArgumentException If the URI is malformed or cannot be parsed.
     *
     * @return string The resulting URI with merged query and fragment parameters.
     *
     * @example
     * $uri = buildUri('https://example.com/path?foo=1#section', [
     *     'query' => ['bar' => '2'],
     *     'fragment' => ['tab' => 'details']
     * ]);
     * // Result: 'https://example.com/path?foo=1&bar=2#tab=details'
     */
    public function buildUri( string $uri , array $options = [] ) :string
    {
        if ( filter_var( $uri , FILTER_VALIDATE_URL ) === false )
        {
            throw new InvalidArgumentException(__METHOD__ . " failed with an invalid URI : " . $uri ) ;
        }

        $parts = parse_url( $uri ) ;

        $existingQuery = [];
        if ( isset( $parts['query'] ) )
        {
            parse_str( $parts['query'] , $existingQuery );
        }

        $newQuery = $options['query'] ?? [];
        $mergedQuery = http_build_query( array_merge( $existingQuery , $newQuery ) );

        // Build fragment
        $newFragment = $options['fragment'] ?? [] ;
        $fragment    = !empty($newFragment) ? http_build_query( $newFragment ) : ( $parts['fragment'] ?? Char::EMPTY ) ;

        // Rebuild URI
        $result = '' ;

        if ( isset( $parts['scheme'] ) )
        {
            $result .= $parts['scheme'] . Char::COLON . Char::DOUBLE_SLASH ;
        }

        if (isset($parts['user']))
        {
            $result .= $parts['user'] ;
            if ( isset($parts['pass'] ) )
            {
                $result .= Char::COLON . $parts['pass'];
            }
            $result .= Char::AT_SIGN ;
        }

        if ( isset($parts['host'] ) )
        {
            $result .= $parts['host'] ;
        }

        if ( isset($parts['port'] ) )
        {
            $result .= Char::COLON . $parts['port'];
        }

        $result .= $parts['path'] ?? '';

        if ($mergedQuery) {
            $result .= Char::QUESTION_MARK . $mergedQuery ;
        }

        if ($fragment) {
            $result .= Char::HASH . $fragment ;
        }

        return $result;
    }
}
<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Class MemcachedStats
 *
 * This class defines constants for the property names returned by the
 * Memcached::getStats() method. Using these constants improves code readability,
 * maintainability, and helps prevent typos when accessing Memcached statistics.
 */
class MemcachedStats
{
    use ConstantsTrait ;

    // --- General Server Information ---

    /**
     * The process ID (PID) of the Memcached server.
     * @var string
     */
    public const string PID = 'pid';

    /**
     * The time in seconds that the Memcached server has been running.
     * @var string
     */
    public const string UPTIME = 'uptime';

    /**
     * The current Unix timestamp on the Memcached server.
     * @var string
     */
    public const string TIME = 'time';

    /**
     * The version of the Memcached software.
     * @var string
     */
    public const string VERSION = 'version';

    /**
     * The version of the libevent library used (if applicable).
     * @var string
     */
    public const string LIB_EVENT = 'libevent';

    /**
     * The size of system pointers (typically 32 or 64).
     * @var string
     */
    public const string POINTER_SIZE = 'pointer_size';

    /**
     * User CPU time used by the Memcached process in seconds.
     * @var string
     */
    public const string RUSAGE_USER_SECONDS = 'rusage_user_seconds';

    /**
     * User CPU time used by the Memcached process in microseconds.
     * @var string
     */
    public const string RUSAGE_USER_MICROSECONDS = 'rusage_user_microseconds';

    /**
     * System CPU time used by the Memcached process in seconds.
     * @var string
     */
    public const string RUSAGE_SYSTEM_SECONDS = 'rusage_system_seconds';

    /**
     * System CPU time used by the Memcached process in microseconds.
     * @var string
     */
    public const string RUSAGE_SYSTEM_MICROSECONDS = 'rusage_system_microseconds';

    // --- Connection Statistics ---

    /**
     * The number of connections currently open.
     * @var string
     */
    public const string CURR_CONNECTIONS = 'curr_connections';

    /**
     * The total number of connections established since the server started.
     * @var string
     */
    public const string TOTAL_CONNECTIONS = 'total_connections';

    /**
     * The number of connection structures allocated.
     * @var string
     */
    public const string CONNECTION_STRUCTURES = 'connection_structures';

    /**
     * The number of file descriptors reserved.
     * @var string
     */
    public const string RESERVED_FDS = 'reserved_fds';

    // --- Command Statistics (Hits/Misses) ---

    /**
     * The total number of "get" commands executed.
     * @var string
     */
    public const string CMD_GET = 'cmd_get';

    /**
     * The total number of "set" commands executed.
     * @var string
     */
    public const string CMD_SET = 'cmd_set';

    /**
     * The total number of "flush" commands executed.
     * @var string
     */
    public const string CMD_FLUSH = 'cmd_flush';

    /**
     * The total number of "touch" commands executed (to update item expiry).
     * @var string
     */
    public const string CMD_TOUCH = 'cmd_touch';

    /**
     * The number of "get" requests that found an item in the cache.
     * @var string
     */
    public const string GET_HITS = 'get_hits';

    /**
     * The number of "get" requests that did not find an item in the cache.
     * @var string
     */
    public const string GET_MISSES = 'get_misses';

    /**
     * The number of "delete" requests that found and deleted an item.
     * @var string
     */
    public const string DELETE_HITS = 'delete_hits';

    /**
     * The number of "delete" requests that did not find the item to delete.
     * @var string
     */
    public const string DELETE_MISSES = 'delete_misses';

    /**
     * The number of "increment" requests that found an item.
     * @var string
     */
    public const string INCR_HITS = 'incr_hits';

    /**
     * The number of "increment" requests that did not find an item.
     * @var string
     */
    public const string INCR_MISSES = 'incr_misses';

    /**
     * The number of "decrement" requests that found an item.
     * @var string
     */
    public const string DECR_HITS = 'decr_hits';

    /**
     * The number of "decrement" requests that did not find an item.
     * @var string
     */
    public const string DECR_MISSES = 'decr_misses';

    /**
     * The number of "compare-and-set" requests that succeeded.
     * @var string
     */
    public const string CAS_HITS = 'cas_hits';

    /**
     * The number of "compare-and-set" requests that failed because the item was not found.
     * @var string
     */
    public const string CAS_MISSES = 'cas_misses';

    /**
     * The number of "compare-and-set" requests that failed because the item's value
     * was modified by another client.
     * @var string
     */
    public const string CAS_BADVAL = 'cas_badval';

    // --- Network I/O Statistics ---

    /**
     * The total number of bytes read by the server over the network.
     * @var string
     */
    public const string BYTES_READ = 'bytes_read';

    /**
     * The total number of bytes written by the server over the network.
     * @var string
     */
    public const string BYTES_WRITTEN = 'bytes_written';

    // --- Storage and Memory Statistics ---

    /**
     * The maximum size allocated for storing items in bytes.
     * @var string
     */
    public const string LIMIT_MAX_BYTES = 'limit_maxbytes';

    /**
     * The number of items currently stored in the cache.
     * @var string
     */
    public const string CURR_ITEMS = 'curr_items';

    /**
     * The total number of items that have been stored in the cache since server start.
     * @var string
     */
    public const string TOTAL_ITEMS = 'total_items';

    /**
     * The number of bytes of data currently stored in the cache.
     * @var string
     */
    public const string BYTES = 'bytes';

    /**
     * The number of items that have been evicted from the cache due to memory limits.
     * @var string
     */
    public const string EVICTIONS = 'evictions';

    /**
     * The number of items whose space has been reclaimed (e.g., due to expiry or purging).
     * @var string
     */
    public const string RECLAIMED = 'reclaimed';

    /**
     * (Less common) Number of times a slab has been moved.
     * @var string
     */
    public const string SLABS_MOVED = 'slabs_moved';

    // --- Other Statistics ---

    /**
     * The number of worker threads configured for Memcached.
     * @var string
     */
    public const string THREADS = 'threads';
}
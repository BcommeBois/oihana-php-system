<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * Enumeration of official character set codes as assigned by IANA and
 * used in Internet standards and documentation.
 *
 * This class defines integer constants representing the numeric identifiers
 * of various character encodings (code pages) commonly used in databases,
 * networking, and application protocols.
 *
 * The constants correspond to values supported by connection options such as
 * IANAAppCodePage, facilitating consistent charset handling in database drivers
 * or other components interacting with textual data.
 *
 * See the official IANA character sets registry for full details:
 * https://www.iana.org/assignments/character-sets/character-sets.xhtml
 *
 * Example usage:
 * ```php
 * use oihana\enums\CharacterSet;
 *
 * $charset = CharacterSet::UTF8;  // 106
 * ```
 *
 * Notes:
 * - The UTF8 constant represents passing Unicode data without conversion
 * when the database is configured for Unicode.
 * - Some values are specific to certain vendors or drivers (e.g. Progress DataDirect),
 * and may not appear in the official IANA list.
 *
 * @package oihana\enums
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class CharacterSet
{
    use ConstantsTrait ;

    /**
     * Passes Unicode data without conversion if the database is set to Unicode.
     */
    const int UTF8 = 106 ;

    const int US_ASCII        = 3 ;
    const int ISO_8859_1      = 4 ;
    const int ISO_8859_2      = 5 ;
    const int ISO_8859_3      = 6 ;
    const int ISO_8859_4      = 7 ;
    const int ISO_8859_5      = 8 ;
    const int ISO_8859_6      = 9 ;
    const int ISO_8859_7      = 10 ;
    const int ISO_8859_8      = 11 ;
    const int ISO_8859_9      = 12 ;
    const int JIS_Encoding    = 16 ;
    const int Shift_JIS       = 17 ;
    const int EUC_JP          = 18 ;
    const int ISO_646_IRV     = 30 ;
    const int KS_C_5601       = 36 ;
    const int ISO_2022_KR     = 37 ;
    const int EUC_KR          = 38 ;
    const int ISO_2022_JP     = 39 ;
    const int ISO_2022_JP_2   = 40 ;
    const int GB_2312_80      = 57 ;
    const int ISO_2022_CN     = 104 ;
    const int ISO_2022_CN_EXT = 105 ;
    const int ISO_8859_13     = 109	 ;
    const int ISO_8859_14     = 110	 ;
    const int ISO_8859_15     = 111	 ;
    const int GBK             = 113	 ;
    const int HP_ROMAN8       = 2004 ;
    const int IBM850          = 2009 ;
    const int IBM852          = 2010 ;
    const int IBM437          = 2011 ;
    const int IBM862          = 2013 ;
    const int IBM_Thai        = 2016 ;
    const int WINDOWS_31J     = 2024 ;
    const int GB2312          = 2025 ;
    const int Big5            = 2026 ;
    const int MACINTOSH       = 2027 ;
    const int IBM037          = 2028 ;
    const int IBM038          = 2029 ;
    const int IBM273          = 2030 ;
    const int IBM277          = 2033 ;
    const int IBM278          = 2034 ;
    const int IBM280          = 2035 ;
    const int IBM284          = 2037 ;
    const int IBM285          = 2038 ;
    const int IBM290          = 2039 ;
    const int IBM297          = 2040 ;
    const int IBM420          = 2041 ;
    const int IBM424          = 2043 ;
    const int IBM500          = 2044 ;
    const int IBM851          = 2045 ;
    const int IBM855          = 2046 ;
    const int IBM857          = 2047 ;
    const int IBM860          = 2048 ;
    const int IBM861          = 2049 ;
    const int IBM863          = 2050 ;
    const int IBM864          = 2051 ;
    const int IBM865          = 2052 ;
    const int IBM868          = 2053 ;
    const int IBM869          = 2054 ;
    const int IBM870          = 2055 ;
    const int IBM871          = 2056 ;
    const int IBM918          = 2062 ;
    const int IBM1026         = 2063 ;
    const int KOI8_R          = 2084 ;
    const int HZ_GB_2312      = 2085 ;
    const int IBM866          = 2086 ;
    const int IBM775          = 2087 ;
    const int IBM00858        = 2089 ;
    const int IBM01140        = 2091 ;
    const int IBM01141        = 2092 ;
    const int IBM01142        = 2093 ;
    const int IBM01143        = 2094 ;
    const int IBM01144        = 2095 ;
    const int IBM01145        = 2096 ;
    const int IBM01146        = 2097 ;
    const int IBM01147        = 2098 ;
    const int IBM01148        = 2099 ;
    const int IBM01149        = 2100 ;
    const int IBM1047         = 2102 ;
    const int WINDOWS_1250    = 2250 ;
    const int WINDOWS_1251    = 2251 ;
    const int WINDOWS_1252    = 2252 ;
    const int WINDOWS_1253    = 2253 ;
    const int WINDOWS_1254    = 2254 ;
    const int WINDOWS_1255    = 2255 ;
    const int WINDOWS_1256    = 2256 ;
    const int WINDOWS_1257    = 2257 ;
    const int WINDOWS_1258    = 2258 ;
    const int TIS_620         = 2259 ;

    // These values are assigned by Progress DataDirect
    // and not appear in the official Character Sets Enumeration.
    const int IBM_939           = 2000000939 ;
    const int IBM_943_P14A_2000 = 2000000943 ;
    const int IBM_1025          = 2000001025 ;
    const int IBM_4396          = 2000004396 ;
    const int IBM_5026          = 2000005026 ;
    const int IBM_5035          = 2000005035 ;
}
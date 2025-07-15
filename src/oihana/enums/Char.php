<?php

namespace oihana\enums;

use oihana\reflections\traits\ConstantsTrait;

/**
 * The enumeration of all basic chars.
 */
class Char
{
    use ConstantsTrait ;

    public const string AMPERSAND         = "&" ;
    public const string APOSTROPHE        = "'" ;
    public const string AT_SIGN           = '@' ;
    public const string ASTERISK          = '*' ;
    public const string BACK_SLASH        = '\\' ;
    public const string BULLET            = '•' ;
    public const string CHECK_MARK        = '✔' ;
    public const string CIRCUMFLEX        = '^' ;
    public const string COLON             = ':' ;
    public const string COMMA             = ',' ;
    public const string COPYRIGHT         = '©' ;
    public const string CROSS_MARK        = '✘' ;
    public const string DASH              = '-' ;
    public const string DEGREE            = '°' ;
    public const string DOLLAR            = '$' ;
    public const string DOT               = '.' ;
    public const string DOUBLE_COLON      = '::' ;
    public const string DOUBLE_DOT        = '..' ;
    public const string DOUBLE_EQUAL      = '==' ;
    public const string DOUBLE_HYPHEN     = '--' ;
    public const string DOUBLE_PIPE       = '||' ;
    public const string DOUBLE_QUOTE      = '"' ;
    public const string DOUBLE_SLASH      = '//' ;
    public const string EM_DASH           = '—' ;
    public const string EN_DASH           = '–' ;
    public const string EMPTY             = ''  ;
    public const string EOL               = PHP_EOL ;
    public const string EQUAL             = '=' ;
    public const string EURO_SIGN         = '€' ;
    public const string EXCLAMATION_MARK  = '!' ;
    public const string GRAVE_ACCENT      = '`' ;
    public const string HASH              = '#' ;
    public const string HEART             = '♥' ;
    public const string HYPHEN            = '-' ;
    public const string INFINITY          = '∞' ;
    public const string LEFT_BRACE        = '{' ;
    public const string LEFT_BRACKET      = '[' ;
    public const string LEFT_PARENTHESIS  = '(' ;
    public const string MICRO_SIGN        = 'µ' ;
    public const string MODULUS           = '%' ;
    public const string NUMBER            = '#' ;
    public const string PERCENT           = '%' ;
    public const string PILCROW           = '¶' ;
    public const string PIPE              = '|' ;
    public const string PLUS              = '+' ;
    public const string PLUS_MINUS        = '±' ;
    public const string QUESTION_MARK     = '?' ;
    public const string QUOTATION_MARK    = '"' ;
    public const string REGISTERED        = '®' ;
    public const string RIGHT_BRACE       = '}' ;
    public const string RIGHT_BRACKET     = ']' ;
    public const string RIGHT_PARENTHESIS = ')' ;
    public const string SECTION_SIGN      = '§' ;
    public const string SEMI_COLON        = ';' ;
    public const string SIMPLE_QUOTE      = "'" ;
    public const string SLASH             = '/' ;
    public const string SNOWFLAKE         = '❄' ;
    public const string SPACE             = ' ' ;
    public const string TILDE             = '~' ;
    public const string TRADEMARK         = '™' ;
    public const string TRIPLE_DOT        = '...' ;
    public const string UNDERLINE         = '_' ;

    public const string SUPERSCRIPT_ZERO              = '⁰' ;  // U+2070
    public const string SUPERSCRIPT_ONE               = '¹' ;  // U+00B9
    public const string SUPERSCRIPT_TWO               = '²' ;  // U+00B2
    public const string SUPERSCRIPT_THREE             = '³' ;  // U+00B3
    public const string SUPERSCRIPT_FOUR              = '⁴' ;  // U+2074
    public const string SUPERSCRIPT_FIVE              = '⁵' ;  // U+2075
    public const string SUPERSCRIPT_SIX               = '⁶' ;  // U+2076
    public const string SUPERSCRIPT_SEVEN             = '⁷' ;  // U+2077
    public const string SUPERSCRIPT_EIGHT             = '⁸' ;  // U+2078
    public const string SUPERSCRIPT_NINE              = '⁹' ;  // U+2079
    public const string SUPERSCRIPT_PLUS              = '⁺' ;  // U+207A
    public const string SUPERSCRIPT_MINUS             = '⁻' ;  // U+207B
    public const string SUPERSCRIPT_EQUAL             = '⁼' ;  // U+207C
    public const string SUPERSCRIPT_LEFT_PARENTHESIS  = '⁽' ;  // U+207D
    public const string SUPERSCRIPT_RIGHT_PARENTHESIS = '⁾' ;

    // Sous-exposants courants (chiffres et signes)
    public const string SUBSCRIPT_ZERO              = '₀' ; // U+2080
    public const string SUBSCRIPT_ONE               = '₁' ; // U+2081
    public const string SUBSCRIPT_TWO               = '₂' ; // U+2082
    public const string SUBSCRIPT_THREE             = '₃' ; // U+2083
    public const string SUBSCRIPT_FOUR              = '₄' ; // U+2084
    public const string SUBSCRIPT_FIVE              = '₅' ; // U+2085
    public const string SUBSCRIPT_SIX               = '₆' ; // U+2086
    public const string SUBSCRIPT_SEVEN             = '₇' ; // U+2087
    public const string SUBSCRIPT_EIGHT             = '₈' ; // U+2088
    public const string SUBSCRIPT_NINE              = '₉' ; // U+2089
    public const string SUBSCRIPT_PLUS              = '₊' ; // U+208A
    public const string SUBSCRIPT_MINUS             = '₋' ; // U+208B
    public const string SUBSCRIPT_EQUAL             = '₌' ; // U+208C
    public const string SUBSCRIPT_LEFT_PARENTHESIS  = '₍' ; // U+208D
    public const string SUBSCRIPT_RIGHT_PARENTHESIS = '₎' ;
}
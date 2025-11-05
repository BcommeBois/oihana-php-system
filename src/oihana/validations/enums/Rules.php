<?php

namespace oihana\validations\enums ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The available rules constants.
 *
 * @see https://github.com/somnambulist-tech/validation?tab=readme-ov-file#available-rules
 *
 * @package oihana\api\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class Rules
{
    use ConstantsTrait ;

    /**
     * The field under this rule must be one of 'on', 'yes', '1', 'true' (the string "true"), or true.
     */
    public const string ACCEPTED = 'accepted' ;

    /**
     * The field under this rule must be a date after the given minimum.
     *
     * The parameter should be any valid string that can be parsed by strtotime. For example:
     *
     * @example
     * ```php
     * after:next week
     * after:2016-12-31
     * after:2016
     * after:2016-12-31 09:56:02
     * ```
     */
    public const string AFTER = 'after' ;

    /**
     * The field under this rule must be entirely alphabetic characters.
     */
    public const string ALPHA = 'alpha' ;

    /**
     * The field under this rule may have alpha-numeric characters, as well as dashes and underscores.
     */
    public const string ALPHA_DASH = 'alpha_dash' ;

    /**
     * The field under this rule must be entirely alpha-numeric characters.
     */
    public const string ALPHA_NUM = 'alpha_num' ;

    /**
     * The field under this rule may have alpha characters, as well as spaces.
     */
    public const string ALPHA_SPACES = 'alpha_spaces' ;

    /**
     * A variation of in: here the values (separated by default with a ,) must all be in the given values.
     */
    public const string ANY_OF = 'any' ;

    /**
     * The field under this rule must be an array.
     */
    public const string ARRAY = 'array' ;

    /**
     * The array can only contain the specified keys, any keys not present will fail validation.
     *
     * By default, associative data has no restrictions on the key => values that can be present.
     *
     * For example: you have filters for a search box that are passed to SQL,
     * only the specified keys should be allowed to be sent and not any value in the array of filters.
     *
     * @example
     * ```php
     * use Somnambulist\Components\Validation\Factory;
     *
     * $validation = $factory->validate([
     * 'filters' => ['foo' => 'bar', 'baz' => 'example']
     * ], [
     * 'filters' => 'array|array_can_only_have_keys:foo,bar',
     * ]);
     *
     * $validation->passes(); // true if filters only has the keys in array_can_only_have_keys
     * ```
     */
    public const string ARRAY_CAN_ONLY_HAVE_KEYS = 'array_can_only_have_keys' ;

    /**
     * The array must contain all the specified keys to be valid.
     *
     * This is useful to ensure that a nested array meets a prescribed format.
     * The same thing can be achieved by using individual rules for each key with required.
     *
     * Note that this will still allow additional keys to be present, it merely validates the presence of specific keys.
     *
     * This rule is best used in conjunction with the array rule, though it can be used standalone.
     *
     * @example
     * ```php
     * use Somnambulist\Components\Validation\Factory;
     *
     * $validation = $factory->validate
     * ([
     *     'filters' => ['foo' => 'bar', 'baz' => 'example']
     * ] ,
     * [
     *     'filters' => 'array|array_must_have_keys:foo,bar,baz',
     * ]);
     *
     * $validation->passes(); // true if filters has all the keys in array_must_have_keys
     * ```
     */
    public const string ARRAY_MUST_HAVE_KEYS = 'array_must_have_keys' ;

    /**
     * The field under this rule must be a date before the given maximum.
     *
     * This also works the same way as the after rule. Pass anything that can be parsed by strtotime
     *
     * @example
     * ```php
     * before:last week
     * before:2016-12-31
     * before:2016
     * before:2016-12-31 09:56:02
     * ```
     */
    public const string BEFORE = 'before' ;

    /**
     * The field under this rule must have a size between min and max params. Value size is calculated in the same way as min and max rule.
     *
     * You can also validate the size of uploaded files using this rule:
     *
     * @example
     * ```php
     * $validation = $validator->validate
     * (
     *     [ 'photo' => $_FILES['photo'] ],
     *     [ 'photo' => 'required|between:1M,2M' ]
     * );
     * ```
     */
    public const string BETWEEN = 'between' ;

    /**
     * The field under this rule must be boolean. Accepted inputs are true, false, 1, 0, "1", and "0".
     */
    public const string BOOLEAN = 'boolean' ;

    /**
     * Define a custom callback to validate the value. This rule cannot be registered using the string syntax.
     *
     * To use this rule, you must use the array syntax and either explicitly specify callback, or pass the closure:
     *
     * @example
     * ```php
     * $validation = $validator->validate( $_POST,
     * [
     *     'even_number' => [
     *          'required',
     *          function ( $value )
     *          {
     *               // false = invalid
     *               return (is_numeric($value) AND $value % 2 === 0);
     *          },
     *          'callback' => fn ($v) => is_numeric($v) && $v % 2 === 0,
     *      ]
     * ]);
     * ```
     *
     * You can set a custom message by returning a string instead of false.
     * To allow for message translation, instead of a literal string; return a message key instead
     * and add this to the message bag on the Factory.
     *
     * Note: returning a message string will be removed in a future version, requiring only boolean responses.
     * Instead, set the message string directly before returning true/false via $this->message = "";
     *
     * ```php
     * $validation = $validator->validate( $_POST,
     * [
     *    'even_number' =>
     *    [
     *         'required',
     *         function ($value)
     *         {
     *              if (!is_numeric($value))
     *              {
     *                  return ":attribute must be numeric.";
     *              }
     *              if ($value % 2 !== 0)
     *              {
     *                   return ":attribute is not even number.";
     *              }
     *              return true; // always return true if validation passes
     *         }
     *     ]
     * ]);
     * ```
     *
     */
    public const string CALLBACK = 'callback' ;

    /**
     * Ensures that a given value matches a valid color expression (e.g. "#ff0000").
     */
    public const string COLOR = 'color' ;

    /**
     * The field under this rule must be valid date following a given format.
     * Parameter format is optional, default format is Y-m-d.
     */
    public const string DATE = 'date' ;

    /**
     * If the attribute has no value, this default will be used in place in the validated data.
     *
     * @example
     * ```
     * use Somnambulist\Components\Validation\Factory;
     *
     * $validation = (new Factory)->validate
     * ([
     *    'enabled' => null
     * ],
     * [
     *     'enabled'   => 'default:1|required|in:0,1'
     *     'published' => 'default:0|required|in:0,1'
     * ]);
     *
     * $validation->passes(); // true
     *
     * // Get the valid/default data
     * $valid_data = $validation->getValidData();
     *
     * $enabled = $valid_data['enabled'];
     * $published = $valid_data['published'];
     * ```
     */
    public const string DEFAULT = 'default' ;

    /**
     * Opposite of same; the field value under this rule must be different to another_field value.
     * @example
     * ```php
     * different:another_field
     * ```
     */
    public const string DIFFERENT = 'different' ;

    /**
     * The field under validation must be numeric and must have an exact length of value.
     * @example
     * ```php
     * digits:value
     * ```
     */
    public const string DIGITS = 'digits' ;

    /**
     * The field under validation must be numeric and have a length between the given min and max.
     * @example
     * ```php
     * digits_between:min,max
     * ```
     */
    public const string DIGITS_BETWEEN = 'digits_between' ;

    /**
     * The field under this validation must be a valid email address according to the built-in PHP filter extension.
     * See {@see FILTER_VALIDATE_EMAIL} for details.
     */
    public const string EMAIL = 'email' ;

    /**
     * The field under this validation must end with another_field.
     * Comparison can be against strings, numbers and array elements.
     */
    public const string ENDS_WITH = 'ends_with' ;

    /**
     * Ensures a given value is **equal to** another field's value or to a fixed numeric constant
     */
    public const string EQUAL = 'equal' ;

    /**
     * The field under this rule must end with an extension corresponding to one of those listed.
     *
     * This is useful for validating a file type for a given path or url.
     * The mimes rule should be used for validating uploads.
     */
    public const string EXTENSION = 'extension' ;

    /**
     * The field under validation must be an float.
     */
    public const string FLOAT = 'float' ;

    /**
     * Ensures a given value is **greater than** another field's value or to a fixed numeric constant
     */
    public const string GREATER_THAN = 'gt' ;

    /**
     * Ensures a given value is **greater than or equal to** another field's value or to a fixed numeric constant
     */
    public const string GREATER_THAN_OR_EQUAL = 'gte' ;

    /**
     * The field under this rule must be included in the given list of values.
     *
     * To help build the string rule, the In (and NotIn) rules have a helper method:
     * ```php
     * use Somnambulist\Components\Validation\Factory;
     * use Somnambulist\Components\Validation\Rules\In;
     *
     * $factory = new Factory();
     * $validation = $factory->validate($data,
     * [
     *    'enabled' =>
     *    [
     *       'required',
     *       In::make([true, 1])
     *    ]
     * ]);
     * ```
     * This rule uses in_array to perform the validation and by default does not perform strict checking.
     * If you require strict checking, you can invoke the rule like this:
     * ```php
     * use Somnambulist\Components\Validation\Factory;
     *
     * $factory = new Factory();
     * $validation = $factory->validate($data,
     * [
     *    'enabled' =>
     *    [
     *       'required',
     *       $factory->rule('in')->values([true, 1])->strict()
     *    ]
     * ]);
     * ```
     */
    public const string IN = 'in' ;

    /**
     * The field under validation must be an integer.
     */
    public const string INTEGER = 'integer' ;

    /**
     * The field under this rule must be a valid ipv4 or ipv6 address.
     */
    public const string IP = 'ipv' ;

    /**
     * The field under this rule must be a valid ipv4 address.
     */
    public const string IPV4 = 'ipv4' ;

    /**
     * The field under this rule must be a valid ipv6 address.
     */
    public const string IPV6 = 'ipv6' ;

    /**
     * The field under this validation must be a valid JSON string.
     */
    public const string JSON = 'json' ;

    /**
     * The field under this validation must be a string of exactly the length specified.
     */
    public const string LENGTH = 'length' ;

    /**
     * Ensures a given value is **less than** another field's value or to a fixed numeric constant
     */
    public const string LESS_THAN = 'lt' ;

    /**
     * Ensures a given value is **less than or equal to** another field's value or to a fixed numeric constant
     */
    public const string LESS_THAN_OR_EQUAL = 'lte' ;

    /**
     * The field under this validation must be in lowercase.
     */
    public const string LOWERCASE = 'lowercase' ;

    /**
     * The field under this rule must have a size less than or equal to the given number. Value size is calculated in the same way as the min rule.
     *
     * You can also validate the maximum size of uploaded files using this rule:
     *
     * @example
     * ```
     * $validation = $validator->validate
     * ([
     *     'photo' => $_FILES['photo']
     * ],
     * [
     *     'photo' => 'required|max:2M'
     * ]);
     * ```
     */
    public const string MAX = 'max' ;

    /**
     * The $_FILES item under validation must have a MIME type corresponding to one of the listed extensions.
     */
    public const string MIMES = 'mimes' ;

    /**
     * The field under this rule must have a size greater than or equal to the given number.
     *
     * For string values, the size corresponds to the number of characters.
     * For integer or float values, size corresponds to its numerical value.
     * For an array, size corresponds to the count of the array.
     *
     * If your value is numeric string, you can use the numeric rule to treat its size as a numeric value
     * instead of the number of characters.
     *
     * You can also validate the minimum size of uploaded files using this rule:
     *
     * @example
     * ```
     * $validation = $validator->validate
     * ([
     *     'photo' => $_FILES['photo']
     * ],
     * [
     *     'photo' => 'required|min:2M'
     * ]);
     * ```
     */
    public const string MIN = 'min' ;

    /**
     * The field under this rule must not be included in the given list of values.
     *
     * This rule also uses in_array and can have strict checks enabled the same way as In.
     *
     * ```php
     * not_in:value_1,value_2,...
     * ```
     */
    public const string NOT_IN = 'not_in' ;

    /**
     * The field under this rule may be empty.
     */
    public const string NULLABLE = 'nullable' ;

    /**
     * The field under this rule must be in the set of inputs, whatever the value is.
     */
    public const string PRESENT = 'present' ;

    /**
     * The field under this rule is not allowed.
     */
    public const string PROHIBITED = 'prohibited' ;

    /**
     * The field under this rule is not allowed if another_field is provided with any of the value(s).
     */
    public const string PROHIBITED_IF = 'prohibited_if' ;

    /**
     * The field under this rule is not allowed unless another_field has one of these values.
     * This is the inverse of prohibited_if.
     */
    public const string PROHIBITED_UNLESS = 'prohibited_unless' ;

    /**
     * The field under this rule must match the given regex.
     *
     * Note: if you require the use of |, then the regex rule must be written in array format instead of as a string.
     *
     * For example:
     * ```php
     * use Somnambulist\Components\Validation\Factory;
     *
     * $validation = (new Factory())->validate
     * ([
     *    'field' => 'value'
     * ],
     * [
     *    'field' =>
     *    [
     *      'required',
     *      'regex' => '/(this|that|value)/'
     *    ]
     * ])
     * ```
     */
    public const string REGEX = 'regex' ;

    /**
     * The field under this rule must have a value that corresponds to rejection i.e. 0 (zero), "0", false, no, "false", off.
     * This is the inverse of the accepted rule.
     */
    public const string REJECTED = 'rejected' ;

    /**
     * The field under this validation must be present and contain a non-empty value.
     *
     * Valid values include non-empty strings, numbers, and arrays with elements.
     * Empty values such as null, empty arrays, or empty strings are considered invalid.
     *
     * Examples:
     * | Value       | Valid |
     * | ----------- | ----- |
     * | 'something' | true  |
     * | '0'         | true  |
     * | 0           | true  |
     * | [0]         | true  |
     * | [null]      | true  |
     * | null        | false |
     * | []          | false |
     * | ''          | false |
     */
    public const string REQUIRED = 'required' ;

    /**
     * The field under this rule must be present and not empty if the another_field field is equal to any value.
     *
     * For example required_if:something,1,yes,on will be required if something's value is one of 1, '1', 'yes', or 'on'.
     *
     * ```php
     * required_if:another_field,value_1,value_2,...
     * ```
     */
    public const string REQUIRED_IF = 'required_if' ;

    /**
     * The field under validation must be present and not empty unless the another_field field is equal to any value.
     * ```php
     * required_unless:another_field,value_1,value_2,...
     * ```
     */
    public const string REQUIRED_UNLESS = 'required_unless' ;

    /**
     * The field under validation must be present and not empty only if any of the other specified fields are present.
     *
     * ```php
     * required_with:field_1,field_2,...
     * ```
     *
     * Note: the behaviour of this rule can be circumvented if the rule this is defined on is sometimes or nullable.
     *
     * For example: if a is "required_with:b", but a is also only sometimes present,
     * then the required_with will never trigger as the sometimes rule will negate it. a would also
     * need to be explicitly passed to trigger the rule.
     */
    public const string REQUIRED_WITH = 'required_with' ;

    /**
     * The field under validation must be present and not empty only if all the other specified fields are present.
     *
     * ```php
     * required_with_all:field_1,field_2,...
     * ```
     */
    public const string REQUIRED_WITH_ALL = 'required_with_all' ;

    /**
     * The field under validation must be present and not empty only when any of the other specified fields are not present.
     */
    public const string REQUIRED_WITHOUT = 'required_without' ;

    /**
     * The field under validation must be present and not empty only when all the other specified fields are not present.
     */
    public const string REQUIRED_WITHOUT_ALL = 'required_without_all' ;

    /**
     * The field under validation requires that the specified fields are present in the input data and are not empty.
     *
     * ```php
     * requires:field_1,field_2,...
     * ```
     *
     * For example: field b "requires:a"; if a is either not present, or has an "empty" value, then the validation fails.
     * "empty" is false, empty string, or null.
     *
     * This is an extension of required_with, however the rule will fail when used with sometimes or nullable.
     * For example: if b "requires:a" and "a" is allowed to be nullable, b will fail as it explicitly requires a with a value.
     */
    public const string REQUIRES = 'requires' ;


    /**
     * The field value under this rule must have the same value as another_field.
     */
    public const string SAME = 'same' ;

    /**
     * Sometimes attributes can be left off or can be null.
     *
     * These cases should be handled carefully and have different results after validation.
     *
     * @example
     * ```php
     * [
     *     'filters' => 'sometimes|array',
     * ]
     * ```
     */
    public const string SOMETIMES = 'sometimes' ;

    /**
     * The field under this validation must start with another_field.
     * Comparison can be against strings, numbers and array elements.
     */
    public const string STARTS_WITH = 'starts_with' ;

    /**
     * The field under this rule must be a PHP string.
     */
    public const string STRING = 'string' ;

    /**
     * The field under this validation must be in uppercase.
     */
    public const string UPPERCASE = 'uppercase' ;

    /**
     * The field under this rule must be a valid url format.
     *
     * The default is to validate the common format: any_scheme://.... You can specify specific URL schemes if you wish.
     *
     * @example
     * ```php
     * $validation = (new Factory)->validate($inputs,
     * [
     *     'random_url' => 'url',          // value can be `any_scheme://...`
     *     'https_url' => 'url:http',      // value must be started with `https://`
     *     'http_url' => 'url:http,https', // value must be started with `http://` or `https://`
     *     'ftp_url' => 'url:ftp',         // value must be started with `ftp://`
     *     'custom_url' => 'url:custom',   // value must be started with `custom://`
     * ]);
     * ```
     */
    public const string URL = 'url' ;

    /**
     * The field under this validation must be a valid UUID and not the nil UUID string.
     */
    public const string UUID = 'uuid' ;
}
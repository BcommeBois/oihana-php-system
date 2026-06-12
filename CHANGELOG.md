# Oihana PHP System OpenSource library - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Controllers
  - `ArchiveTrait` (`oihana\controllers\traits`) bundling files into a download response: `tarResponse()` (tar GZIP / BZIP2 / NONE, via `oihana\files\archive\tar\tar()`) and `zipResponse()` (renamed from `FileTrait::zip()`), sharing a private `archiveDownload()` helper for header emission, streaming and temp-file cleanup. Content headers are toggled through `FileResponseOption`. (CI now enables the `bz2` extension so bzip2 archives are exercised.)
  - `UploadTrait` (`oihana\controllers\traits`) receiving PSR-7 file uploads: `receiveUpload()` (single → stored path) and `receiveUploads()` (multiple → array of paths). Each file is validated (upload error code, optional `maxSize`, optional `allowedMimeTypes` checked with `oihana\files\validateMimeType()` on the stored file), its name sanitized (basename, anti-traversal), then moved into the destination; a `FileException` is thrown on any failure. Backed by the new `UploadOption` enum (`allowedMimeTypes`, `filename`, `maxSize`, `overwrite`).
  - `FileEncryptionTrait` (`oihana\controllers\traits`) encrypting/decrypting files through a DI-provided `oihana\files\openssl\OpenSSLFileEncryption` (passphrase lives in the container, never in the trait): `initializeFileEncryption()`, `encryptFile()`/`decryptFile()` (return the produced path), and `encryptedFileResponse()`/`decryptFileResponse()` (stream the produced file then remove the temp). When the encryption is not configured the response helpers return a `500`. Requires the `ext-openssl` extension (added to `require` and to the CI extensions).
  - `FileResponseOption` enum (`oihana\controllers\enums`) centralizing the file/image response option keys (`useContentType`, `useContentLength`, `useContentDisposition`, `contentDisposition`, `format`) to avoid magic strings.
  - `ImagickResponseOption` enum (`compression`, `quality`, `gray`, `strip`) and `ResizeOption` enum (`width`, `height`, `maxWidth`, `maxHeight`) backing `ImageTrait`'s Imagick transform and resize options.
  - `ImageTrait::initializeImagePath()` with a public `$imagePath` property, the `IMAGE_PATH` init key and `DEFAULT_COMPRESSION`/`DEFAULT_FORMAT`/`DEFAULT_MAX_HEIGHT`/`DEFAULT_MAX_WIDTH`/`DEFAULT_QUALITY` constants. The init accepts either a path string or an array keyed by `IMAGE_PATH` (mirrors `MockTrait::initializeMock()`).
- Traits
  - `LazyTrait` : Provides a configurable `lazy` mode resolved from the DI container, an initialization array or the property default (`initializeLazy()`, `isLazy()`, `LAZY` constant).
- Graphics
  - `AspectRatio` exposes simplified components as read-only properties: `aspectWidth`, `aspectHeight`, `locked`.
  - `AspectRatio::ratio()` returns the simplified ratio as a string (e.g. `16:9`).
  - `AspectRatio::toArray()` returns the dimensions and ratio as an associative array.
  - `AspectRatio::__toString()` formats the instance as `WxH (W:H)`.
  - `AspectRatio::fromRatio()` static factory builds an instance from a simplified ratio plus a target width.
  - `AspectRatio::setWidth()` / `setHeight()` public setters (mirroring the property assignments).
  - Field-name constants on `AspectRatio` (`WIDTH`, `HEIGHT`, `ASPECT_WIDTH`, `ASPECT_HEIGHT`, `RATIO`, `LOCKED`) to avoid magic strings.
- Tests
  - Comprehensive `AspectRatioTest` coverage (32 tests, 84 assertions) including validation, factory, fluent API and locked-mode rounding.
  - `LazyTraitTest` coverage (11 tests, 17 assertions) including init casting, container precedence and fallback.

### Changed

- Controllers
  - `FileTrait::fileResponse()` now validates the target with `oihana\files\assertFile()` before reading it: a missing or unreadable file raises a `FileException` caught by the existing handler (clean `500` response) instead of leaking `E_WARNING`s from `mime_content_type()`/`filesize()`/`file_get_contents()`. The `Content-Length` header value is now cast to a string (PSR-7 compliant).
  - `FileTrait::zip()` was extracted into `ArchiveTrait` and renamed `zipResponse()` (see _Added_). It now accepts an `$options` array (`FileResponseOption`) and shares the `archiveDownload()` helper with `tarResponse()`; the `Content-Disposition` filename is the archive **basename** (previously the full path). It still suppresses the redundant `E_WARNING` from `ZipArchive::open()` on failure (already surfaced as a `500`), removes the temporary archive after streaming, and uses `CacheControlDirective::NO_CACHE` for `Pragma`.
  - `ImageTrait::imageResponse()` now validates the file with `oihana\files\assertFile()` first (missing/unreadable file → clean `500` instead of leaked warnings), mirroring `FileTrait::fileResponse()`. The response/Imagick/resize option keys are extracted into the `FileResponseOption`, `ImagickResponseOption` and `ResizeOption` enums, the default values become trait constants, and the option arrays are read directly (`$options[KEY] ?? self::DEFAULT_X`) instead of the previous list-destructuring with spread defaults. `Content-Length` values are cast to strings (PSR-7).
  - `ImageTrait::getImagesRoot()` is renamed to `getImagePath()` and now returns the new `$imagePath` property instead of reading `$this->config['images']['root']` (the trait no longer depends on a `config` property).
- Helpers
  - The `getModel()`, `resolveDependency()` and `getDocumentsModel()` helper files moved from `src/oihana/models/helpers/` to `src/oihana/controllers/helpers/` so their physical location matches their already-declared `oihana\controllers\helpers` namespace. The fully-qualified function names are unchanged, so callers are unaffected. Their tests moved to `tests/oihana/controllers/helpers/` accordingly (and a wrong namespace on `GetDocumentsModelTest` was corrected), and the `composer.json` `files` autoload entries were updated.
- Date
  - `TimeInterval` now delegates the integral/fractional split of day and hour values to `oihana\core\numbers\modf()` (php-core); the private `numberBreakdown()` helper is removed (its negative-number branch was unreachable from the `(\d+...)` regex captures).
- Logging
  - `Logger` I/O calls (`mkdir`, `fopen`, `chmod`, `fwrite`) are now error-suppressed so a failed syscall returns cleanly instead of leaking an `E_WARNING` (harmonizes with `LoggerManager::ensureDirectory()`); behavior on the success path is unchanged. The duplicated `0664` literal is extracted into the private `DEFAULT_FILE_PERMISSIONS` constant, and `$_defaultPermissions` becomes the typed `DEFAULT_PERMISSIONS` constant.
  - `EmojiProcessor::__invoke()` simplified to a single lookup (mirroring `SymbolProcessor`); the previous `if/else` had an unreachable branch (all Monolog levels are mapped). Behavior is unchanged.
- Graphics
  - `AspectRatio` width and height are now `int` (previously `float|int`); negative values throw `InvalidArgumentException`.
  - `AspectRatio::lock()` and `AspectRatio::unlock()` now return `$this` (fluent API).
  - `AspectRatio` class members reordered per project convention.

### Fixed

- Controllers
  - `ImageTrait::imagickResponse()` no longer raises a `TypeError` from its error handler: the `catch` called `fail( $response , 500 , $e->getMessage() )`, passing the `Response` into the `?Request` slot and `500` into the `?Response` slot. It now calls `fail( null , $response , 500 , ... )` (the method has no request parameter), so an Imagick failure returns a proper `500` response instead of crashing.
  - `HttpCacheTrait::initializeHttpCache()` now reads the `CacheProvider` from the `$container` argument it just probed with `has()`, instead of from `$this->container`. The trait declares no `container` property (it does not use `ContainerTrait`), so resolving the provider from the DI container raised an uninitialized-property error in standalone use; it only worked inside `Controller` by accident. Now consistent with the sibling traits (`ApiTrait`, `RouterTrait`, `TwigTrait`, `BaseUrlTrait`).
- Date
  - `TimeInterval::parse()` no longer drops the fractional part of seconds on durations >= 60s: the decimal-precision detection searched for a space instead of the decimal point, so `parse(90.25)` returned `30.0` seconds instead of `30.25` (fractions were preserved below 60s only).
- Logging
  - `MonoLogManager` no longer coerces the default `Level::Debug` enum through `intval()`: building the manager without an explicit `level` emitted an `E_WARNING` and silently set the level to `1` instead of `Debug`. A `Level` instance is now preserved as-is; only non-enum values are cast to `int`.
- Models
  - `AlterBindVarsTrait::alterBindVars()` no longer throws a `TypeError` when passed `null`: the nullable `?array` argument was forwarded to `oihana\core\arrays\clean()`, which requires a strict `array`. `null` now short-circuits to `null` (consistent with the `?array` return type).
- Graphics
  - `AspectRatio` locked mode no longer drifts on rounded values: `setWidth()` / `setHeight()` skip `recalculateRatio()` when locked, preserving the snapshot aspect ratio.
  - `AspectRatio::fromRatio()` now validates that `$width > 0`.

### Removed

- Controllers
  - `FileTrait`: removed the unused public `$tmpPath` property (dead code — never read or written).
  - `ImageTrait`: removed the unused public `$compression` property (dead code — never read; the default is now the `DEFAULT_COMPRESSION` constant) and the `$image_response_default_options`/`$imagick_response_default_options`/`$resize_options_default` array properties (replaced by `DEFAULT_*` constants).
- Logging
  - `Logger`: removed the unused private `$_defaultSeverity` static property (dead code — never referenced).
- Graphics
  - `AspectRatio::isLocked()` (replaced by the read-only `$locked` property).

### Security

- Init
  - `initDefinitions()` now passes `$basePath` as the `allowedBase` argument to `requireAndMergeArrays()`, enforcing that every discovered `.php` file resolves inside the base directory. Defense in depth against arbitrary file inclusion via symlinks or path traversal in the definitions tree.

## [0.1.0] - 2026-05-20

### Added

- Controllers
  - Controllers
    - ModelCallTrait : Provides a centralized lifecycle around model calls for controllers.
  - Enums
    - Add the ControllerParam::CAPABILITIES and ControllerParam::CAPABILITIES_ENABLED constants
    - Add the Skin::INTERNAL special skin constant. 
  - Helpers
    - getBodyParam()
    - getBodyParams()
    - getQueryParam()
    - getParam()
      - getParamArray() 
      - getParamBool()
      - getParamFloat() 
      - getParamFloatRange() 
      - getParamI18n()
      - getParamInt()
      - getParamIntRange()
      - getParamNumberRange()
      - getParamString()
  - `StatusTrait::successWithNewBody()` — variant of `success()` that swaps
    the PSR-7 body for a fresh stream before writing the envelope. Use when
    an upstream actor (typically a sub-controller sharing the response chain)
    may have already written into the body. Prevents the `}{` double-envelope
    bug that breaks strict JSON parsers (NextJS RSC, modern fetch).
  - `StatusTrait::withFreshBody()` — returns the response with a fresh,
    empty PSR-7 body stream. Composable helper: chain before any other
    response helper (`fail`, `status`, `success`, `response`) when an
    upstream actor may have written into the shared body. Backs
    `successWithNewBody()` internally.
- Logging
  - Adds the CompositeLogger class
- Models
  - Interfaces
    - StreamModel interface with the `public function stream( array $init = [] ):Generator` signature.
  - Traits
    - EnsureKeysTrait
    - PropertyTrait
- Routes
  - http
    - ListRoute : GET route dispatching to the controller's `list()` method (collection read).
    - DeleteAllRoute : DELETE route dispatching to the controller's `deleteAll()` method (collection delete).
- Tests
  - Routes
    - http : full unit-test coverage for HttpMethodRoute, GetRoute, PostRoute, PutRoute, PatchRoute, DeleteRoute, ListRoute and DeleteAllRoute (route registration, default and overridden controller method, missing controller / missing method fallbacks).
- Validations
  - Helpers:
    - after, before, between, date, different, digits, digitsBetween, endsWith, length, max, min, regex, same, startsWith, url
    - rule() and rules() functions
  - Enums
    - Rules: ISO8601_DATE_TIME, ISO8601_DURATION, ISO8601_DATE_TIME_OR_DURATION
  - Rules: 
    - ConstantsRule, I18nRule
    - ISO8601DateTimeRule, ISO8601DurationRule, ISO8601DateTimeOrDurationRule
    - auth: JWTAlgorithmRule, EffectRule
    - http: HttpMethodRule
    - numeric: EqualRule, GreaterThanOrEqualRule, GreaterThanRule, LessThan, LessThanOrEqualRule, RangeRule
    - geo: LatitudeRule, LongitudeRule, ElevationRule
    - models: ExistModelRule, UniqueModelRule

### Fixed

Change filterLanguages signature to accept mixed and expand the docblock to clarify that non-array/object inputs are treated as invalid and return null (callers should validate upstream). 
Adjust formatting in examples and sanitize callback type. 
Add unit tests to assert the helper returns null for scalar inputs (string, int, bool). 
These changes document and test the helper's permissive behavior for invalid input shapes.

Resolve all PHPUnit 12 deprecations (`Using with*() on a test stub has no effect and is deprecated.`) across the test suite. Affected files: GetControllerTest, JsonTraitTest, CompositeLoggerTest, DocumentUrlTest, PDOTraitTest, CacheableTraitTest, MysqlModelTest. Tests that verify a call now use `createMock()` + `expects($this->once())->method(...)->with(...)`; tests that only need a return-value scaffold keep `createStub()` + `->method()->willReturn()`. As a side-effect, the CompositeLogger broadcast tests and several CacheableTrait tests now genuinely verify the calls instead of silently ignoring them.

Fix the `DateTrait` docblock (was incorrectly copy-pasted from an ArangoDB command) and reuse the trait's own `DEFAULT_*` constants as defaults for `$dateFormat` / `$timezone` instead of duplicated string literals.

### Changed

- `oihana\date\TimeInterval`: replace the tautological `get` hooks on `$days`, `$hours`, `$hoursPerDay`, `$minutes`, `$seconds` with the PHP 8.4 `public private(set)` notation. Public read access is unchanged; external writes are now disallowed (they were already not exposed via a setter). Method signatures and runtime behavior are untouched.
- `oihana\date\TimeInterval`: complete and tighten the docblocks across the class — note the side-effect of the optional `$duration` parameter on `formatted()` / `humanize()` / `toSeconds()` / `toMinutes()` (re-`parse()` mutates the instance), clarify the `parse()` contract, document the regex properties and the private `numberBreakdown()` shape. Remove the misleading `@access private` tag from the public `reset()` method.

### Removed

Use now the oihana-php-signals library and remove : 
- src\oihana\signal\Message
- src\oihana\signal\Notice
- src\oihana\signal\Payload
- src\oihana\signal\Receiver
- src\oihana\signal\Signal
- src\oihana\signal\SignalEntry
- src\oihana\signal\Signaler

Move the `oihana\mysql` package to the standalone [`oihana/php-mysql`](https://github.com/BcommeBois/oihana-php-mysql) library and remove:
- src\oihana\mysql\enums\MysqlParam
- src\oihana\mysql\enums\MysqlParamTrait
- src\oihana\mysql\enums\MysqlPrivileges
- src\oihana\mysql\traits\MysqlAssertionsTrait
- src\oihana\mysql\traits\MysqlDatabaseTrait
- src\oihana\mysql\traits\MysqlPrivilegeTrait
- src\oihana\mysql\traits\MysqlRootTrait
- src\oihana\mysql\traits\MysqlTableTrait
- src\oihana\mysql\traits\MysqlUserTrait
- src\oihana\mysql\MysqlDSN
- src\oihana\mysql\MysqlModel
- src\oihana\mysql\MysqlPDOBuilder

## [0.0.2] - 2025-10-29 (alpha)

### Added

- oihana\controllers\helpers\getController

- oihana\models\helpers\cacheCollection
- oihana\models\helpers\documentUrl
- oihana\models\helpers\getDocumentModel
- oihana\models\helpers\getModel
- oihana\models\helpers\resolveDependency

- oihana\models\enums\Alter
- oihana\models\enums\NoticeType
- oihana\models\notices\AfterDelete
- oihana\models\notices\AfterInsert
- oihana\models\notices\AfterReplace
- oihana\models\notices\AfterTruncate
- oihana\models\notices\AfterUpdate
- oihana\models\notices\AfterUpsert
- oihana\models\notices\BeforeDelete
- oihana\models\notices\BeforeInsert
- oihana\models\notices\BeforeReplace
- oihana\models\notices\BeforeTruncate
- oihana\models\notices\BeforeUpdate
- oihana\models\notices\BeforeUpsert
- oihana\models\traits\alters\AlterNotPropertyTrait

- oihana\signal\Message
- oihana\signal\Notice
- oihana\signal\Payload
- oihana\signal\Receiver
- oihana\signal\Signal
- oihana\signal\SignalEntry
- oihana\signal\Signaler

- oihana\validations\rules\ColorRule
- oihana\validations\rules\ContainerRule (abstract)
- oihana\validations\rules\InstanceOfRule
- oihana\validations\rules\ISO8601DateRule
- oihana\validations\rules\PostalCodeRule
- oihana\validations\rules\StartsWithRule

### Changed

- Move oihana\traits\AlterTrait (+dependencies) -> oihana\models\traits\AlterTrait
- Move oihana\traits\BindTrait -> oihana\models\traits\BindTrait
- oihana\traits\SortTrait

## [0.0.0] - 2025-08-13 (alpha)

### Added

- Adds the oihana\controllers package (beta)
- Adds the oihana\routes package (beta)

- Adds oihana\date\TimeInterval
- Adds oihana\date\traits\DateTrait

- Adds oihana\http\HttpHeaders
- Adds oihana\http\HttpMethod
- Adds oihana\http\HttpParamStrategy

- Adds oihana\init\initConfig
- Adds oihana\init\initContainer
- Adds oihana\init\initDefaultTimezone
- Adds oihana\init\initDefinitions
- Adds oihana\init\initErrors
- Adds oihana\init\initMemoryLimit
- Adds oihana\init\setIniIfExists

- Adds oihana\logging\Logger
- Adds oihana\logging\LoggerConfig
- Adds oihana\logging\LoggerManager
- Adds oihana\logging\LoggerManagerTrait
- Adds oihana\logging\LoggerTrait
- Adds oihana\logging\MonoLogConfig
- Adds oihana\logging\MonoLogManager
- Adds oihana\logging\monolog\processors\EmojiProcessor
- Adds oihana\logging\monolog\processors\SymbolProcessor

- Adds oihana\models\Model
- Adds oihana\models\interfaces\CountModel
- Adds oihana\models\interfaces\DeleteAllModel
- Adds oihana\models\interfaces\DeleteModel
- Adds oihana\models\interfaces\DocumentsModel
- Adds oihana\models\interfaces\ExistModel
- Adds oihana\models\interfaces\GetModel
- Adds oihana\models\interfaces\InsertModel
- Adds oihana\models\interfaces\ListModel
- Adds oihana\models\interfaces\ReplaceModel
- Adds oihana\models\interfaces\TruncateModel
- Adds oihana\models\interfaces\UpdateModel
- Adds oihana\models\interfaces\UpsertModel
- Adds oihana\models\helpers\documentUrl
- Adds oihana\models\pdo\PDOModel
- Adds oihana\models\pdo\PDOTrait
- Adds oihana\models\traits\DocumentsTrait
- Adds oihana\models\traits\ListModelTrait
- Adds oihana\models\traits\ModelTrait

- Adds oihana\options\Option
- Adds oihana\options\Options

- Adds oihana\traits\AlterDocumentTrait
- Adds oihana\traits\BindTrait
- Adds oihana\traits\CacheableTrait
- Adds oihana\traits\ConfigTrait
- Adds oihana\traits\ContainerTrait
- Adds oihana\traits\DebugTrait
- Adds oihana\traits\IDTrait
- Adds oihana\traits\JsonOptionsTrait
- Adds oihana\traits\KeyValueTrait
- Adds oihana\traits\LockableTrait
- Adds oihana\traits\PDOTrait
- Adds oihana\traits\QueryIDTrait
- Adds oihana\traits\ToStringTrait
- Adds oihana\traits\UnsupportedTrait
- Adds oihana\traits\UriTrait

- Adds oihana\traits\alters\AlterArrayCleanPropertyTrait
- Adds oihana\traits\alters\AlterArrayProperty
- Adds oihana\traits\alters\AlterCallablePropertyTrait
- Adds oihana\traits\alters\AlterFloatPropertyTrait
- Adds oihana\traits\alters\AlterGetDocumentPropertyTrait
- Adds oihana\traits\alters\AlterIntPropertyTrait
- Adds oihana\traits\alters\AlterJSONParsePropertyTrait
- Adds oihana\traits\alters\AlterJsonStringifyPropertyTrait
- Adds oihana\traits\alters\AlterUrlPropertyTrait

- Adds oihana\traits\strings\ExpressionTrait


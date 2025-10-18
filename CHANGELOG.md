# Oihana PHP System OpenSource library - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- oihana\models\helpers\getModel

- oihana\mysql\enums\traits\MysqlParamTrait
- oihana\mysql\enums\MysqlParam
- oihana\mysql\enums\MysqlPrivileges
- oihana\mysql\traits\MysqlAssertionsTrait
- oihana\mysql\traits\MysqlDatabaseTrait
- oihana\mysql\traits\MysqlPrivilegeTrait
- oihana\mysql\traits\MysqlRootTrait
- oihana\mysql\traits\MysqlTableTrait
- oihana\mysql\traits\MysqlUserTrait
- oihana\mysql\MysqlDSN
- oihana\mysql\MysqlModel
- oihana\mysql\MysqlPDOBuilder

- oihana\models\enums\Alter
- oihana\models\traits\alters\AlterNotPropertyTrait

### Changed

- Move oihana\traits\AlterTrait (+dependencies) -> oihana\models\traits\AlterTrait
- oihana\traits\SortTrait

### TODO

  - Move oihana\mysql package to a standalone library.

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


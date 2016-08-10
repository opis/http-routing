# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## 5.0.x-dev
### Added
- Support for PHP 7.0.x

### Removed
- Support for PHP 5.x
- `Opis\HttpRouting\Dispatcher` class

### Changed
- All classes were modified in order to support transition to PHP 7.0.x
- Updated `opis/routing` dependency to version `5.0.x-dev`
- Updated `phpunit/phpunit` dependency(dev mode) to version `5.4.*`
- `Opis\HttpRouting\Path` class was renamed to `Opis\HttpRouting\Context` 

### Fixed
- Nothing

## v4.0.2 - 2016.03.20
### Added
- Nothig

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Fixed a bug in `Route::domainCompiler`  method

## v4.0.1 - 2016.01.19
### Added
- Nothig

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Fixed a bug in `Opis\HttpRouting\CallbackFilter`

## v4.0.0 - 2016.01.16
### Added
- Tests

### Removed
- Removed `branch-alias` property from `composer.json` file

### Changed
- Updated `opis/routing` library dependency to version `^4.1.0`
- Modified classes to reflect changes in `opis/routing` library

### Fixed
- Fixed CS

## v3.0.0 - 2015.07.31
### Added
- `before`, `after` and `access` methods were added to `Opis\HttpRouting\Route` class
-  `CallbackFilter` class to replace `ClosureFilter` class 
- `HttpError` class

### Removed
- `preFilter`, `postFilter` and `accessFilter` methods were removed from `Opis\HttpRouting\Route` class
- `ClosureFilter` class 

### Changed
- Updated `opis/routing` library dependency to version `3.0.*`

### Fixed
- Nothing

## v2.5.0 - 2015.03.20
### Added
- Support for late binding

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.5.*`

### Fixed
- Nothing

## v2.4.2 - 2014.11.25
### Added
- Autoload file

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Nothing

## v2.4.1 - 2014.11.11
### Added
- Nothig

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Bugfix in `Opis\HttpRouting\Route` class

## v2.4.0 - 2014.10.23
### Added
- Nothig

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.4.*`

### Fixed
- Nothing

## v2.3.0 - 2014.06.11
### Added
- Nothig

### Removed
- Nothing

### Changed
- Updated `opis/routing` library dependency to version `2.3.*`
- Updated `Opis\HttpRouting\Route` class to reflect changes that were made in `opis/routing`.

### Fixed
- Nothing

## v2.2.2 - 2014.06.08
### Added
- Nothig

### Removed
- Nothing

### Changed
- Modified serialize method to be compatible with `opis/routing:2.2.1`

### Fixed
- Nothing

## v2.2.1 - 2014.06.05
### Added
- Nothig

### Removed
- Nothing

### Changed
- Nothing

### Fixed
- Fixed a bug in `Opis\HttpRouting\RouteCollection`

## v2.2.0 - 2014.06.03
### Added
- Changelog file
- Added `accessDenied` method to `Opis\HttpRouting\RouteCollection`
- Removed `useFilters` method from `Opis\HttpRouting\Route`
- Added `preFilter`, `postFilter` and `accessFilter` methods to `Opis\HttpRouting\Route` class
- Added `Opis\HttpRouting\ClosureFilter` class

### Removed
- Nothing

### Changed
- Modified `Opis\HttpRouting\UserFilter` class
- Changed filter order in `Opis\HttpRouting\Router`
- Updated `opis/routing` dependency to version `2.2.*`

### Fixed
- Nothing

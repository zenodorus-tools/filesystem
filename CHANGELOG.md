# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.6] - 2018-02-08

### Fixed

- `Filesystem::resolve()` now respects paths that were absolute when fed to it.
  (Previously it would convert them into relative paths, which is...bad.)

## [0.1.5] - 2018-02-08

### Fixed

- `Filesystem::slash()` will now respect trailing/leading slashes, and will
  remove any double slashes that are created by it or fed into it.
## [0.1.4] - 2018-02-08

### Added

- `Filesystem::slashAr()` which passed array contents to `Filesystem::slash()`
  as individual arguments.

## [0.1.3] - 2018-02-08

### Added

- `Filesystem::recursiveRemove()` which removed everything in a directory with
  one command.

## [0.1.2] - 2018-02-07

### Fixed

- Typo in `Filesystem::slash()`.

## [0.1.1] - 2018-02-07

### Changed

- `Filesystem::slash()` now accepts an arbitrary number of arguments, and
  contactenates them all.
- `Filesystem::resolveReal()` uses `file_exists()` instead of `realpath()`.

### Added

- Tests for longer list of arguments to `Filesystem::slash()`.

## [0.1.0] - 2018-02-06

### Added

- Created project.
- Added `Filesystem` methods:
  - `slash()` adds a directory separator between strings.
  - `resolve()` attempts to get a nice directory path.
  - `resolveReal()` is similar to `resolve()` but handles real, absolute paths.
- Added `Directories` methods:
  - `isBeneath()` compares path strings to determine parentage.
  - `isBeneathResolve()` compare strings resolved with `resolve()` to determine
    parentage.
  - `isBeneathReal()` compare strings for real, absolute paths to determine
    parentage.
- Added tests for `Filesystem` and `Directories` methods.
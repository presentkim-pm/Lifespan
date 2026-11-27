# Changelog

## 5.0.0 (2026-11-27)

The Poggit plugin version for the original [pmmp/PocketMine-MP](https://github.com/pmmp/PocketMine-MP) 5.x (4.0.0) is kept
on the [`legacy/pmmp-5.44`](https://github.com/presentkim-pm/LifeSpan/tree/legacy/pmmp-5.44) branch.

### ⚠️ Breaking changes
- Targets [axolotl-pm/PocketMine-MP](https://github.com/axolotl-pm/PocketMine-MP) `5.51` (BedrockProtocol 64,
  Bedrock `1.26.60`) and **PHP 8.4+**.
- Distributed as a phar from GitHub Releases instead of Poggit.

### 🔄 Changes
- `composer.json` added (Composer is only used for development and testing); `plugin.yml` has
  `src-namespace-prefix` and the source files are no longer nested in `src/kim/present/lifespan/`.
- `Main` is now `final` and `onEnable()` is `protected`.
- PHPUnit tests (class loading, `plugin.yml` consistency and the seconds-to-ticks conversion).

### 🐛 Fixes
- A decimal value in `config.yml` such as `60.0` or `2.5` made the plugin fail to enable with a `TypeError`; values are
  now converted to ticks properly.
- An invalid `item-lifespan` / `arrow-lifespan` no longer disables the plugin: it is reported and the default is used.

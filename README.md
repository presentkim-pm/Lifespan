<!-- PROJECT BADGES -->
<div align="center">

![Version][version-badge]
[![Stars][stars-badge]][stars-url]
[![License][license-badge]][license-url]

</div>


<!-- PROJECT LOGO -->
<br />
<div align="center">
  <img src="https://raw.githubusercontent.com/presentkim-pm/LifeSpan/main/assets/icon.png" alt="Logo" width="80" height="80">
  <h3>Lifespan</h3>
  <p align="center">
    A plugin that sets the lifespan of items and arrows!

[Contact to me][author-discord] · [Report a bug][issues-url] · [Request a feature][issues-url]

  </p>
</div>


<!-- ABOUT THE PROJECT -->

### Overview

- **What it does**
  - Sets the lifespan of item entities and arrow entities, configurable in `config.yml`.
- **Why it exists**
  - Do you want items to disappear in less than 5 minutes? Or is there no need for arrows to exist for more than 1
    minute? Or have you been bothered by a periodic cleaning plugin removing recently dropped items?
    Primarily useful as a replacement for periodic item entity removal plugins.

---

### Installation & Requirements

- **Server**
  - [axolotl-pm/PocketMine-MP](https://github.com/axolotl-pm/PocketMine-MP) `5.51` or higher (`plugin.yml` `api: [5.51.0]`),
    which uses BedrockProtocol 64 (Bedrock `1.26.60`).
  - PHP `8.4` or higher.
- **Installation**
  - Download `Lifespan.phar` from [GitHub Releases][releases-url] and place it into your server's `plugins` directory.
  - Restart the server; the plugin will load automatically according to `plugin.yml`.
- **Old versions**
  - The version for the original [pmmp/PocketMine-MP](https://github.com/pmmp/PocketMine-MP) `5.x` (PHP 8.1+, distributed
    through Poggit) is kept on the [`legacy/pmmp-5.44`](https://github.com/presentkim-pm/LifeSpan/tree/legacy/pmmp-5.44) branch.
- **Development**
  - `composer install` then `composer test` runs the PHPUnit tests.

---

### Configuration

`config.yml`:

```yaml
# Item entity lifespan [float: seconds] (default: 300)
item-lifespan: 300

# arrow entity lifespan [float: seconds] (default: 60)
arrow-lifespan: 60
```

- Values are in seconds and may be decimals. The maximum is `1638` seconds (`0x7fff` ticks).
- An invalid value is reported in the console and replaced with the default instead of disabling the plugin.
- Other plugins can also change the lifespans at runtime with `Main::setItemLifespan()` and `Main::setArrowLifespan()`
  (in ticks).

---

### License

Distributed under the **LGPL 3.0**. See [LICENSE][license-url] for more information

-----

[author-discord]: https://discordapp.com/users/345772340279508993

[version-badge]: https://img.shields.io/github/v/release/presentkim-pm/LifeSpan?display_name=tag&style=for-the-badge&label=VERSION

[release-badge]: https://img.shields.io/github/downloads/presentkim-pm/LifeSpan/total?style=for-the-badge&label=GITHUB%20

[stars-badge]: https://img.shields.io/github/stars/presentkim-pm/LifeSpan.svg?style=for-the-badge

[license-badge]: https://img.shields.io/github/license/presentkim-pm/LifeSpan.svg?style=for-the-badge

[stars-url]: https://github.com/presentkim-pm/LifeSpan/stargazers

[releases-url]: https://github.com/presentkim-pm/LifeSpan/releases

[issues-url]: https://github.com/presentkim-pm/LifeSpan/issues

[license-url]: https://github.com/presentkim-pm/LifeSpan/blob/main/LICENSE

[project-icon]: https://raw.githubusercontent.com/presentkim-pm/LifeSpan/main/assets/icon.png

[![Telegram](https://img.shields.io/badge/Telegram-PresentKim-blue.svg?logo=telegram)](https://t.me/PresentKim)

[![icon/192x192](assets/icon/192x192.png?raw=true)]()

[![License](https://img.shields.io/github/license/PMMPPlugin/LifeSpan.svg?label=License)](LICENSE)
[![Poggit](https://poggit.pmmp.io/ci.shield/PMMPPlugin/LifeSpan/LifeSpan)](https://poggit.pmmp.io/ci/PMMPPlugin/LifeSpan)
[![Release](https://img.shields.io/github/release/PMMPPlugin/LifeSpan.svg?label=Release)](https://github.com/PMMPPlugin/LifeSpan/releases/latest)
[![Download](https://img.shields.io/github/downloads/PMMPPlugin/LifeSpan/total.svg?label=Download)](https://github.com/PMMPPlugin/LifeSpan/releases/latest)


A plugin control item and arrow's lifespan for PocketMine-MP

## Command
Main command : `/lifespan <item | arrow | lang | reload | save>`

| subcommand | arguments           | description            |
| ---------- | ------------------- | ---------------------- |
| Item       | \<tick\>            | Set item's lifespan    |
| Arrow      | \<tick\>            | Set arrow's lifespan   |
| Lang       | \<language prefix\> | Load default lang file |
| Reload     |                     | Reload all data        |
| Save       |                     | Save all data          |




## Permission
| permission          | default | description       |
| ------------------- | ------- | ----------------- |
| lifespan.cmd        | OP      | main command      |
|                     |         |                   |
| lifespan.cmd.item   | OP      | item  subcommand  |
| lifespan.cmd.arrow  | OP      | arrow subcommand  |
| lifespan.cmd.lang   | OP      | lang subcommand   |
| lifespan.cmd.reload | OP      | reload subcommand |
| lifespan.cmd.save   | OP      | save subcommand   |




## ChangeLog
### v1.0.0 [![Source](https://img.shields.io/badge/source-v1.0.0-blue.png?label=source)](https://github.com/PMMPPlugin/LifeSpan/tree/v1.0.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/LifeSpan/v1.0.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/LifeSpan/releases/v1.0.0)
- First release
  
  
---
### v1.1.0 [![Source](https://img.shields.io/badge/source-v1.1.0-blue.png?label=source)](https://github.com/PMMPPlugin/LifeSpan/tree/v1.1.0) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/LifeSpan/v1.1.0/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/LifeSpan/releases/v1.1.0)
- \[Fixed\] main command config not work
- \[Changed\] permission
- \[Changed\] translation method
- \[Changed\] command structure
  
  
---
### v1.1.1 [![Source](https://img.shields.io/badge/source-v1.1.1-blue.png?label=source)](https://github.com/PMMPPlugin/LifeSpan/tree/v1.1.1) [![Release](https://img.shields.io/github/downloads/PMMPPlugin/LifeSpan/v1.1.1/total.png?label=download&colorB=1fadad)](https://github.com/PMMPPlugin/LifeSpan/releases/v1.1.1)
- \[Changed\] Add return type hint
- \[Fixed\] Violation of PSR-0
- \[Changed\] Rename main class to LifeSpan
- \[Added\] Add PluginCommand getter and setter
- \[Added\] Add getters and setters to SubCommand
- \[Fixed\] Add api 3.0.0-ALPHA11
- \[Added\] Add website and description
- \[Changed\] Show only subcommands that sender have permission to use
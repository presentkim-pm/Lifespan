# LifeSpan [![license](https://img.shields.io/github/license/Blugin/LifeSpan-PMMP.svg?label=License)](LICENSE)
[![icon](assets/icon/192x192.png?raw=true)]()  

[![release](https://img.shields.io/github/release/Blugin/LifeSpan-PMMP.svg?label=Release)](https://github.com/Blugin/LifeSpan-PMMP/releases/latest) [![download](https://img.shields.io/github/downloads/Blugin/LifeSpan-PMMP/total.svg?label=Download)](https://github.com/Blugin/LifeSpan-PMMP/releases/latest)


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
  
<br/><br/>
  
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
  
<br/><br/>
  
## Required API
- PocketMine-MP : higher than [Build #937](https://jenkins.pmmp.io/job/PocketMine-MP/937)
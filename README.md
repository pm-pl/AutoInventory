# AutoInventory

AutoInventory is a powerful and user-friendly plugin for automated inventory management in Minecraft.

[![](https://poggit.pmmp.io/shield.state/AutoInventory)](https://poggit.pmmp.io/p/AutoInventory)
<a href="https://poggit.pmmp.io/p/AutoInventory"><img src="https://poggit.pmmp.io/shield.state/AutoInventory"></a> [![](https://poggit.pmmp.io/shield.api/AutoInventory)](https://poggit.pmmp.io/p/AutoInventory)
<a href="https://poggit.pmmp.io/p/AutoInventory"><img src="https://poggit.pmmp.io/shield.api/AutoInventory"></a>

# Features

• Automatic item collection: Items dropped from blocks are directly added to the player's inventory.\
• Inventory handling: When the inventory is full, a customizable popup message is displayed to the player.\
• Experience management: Automatically adds experience points to the player when eligible.

# Usage

AutoInventory works seamlessly in the background once installed. Simply join the game and\
start collecting items automatically. If your inventory becomes full, a helpful message will\
notify you.

# Config

```# Config Version (DO NOT TOUCH)
version: 1.1.0
# full inventory message: use '&' symbol for color codes
full_inventory_message: "&cYour inventory is now full!"
# auto collecting experience true = enable, false = disable
auto_experience: true
# list the worlds you want to enable the plugin on.
enabled_worlds:
  - world
  - world2
# list the worlds you want to disable the plugin on.
disabled_worlds:
  - world3

  - world2
  ```

# Todo

- [x] Implement auto pick up for mobs
- [x] Implement auto xp for mobs
- [x] Auto XP for players
- [x] Different message types (message, actionbar)
- [x] Auto config updater

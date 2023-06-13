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

```# full inventory message: use '&' symbol for color codes
full_inventory_message: "&cYour inventory is now &6full!"
# auto collecting experience true = enable, false = disable
auto_experience: true
# list the worlds you want to enable the plugin on.
enabled_worlds:
  - world
# list the worlds you want to disable the plugin on.
disabled_worlds:
  - world2
  ```

# Todo

- [ ] Implement auto pick up for mobs
- [ ] Implement auto xp for mobs
- [ ] Different message types (message, actionbar)
- [ ] Auto config updater

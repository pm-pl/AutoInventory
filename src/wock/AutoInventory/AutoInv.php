<?php

namespace wock\AutoInventory;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class AutoInv extends PluginBase implements Listener {

    /** @var string[] */
    public $enabledWorlds;

    /** @var string[] */
    public $disabledWorlds;

    public function onEnable(): void
    {
        // Load the plugin configuration
        $this->saveDefaultConfig();
        $this->reloadConfig();

        // Get the enabled and disabled worlds from the configuration
        $this->enabledWorlds = $this->getConfig()->get("enabled_worlds", []);
        $this->disabledWorlds = $this->getConfig()->get("disabled_worlds", []);

        // Register the event listener
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function isWorldEnabled(string $worldName): bool
    {
        return in_array($worldName, $this->enabledWorlds);
    }

    public function isWorldDisabled(string $worldName): bool
    {
        return in_array($worldName, $this->disabledWorlds);
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $config = $this->getConfig();
        $autoExpEnabled = $config->get("auto_experience", true);
        $enabledWorlds = $config->get("enabled_worlds", []);
        $disabledWorlds = $config->get("disabled_worlds", []);

        $worldName = $player->getWorld()->getFolderName();

        if (in_array($worldName, $enabledWorlds)) {
            $drops = $event->getDrops();
            $inventory = $player->getInventory();
            foreach ($drops as $drop) {
                if (!$inventory->canAddItem($drop)) {
                    $event->cancel();
                    $this->showFullInventoryPopup($player);
                    return;
                }
                $inventory->addItem($drop);
            }
            $event->setDrops([]);

            if ($autoExpEnabled) {
                // Give experience to the player
                $xpDrops = $event->getXpDropAmount();
                $player->getXpManager()->addXp($xpDrops);
                $event->setXpDropAmount(0);
            }
        } elseif (in_array($worldName, $disabledWorlds)) {
            // The world is in the disabled list, do not execute the method
            return;
        }
    }

    public function showFullInventoryPopup(Player $player)
    {
        $config = $this->getConfig();
        $message = $config->get("full_inventory_message", "Your inventory is now full!");

        // Replace '&' with '§' for color formatting
        $formattedMessage = str_replace('&', '§', $message);

        $player->sendTitle($formattedMessage);
    }
}

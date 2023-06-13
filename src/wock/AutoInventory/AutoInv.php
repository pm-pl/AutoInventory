<?php

namespace wock\AutoInventory;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class AutoInv extends PluginBase implements Listener {

    /** @var AutoInv */
    private static $instance;

    /** @var string[] */
    public array $enabledWorlds;

    /** @var string[] */
    public array $disabledWorlds;

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        // Load the plugin configuration
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->updateConfig();

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

    public function updateConfig(): void
    {
        $currentVersion = $this->getConfig()->get("version", null);
        $latestVersion = "1.1.0";

        if ($currentVersion === null) {
            // Update specific config values (IGNORE)
            $config = $this->getConfig();
            $config->set("auto_experience", $config->get("auto_experience_enable", true));
            $config->remove("auto_experience_enable");

            $config->set("version", $latestVersion);
            $config->save();
        } elseif ($currentVersion !== $latestVersion) {
            $config = $this->getConfig();
            // other necessary updates here (FOR FUTURE IGNORE)

            $config->set("version", $latestVersion);
            $config->save();
        }
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

    public static function getInstance(): AutoInv
    {
        return self::$instance;
    }
}

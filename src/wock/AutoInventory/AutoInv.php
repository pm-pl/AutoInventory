<?php

namespace wock\AutoInventory;

use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class AutoInv extends PluginBase implements Listener {

    /** @var AutoInv */
    private static AutoInv $instance;

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
        $latestVersion = "1.1.1";

        if ($currentVersion === null) {
            // Add the message_type option
            $config = $this->getConfig();
            $config->set("message_type", "title");

            // Update specific config values (IGNORE)
            $config->set("auto_experience", $config->get("auto_experience_enable", true));
            $config->remove("auto_experience_enable");

            $config->set("version", $latestVersion);
            $config->save();
        } elseif ($currentVersion !== $latestVersion) {
            // Perform other necessary updates here (FOR FUTURE IGNORE)

            // Check if the message_type option exists and add it if necessary
            $config = $this->getConfig();
            if (!$config->exists("message_type")) {
                $config->set("message_type", "title");
                $config->save();
            }

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
        $dropOnFullInv = $config->get("drop-on-full-inv", true); // Read the config option

        $worldName = $player->getWorld()->getFolderName();

        if (in_array($worldName, $enabledWorlds)) {
            $drops = $event->getDrops();
            $inventory = $player->getInventory();
            $dropItems = [];

            foreach ($drops as $drop) {
                if (!$inventory->canAddItem($drop)) {
                    if ($dropOnFullInv) {
                        // Add the items to the dropItems array
                        $dropItems[] = $drop;
                    } else {
                        // Cancel the block break event
                        $event->cancel();
                        $this->showFullInventoryMessage($player);
                    }
                } else {
                    $inventory->addItem($drop);
                }
            }

            // Drop the items on the ground if dropItems array is not empty
            if (!empty($dropItems)) {
                foreach ($dropItems as $dropItem) {
                    $player->getWorld()->dropItem($player->getPosition(), $dropItem);
                }
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

    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        $config = $this->getConfig();
        $autoExpEnabled = $config->get("auto_experience", true);
        if ($autoExpEnabled) {
            if($cause instanceof EntityDamageByEntityEvent){
                $damager = $cause->getDamager();
                if($damager instanceof Player){
                    $damager->getXpManager()->addXp($player->getXpDropAmount());
                    $event->setXpDropAmount(0);
                }
            }
        }
    }

    public function onEntityDeath(EntityDeathEvent $event): void
    {
        $entity = $event->getEntity();
        $cause = $entity->getLastDamageCause();
        $config = $this->getConfig();
        $autoExpEnabled = $config->get("auto_experience", true);
        $dropOnFullInv = $config->get("drop-on-full-inv", true);

        if ($cause instanceof EntityDamageByEntityEvent) {
            if ($autoExpEnabled) {
                $damager = $cause->getDamager();
                if ($damager instanceof Player) {
                    $damager->getXpManager()->addXp($entity->getXpDropAmount());
                    $event->setXpDropAmount(0);
                }
            }

            if ($dropOnFullInv) {
                $drops = $event->getDrops();
                $damager = $cause->getDamager();

                if ($damager instanceof Player) {
                    $inventory = $damager->getInventory();
                    foreach ($drops as $drop) {
                        if (!$inventory->canAddItem($drop)) {
                            return;
                        }
                        $inventory->addItem($drop);
                    }
                }

                $event->setDrops([]);
            }
        }
    }

    public function showFullInventoryMessage(Player $player)
    {
        $config = $this->getConfig();
        $message = $config->get("full_inventory_message", "Your inventory is now full!");
        $messageType = strtolower($config->get("message_type", "title")); // Get the message type from the config

        $formattedMessage = str_replace('&', '§', $message);

        switch ($messageType) {
            case "title":
                $player->sendTitle($formattedMessage);
                break;
            case "actionbar":
                $player->sendActionBarMessage($formattedMessage);
                break;
            case "chat":
            default:
                $player->sendMessage($formattedMessage);
                break;
        }
    }

    public static function getInstance(): AutoInv
    {
        return self::$instance;
    }
}

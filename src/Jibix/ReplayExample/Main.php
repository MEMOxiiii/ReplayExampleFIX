<?php
/**
 * Class Main
 * @author Jibix
 * @date 20.04.2025 - 16:52
 * @project ReplayExample
 */
namespace Jibix\ReplayExample;
use Jibix\ReplayExample\libs\Jibix\AsyncMedoo\AsyncMedoo;
use Jibix\ReplayExample\libs\Jibix\AsyncMedoo\MySQLCredentials;
use Jibix\ReplayExample\libs\Jibix\FunctionalItem\FunctionalItemManager;
use Jibix\ReplayExample\libs\Jibix\Replay\listener\replay\ReplayListener;
use Jibix\ReplayExample\libs\Jibix\Replay\provider\type\JsonProvider;
use Jibix\ReplayExample\command\ReplayCommand;
use Jibix\ReplayExample\item\replay\EventLogItem;
use Jibix\ReplayExample\item\replay\PauseItem;
use Jibix\ReplayExample\item\replay\QuitReplayItem;
use Jibix\ReplayExample\item\replay\ReverseItem;
use Jibix\ReplayExample\item\replay\RewindItem;
use Jibix\ReplayExample\item\replay\SkipItem;
use Jibix\ReplayExample\item\replay\SpeedItem;
use Jibix\ReplayExample\item\ReplaySelectorItem;
use Jibix\ReplayExample\libs\Jibix\Replay\provider\type\MySQLProvider;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\replayer\ReplaySettings;
use Jibix\ReplayExample\session\ReplaySession;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;


class Main extends PluginBase{
    use SingletonTrait{
        setInstance as private;
        reset as private;
    }

    private const TYPE_REPLAY = "replay";

    private ReplaySettings $settings;
    private bool $isReplayServer = false;

    protected function onLoad(): void{
        self::setInstance($this);
        $this->saveDefaultConfig();
        $data = $this->getConfig()->getAll();
        AsyncMedoo::initialize(MySQLCredentials::fromArray($data['mysql-credentials'] ?? []));
        $this->settings = new ReplaySettings(
            $this,
            match (mb_strtolower($data['provider'] ?? "mysql")) {
                "json" => new JsonProvider(empty($path = $data['json-path']) ? $this->getDataFolder() . "replays.json" : $path),
                default => new MySQLProvider(),
            },
            $data['unreversable-level-event-ids'] ?? [],
        );

        if (mb_strtolower($data["server-type"] ?? "") === self::TYPE_REPLAY) $this->isReplayServer = true;
    }

    protected function onEnable(): void{
        if ($this->isReplayServer) {
            $this->getServer()->getLogger()->notice("Using the replay system in§b replay§r mode");
            $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
            $this->getServer()->getPluginManager()->registerEvents(new ReplayListener(), $this);
            FunctionalItemManager::register($this);
            FunctionalItemManager::getInstance()->registerFunctionalItem(
                new ReplaySelectorItem(),

                new EventLogItem(),
                new PauseItem(),
                new ReverseItem(),
                new RewindItem(),
                new SkipItem(),
                new SpeedItem(),
                new QuitReplayItem()
            );
        } else {
            $this->getServer()->getLogger()->notice("Using the replay system in§b record§r mode");
            $this->getServer()->getCommandMap()->register($this->getName(), new ReplayCommand($this, "replay"));
        }
    }

    protected function onDisable(): void{
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            ReplaySession::get($player)->getReplay()?->end();
        }
    }

    public function getSettings(): ReplaySettings{
        return $this->settings;
    }
}
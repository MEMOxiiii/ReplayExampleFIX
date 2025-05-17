<?php
namespace Jibix\ReplayExample\libs\Jibix\Replay\replay\action\type\world;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\Action;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\ActionIds;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\type\WorldAction;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\replayer\Replay;
use Jibix\ReplayExample\libs\Jibix\Replay\util\BinaryStream;


/**
 * Class WorldChangeTimeAction
 * @author Jibix
 * @date 25.12.2024 - 23:15
 * @project Replay
 */
class WorldChangeTimeAction extends WorldAction{

    protected const ID = ActionIds::WORLD_CHANGE_TIME;

    private int $time;

    public static function create(int $time): self{
        $action = new self();
        $action->time = $time;
        return $action;
    }

    public function serialize(BinaryStream $stream): void{
        $stream->putInt($this->time);
    }

    public function deserialize(BinaryStream $stream): void{
        $this->time = $stream->getInt();
    }

    public function handle(Replay $replay): void{
        $replay->getWorld()->setTime($this->time);
    }

    public function handleReversed(Replay $replay): ?Action{
        return self::create($replay->getWorld()->getTime());
    }
}
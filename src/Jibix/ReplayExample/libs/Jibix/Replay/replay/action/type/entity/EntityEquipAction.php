<?php
namespace Jibix\ReplayExample\libs\Jibix\Replay\replay\action\type\entity;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\Action;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\ActionIds;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\action\type\EntityAction;
use Jibix\ReplayExample\libs\Jibix\Replay\replay\replayer\Replay;
use Jibix\ReplayExample\libs\Jibix\Replay\util\BinaryStream;
use pocketmine\item\Item;


/**
 * Class EntityEquipAction
 * @author Jibix
 * @date 25.12.2024 - 22:48
 * @project Replay
 */
class EntityEquipAction extends EntityAction{

    protected const ID = ActionIds::ENTITY_EQUIP;

    private Item $item;

    public static function create(int $entityId, Item $item): self{
        $action = new self();
        $action->entityId = $entityId;
        $action->item = $item;
        return $action;
    }

    public function serialize(BinaryStream $stream): void{
        parent::serialize($stream);
        $stream->putItem($this->item);
    }

    public function deserialize(BinaryStream $stream): void{
        parent::deserialize($stream);
        $this->item = $stream->getItem();
    }

    public function handle(Replay $replay): void{
        $replay->getEntity($this->entityId)?->getInventory()->setItemInHand($this->item);
    }

    public function handleReversed(Replay $replay): ?Action{
        return self::create($this->entityId, $replay->getEntity($this->entityId)->getInventory()->getItemInHand());
    }
}
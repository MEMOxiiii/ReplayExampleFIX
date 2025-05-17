<?php
namespace Jibix\ReplayExample\libs\Jibix\Replay\entity\type;
use Jibix\ReplayExample\libs\Jibix\Replay\entity\CustomNetworkIdTrait;
use Jibix\ReplayExample\libs\Jibix\Replay\entity\ReplayEntity;
use Jibix\ReplayExample\libs\Jibix\Replay\entity\ReplayEntityTrait;
use pocketmine\entity\Entity;



/**
 * Class DefaultReplayEntity
 * @author Jibix
 * @date 26.12.2024 - 00:59
 * @project Replay
 */
class DefaultReplayEntity extends Entity implements ReplayEntity{
    use ReplayEntityTrait;
    use CustomNetworkIdTrait;
}
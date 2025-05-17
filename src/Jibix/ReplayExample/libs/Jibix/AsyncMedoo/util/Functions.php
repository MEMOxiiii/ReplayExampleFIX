<?php
namespace Jibix\ReplayExample\libs\Jibix\AsyncMedoo\util;
use Closure;
use Jibix\ReplayExample\libs\Jibix\AsyncMedoo\AsyncExecutor;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;


if (!function_exists('async')) {
    function async(AsyncTask|Closure $task, ?Closure $onComplete = null, ?Closure $onError = null): void{
        if ($task instanceof AsyncTask) {
            Server::getInstance()->getAsyncPool()->submitTask($task);
            return;
        }
        AsyncExecutor::execute($task, $onComplete, $onError);
    }
}
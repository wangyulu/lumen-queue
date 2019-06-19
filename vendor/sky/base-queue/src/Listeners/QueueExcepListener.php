<?php
/**
 * Created by PhpStorm.
 * User: sky
 * Date: 2019-05-13
 * Time: 09:22
 */

namespace Sky\BaseQueue\Listeners;

use Log;
use Sky\BaseQueue\Models\QueueModel;
use Sky\BaseQueue\Models\QueueLogModel;
use Sky\BaseQueue\Events\QueueExcepEvent;

class QueueExcepListener extends BaseListener
{
    public function handle(QueueExcepEvent $event)
    {
        $queue = $this->getQueueByClassName($event->getClassName());
        if (!$queue) {
            Log::error('base-queue', [$event->getClassName() . ' not fund']);
            return;
        }

        $this->setQueueStatusToExcep($queue);
        $logQuery = QueueLogModel::query();
        $data     = [
            'queue_id'       => $queue->id,
            'queue_uuid'     => $event->getQueueUuid(),
            'status'         => QueueModel::STATUS_EXCEP,
            'err'            => $event->excep,
            'payload'        => $event->payload,
            'execution_time' => getExecTime()
        ];

        $logQuery->updateOrCreate(array_only($data, ['queue_uuid', 'queue_id']), $data);
    }
}
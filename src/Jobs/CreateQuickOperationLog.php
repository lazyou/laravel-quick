<?php


namespace Lazyou\Quick\Jobs;

use Lazyou\Quick\Models\QuickOperationLog;
use Lazyou\Quick\Models\QuickPermission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 创建操作日志的队列
 * Class CreateOperationLog.
 */
class CreateQuickOperationLog implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $log = [];

    /**
     * Create a new job instance.
     *
     * @param mixed $log
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->setPermissionId();

        // TODO: 考虑优化为只有存在权限的才做操作日志记录
//        if ($this->log['permission_id']) {
        QuickOperationLog::query()->create($this->log);
//        }
    }

    protected function setPermissionId()
    {
        $this->log['permission_id'] = 0;

        if ($this->log['as']) {
            $permission = QuickPermission::query()->where('as', $this->log['as'])->first();
            $this->log['permission_id'] = is_null($permission) ? 0 : $permission->id;
        }
    }
}

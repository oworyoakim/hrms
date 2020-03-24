<?php

namespace App\Console\Commands;

use App\Models\LeaveApplication;
use Exception;
use Illuminate\Console\Command;

class ExpireLeaveApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave_application:expire {leave_application_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to expire a leave application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $id = $this->argument('leave_application_id');
            if (!$id)
            {
                $this->error('Leave Application ID Required');
            }
            $leaveApplication = LeaveApplication::query()->find($id);
            if (!$leaveApplication)
            {
                $this->error('Leave Application not found');
            }
            $leaveApplication->status = 'expired';
            $leaveApplication->save();
            $this->info('Leave Application has been expired');
        } catch (Exception $ex)
        {
            $this->error($ex->getMessage());
        }
    }
}

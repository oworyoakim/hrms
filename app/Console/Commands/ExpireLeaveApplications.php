<?php

namespace App\Console\Commands;

use App\Models\LeaveApplication;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExpireLeaveApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave_applications:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to expire leave applications';

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
        try{
            foreach (LeaveApplication::query()->whereIn('status',['pending','verified','approved'])->get() as $leaveApplication){
                if($leaveApplication->start_date->lessThanOrEqualTo(Carbon::today())){
                    $this->call('leave_application:expire',['leave_application_id',$leaveApplication->id]);
                }
            }
            $this->info('Leave Applications expired');
        }catch (Exception $ex){
            $this->error($ex->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Resignation;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ResignEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:resign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to resign employees whose resignations have not been declined and the start date is today';

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
        DB::beginTransaction();
        try
        {
            $resignations = Resignation::grantable()
                                       ->whereDate('start_date', '<=', Carbon::today())
                                       ->get();
            foreach ($resignations as $resignation)
            {
                if ($resignation->employee)
                {
                    $startDate = !empty($resignation->approved_start_date) ? $resignation->approved_start_date : $resignation->start_date;
                    if ($startDate->lessThanOrEqualTo(Carbon::today()))
                    {
                        $resignation->employee->resign($startDate, $resignation->reason);
                        $resignation->status = Resignation::STATUS_GRANTED;
                        $resignation->save();
                    }
                }
            }
            DB::commit();
        } catch (Exception $ex)
        {
            DB::rollBack();
        }
    }
}

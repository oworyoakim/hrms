<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = "SELECT leave_policies.id AS policy_id,leave_policies.leave_type_id, leave_policies.gender,policy_scales.salary_scale_id,leave_policies.active as policy_status, policy_scales.active AS scale_status FROM policy_scales LEFT JOIN leave_policies ON(policy_scales.leave_policy_id = leave_policies.id)";
        DB::statement("CREATE OR REPLACE VIEW leave_policy_view AS ({$query})");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS leave_policy_view');
    }
}

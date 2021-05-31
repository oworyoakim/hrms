<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(LeaveApplicationStatusesTableSeeder::class);
//        $this->call(LeaveStatusesTableSeeder::class);
//        $this->call(RelationshipsTableSeeder::class);
//        $this->call(MaritalStatusesTableSeeder::class);
//        $this->call(GendersTableSeeder::class);
//        $this->call(TitlesTableSeeder::class);
//        $this->call(ReligionsTableSeeder::class);
//        $this->call(EmployeeStatusesTableSeeder::class);
//        $this->call(EmploymentStatusesTableSeeder::class);
//        $this->call(EmploymentTermsTableSeeder::class);
//        $this->call(EmploymentTypesTableSeeder::class);
//        $this->call(EmploymentActionsTableSeeder::class);
//        $this->call(SalaryScalesTableSeeder::class);
//        $this->call(DirectoratesTableSeeder::class);
//        $this->call(LeavesTypesTableSeeder::class);
//        $this->call(DocumentsTableSeeder::class);
        $this->call(PerformanceStandardsTableSeeder::class);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class BankInfoController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $employee_id = $request->get('employeeId');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $banks = Bank::query()
                         ->where('employee_id', $employee_id)
                         ->get()
                         ->map(function (Bank $bank) {
                             return $bank->getDetails();
                         });
            return response()->json($banks);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $rules = [
                'bankName' => 'required',
                'bankBranch' => 'required',
                'accountName' => 'required',
                'accountNumber' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $employeeId = $request->get('employeeId');
            $employee = Employee::query()->find($employeeId);
            if (!$employee)
            {
                throw new Exception('Employee required!');
            }
            $data = [
                //'employee_id' => $employeeId,
                'bank_name' => $request->get('bankName'),
                'bank_branch' => $request->get('bankBranch'),
                'account_name' => $request->get('accountName'),
                'account_number' => $request->get('accountNumber'),
                'swift_code' => $request->get('swiftCode'),
                'created_by' => $request->get('userId'),
            ];
            $employee->banks()->save(new Bank($data));
            //Bank::query()->create($data);
            return response()->json("Bank info created!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'bankName' => 'required',
                'bankBranch' => 'required',
                'accountName' => 'required',
                'accountNumber' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $employeeId = $request->get('employeeId');
            $employee = Employee::query()->find($employeeId);
            if (!$employee)
            {
                throw new Exception('Employee required!');
            }

            $bank = $employee->banks()->find($id);

            if (!$bank)
            {
                throw new Exception('Bank info not found!');
            }

            $bank->bank_name = $request->get('bankName');
            $bank->bank_branch = $request->get('bankBranch');
            $bank->account_name = $request->get('accountName');
            $bank->account_number = $request->get('accountNumber');
            $bank->swift_code = $request->get('swiftCode');

            $bank->save();

            return response()->json("Bank info updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}

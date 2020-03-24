<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class BankInfoController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $banks = Bank::query()->where('employee_id', $employee_id)->get();
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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee Required!');
            }
            $data = [
                'employee_id' => $employee_id,
                'bank_name' => $request->get('bank_name'),
                'bank_branch' => $request->get('bank_branch'),
                'account_name' => $request->get('account_name'),
                'account_number' => $request->get('account_number'),
                'swift_code' => $request->get('swift_code'),
            ];
            $bank = Bank::query()->create($data);
            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $id = $request->get('id');
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee Required!');
            }

            $bank = Bank::query()->where('employee_id', $employee_id)->find($id);

            if (!$bank)
            {
                throw new Exception('Bank not found!');
            }

            $bank->bank_name = $request->get('bank_name');
            $bank->bank_branch = $request->get('bank_branch');
            $bank->account_name = $request->get('account_name');
            $bank->account_number = $request->get('account_number');
            $bank->swift_code = $request->get('swift_code');
            $bank->save();

            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}

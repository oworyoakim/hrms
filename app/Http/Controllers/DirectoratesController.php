<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Section;
use App\Scopes\IsDirectorate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class DirectoratesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $directorates = Directorate::all()
                                       ->map(function (Directorate $directorate) {
                                           return $directorate->getDetails();
                                       });
            return response()->json($directorates);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function indexUnscoped(Request $request)
    {
        try
        {
            $directorates = Directorate::query()
                                       ->withoutGlobalScope(IsDirectorate::class)
                                       ->get()
                                       ->map(function (Directorate $directorate) {
                                           return $directorate->getDetails();
                                       });
            return response()->json($directorates);
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
                'title' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ];
            Directorate::query()->create($data);
            return response()->json('Directorate created!');
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
                'title' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $directorate = Directorate::query()->find($id);
            if (!$directorate)
            {
                throw new Exception('Directorate not found!');
            }
            $directorate->title = $request->get('title');
            $directorate->description = $request->get('description');
            $directorate->updated_by = $request->get('userId');

            $directorate->save();

            return response()->json('Directorate updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $id = $request->get('directorateId');
            $directorate = Directorate::query()->withoutGlobalScope(IsDirectorate::class)->find($id);
            if (!$directorate)
            {
                throw new Exception('Directorate not found!');
            }
            /*
                        // departments
                        $departments = $directorate->departments()
                                                   ->get()
                                                   ->map(function (Department $department) {
                                                       return $department->getDetails();
                                                   });

                        // divisions
                        $divisions = $directorate->divisions()
                                                 ->get()
                                                 ->map(function (Division $division) {
                                                     return $division->getDetails();
                                                 });
                        // sections
                        $sections = $directorate->sections()
                                                ->get()
                                                ->map(function (Section $section) {
                                                    return $section->getDetails();
                                                });
                        // employees
            */
            $directorate = $directorate->getDetails();
//            $directorate->departments = $departments;
//            $directorate->divisions = $divisions;
//            $directorate->sections = $sections;
//            $directorate->employees = $sections;

            return response()->json($directorate);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function employees(Request $request)
    {
        try
        {
            $id = $request->get('directorateId');
            $directorate = Directorate::query()->withoutGlobalScope(IsDirectorate::class)->find($id);
            if (!$directorate)
            {
                throw new Exception('Directorate not found!');
            }
            $employeeBuilder = Employee::query();
            if ($directorate->id > 1)
            {
                $employeeBuilder->where('directorate_id', $id);
            } else
            {
                $employeeBuilder->whereNull('directorate_id');
            }
            $employees = $employeeBuilder->get()->map(function (Employee $employee) {
                return $employee->getDetails(false);
            });

            return response()->json($employees);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('directorateId');
            $directorate = Directorate::query()->find($id);
            if (!$directorate)
            {
                throw new Exception("Directorate not found!");
            }
            $directorate->delete();
            return response()->json('Directorate deleted!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class SectionsController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $builder = Section::with(['directorate', 'department', 'division']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
                if ($directorate_id = $request->get('directorate_id'))
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }
            if ($department_id = $request->get('department_id'))
            {
                $builder->where('department_id', $department_id);
            }
            if ($division_id = $request->get('division_id'))
            {
                $builder->where('division_id', $division_id);
            }
            $sections = $builder->get();
            return response()->json($sections);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directorate_id' => $request->get('directorate_id'),
                'department_id' => $request->get('department_id'),
                'division_id' => $request->get('division_id'),
            ];
            Section::create($data);
            return response()->json('Record saved!');
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
            $section = Section::find($id);
            if (!$section)
            {
                throw new Exception('Section not found!');
            }
            $section->title = $request->get('title');
            $section->description = $request->get('description');

            if ($directorate_id = $request->get('directorate_id'))
            {
                $section->directorate_id = $directorate_id;
            }
            if ($department_id = $request->get('department_id'))
            {
                $section->department_id = $department_id;
            }
            if ($division_id = $request->get('division_id'))
            {
                $section->division_id = $division_id;
            }
            $section->save();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request, $id)
    {
        try
        {
            $builder = Section::with(['directorate', 'department', 'division']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
            }
            $section = $builder->find($id);

            if (!$section)
            {
                throw new Exception('Section not found');
            }

            $data = [
                'section' => $section,
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('section_id');
            $secrion = Section::find($id);
            if (!$secrion)
            {
                throw new Exception("Section not found!");
            }
            $secrion->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}

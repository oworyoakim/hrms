<?php

namespace App\Http\Controllers;

use App\Models\Directorate;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class DirectoratesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $data = Directorate::all();
            return response()->json($data);
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
            ];
            Directorate::create($data);
            return response()->json('Record Saved!');
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
            $directorate = Directorate::find($id);
            if (!$directorate)
            {
                throw new Exception('Directorate not found!');
            }
            $directorate->title = $request->get('title');
            $directorate->description = $request->get('description');

            $directorate->save();

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
            $directorate = Directorate::find($id);
            if (!$directorate)
            {
                throw new Exception('Directorate not found!');
            }

            $data = [
                'directorate' => $directorate,
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
            $id = $request->get('directorate_id');
            $directorate = Directorate::find($id);
            if (!$directorate)
            {
                throw new Exception("Directorate not found!");
            }
            $directorate->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @param array $data
     * @param array $rules
     * @return bool
     * @throws Exception
     */
    protected function validateData($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails())
        {
            $errors = Collection::make();
            foreach ($validator->errors()->messages() as $key => $messages)
            {
                $field = ucfirst($key);
                $message = implode(', ', $messages);
                $error = "{$field}: {$message}";
                $errors->push($error);
            }
            throw new Exception($errors->implode('<br>'));
        }
        return true;
    }
}

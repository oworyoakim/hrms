<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/**
 * @var Route $router
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->get('', 'HomeController@getDashboardStatistics');
    $router->get('dashboard-statistics', 'HomeController@getDashboardStatistics');
    $router->get('form-selections-options', 'HomeController@getFormSelectionsOptions');
    $router->get('can-login', 'HomeController@canLogin');

    // Directorates
    $router->group(['prefix' => 'directorates'], function () use ($router) {
        $router->get('', 'DirectoratesController@index');
        $router->get('show', 'DirectoratesController@show');
        $router->post('', 'DirectoratesController@store');
        $router->put('', 'DirectoratesController@update');
        $router->delete('', 'DirectoratesController@delete');
    });

    // Departments
    $router->group(['prefix' => 'departments'], function () use ($router) {
        $router->get('', 'DepartmentsController@index');
        $router->get('show', 'DepartmentsController@show');
        $router->post('', 'DepartmentsController@store');
        $router->put('', 'DepartmentsController@update');
        $router->delete('', 'DepartmentsController@delete');
    });

    // Divisions
    $router->group(['prefix' => 'divisions'], function () use ($router) {
        $router->get('', 'DivisionsController@index');
        $router->get('{id}', 'DivisionsController@show');
        $router->post('', 'DivisionsController@store');
        $router->put('', 'DivisionsController@update');
        $router->delete('', 'DivisionsController@delete');
    });

    // Sections
    $router->group(['prefix' => 'sections'], function () use ($router) {
        $router->get('', 'SectionsController@index');
        $router->get('show', 'SectionsController@show');
        $router->post('', 'SectionsController@store');
        $router->put('', 'SectionsController@update');
        $router->delete('', 'SectionsController@delete');
    });

    // Designations
    $router->group(['prefix' => 'designations'], function () use ($router) {
        $router->get('', 'DesignationsController@index');
        $router->post('', 'DesignationsController@store');
        $router->put('', 'DesignationsController@update');
    });


    // DELEGATIONS
    $router->group(['prefix' => 'delegations'], function () use ($router) {
        $router->get('', 'DelegationsController@index');
        $router->post('', 'DelegationsController@store');
        $router->put('', 'DelegationsController@update');
        $router->delete('', 'DelegationsController@delete');
    });

    // DOCUMENTS
    $router->group(['prefix' => 'documents'], function () use ($router) {
        $router->get('', 'DocumentsController@index');
        $router->post('', 'DocumentsController@store');
    });

    // salary-scales
    $router->group(['prefix' => 'salary-scales'], function () use ($router) {
        $router->get('', 'SalaryScalesController@index');
        $router->post('', 'SalaryScalesController@store');
        $router->put('', 'SalaryScalesController@update');
        $router->delete('', 'SalaryScalesController@delete');
    });

    // employees
    $router->group(['prefix' => 'employees'], function () use ($router) {
        // employees
        $router->get('', 'EmployeesController@index');
        $router->post('', 'EmployeesController@store');
        $router->put('', 'EmployeesController@update');
        $router->get('next-id', 'EmployeesController@nextId');

        // employees/profile
        $router->group(['prefix' => 'profile'], function () use ($router) {
            $router->get('', 'EmployeeProfileController@index');
            $router->patch('', 'EmployeeProfileController@update');
            $router->get('download', 'EmployeeProfileController@download');
        });

        // employees/education
        $router->group(['prefix' => 'education'], function () use ($router) {
            $router->get('', 'EducationInfoController@index');
            $router->post('', 'EducationInfoController@store');
            $router->put('', 'EducationInfoController@update');
        });

        // employees/bank
        $router->group(['prefix' => 'bank'], function () use ($router) {
            $router->get('', 'BankInfoController@index');
            $router->post('', 'BankInfoController@store');
            $router->put('', 'BankInfoController@update');
        });

        // employees/experience
        $router->group(['prefix' => 'experience'], function () use ($router) {
            $router->get('', 'ExperienceInfoController@index');
            $router->post('', 'ExperienceInfoController@store');
            $router->put('', 'ExperienceInfoController@update');
        });

        // employees/related-persons
        $router->group(['prefix' => 'related-persons'], function () use ($router) {
            $router->get('', 'RelatedPersonInfoController@index');
            $router->post('', 'RelatedPersonInfoController@store');
            $router->put('', 'RelatedPersonInfoController@update');
        });
        // more on employees

    });

    // leaves
    $router->group(['prefix' => 'leaves'], function () use ($router) {
        // leaves
        $router->get('', 'LeavesController@index');

        // leaves/types
        $router->group(['prefix' => 'types'], function () use ($router) {
            $router->get('', 'LeaveTypesController@index');
            $router->post('', 'LeaveTypesController@store');
            $router->put('', 'LeaveTypesController@update');
            $router->patch('activate', 'LeaveTypesController@activate');
            $router->patch('deactivate', 'LeaveTypesController@deactivate');
            $router->delete('', 'LeaveTypesController@delete');
        });

        // leaves/policies
        $router->group(['prefix' => 'policies'], function () use ($router) {
            $router->get('', 'LeavePoliciesController@index');
            $router->post('', 'LeavePoliciesController@store');
            $router->put('', 'LeavePoliciesController@update');
            $router->delete('', 'LeavePoliciesController@delete');
            $router->patch('activate', 'LeavePoliciesController@activate');
            $router->patch('deactivate', 'LeavePoliciesController@deactivate');
        });

        // leaves/applications
        $router->group(['prefix' => 'applications'], function () use ($router) {
            $router->get('', 'LeaveApplicationsController@index');
            $router->post('', 'LeaveApplicationsController@store');
            $router->put('', 'LeaveApplicationsController@update');
            $router->delete('', 'LeaveApplicationsController@delete');
            $router->patch('verify', 'LeaveApplicationsController@verify');
            $router->patch('return', 'LeaveApplicationsController@returnApplication');
            $router->patch('approve', 'LeaveApplicationsController@approve');
            $router->patch('decline', 'LeaveApplicationsController@decline');
            $router->patch('grant', 'LeaveApplicationsController@grant');
            $router->patch('reject', 'LeaveApplicationsController@reject');
        });
    });
});

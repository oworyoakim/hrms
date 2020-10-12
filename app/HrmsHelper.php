<?php


namespace App;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use stdClass;

class HrmsHelper
{
    public static function generatePagination(LengthAwarePaginator $paginator)
    {
        $pagination = new stdClass();

        $pagination->currentPage = $paginator->currentPage();
        $pagination->nextPage = $pagination->currentPage + 1;
        $pagination->previousPage = null;
        if($pagination->currentPage > 1){
            $pagination->previousPage = $pagination->currentPage - 1;
        }
        $pagination->pages = $paginator->lastPage();
        $pagination->firstPage = 1;
        $pagination->lastPage = $paginator->lastPage();
        $pagination->from = $paginator->firstItem();
        $pagination->to = $paginator->lastItem();
        $pagination->perPage = $paginator->perPage();
        $pagination->total = $paginator->total();
        $pagination->hasPages = $paginator->currentPage() != 1 || $paginator->hasMorePages();
        $pagination->hasMorePages = $paginator->hasMorePages();

        return $pagination;
    }
}

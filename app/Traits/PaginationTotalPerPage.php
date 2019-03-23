<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaginationTotalPerPage
{
    public $totalPerPage = 10;

    public function getTotalPerPage()
    {
        if ((int)(request()->totalPerPage) > 0) {
            $this->totalPerPage = (int)request()->totalPerPage;
        }
        if ($this->totalPerPage > 100) {
            $this->totalPerPage = 100;
        }
        return $this->totalPerPage;
    }
}

<?php

namespace app\helpers\Html {

    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Create pagination markup
     *
     * @param LengthAwarePaginator $paginator
     */
    function pagination(LengthAwarePaginator $paginator) {
        $totalResults = $paginator->total();
        $resultsPerPage = $paginator->perPage();
        $currentPage = $paginator->currentPage();

        //total pages to show
        $totalPages = ceil($totalResults / $resultsPerPage);

        //if only one page then no point in showing a single paginated link
        if ($totalPages <= 1) {
            return '';
        }

        //show not more than 3 paginated links on right and left side
        $rightLinks = $currentPage + 3;
        $previousLinks = $currentPage - 3;

        $html = '<nav aria-label="Page navigation" class="text-center"><ul class="pagination">';
        //if page number 1 is not shown then show the "First page" link
        if ($previousLinks > 1) {
            $html .= '<li><a href="' . $paginator->previousPageUrl() . '" aria-label="First"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
        }

        //disable previous button when first page
        if ($currentPage == 1) {
            $html .= '<li class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        //if current page > 1 only then show previous page
        if ($currentPage > 1) {
            $html .= '<li><a href="' . $paginator->previousPageUrl() . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        //Create left-hand side links
        for ($i = 1; $i <= $paginator->lastPage(); $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="active"><a>' . $i . '</a></li>';
            } else {
                $html .= '<li><a href="' . $paginator->url($i) . '">' . $i . '</a></li>';
            }
        }

        //if current page is not last page then only show next page link
        if ($currentPage != $totalPages) {
            $html .= '<li><a href="' . $paginator->nextPageUrl() . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }

        //if current page is last page then show next page link disabled
        if ($currentPage == $totalPages) {
            $html .= '<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        }

        if ($rightLinks < $totalPages) {
            $html .= '<li><a href="' . $paginator->url(1) . '" aria-label="First"><span aria-hidden="true">&raquo;&raquo;</span></a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

}


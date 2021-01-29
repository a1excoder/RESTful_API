<?php

namespace core;

class Pagination
{

    protected $query;
    protected $total_pages;
    protected $page;
    protected $pagination = array();


    public function getPagination($connect, $per_page, $from, $page)
    {

        $this->page = (int) $page;
        $total_count = $connect->query("SELECT COUNT(`id`) AS `total_count` FROM `{$from}`")->fetch_assoc()['total_count'];
        $this->total_pages = ceil($total_count / $per_page);

        if ($this->page <= 1 || $this->page > $this->total_pages) {
            $this->page = 1;
        }

        $offset = ($per_page * $this->page) - $per_page;
        $this->query = $connect->query("SELECT * FROM `{$from}` ORDER BY `id` DESC LIMIT {$offset}, {$per_page}");


        for ($onePage = 1; $onePage <= $this->total_pages; $onePage++) {

            if ($onePage == $this->page) {
                $status = true;
            } else {
                $status = false;
            }


            $this->pagination[] = [
                'id' => $onePage,
                'status' => $status
            ];

        }

    }

}

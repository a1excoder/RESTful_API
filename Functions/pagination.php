<?php

class Pagination {

    public $query;
    public $total_pages;
    public $page;
    public $pagination = array();

    public function getPagination($connect, $per_page, $from) {

        if (isset($_GET['page'])) {
            $this->page = (int) $_GET['page'];
        }


        $total_count_q = mysqli_query($connect, "SELECT COUNT(`id`) AS `total_count` FROM `{$from}`");
        $total_count = mysqli_fetch_assoc($total_count_q)['total_count'];
        $this->total_pages = ceil($total_count / $per_page);

        if ($this->page <= 1 || $this->page > $this->total_pages) {
            $this->page = 1;
        }

        $offset = ($per_page * $this->page) - $per_page;

        $this->query = mysqli_query($connect, "SELECT * FROM `{$from}` ORDER BY `id` DESC LIMIT {$offset}, {$per_page}");


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

<?php

class Pagination {

    # variable declarations
    protected $query;
    protected $total_pages;
    protected $page;
    protected $pagination = array();

    # general function for view posts by pagination
    public function getPagination($connect, $per_page, $from) {

        if (isset($_GET['page'])) {
            $this->page = (int) $_GET['page'];
        }

        # this lines count posts
        $total_count_q = $connect->query("SELECT COUNT(`id`) AS `total_count` FROM `{$from}`");
        $total_count = $total_count_q->fetch_assoc()['total_count'];

        # this line counts an integer
        $this->total_pages = ceil($total_count / $per_page);

        # this line check variable 'page' to that it is correct
        if ($this->page <= 1 || $this->page > $this->total_pages) {
            $this->page = 1;
        }

        # micro algorithm for pagination
        $offset = ($per_page * $this->page) - $per_page;

        # request in database for views posts by pagination
        $this->query = $connect->query("SELECT * FROM `{$from}` ORDER BY `id` DESC LIMIT {$offset}, {$per_page}");


        # cycle for view pagination numbers at page
        for ($onePage = 1; $onePage <= $this->total_pages; $onePage++) {
            if ($onePage == $this->page) {
                $status = true;
            } else {
                $status = false;
            }

            # array pagination numbers
            $this->pagination[] = [
                'id' => $onePage,
                'status' => $status
            ];

        }

    }

}

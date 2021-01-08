<?php
header('Content-type: application/json');


class index {

    private $connect = ['127.0.0.1', 'root', '', 'rest_api'];

    public function viewPosts() {
        $connect = mysqli_connect($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $per_page = 6;
        $page = 1;

        if (isset($_GET['page'])) {
            $page = (int) $_GET['page'];
        }

        $total_count_q = mysqli_query($connect, "SELECT COUNT(`id`) AS `total_count` FROM `posts`");
        $total_count = mysqli_fetch_assoc($total_count_q);
        $total_count = $total_count['total_count'];
        $total_pages = ceil($total_count / $per_page);

        if ($page <= 1 || $page > $total_pages) {
            $page = 1;
        }

        $offset = ($per_page * $page) - $per_page;

        $query = mysqli_query($connect, "SELECT * FROM `posts` ORDER BY `id` DESC LIMIT {$offset}, {$per_page}");

        $jsonArray = array();

        for ($onePage = 1; $onePage <= $total_pages; $onePage++) {
            if ($onePage == $page) {
                $status = true;
            } else {
                $status = false;
            }

            $jsonArray['pagination'][] = [
                'page' => [
                    'id' => $onePage,
                    'status' => $status
                ]
            ];

        }

        while ($post = mysqli_fetch_assoc($query)) {
            $jsonArray[] = [
                'id' => (int) $post['id'],
                'title' => $post['title'],
                'category' => $post['category'],
                'query' => mb_substr($post['query'], 0, 200) . "..."
            ];
        }

        echo json_encode($jsonArray);
        mysqli_close($connect);

    }


    public function viewPost(int $id) {
        $connect = mysqli_connect($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $query = mysqli_query($connect, "SELECT * FROM `posts` WHERE `id` = {$id}");
        $post = mysqli_fetch_assoc($query);
        $jsonArray['post'] = [
            'post_id' => (int) $post['id'],
            'datetime' => $post['datetime'],
            'title' => $post['title'],
            'category' => $post['category'],
            'query' => $post['query']
        ];

        echo json_encode($jsonArray);
        mysqli_close($connect);

    }


}

$new = new index();
//$new->viewPosts();
$new->viewPost($_GET['id']);

<?php
header('Content-type: application/json');

include_once( __DIR__ . '/Functions/pagination.php' );


class index extends Pagination {

    private $connect = ['127.0.0.1', 'root', '', 'rest_api'];

    public function viewPosts() {
        $connect = mysqli_connect($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $per_page = 6;
        $page = 1;
        $from = 'posts';

        $jsonArray = array();

        $this->getPagination($connect, $per_page, $from);


        while ($post = mysqli_fetch_assoc($this->query)) {
            $jsonArray[] = [
                'id' => (int) $post['id'],
                'title' => $post['title'],
                'category' => $post['category'],
                'query' => mb_substr($post['query'], 0, 200) . "..."
            ];
        }

        $jsonArray[] = [
            'pagination' => $this->pagination
        ];

        echo json_encode($jsonArray);
        mysqli_close($connect);

    }


    public function viewPost(int $id) {
        $connect = mysqli_connect($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $query = mysqli_query($connect, "SELECT * FROM `posts` WHERE `id` = {(int) $id}");
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
$new->viewPosts();
//$new->viewPost($_GET['id']);

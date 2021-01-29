<?php
header('Content-type: application/json');

include_once __DIR__ . '/core/pagination.php';
include_once __DIR__ . '/core/Routes.php';

use core\Routes as Router;
use core\Pagination as Pagination;


class index extends Pagination
{

    private $connect = ['127.0.0.1', 'root', '', 'rest_api'];

    public function viewPosts(int $page = 1)
    {
        $connect = new mysqli($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $this->getPagination($connect, 6, 'posts', $page);

        $jsonArray = array();
        $jsonArray['pagination'] = $this->pagination;


        while ($post = $this->query->fetch_assoc()) {
            $jsonArray['posts'][] = [
                'id' => (int) $post['id'],
                'title' => $post['title'],
                'category' => $post['category'],
                'query' => mb_substr($post['query'], 0, 200) . "..."
            ];
        }

        echo json_encode($jsonArray);
        $connect->close();

    }


    public function viewPost(int $id)
    {
        $connect = new mysqli($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);

        $post = $connect->query("SELECT * FROM `posts` WHERE `id` = {$id}")->fetch_assoc();

        if (!$post) {

            http_response_code(404);
            echo json_encode([
                'status' => false,
                'message' => 'Post not found'
            ]);

            $connect->close();
        } else {

            $jsonArray['post'] = [
                'post_id' => (int) $post['id'],
                'datetime' => $post['datetime'],
                'title' => $post['title'],
                'category' => $post['category'],
                'query' => $post['query']
            ];

            echo json_encode($jsonArray);
            $connect->close();
        }

    }


    public function addPost()
    {
        $METHOD = $_SERVER['REQUEST_METHOD'];

        if ($METHOD == 'POST') {
            $connect = new mysqli($this->connect[0], $this->connect[1], $this->connect[2], $this->connect[3]);
            $data = $_POST;

            if (empty($data['title']) || empty($data['category']) || empty($data['query'])) {
                http_response_code(406);
                echo json_encode([
                    'status' => false
                ]);

            } else {
                $connect->query("INSERT INTO `posts` (`id`, `title`, `datetime`, `category`, `query`) VALUES ".
                    "(NULL, '{$data['title']}', current_timestamp(), '{$data['category']}', '{$data['query']}'); ");

                echo json_encode([
                    'status' => true,
                    'message' => 'New post has been created'
                ]);
            }

            $connect->close();
        } else {
            http_response_code(405);
            echo json_encode([
                'status' => false,
                'message' => 'This is not a POST method'
            ]);
        }

    }


}


Router::route('/', function () {
    $new = new index();
    $new->viewPosts();
});

Router::route('/post/new', function () {
    $new = new index();
    $new->addPost();
});

Router::route('/page/(\w+)', function (int $page) {
    $new = new index();
    $new->viewPosts($page);
});

Router::route('/post/(\w+)', function (int $id) {
    $new = new index();
    $new->viewPost($id);
});


Router::execute($_SERVER['REQUEST_URI']);

<?php

namespace Mini\Model;

use PDO;

class Model
{
    /**
     * The database connection
     * @var PDO
     */
	private $db;

    /**
     * When creating the model, the configs for database connection creation are needed
     * @param $config
     */
    function __construct($config)
    {
        // PDO db connection statement preparation
        $dsn = 'mysql:host=' . $config['db_host'] . ';dbname='    . $config['db_name'] . ';port=' . $config['db_port'];

        // note the PDO::FETCH_OBJ, returning object ($result->id) instead of array ($result["id"])
        // @see http://php.net/manual/de/pdo.construct.php
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

        // create new PDO db connection
        $this->db = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
		$this->db->query('SET CHARACTER SET utf8');
	}

	/**
	 * LOGIN
	 */
	public function login_user($post)
	{
		$sql = "SELECT `id`,`username`,`email`,`level` FROM `users` WHERE `username` = :username AND `password` = :password";
		$query = $this->db->prepare($sql);
        $query->execute(array(':username' => $post['username'], ':password' => hash('sha256', $post['password'])));
        return $query->fetch();
	}
	/**
	 * PRODUCTS
	 */
	public function get_products($id = 0)
	{
		$where = "";
		if ($id !== 0) {
			$where = " WHERE a.`id` = :id";
		}

		$prefix = " AND";
		if (empty($where)) {
			$prefix = " WHERE ";
		}
		$sql = "SELECT a.*, b.`username` FROM `products` AS a, `users` AS b ".$where.$prefix." a.`user_id` = b.`id` ORDER BY a.`id` DESC";
		$query = $this->db->prepare($sql);

		if ($id !== 0) {
	        $parameters = array(
				':id' => $id
			);
	        $query->execute($parameters);
	        return $query->fetch();
		}else{
	        $query->execute();
	        return $query->fetchAll();
		}
	}

	public function product_save($post)
	{
		if ($post['id'] == 0) {
	        $sql = "INSERT INTO products (title, details, preview, user_id, post_on) VALUES (:title, :details, :preview, :user_id, NOW())";
	        $parameters = array(
				':title' => $post['title'],
				':details' => $post['details'],
				':preview' => $post['file_name'],
				':user_id' => $post['user_id'],
			);
		}else{
			if (!empty($post['file_name'])) {
		        $sql = "UPDATE products SET title = :title, details = :details, preview = :preview WHERE id = :id";
		        $parameters = array(
					':title' => $post['title'],
					':details' => $post['details'],
					':preview' => $post['file_name'],
					':id' => $post['id'],
				);
			}else{
		        $sql = "UPDATE products SET title = :title, details = :details WHERE id = :id";
		        $parameters = array(
					':title' => $post['title'],
					':details' => $post['details'],
					':id' => $post['id'],
				);
			}
		}
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
	}

	public function product_delete($id)
	{
        $query = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $query->execute(array(':id' => $id));

        $query = $this->db->prepare("DELETE FROM comments WHERE product_id = :id");
        $query->execute(array(':id' => $id));
	}

	/**
	 * USERS
	 */
	public function get_users($id = 0)
	{
		$where = "";
		if ($id !== 0) {
			$where = " AND `id` = :id";
		}
		$sql = "SELECT * FROM `users` WHERE `level` != '99'".$where." ORDER BY `id` DESC";
		$query = $this->db->prepare($sql);

		if ($id !== 0) {
	        $parameters = array(
				':id' => $id
			);
	        $query->execute($parameters);
	        return $query->fetch();
		}else{
	        $query->execute();
	        return $query->fetchAll();
		}
	}

	public function find_by_username($post)
	{
		$sql = "SELECT * FROM `users` WHERE `username` = :username AND `id` != :id";
		$query = $this->db->prepare($sql);
        $query->execute(array(
			':username' => $post['username'],
			':id' => $post['id'],
		));
        return $query->fetch();
	}

	public function user_save($post)
	{
		if (!empty($post['password'])) {
			$post['password'] = hash('sha256', $post['password']);
		}

		if ($post['id'] == 0) {

	        $sql = "INSERT INTO users (username, password, email, level) VALUES (:username, :password, :email, 1)";
	        $parameters = array(
				':username' => $post['username'],
				':password' => $post['password'],
				':email' => $post['email'],
			);
	        $query = $this->db->prepare($sql);
	        $query->execute($parameters);
		}else{

			if (!empty($post['password'])) {

		        $sql = "UPDATE users SET username = :username, password = :password, email = :email WHERE id = :id";
				$parameters = array(
					':username' => $post['username'],
					':password' => $post['password'],
					':email' => $post['email'],
					':id' => $post['id'],
				);
			}else{
		        $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
				$parameters = array(
					':username' => $post['username'],
					':email' => $post['email'],
					':id' => $post['id'],
				);
			}

	        $query = $this->db->prepare($sql);
	        $query->execute($parameters);
		}
	}

	public function user_delete($id)
	{
        $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $query->execute(array(':id' => $id));

        $query = $this->db->prepare("DELETE FROM products WHERE user_id = :id");
        $query->execute(array(':id' => $id));

        $query = $this->db->prepare("DELETE FROM comments WHERE user_id = :id");
        $query->execute(array(':id' => $id));
	}

	public function get_comments($id)
	{
		$sql = "SELECT a.*, b.username FROM `comments` AS a, `users` AS b WHERE a.`product_id` = :id AND a.`user_id` = b.`id` ORDER BY a.`id` DESC";
		$query = $this->db->prepare($sql);
        $query->execute(array(':id' => $id));
        return $query->fetchAll();
	}
	public function comment_save($post)
	{
        $sql = "INSERT INTO comments (detail, product_id, user_id, post_on) VALUES (:detail, :product_id, :user_id, NOW())";
        $parameters = array(
			':detail' => $post['detail'],
			':product_id' => $post['product_id'],
			':user_id' => $post['user_id'],
		);
        $query = $this->db->prepare($sql);
        $query->execute($parameters);
	}

	public function comment_delete($id)
	{
        $query = $this->db->prepare("DELETE FROM comments WHERE id = :id");
        $query->execute(array(':id' => $id));
	}
}

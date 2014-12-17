<?php
namespace Mini\Model;
use PDO;
/**
 * PROTOTYPE EXTENDS FROM MODEL\MODEL
 */
class Product extends Model
{
    function __construct($config)
    {
        parent::__construct($config);
    }
	public function get_products($id = 0)
	{
		$where = "";
		if ($id !== 0) {
			$where = " WHERE `id` = :id";
		}
		$sql = "SELECT * FROM `products`".$where;
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
}

?>

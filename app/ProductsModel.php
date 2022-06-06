<?php

namespace App;

final class ProductsModel extends Model
{
    public function clear(): void
    {
        $this->db->query('TRUNCATE TABLE `products`');
    }

    public function getAll(): array
    {
        $data = [];
        $products = $this->db->query('SELECT * FROM `products` ORDER BY `ID` DESC');

        foreach ($products as $product) {
            $Product = new Product();
            $Product->id = (int) $product['id'];
            $Product->name = $product['name'];
            $Product->href = $product['href'];
            $Product->src = $product['src'];
            $Product->price = (float) $product['price'];

            $data[] = $Product;
        }

        return $data;
    }

    public function add(array $products): void
    {
        foreach ($products as $product) {
            $sql = <<<SQL
                INSERT INTO `products` (`name`, `href`, `src`, `price`) VALUES
                    (:name, :href, :src, :price);
            SQL;

            $sth = $this->db->prepare($sql);

            $sth->execute([
                ':name' => $product->name,
                ':href' => $product->href,
                ':src' => $product->src,
                ':price' => $product->price,
            ]);
        }
    }
}

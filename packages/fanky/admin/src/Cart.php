<?php namespace Fanky\Admin;

use Session;

class Cart {

    private static $key = 'cart';

    public static function add($item): void
    {
        $cart = self::all();

        $cart[$item['id']] = $item;
        Session::put(self::$key, $cart);
    }

    public static function count(): int
    {
        $cart = self::all();

        return count($cart);
    }

    public static function total_items() {
        $cart = self::all();

        $total_items = 0;
        foreach ($cart as $item) {
            $total_items += $item['count'];
        }

        return $total_items;
    }

    public static function total_discount() {
        $cart = self::all();

        $total_discount = 0;
        foreach ($cart as $item) {
            $total_discount += $item['discount_delivery'] + $item['discount_payment'];
        }

        return $total_discount;
    }

    public static function remove($id): void
    {
        $cart = self::all();
        unset($cart[$id]);
        Session::put(self::$key, $cart);
    }

    public static function ifInCart($id): bool {
        $cart = self::all();
        return isset($cart[$id]);
    }

    public static function ifInCartGetCount($id) {
        $cart = self::all();
        return isset($cart[$id]) ? $cart[$id]['count'] : 0;
    }

    public static function updateCount($id, $count): void
    {
        $cart = self::all();
        if (isset($cart[$id])) {
            $cart[$id]['count'] = $count;
            Session::put(self::$key, $cart);
        }
    }

    public static function getItem($id) {
        $cart = self::all();
        if (isset($cart[$id])) {
            return $cart[$id];
        }
    }

    public static function purge(): void
    {
        Session::put(self::$key, []);
    }

    public static function all(): array {
        $res = Session::get(self::$key, []);
        return is_array($res) ? $res : [];
    }

    public static function sum(): int {
        $cart = self::all();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['count'] * $item['price'];
        }
        return $sum;
    }
}

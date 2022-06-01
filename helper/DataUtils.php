<?php


class DataUtils {

    public static function invalid($data): bool {
        return $data == null || $data == "";
    }
}
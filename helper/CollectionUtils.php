<?php
class CollectionUtils {

    public static function lessThanByItem($obj, int $int) {
        if (!is_array($obj)) {
            return false;
        }
        return $obj == null || count($obj) < $int;
    }
}
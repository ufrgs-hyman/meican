<?php 

namespace meican\base\components;

class ColorUtils {

    static function generate() {
        return sprintf('#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255)); 
    }

}
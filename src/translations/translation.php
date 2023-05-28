<?php

class TranslationNotFoundException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

function get_translation($conn, $lang) {
    $file_name = $lang . ".json";

    $file = file_get_contents($file_name);

    if (!$file) {
        throw new TranslationNotFoundException("Could not open the file: $file_name");
    }

    $json = json_decode($file, true);

    return $json;
}

?>
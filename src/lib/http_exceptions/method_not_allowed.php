<?php

class MethodNotAllowedException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

?>
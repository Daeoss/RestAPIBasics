<?php

class BaseController {
    public function __call($name, $arguments) {
        $this->sendOutput("", array('HTTP/1.1 404 Not Found'));
    }

    protected function getUriSegments() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode("/", $uri);

        return $uri;
    }

    protected function getQueryStringParams() {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    protected function sendOutput($data, $httpHeaders = array()) {
        header_remove("Set-Cookie");
        if(is_array($httpHeaders) && count($httpHeaders)) {
            foreach($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }

    protected function validateUserInput($data) {
        $errors = [];
        if (empty($data['username'])) {
            $errors[] = "Username cannot be empty.";
        }
        if (empty($data['user_email'])) {
            $errors[] = "Email cannot be empty.";
        }
        if (empty($data['user_status'])) {
            $errors[] = "Status cannot be empty.";
        }
        if (!empty($errors)) {
            $this->sendOutput(json_encode(array('error' => implode("", $errors)
            )), array("Content-Type: application/json", "HTTP/1.1 400 Bad Request"));
        }
    }

    protected function getFormat($data) {
        $format = "";
        foreach ($data as $value) {
            if (is_int($value)) {
                $format .= 'i';
            } elseif (is_float($value)) {
                $format .= 'd';
            } else {
                $format .= 's';
            }
        }
        return $format;
    }

    protected function prepareData($fieldsToCheck, $data) {
        $fields = $params = [];
        $format = '';
        foreach($fieldsToCheck as $fieldToCheck => $type) {
            if(isset($data[$fieldToCheck])) {
                $fields[] = $fieldToCheck;
                $params[] = $data[$fieldToCheck];
                $format .= "$type";
            }
        }

        return [
            'fields' => $fields,
            'params' => $params,
            'format' => $format
        ];
    }
}
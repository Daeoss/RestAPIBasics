<?php

class UserController extends BaseController {
    public function listAction() {
        $strErrorDesc = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if(strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();
                $intLimit = 10;
                if(isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }

                $arrUsers = $userModel->getUsers($intLimit);
                $responseData = json_encode($arrUsers);
            }catch(Error $e) {
                $strErrorDesc = $e->getMessage().". Something went wrong! Please retry.";
                $strErrorHeader = "HTTP/1.1 500 Internal Server Error";
            }
        }else{
            $strErrorDesc = "Method not supported!";
            $strErrorHeader = "HTTP/1.1 422 Unprocessable Entity";
        }

        if(!$strErrorDesc) {
            $this->sendOutput($responseData, array("Content-Type: application/json", "HTTP/1.1 200 OK"));
        }else{
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }
    }

    public function addAction() {
        $strErrorDesc = "";
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        if(strtoupper($requestMethod) == 'POST') {
            try {
                // Check if inputs are empty
                $this->validateUserInput($data);

                //Check if user exists
                $userModel = new UserModel();
                $prepareData = $this->prepareData(['username' => 's', 'user_email' => 's'],$data);
                $userExists = $userModel->findBy($prepareData['fields'], $prepareData['params'], $prepareData['format']);
                if($userExists) {
                    $strErrorDesc = "User already exists!";
                    $strErrorHeader = "HTTP/1.1 409 Conflict";
                }else{
                    $arrUsers = $userModel->saveUsers(array_keys($data), array_values($data), $this->getFormat($data));
                    $responseData = json_encode($arrUsers);
                }
            }catch(Error $e) {
                $strErrorDesc = $e->getMessage().". Something went wrong! Please retry.";
                $strErrorHeader = "HTTP/1.1 500 Internal Server Error";
            }
        }else{
            $strErrorDesc = "Method not supported!";
            $strErrorHeader = "HTTP/1.1 422 Unprocessable Entity";
        }

        if(!$strErrorDesc) {
            $this->sendOutput($responseData, array("Content-Type: application/json", "HTTP/1.1 200 OK"));
        }else{
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }
    }

    public function updateAction($id) {
        $strErrorDesc = "";
        $responseData = json_encode([]);
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if($id == 0) {
            $strErrorDesc = "Invalid ID!";
            $strErrorHeader = "HTTP/1.1 400 Bad Request";
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if(strtoupper($requestMethod) == 'PUT') {
            try {
                //Check if user exists
                $userModel = new UserModel();
                $prepareData = $this->prepareData(['username' => 's', 'user_email' => 's'],$data);
                $userExists = $userModel->findBy($prepareData['fields'], $prepareData['params'], $prepareData['format']);
                
                if($userExists) {
                    $strErrorDesc = "User already exists!";
                    $strErrorHeader = "HTTP/1.1 409 Conflict";
                }else{               
                    $arrUsers = $userModel->updateUsers(array_keys($data), [...array_values($data), $id], $this->getFormat($data)."i");
                    $responseData = json_encode($arrUsers);
                }
            }catch(Error $e) {
                $strErrorDesc = $e->getMessage().". Something went wrong! Please retry.";
                $strErrorHeader = "HTTP/1.1 500 Internal Server Error";
            }
        }else{
            $strErrorDesc = "Method not supported!";
            $strErrorHeader = "HTTP/1.1 422 Unprocessable Entity";
        }

        if(!$strErrorDesc) {
            $this->sendOutput($responseData, array("Content-Type: application/json", "HTTP/1.1 200 OK"));
        }else{
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }
    }

    public function deleteAction($id) {
        $strErrorDesc = "";
        $responseData = json_encode([]);
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if($id == 0) {
            $strErrorDesc = "Invalid ID!";
            $strErrorHeader = "HTTP/1.1 400 Bad Request";
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }

        if(strtoupper($requestMethod) == 'DELETE') {
            try {
                $userModel = new UserModel();
                $arrUsers = $userModel->deleteUser([$id], 'i');
                $responseData = json_encode($arrUsers);
            }catch(Error $e) {
                $strErrorDesc = $e->getMessage().". Something went wrong! Please retry.";
                $strErrorHeader = "HTTP/1.1 500 Internal Server Error";
            }
        }else{
            $strErrorDesc = "Method not supported!";
            $strErrorHeader = "HTTP/1.1 422 Unprocessable Entity";
        }

        if(!$strErrorDesc) {
            $this->sendOutput($responseData, array("Content-Type: application/json", "HTTP/1.1 200 OK"));
        }else{
            $this->sendOutput(json_encode(array('error' => $strErrorDesc
            )), array("Content-Type: application/json", $strErrorHeader));
        }
    }
}
<?php
namespace root\models;

use myownphpcms\core\database\DbDataModel;

class UserLogin extends DbDataModel {

    protected function setFieldName()
    {
        return "user_login";
    }

    protected function useFields(){
        return [
            "id",
            "user_name",
            "user_email",
            "user_pass",
            "active_status"
        ];
    }
}
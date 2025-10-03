<?php
namespace root\models;

use myownphpcms\core\database\DbDataModel;

/**
 * @property int $id
 * @property string $user_name
 * @property string $user_email
 * @property string $user_pass
 * @property string $active_status
 */
class UserLogin extends DbDataModel {

    protected function setTableName()
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
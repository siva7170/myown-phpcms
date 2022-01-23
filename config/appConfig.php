<?php
use \myownphpcms\core\config\DatabaseConfig;
use \myownphpcms\core\config\AppConfig;

return new AppConfig([
    "coreConfig"=>[
        "app"=>[
            "default"=>[
                "defaultIndexRoot"=>"root",
                "webFilesRoot"=>"wwwroot",
                "appInternalRoot"=>"D:\\web\wwwroot\\myownphpcms\\",
                "appExternalRoot"=>"http//localhost/myownphpcms/",
                "dbConfig"=>[
                    new DatabaseConfig("localhost","root","","myownphpcms"),
                    new DatabaseConfig("localhost","root","","myownphpcms")
                ],
            ]
        ]
    ],
]);
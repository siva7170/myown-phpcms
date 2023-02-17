<?php
use myownphpcms\core\config\DatabaseConfig;
use myownphpcms\core\config\AppConfig;

return new AppConfig([
    "coreConfig"=>[
        "app"=>[
            "default"=>[
                "defaultIndexRoot"=>"root",
                "webFilesRoot"=>"wwwroot",
                "appInternalRoot"=>"D:\\web\\php\\myown-phpcms",
                "appExternalRoot"=>"http//localhost/",
                "errorRoute"=>"error/notfound",
                "dbConfig"=>[
                    new DatabaseConfig("localhost","root","","myownphpcms"),
                    new DatabaseConfig("localhost","root","","myownphpcms")
                ],
            ]
        ]
    ],
]);
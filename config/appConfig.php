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
                    new DatabaseConfig("localhost","root","","my_test")
                ],
                "urlSegments"=>[
                    "^page\/viewpost\/([A-Za-z0-9]+)\/([A-Za-z0-9]+)$"=>new \myownphpcms\core\handler\URLSegment("root","page","viewpost",[
                        "[1]"=>"categorySlug",
                        "[2]"=>"postSlug"
                    ])
                ]
            ]
        ]
    ],
]);
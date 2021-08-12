<?php

    return [

        /**
         * profile images
         */

        'images' => [
                        (object) ['name' => 'avatar_1', 'route' => 'img/profiles/avatar1.png']
        ],
        /**
         * enum('video','pdf','image','audio','text','file','link')
         */
        'file_type' => [
                            'video' => 'video',
                            'pdf' => 'pdf',
                            'image' => 'imagen',
                            'audio' => 'audio',
                            'text' => 'texto',
                            'file' => 'archivo',
                            'link' => 'enlace'
                        ],

    ]

?>
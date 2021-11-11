<?php

return [
    /*
     * The default driver to use
     *
     * Possible options may include
     * - null: Don't translate any text automatically
     * - aws: Use the AWS Setting service
     */
    'default' => 'null',

    /*
     * Possible configurations
     *
     * Each configuration must have a unique name. They must specify a driver using the notation
     * ```[TranslationManager::DRIVER_KEY => 'null]'```
     * and any configuration for the driver should be added to the array too.
     */
    'configurations' => [
    ],

    /*
     * The table to save translations in
     */
    'table' => 'translations',

    /**
     * List of languages supported. A user cannot translate to a language not in this list.
     *
     * Leave as an empty array to support all languages.
     */
    'supported_languages' => [],

    /**
     * Configuration for the Detect functionality
     */
    'detection' => [
        /*
         * The key in the request holding the target language
         */
        'body_key' => 'language',

        /*
         * The key of the cookie holding the target language
         */
        'cookie_key' => 'language',

        /*
         * Config for detecting the target language using the header
         */
        'header' => [

            /*
             * ISO-639-1 language codes that are allowed
             */
            'allowed_languages' => []
        ]
    ],

    /*
     * The URL at which the translation API is found
     */
    'translate_api_url' => '_translate'

];

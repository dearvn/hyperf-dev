<?php
declare(strict_types=1);

return [
    'login_type' => env('JWT_LOGIN_TYPE', 'mpop'), //Login method, sso is single sign-on, mpop is multi-sign-on
    /**
    *The key value of uid must exist in the single sign-on custom data, you can define this key by yourself, as long as the key exists in the custom data
    */
    'sso_key' => 'uid',

    'secret' => env('JWT_SECRET', 'phper666'), //Asymmetric encryption uses strings, please use your own encrypted strings
    /**
    *JWT permission keys
    *Symmetric algorithm: HS256, HS384 & HS512 use `JWT_SECRET`.
    *Asymmetric Algorithms: RS256, RS384 & RS512 /ES256, ES384 & ES512 use the following public and private keys.
    */
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'), // 公钥，例如：'file:///path/to/public/key'
        'private' => env('JWT_PRIVATE_KEY'), // 私钥，例如：'file:///path/to/private/key'
    ],

    'ttl' => env('JWT_TTL', 7200), // token过期时间，单位为秒

    'ttl_cache' => env('JWT_TTL_CACHE', 7200), //token 缓存时间 单位为秒

    'alg' => env('JWT_ALG', 'HS256'), // jwt的hearder加密算法

    /**
     * Support algorithm
     */
    'supported_algs' => [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
        'ES256' => 'Lcobucci\JWT\Signer\Ecdsa\Sha256',
        'ES384' => 'Lcobucci\JWT\Signer\Ecdsa\Sha384',
        'ES512' => 'Lcobucci\JWT\Signer\Ecdsa\Sha512',
        'RS256' => 'Lcobucci\JWT\Signer\Rsa\Sha256',
        'RS384' => 'Lcobucci\JWT\Signer\Rsa\Sha384',
        'RS512' => 'Lcobucci\JWT\Signer\Rsa\Sha512',
    ],

    /**
     * Symmetric algorithm name
     */
    'symmetry_algs' => [
        'HS256',
        'HS384',
        'HS512'
    ],

    /**
     * Name of asymmetric algorithm
     */
    'asymmetric_algs' => [
        'RS256',
        'RS384',
        'RS512',
        'ES256',
        'ES384',
        'ES512',
    ],

    /**
     * Whether to open the black list, the cancellation and refresh of a single login and multi -point login make the original token fail, you must start the black list. At present, the blacklisted cache only supports the Hyperf cache driver
     */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /**
     * The wide limit time of the blacklist is: second, note: If you use a single point login, the wide limit time is invalid
     */
    'blacklist_grace_period' => env('JWT_TTL', 0),

    /**
     * The blacklisure cache token time, note: This time must be set a little larger than the time period of the token. The default is 1 day. It is best to set up the same time as
     */
    'blacklist_cache_ttl' => env('JWT_TTL', 86400),

    'blacklist_prefix' => 'hyperf-api', //Prefix for blacklist cache
    /**
    * To distinguish tokens in different scenarios, for example, your project may have multiple types of application interface authentication, define it yourself below, I am just giving an example
    * The following configuration will automatically overwrite the root configuration, for example, the data in application1 will overwrite the root data
    * The following scene will be merged with the root data
    * scene must have a default
    * What is root data, the one-dimensional array of this configuration, except the scene is called root configuration
    */
    'scene' => [
        'default' => [],
        'application1' => [
            'secret' => 'application1', //Asymmetric encryption uses strings, please use your own encrypted strings
            'login_type' => 'sso', //Login method, sso is single sign-on, mpop is multi-sign-on
            'sso_key' => 'uid',
            'ttl' => 7200, //Token expiration time, in seconds
            'blacklist_cache_ttl' => env('JWT_TTL', 7200), //Blacklist cache token time, note: this time must be set a little longer than the token expiration time, the default is 100 seconds, it is best to set the same as the expiration time
        ],
        'application2' => [
            'secret' => 'application2', //Asymmetric encryption uses strings, please use your own encrypted strings
            'login_type' => 'sso', //Login method, sso is single sign-on, mpop is multi-sign-on
            'sso_key' => 'uid',
            'ttl' => 7200, //Token expiration time, in seconds
            'blacklist_cache_ttl' => env('JWT_TTL', 7200), //Blacklist cache token time, note: this time must be set a little longer than the token expiration time, the default is 100 seconds, it is best to set the same as the expiration time
        ],
        'application3' => [
            'secret' => 'application3', //Asymmetric encryption uses strings, please use your own encrypted strings
            'login_type' => 'mppo', //Login method, sso is single sign-on, mpop is multi-sign-on
            'ttl' => 7200, //Token expiration time, in seconds
            'blacklist_cache_ttl' => env('JWT_TTL', 7200), //Blacklist cache token time, note: this time must be set a little longer than the token expiration time, the default is 100 seconds, it is best to set the same as the expiration time
        ]
    ],
    'model' => [ //TODO supports direct access to the data of a model
        'class' => '',
        'pk' => 'uid'
    ]
];

RedkingOAuthBundle
==================

This bundle implement methods to create client based on FOSUserBundle.

[See flow in the examples](Resources/doc/flow.md)

Installation
------------

### A) Composer installation

```json
{
    "require": {
        "redking/redking/oauth-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:redkingteam/redkingoauthbundle.git"
        }
    ]
}
```

### B) Bundle activation

Enable the bundle in kernel :

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Redking\Bundle\OAuthBundle\RedkingOAuthBundle(),
        
    );
}
```

Configuration
-------------

### A) Config

Tell FOSUserBundle and FOSOAuthBundle to look at the classes in our bundle : 

``` yaml
# app/config/config.yml

# Fos User
fos_user:
    db_driver: mongodb
    firewall_name: main
    user_class: Redking\Bundle\OAuthBundle\Document\User

# Fos OAuth
fos_oauth_server:
    db_driver: mongodb
    client_class:        Redking\Bundle\OAuthBundle\Document\Client
    access_token_class:  Redking\Bundle\OAuthBundle\Document\AccessToken
    refresh_token_class: Redking\Bundle\OAuthBundle\Document\RefreshToken
    auth_code_class:     Redking\Bundle\OAuthBundle\Document\AuthCode
    service:
        user_provider: fos_user.user_manager
```


#### A.1) Facebook integration [optionnal]

Define facebook app id and secret in Configuration.

``` yaml
# app/config/config.yml

redking_o_auth:
    facebook:
        id: %facebook.id%
        secret: %facebook.secret%
```

Users can be registred by doing a POST request to `/oauth/bridge/facebook_token` with a `token` parameter.

### B) Routing

``` yaml
# app/config/routing.yml
RedkingOAuthBundle:
    resource: "@RedkingOAuthBundle/Resources/config/routing.xml"
    prefix:   /
```

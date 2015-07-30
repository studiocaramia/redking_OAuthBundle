Reminder sur comment le workflow du serveur OAuth
=================================================

## Workflow avec assocation user-client ##


Dans ce workflow, il faut d'abord se connecter en tant que User, puis accepter d'associer son compte au Client.
De là, on reçoit un authcode qui nous permet de demander le token.

### Pré-requis ###

Il faut d'abord créer un utilisateur FosUser

### Création du client OAuth ###

``` shell
php app/console redking:oauth-server:client:create --redirect-uri=http://server.local --grant-type=token --grant-type=refresh_token --grant-type=authorization_code Redking
```

Sur la console s'affiche le `client_id` et `client_secret`.

###  Authentification classique sur OAuth ### 

```
http://server.local/app_dev.php/oauth/v2/auth_login
```

### Association entre user et client ###

Une fois le client authentifié, il doit lier son compte au Client

```
http://server.local/app_dev.php/oauth/v2/auth?client_id={client_id}&response_type=code&redirect_uri=http%3A%2F%2Fserver.local
```

Une fois validé, on est redirigé vers 

```
http://server.local/?code=N2VmM2M5ODJiYjAwMmVmMGQ4OGJmZDIzYjM1N2M5NDg3Zjk3MGJkMTU5NzRhYzZhOGY1YmZjNmZiYWM2MmMxOA
```

C'est ce code qui nous permet de demander un token, il faut faire vite, il a une durée de vie de 30 secondes

### Recupération du token ###

```
http://server.local/app_dev.php/oauth/v2/token?grant_type=authorization_code&client_id={client_id}&client_secret={client_secret}&redirect_uri=http%3A%2F%2Fserver.local&code=N2VmM2M5ODJiYjAwMmVmMGQ4OGJmZDIzYjM1N2M5NDg3Zjk3MGJkMTU5NzRhYzZhOGY1YmZjNmZiYWM2MmMxOA
```

On reçoit un json de la forme 

``` json
{"access_token":"NjQzY2RkMDBhYTUzYzk2YjI0NjcyNTk3MTMyNDMwZTYxOTE0OTllMzI0MDFjMjBjMzgxZjI1MTE1MjAzYWYzOQ","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"ODU5MDFlOWViN2UyODVlMTVhNWQxNDM4MjYwOGRlZjA1YTEwZTY5NTU3YWJmNGIyN2QxMGQ5MzdiMDdiNTYxMQ"}
```


### Demande de rafraichissement du token ### 

```
http://server.local/app_dev.php/oauth/v2/token?grant_type=refresh_token&client_id={client_id}&client_secret={client_secret}&refresh_token={token}
```

On reçoit le même type de réponse json.


## Workflow sans assocation user-client ##

Ici il est juste besoin  de demander un token, ou de le rafraichir s'il a expiré

### Création du Client ###

``` shell
php app/console redking:oauth-server:client:create --redirect-uri=http://server.local --grant-type="authorization_code" --grant-type="password" --grant-type="refresh_token" --grant-type="token" --grant-type="client_credentials" Redking
```

Sur la console s'affiche le `client_id` et `client_secret`

### Récupération du token et du refresh token

```
http://server.local/app_dev.php/oauth/v2/token?client_id={client_id}&client_secret={client_secret}&grant_type=client_credentials
```

On reçoit un json de la forme 

``` json
{"access_token":"NjQzY2RkMDBhYTUzYzk2YjI0NjcyNTk3MTMyNDMwZTYxOTE0OTllMzI0MDFjMjBjMzgxZjI1MTE1MjAzYWYzOQ","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"ODU5MDFlOWViN2UyODVlMTVhNWQxNDM4MjYwOGRlZjA1YTEwZTY5NTU3YWJmNGIyN2QxMGQ5MzdiMDdiNTYxMQ"}
```

### Demande de rafraichissement du token ### 

> http://server.local/app_dev.php/oauth/v2/token?grant_type=refresh_token&client_id={client_id}&client_secret={client_secret}&refresh_token={token}

On reçoit le même type de réponse json.


## Accéder à une méthode de l'API de manière sécurisée ##


Il faut envoyer le header HTTP suivant : 

key : Authorization
value : Bearer {token}

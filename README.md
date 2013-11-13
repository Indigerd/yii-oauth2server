#yii-oauth2server

yii-oauth2server is Yii extension that provide  standards compliant OAuth 2.0 authorization and resource server.

##Current Features

###Authorization Server

The authorization server is a flexible class and the following core specification grants are implemented:

* authorization code
* refresh token
* client credentials
* password (user credentials)

###Resource Server

The resource server allows you to secure your API endpoints by checking for a valid OAuth access token in the request.

##Installation and usage


You can find example of usage in demo folder

1) clone the repository to YOUR_EXTENSIONS_DIR/oauth2server
2) create database tables from file data/schema.mysql.sql
3) modify your config file to enable extension
for example

```
'components'=>array(
        .........
        'oauth2Auth'=>array(
            'class'=>'ext.oauth2server.OAuth2ServerAuth',
            'identityClass'=>'UserIdentity',
            'loginEndpoint'=>'site/login',
            'authorizeEndpoint'=>'/index.php/oauth2/authorize',
        ),
        'oauth2Resource'=>array(
            'class'=>'ext.oauth2server.OAuth2ServerResource',
        ),
        .........
```

4) to use authorization controller create controller that will handle authorization grants flow
for example controllers/Oauth2Controller.php

```
class Oauth2Controller extends CController
{

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index','token', 'authorize'),
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $server = Yii::app()->oauth2Auth;
        $server->initOAuth2Request();
    }

    public function actionAuthorize()
    {
        $server = Yii::app()->oauth2Auth;
        $server->authorize();
        $this->render('authorize', array('params'=>$server->getSessionParams()));
    }

    public function actionToken()
    {
        $server = Yii::app()->oauth2Auth;
        $server->issueAccessToken();
    }

}
```

also create template to handle user authorize step
for example views/oauth2/authorize.php

```
<form method="POST" action="">
    <input type="submit" name="accept" value=" Accept "/>
    <input type="submit" name="reject" value=" Reject "/>
</form>
```

5) example of usage resource server in your code

```
    $server = Yii::app()->oauth2Resource;
    $server->checkToken();
    $item=$this->loadModel();
    $response = new OAuth2ServerResponse;
    $response
        ->set($item->getAttributes())
        ->send();
```

##REQUIREMENTS

PHP >= 5.3.0

Tested on Yii Framework 1.1.14

##License

yii-oauth2server is released under the MIT License. See the bundled LICENSE file for details.
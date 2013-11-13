<?php


class Oauth2Controller extends Controller
{
    public $layout='column2';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
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

<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */
class OAuth2ServerModelSessionScope extends CActiveRecord {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{oauth2server_session_scopes}}';
    }

}

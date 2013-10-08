<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */
class OAuth2ServerModelScope extends CActiveRecord implements \OAuth2\Storage\ScopeInterface {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{oauth2server_scopes}}';
    }


    public function getScope($scope)
    {
        $result = false;
        if ($row = $this->find('scope=:scope', array(':scope'=>$scope))) {
            $result = array(
                'id'	      =>	$row->id,
                'scope'	      =>	$row->scope,
                'name'	      =>	$row->name,
                'description' =>	$row->description
            );
        }
        return $result;
    }
}

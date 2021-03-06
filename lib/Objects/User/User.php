<?php

namespace lib\Objects\User;

use \lib\Controllers\MedalsController;
use \lib\Database\DataBaseSingleton;

/**
 * Description of user
 *
 * @author Łza
 */
class User
{

    private $userId;
    private $userName;

    private $foundGeocachesCount;
    private $notFoundGeocachesCount;
    private $hiddenGeocachesCount;
    private $email;

    /* @var $homeCoordinates \lib\Objects\Coordinates\Coordinates */
    private $homeCoordinates;
    private $medals = null;
    private $country;

    private $profileUrl = null;

    /**
     * construct class using $userId (fields will be loaded from db)
     * OR, if you have already user data row fetched from db row ($userDbRow), object is created using this data
     *
     * @param type $userId - user identifier in db
     * @param type $userDbRow - array - user data taken from db, from table user.
     */
    public function __construct(array $params)
    {
        if(isset($params['userId'])){
            $this->userId = (int) $params['userId'];
            $this->loadUserDataFromDb();

        }else if(isset($params['userDbRow'])){
            $this->setUserFieldsByUsedDbRow( $params['userDbRow'] );

        }else if(isset( $params['okapiRow']) ){
            $this->loadFromOKAPIRsp( $params['okapiRow'] );
        }

    }

    public function loadFromOKAPIRsp($okapiRow)
    {
        //load user data from row returned by OKAPI
        foreach ( $okapiRow as $field => $value ){
            switch($field){
                case 'uuid':                //geocache owner's user ID,
                    $this->userId = $value;
                    break;
                case 'username':            //name of the user,
                    $this->userName = $value;
                    break;
                case 'profile_url':         //URL of the user profile page,
                    $this->profileUrl = $value;
                    break;
                default:
                    error_log(__METHOD__.": Unknown field: $field (value: $value)");
            }
        }
    }

    public function getMedals()
    {
        //medals are not loaded in constructor - check if it is ready
        if( is_null($this->medals) ){
            //medals not loaded before - load from DB
            $this->loadMedalsFromDb();
        }
        return $this->medals;
    }

    private function loadUserDataFromDb()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $queryById = "SELECT username, founds_count, notfounds_count, hidden_count, latitude, longitude, country, email FROM `user` WHERE `user_id`=:1 LIMIT 1";
        $db->multiVariableQuery($queryById, $this->userId);
        $userDbRow = $db->dbResultFetch();
        $this->setUserFieldsByUsedDbRow($userDbRow);
    }

    private function setUserFieldsByUsedDbRow(array $dbRow)
    {
        foreach($dbRow as $key=>$value){
            switch($key){
                case 'user_id':         $this->userId = $value; break;
                case 'username':        $this->userName = $value; break;
                case 'founds_count':    $this->foundGeocachesCount = $value; break;
                case 'notfounds_count': $this->notFoundGeocachesCount = $value; break;
                case 'hidden_count':    $this->hiddenGeocachesCount = $value; break;
                case 'email':           $this->email = $value; break;
                case 'country':         $this->country = $value; break;
                case 'latitude':
                case 'longitude':
                    //lat|lon are handling below
                    break;
                default:
                    error_log(__METHOD__.": Unknown column: $key");
            }
        }

        //if coordinates are present set the homeCords.
        if(isset($dbRow['latitude'])&& isset($dbRow['longitude'])){
            $this->homeCoordinates =
                new \lib\Objects\Coordinates\Coordinates( array('dbRow' => $dbRow) );
        }
    }

    public function loadMedalsFromDb()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $query = 'SELECT `medal_type`, `prized_time`, `medal_level` FROM `medals` WHERE `user_id`=:1';
        $db->multiVariableQuery($query, $this->userId);
        $medalsDb = $db->dbResultFetchAll();
        $this->medals = new \ArrayObject;
        $medalController = new MedalsController;
        foreach ($medalsDb as $medalRow) {
            $this->medals[] = $medalController->getMedal(array('prizedTime' => $medalRow['prized_time'], 'medalId' => (int) $medalRow['medal_type'], 'level' => $medalRow['medal_level']));
            // $this->medals[] = new \lib\Objects\Medals\Medal(array('prizedTime' => $medalRow['prized_time'], 'medalId' => (int) $medalRow['medal_type'], 'level' => $medalRow['medal_level']));
        }
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUserInformation()
    {
        return array(
            'userName' => $this->userName,
        );
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }
    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

}

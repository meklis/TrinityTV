<?php

namespace Meklis\Network\TrinityTV;


use \GuzzleHttp\Client;

/**
 * TrinityTV low-level API implementation
 *
 * http://trinity-tv.net/
 * http://partners.trinity-tv.net/
 */
class Api
{

    /**
     * Partner ID
     *
     * @var string
     */
    protected $partnerId = '';

    /**
     * Key to generate an authorization request
     *
     * @var string
     */
    protected $salt = '';

    /**
     * API URL
     *
     * @var string
     */
    protected $urlApi = 'http://partners.trinity-tv.net/partners';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;


    public function __construct($partnerId = '', $salt = '', $urlApi = '')
    {
        $this->partnerId = $partnerId;
        $this->salt = $salt;

        if (!empty($urlApi)) {
            $this->urlApi = $urlApi;
        }
        $this->client = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]
        );
    }


    /**
     * Add subscription to user
     *
     * @param int $localid
     * @param $subscrid
     * @return bool|mixed
     * @throws \Exception
     */
    public function createUser($subscrid, $localid = 0)
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $subscrid . $this->salt);

        $uri = $this->urlApi . '/user/create?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&subscrid=' . $subscrid . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Change User Data
     *
     * @param int $localid
     * @param string $lastname
     * @param string $firstname
     * @param string $middlename
     * @param string $address
     * @return bool|mixed
     * @throws \Exception
     */
    public function updateUser($localid = 0, $lastname = '', $firstname = '', $middlename = '', $address = '')
    {
        $requestid = $this->getRequestId();

        $firstname = urlencode($firstname);
        $lastname = urlencode($lastname);
        $middlename = urlencode($middlename);
        $address = urlencode($address);

        $hash = md5($requestid . $this->partnerId . $localid . $firstname . $lastname . $middlename . $address . $this->salt);

        $uri = $this->urlApi . '/user/updateuser?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&lastname=' . $lastname . '&firstname=' . $firstname . '&middlename=' . $middlename . '&address=' . $address . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Getting a list of users and their statuses.
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function listUsers()
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $this->salt);

        $uri = $this->urlApi . '/user/subscriberlist?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Suspending and Restoring a Subscription
     *
     * @param int $localid
     * @param string $operationid
     * @return bool|mixed
     * @throws \Exception
     */
    public function subscription($localid = 0, $operationid = 'suspend')
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $operationid . $this->salt);

        $uri = $this->urlApi . '/user/subscription?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&operationid=' . $operationid . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Getting the list of subscriptions of the user.
     *
     * @param int $localid
     * @return bool|mixed
     * @throws \Exception
     */
    public function subscriptionInfo($localid = 0)
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $this->salt);

        $uri = $this->urlApi . '/user/subscriptioninfo?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Authorization MAC / UUID device
     *
     * @param int $localid
     * @param string $mac
     * @param string $uuid
     * @return bool|mixed
     * @throws \Exception
     */
    public function addDevice($localid = 0, $mac = '', $uuid = '')
    {
        $requestid = $this->getRequestId();

        // The string, mac device subscriber, 12 characters in uppercase
        $mac = str_replace(array(
            "-",
            ":"
        ), "", strtoupper($mac));

        if($mac && $uuid) {
            $hash = md5($requestid . $this->partnerId . $localid . $mac . $uuid . $this->salt);
            $uri = $this->urlApi . '/user/autorizedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&mac=' . $mac . '&uuid=' . $uuid . '&hash=' . $hash;
        } elseif ($mac) {
            $hash = md5($requestid . $this->partnerId . $localid . $mac . $this->salt);
            $uri = $this->urlApi . '/user/autorizedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&mac=' . $mac . '&hash=' . $hash;
        } else {
            $hash = md5($requestid . $this->partnerId . $localid . $uuid . $this->salt);
            $uri = $this->urlApi . '/user/autorizedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&uuid=' . $uuid . '&hash=' . $hash;
        }
        return $this->sendRequest($uri);
    }


    /**
     * Authorization of MAC / UUID device by code
     *
     * @param int $localid
     * @param string $code
     * @return bool|mixed
     * @throws \Exception
     */
    public function addDeviceByCode($localid = 0, $code = '')
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $code . $this->salt);

        $uri = $this->urlApi . '/user/autorizebycode?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&code=' . $code . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }


    /**
     * Deauthorize MAC / UUID devices
     *
     * @param int $localid
     * @param string $mac
     * @param string $uuid
     * @return bool|mixed
     * @throws \Exception
     */
    public function deleteDevice($localid = 0, $mac = '', $uuid = '')
    {
        $requestid = $this->getRequestId();

        // The string, mac device subscriber, 12 characters in uppercase
        $mac = str_replace(array(
            "-",
            ":"
        ), "", strtoupper($mac));

        if($mac && $uuid) {
            $hash = md5($requestid . $this->partnerId . $localid . $mac . $uuid. $this->salt);
            $uri = $this->urlApi . '/user/deletedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&mac=' . $mac . '&uuid=' . $uuid . '&hash=' . $hash;
        } elseif ($mac) {
            $hash = md5($requestid . $this->partnerId . $localid . $mac . $this->salt);
            $uri = $this->urlApi . '/user/deletedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&mac=' . $mac . '&hash=' . $hash;
        } else {
            $hash = md5($requestid . $this->partnerId . $localid . $uuid . $this->salt);
            $uri = $this->urlApi . '/user/deletedevice?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid  . '&uuid=' . $uuid . '&hash=' . $hash;
        }

        return $this->sendRequest($uri);
    }


    /**
     * Listing authorized MAC / UUID devices
     *
     * @param int $localid
     * @return bool|mixed
     * @throws \Exception
     */
    public function listDevices($localid = 0)
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $this->salt);

        $uri = $this->urlApi . '/user/devicelist?requestid=' . $requestid . '&partnerid=' . $this->partnerId . '&localid=' . $localid . '&hash=' . $hash;

        return $this->sendRequest($uri);
    }

    /**
     * Create new playlist for user
     *
     * @param int $localid
     * @return bool|mixed
     * @throws \Exception
     */
    public function addPlaylist($localid = 0)
    {
        $requestid = $this->getRequestId();

        $hash = md5($requestid . $this->partnerId . $localid . $this->salt);

        $uri = $this->urlApi . '/user/getplaylist' .
            '?requestid=' . $requestid .
            '&partnerid=' . $this->partnerId .
            '&localid=' . $localid .
            '&hash=' . $hash
        ;

        return $this->sendRequest($uri);
    }

    /**
     *
     * Delete palylist from user
     *
     * @param $localid
     * @param $playlistURL
     * @return bool|mixed
     * @throws \Exception
     */
    public function deletePlayList($localid , $playlistURL)
    {
        return  $this->deleteDevice($localid, $playlistURL);
    }


    /**
     * Generate Unique number
     *
     * @return mixed
     */
    private function getRequestId()
    {

        list($usec, $sec) = explode(' ', microtime());

        return str_replace('.', '', ((float)$sec . (float)$usec));
    }


    /**
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    private function sendRequest($url)
    {
        $response = $this->client->get($url);
        if(!in_array($response->getStatusCode(), [200,201,400] )) {
            throw new \Exception("Response from trinityTV: " . $response->getReasonPhrase());
        }

        if (!empty($response->getBody())) {
            return json_decode($response->getBody());
        } else {
            throw new \Exception("Empty body");
        }
    }
}
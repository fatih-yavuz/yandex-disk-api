<?php
/**
 * Created by PhpStorm.
 * User: fatih
 * Date: 21/03/2017
 * Time: 09:52
 */

require 'autoload.php';


/**
 * Class Disk
 */
class Disk {

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $callback_url;
    /**
     * @var string
     */
    private $base_uri = 'https://cloud-api.yandex.net/v1/disk/';
    /**
     * @var string
     */
    private $auth_type = 'OAuth';


    /**
     * Disk constructor.
     * @param $id
     * @param $password
     * @param $callback
     */
    public function __construct($id, $password, $callback)
    {
        $this->id = $id;
        $this->password = $password;
        $this->callback_url = $callback;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->callback_url;
    }

    /**
     * @param string $callback_url
     */
    public function setCallbackUrl(string $callback_url)
    {
        $this->callback_url = $callback_url;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->base_uri;
    }

    /**
     * @param string $base_uri
     */
    public function setBaseUri(string $base_uri)
    {
        $this->base_uri = $base_uri;
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->auth_type;
    }

    /**
     * @param string $auth_type
     */
    public function setAuthType(string $auth_type)
    {
        $this->auth_type = $auth_type;
    }


}
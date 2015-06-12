<?php
/**
 * Created by PhpStorm.
 * User: Nefa
 * Date: 04.06.2015
 * Time: 22:32
 */

class LoginUserObject {
    const AUTH_CANCEL = 401;
    const AUTH_EXPIRED = 403;
    const AUTH_ERROR = 410;
    const STATUS_OK = "ok";
    const STATUS_ERROR = "error";

    private $status, $accessToken, $nickname, $accountID, $expiresAt, $code, $message;
    private $error = false;

    function __construct($data=[]){
        $this->error = !isset($data["status"],$data["access_token"],$data["nickname"],$data["account_id"],$data["expires_at"]);
        $this->status = isset($data["status"]) ? $data["status"] : null;
        $this->accessToken = isset($data["access_token"]) ? $data["access_token"] : null;
        $this->nickname = isset($data["nickname"]) ? $data["nickname"] : null;
        $this->accountID = isset($data["account_id"]) ? $data["account_id"] : null;
        $this->expiresAt = isset($data["expires_at"]) ? $data["expires_at"] : null;
        $this->code = isset($data["code"]) ? $data["code"] : null;
        $this->message = isset($data["message"]) ? $data["message"] : null;
    }

    public function isError(){return $this->error;}
    public function isLogin(){return $this->status === self::STATUS_OK;}
    public function isUser(){return $this->accountID !== null;}

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @return mixed
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param null $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @param null $accountID
     */
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }

    /**
     * @param null $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @param null $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param boolean $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

}
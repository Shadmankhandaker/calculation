<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct( $userId ) {
        $this->userId = $userId;
    }
    private $userId;
    private $kindOfUser;
    private $kindOfCommissionFee;
    private $freeWithdraw = 3;

  

    public function getUserId() {
        return $this->userId;
    }

    public function setKindOfUser( $kindOfUser ) {
        $this->kindOfUser = $kindOfUser;
    }
    public function getKindOfUser() {
        return $this->kindOfUser;
    }

    public function setKindOfCommissionFee( $kindOfCommissionFee ) {
        $this->kindOfCommissionFee = $kindOfCommissionFee;
    }
    public function getKindOfCommissionFee() {
        return $this->kindOfCommissionFee;
    }

    public function isFreeWithdraw( $withdrawInWeekForPrivateUser ) {
        if ( count( $withdrawInWeekForPrivateUser ) > $this->freeWithdraw ) {
            return true;
        }
        return false;
    }

}
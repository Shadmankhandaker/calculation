<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

class CommissionFeeController extends Controller
{

    private $withdrawInWeekForPrivateUserByDate = [];

    private function getData( $userId, $date, $value, $currency ) {

        $valueInEURO = $this->changeInEuro( $value, $currency );
        $exceeded1000 = 0;
        if ( $valueInEURO > 1000 ) {
            $exceeded1000 = $valueInEURO - 1000;
        }

        return [ 
            'userId'    => $userId,
            'date'      => $date, 
            'value'     => $value, 
            'totalValueInEURO'  => $valueInEURO, 
            'exceeded1000'      => $exceeded1000,
            'currency'  => $currency
        ];
    }

    private function changeInEuro( $value, $currency ) {

        $exchangeRates = new ExchangeRate();
        $cours = 1;
        switch ( $currency ) {
            case 'USD':
                $cours = $exchangeRates->exchangeRate("EUR", "USD");
                break;
            case 'JPY':
                $cours = $exchangeRates->exchangeRate("EUR", "JPY");
                break;

            default:
                $cours = 1;
        }

        return $value / $cours;
    }

    /*
        $row[
            userId => [
                'year' => [
                    numberOfWeek = number
                ]
            ]
        ]
    */
    public function setWithdrawInWeekForPrivateUserByDate( $userId, $date, $value, $currency ) {

        $year = explode( '-', $date )[0];
        $numberOfWeek = date("W", strtotime( $date ) );

        $data = $this->getData( $userId, $date, $value, $currency );
        
        if ( array_key_exists( $userId, $this->withdrawInWeekForPrivateUserByDate ) ) {
            if ( array_key_exists( $year, $this->withdrawInWeekForPrivateUserByDate[ $userId ] ) ) {
                if ( array_key_exists( $numberOfWeek, $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ] ) ) {
                    $numberOfWeekArr = $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ $numberOfWeek ];
                    $data['totalValueInEURO'] += $numberOfWeekArr[ count( $numberOfWeekArr ) - 1 ][ 'totalValueInEURO' ];
                    if ( $numberOfWeekArr[ count( $numberOfWeekArr ) - 1 ][ 'exceeded1000' ] > 0 ) {
                        $data['exceeded1000'] = $this->changeInEuro( $data['value'], $data['currency'] );
                    } else if ( $data['totalValueInEURO'] > 1000 ) {
                        $data['exceeded1000'] = $data['totalValueInEURO'] - 1000;
                    }


                    array_push( $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ $numberOfWeek ], $data ); 
                } else {
                    $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ $numberOfWeek ] = [ $data ];
                }
            } else {
                $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ] = [ $numberOfWeek => [ $data ] ] ;
            }
        } else {
            $weekArr = [ $numberOfWeek => [ $data ] ];
            $yearArr = [ $year => $weekArr ];
            $this->withdrawInWeekForPrivateUserByDate[ $userId ] = $yearArr;
        }

        return $this->withdrawInWeekForPrivateUserByDate;
    }

    public function getWithdrawInWeekForPrivateUserByDate( $userId, $date ) {
        $year = explode( '-', $date )[0];
        $numberOfWeek = date("W", strtotime( $date ) );
        return $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ $numberOfWeek ];
    }

    public function getWithdrawInWeekForPrivateAllUsersByDate( ) {
        return $this->withdrawInWeekForPrivateUserByDate;
    }

    public function getWeekOnDecemberFromBeforeYearValueInEuro( $userId, $year ) {
        if ( isset( $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ '01' ] ) ) {
            $value = 0;
            $commissionFeeForThisWeek = $this->withdrawInWeekForPrivateUserByDate[ $userId ][ $year ][ '01' ];
            for ( $i = 0; $i < count( $commissionFeeForThisWeek ); $i++ ) {
                $row = $commissionFeeForThisWeek[ $i ];
                if ( date("m", strtotime( $row[ 'date' ] ) ) === '12' ) {
                    $value += $this->changeInEuro( $row[ 'value' ], $row[ 'currency' ] );
                }
            }
            return $value;
        }
        return;
    }

}
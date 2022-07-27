<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {    
        return view('index');
    }

    public function get_file( Request $request ) {

        $commisionFee = new CommissionFeeController();
        $handle = fopen($_FILES["get_file"]["tmp_name"], 'r');

        $fileContent = array();
        while(!feof($handle)) {      
            $fpTotal = fgetcsv($handle, '', ',', '\\');
            array_push($fileContent,$fpTotal);
        }
        fclose($handle);

        $withdrawInWeekForPrivateUser = [];
        for( $i = 0; $i < count( $fileContent ); $i++ ) {

            $row    = $fileContent[$i];
            $date   = date_format( date_create( $row[0] ) ,"Y-m-d");
            $year   = explode( '-', $date )[0];
            $isJanuary = ( date("m", strtotime( $date ) ) === '01' );
            $userId = $row[1];
            $kindOfUser             = $row[2];
            $kindOfCommissionFee    = $row[3];
            $value      = $row[4];
            $currency   = $row[5];

            $commissionFee = 0;
            $user = new UserController( $userId );
            if ( $kindOfUser === 'private' && $kindOfCommissionFee === 'withdraw' ) {
                // setWithdrawInWeekForPrivateUserByDate
                $commisionFee->setWithdrawInWeekForPrivateUserByDate( $userId, $date, $value, $currency );
                $withdrawInWeekForPrivateUser = $commisionFee->getWithdrawInWeekForPrivateUserByDate( $userId, $date );
                $lastWithdrawInWeekForPrivateUser = $withdrawInWeekForPrivateUser[ count( $withdrawInWeekForPrivateUser ) - 1 ];
                $exceeded1000 = $lastWithdrawInWeekForPrivateUser["exceeded1000"];
                $firstWeekOnDecemberFromBeforeYearValueInEuro = $commisionFee->getWeekOnDecemberFromBeforeYearValueInEuro( $userId, $year - 1 );

                if ( $user->isFreeWithdraw( $withdrawInWeekForPrivateUser ) ) {
                    $commissionFee = $value * 0.3 / 100;
                } else if ( $isJanuary && $exceeded1000 <= 1000 && $firstWeekOnDecemberFromBeforeYearValueInEuro > 1000 ) {
                    $commissionFee = $value * 0.3 / 100;
                } else if ( $exceeded1000 > 0 ) {
                   
                    $commissionFee = $exceeded1000 * 0.3 / 100;
                } 
            } else if( $kindOfCommissionFee === 'deposit' ) {
                $commissionFee = $value * 0.03 / 100;
            } else if( $kindOfUser === 'business' ) {
                $commissionFee = $value * 0.5 / 100;
            }

            echo '<center><pre>';
            
            echo(round( $commissionFee, 2 ));
            echo '</pre></center>';

        }
        

        return view('index');

    }

}
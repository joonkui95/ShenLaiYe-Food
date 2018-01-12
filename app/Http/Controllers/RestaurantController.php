<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\PlacesApi;
use App\Restaurant;
use GuzzleHttp\Client;

class RestaurantController extends Controller
{
    public function __construct(){

        $this->key = 'AIzaSyCDADeOH-8PmS0Nu5fqbbKsR3EZT1FAtSw';
        // $this->key = 'AIzaSyCU8D8CL7EkRjDnfhFBJRHoNTpM0pOqE6Q';
        
        $this->googlePlaces = new PlacesApi($this->key);
    }

    public function search(){
        $response = $this->googlePlaces->placeAutocomplete(request()->name);
        return $response['predictions'];
    }

    // public function place_id_to_
    /**
     * 測試頁面
     * @return \Illuminate\Http\Response
     */
    public function test(){
        $type = 'food';
        $location = '24.178829, 120.646438';
        $radius = 1000;
        $response = $this->googlePlaces->nearbySearch($location,$radius,[
            // 'language'=>'zh-TW',
            'language'=>'en',
            'type'=>$type,
        ]);
        // $token = isset($response['next_page_token']) ?
        //             $response['next_page_token']: 
        //             0;
        // do {
        //     if ($token) {
        //         do {
        //             $temp = $googlePlaces->nearbySearch($location,$radius,['type'=>$type,'pagetoken'=>$token]);
        //         } while($temp['status'] == 'INVALID_REQUEST' );
        //         if ($temp['status'] == 'OK') {
        //             $temp['results'] = $temp['results']->merge($response['results']);
        //             $response = $temp;
        //             $token = isset($response['next_page_token']) ?$response['next_page_token']: null;
        //         }
        //     }
        // } while($token);


        $data = collect();
        if ($response['status']=='OK')
        foreach ($response['results'] as $value) 
            $data->push(Restaurant::firstOrCreate(['place_id'=>$value['place_id']],[
                            'location'=>['type'=>'Point', 'coordinates'=>[$value['geometry']['location']['lng'],$value['geometry']['location']['lat']]],
                            'name'=>$value['name'],
                            'place_id'=>$value['place_id'],
                            'rating'=>isset($value['rating']) ? $value['rating']: 0,
                            'vicinity'=>$value['vicinity'],
                        ]));
        dump($data);
    }
}

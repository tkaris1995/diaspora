<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 4/13/2017
 * Time: 10:56 AM
 */

namespace App\Http\Controllers;

use App\ApprovedBooking;
use App\OccupiedRooms;
use App\RegisterHostel;
use App\RegisterHostelRooms;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\HostelRooms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

class RegisterHostelRoomsController extends Controller
{
    public function __construct()
    {
        $njokerio = RegisterHostel::where("hregion", "Njokerio")->get();
        View::share("njokerio", $njokerio);
        $right = RegisterHostel::where("hregion", "Right")->get();
        View::share("right", $right);
        $booster = RegisterHostel::where("hregion", "Booster")->get();
        View::share("booster", $booster);
        $carwash = RegisterHostel::where("hregion", "Carwash")->get();
        View::share("carwash", $carwash);
    }

    public function create($hostel_id)
    {
        $hostel = RegisterHostel::findorFail($hostel_id);
        return view('registerHostelRooms', compact('hostel'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'rno' => 'required|numeric|min:1',
            'capacity' => 'required|numeric|min:1',
            'rent' => 'required|numeric|min:100'

        ]);
        $hostelrooms = new RegisterHostelRooms($request->all());
        $hostelrooms->save();

        //return success message to page
        return redirect()->action('RegisterHostelRoomsController@create', $request->hostel_id)
            ->with('status', $request->rno . ' Successfully Registered to the System as a room.');
    }

    public function tour($hostel_id)
    {
        $hostel = RegisterHostel::findorFail($hostel_id);
        $hostel['rooms'] = RegisterHostelRooms::where('hostel_id', $hostel_id)->get();
       // foreach($hostel)
        $roomdetails = RegisterHostelRooms::where('hostel_id', $hostel_id)->get();
        foreach($roomdetails as $roomdetail) {
            $roomdetail['occupied'] = OccupiedRooms::where('room_id', $roomdetail->id)->get();
            $roomdetail['current_capacity'] = OccupiedRooms::where('room_id', $roomdetail->id)->where('status', 1)->count();
            $roomdetail['percent'] = ($roomdetail->current_capacity/$roomdetail->capacity)*100;
        }
       // $join = DB::table('register_hostel_rooms')->join('occupied_rooms', 'register_hostel_rooms.id', '=', 'occupied_rooms.room_id')->get();
        /*$add = DB::table('register_hostel_rooms')->join('occupied_rooms', 'register_hostel_rooms.id', '=', 'occupied_rooms.room_id')->count();*/
            /*$per = $rd->currentcapacity/$rd->capacity;*/
        /*}*/
        if(!Auth::guest()){
            $app = ApprovedBooking::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        }

        //dd($join);
        return view('tour', compact('hostel', 'app', 'roomdetails', 'capacity', 'per', 'join', 'cal'));
    }

    public function about($hostel_id)
    {
        $hostel = RegisterHostel::findorFail($hostel_id);
        $hostel['rooms'] = RegisterHostelRooms::where('hostel_id', $hostel_id)->get();
        if(!Auth::guest()){
            $app = ApprovedBooking::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        }
        $get = $hostel['landlord_id'];
        $ret = User::findorFail($get);
        /*return Response::json($hostel);*/
        /*dd($hostel);*/
        return view('about', compact('hostel', 'ret', 'app'));
    }

    public function contact($hostel_id)
    {
        $hostel = RegisterHostel::findorFail($hostel_id);
        $hostel['rooms'] = RegisterHostelRooms::where('hostel_id', $hostel_id)->get();
        if(!Auth::guest()){
            $app = ApprovedBooking::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->get();
        }
        $get = $hostel['landlord_id'];
        $ret = User::findorFail($get);
        /*return Response::json($hostel);*/
        /*dd($hostel);*/
        return view('contact', compact('hostel', 'ret', 'app'));
    }
}
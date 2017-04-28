<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 4/27/2017
 * Time: 5:49 PM
 */

namespace App\Http\Controllers;


class RegisterHostelRoomsNjokerioController extends Controller
{
    public function __construct()
    {
        $njokerio = Hostel::where("hregion", "Njokerio")->get();
        View::share("njokerio", $njokerio);
        $right = Hostel::where("hregion", "Right")->get();
        View::share("right", $right);
        $booster = Hostel::where("hregion", "Booster")->get();
        View::share("booster", $booster);
        $carwash = Hostel::where("hregion", "Carwash")->get();
        View::share("carwash", $carwash);
    }

    public function create($hostel_id)
    {
        $hostel = Hostel::findorFail($hostel_id);
        return view('registerHostelRooms', compact('hostel'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            //'rno' => 'required|number',
            'pics' => 'mimes:jpeg,jpg,png|max:10000'
        ]);
        //save user to db
        $hostelrooms = new HostelRoomsNjokerio($request->all());
        if ($request->pics != null) {
            $file = Input::file('pics');

            $originname = $file->getClientOriginalName();

            $filename = pathinfo($originname, PATHINFO_FILENAME);
            $extension = pathinfo($originname, PATHINFO_EXTENSION);
            $name = $filename . '.' . time() . '.' . $extension;

            Input::file('pics')->move(public_path() . '/photos/', $name);
        }
        $hostelrooms['pics'] = $name;
        $hostelrooms->save();

        //return success message to page
        return redirect()->action('RegisterHostelRoomsController@create', $request->hostel_id)
            ->with('rno', $request->rno . ' Successfully Registered to the System as a room.')
            ->with('newHostelRooms', $hostelrooms);
    }

    public function tour($hostel_id)
    {
        $hostel = Hostel::findorFail($hostel_id);
        $hostel['rooms'] = HostelRoomsNjokerio::where('hostel_id', $hostel_id)->get();
        /*return Response::json($hostel);*/
        /*dd($hostel);*/
        return view('tour', compact('hostel'));
    }

    public function about($hostel_id)
    {
        $hostel = Hostel::findorFail($hostel_id);
        $hostel['rooms'] = HostelRooms::where('hostel_id', $hostel_id)->get();
        /*return Response::json($hostel);*/
        /*dd($hostel);*/
        return view('about', compact('hostel'));
    }

    public function contact($hostel_id)
    {
        $hostel = Hostel::findorFail($hostel_id);
        $hostel['rooms'] = HostelRooms::where('hostel_id', $hostel_id)->get();
        /*return Response::json($hostel);*/
        /*dd($hostel);*/
        return view('contact', compact('hostel'));
    }
}
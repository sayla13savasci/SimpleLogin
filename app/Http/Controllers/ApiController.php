<?php

namespace App\Http\Controllers;

use App\CommuteInfo;
use App\PasswordReset;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function userLogin(Request $request)
    {
        $credentials = $request->only('user_name', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function userReg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|unique:users',
            'name' => 'required|alpha|min:3',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|unique:users',
        ]);

        if (!preg_match("/^(?:\+?88)?01[13-9]\d{8}$/", $request->phone)) {
            return response()->json(["Phone" => "Not valid BD Number"], 200);
        }
        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }

        $user = new User();
        $user->name = $request->name;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->save();
        $success['token'] = $user->createToken('MyApp')->accessToken;
        return response()->json(['success' => $success], 200);
    }

    public function forgotPassword(Request $request)
    {
        $pass_reset = new PasswordReset();
        $pass_reset->email = $request->email;
        $pass_reset->token = mt_rand(10000, 200000000);
        $pass_reset->save();

        //Send this token to email
        return response()->json("Password reset code sent to ur email. please click the link to reset", 200);
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'price' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }

        $product = new Product();
        $product->title = $request->title;
        $product->content = $request->content;
        $product->price = $request->price;
        $product->save();
        return response()->json("Product Saved Successfully", 200);
    }

    public function editProduct(Request $request)
    {
        $product = Product::find($request->pid);
        $product->title = $request->title;
        $product->content = $request->content;
        $product->price = $request->price;
        $product->save();
        return response()->json("Product Updated Successfully", 200);
    }

    public function deleteProduct(Request $request)
    {
        Product::where('id', $request->pid)->delete();
        return response()->json("Product Deleted Successfully", 200);
    }

    public function userDetails(Request $request)
    {
        $user_details = User::find(Auth::user()->id);
        return response()->json($user_details, 200);
    }

    public function searchCommute(Request $request)
    {
        $starttime = $request->starttime;
        $endtime = $request->endtime;
        $origin = $request->origin;
        $destination = $request->destination;
        $type = $request->type;
        $my_id = $request->my_id;

        $searchResults = CommuteInfo::where('startTime', '>=', $starttime)->where('endTime', '<=', $endtime)
            ->where('origin', '=', $origin)->where('destination', '=', $destination)->where('type', '=', $type)
            ->where('user_id', '!=', $my_id)->get();
        // dd($searchResults);

        if ($searchResults) {
            $response = array(
                'message' => 'successfull'
            );
            return response()->json($response, 200);
        } else {
            $response = array(
                'message' => 'Commute Do not match'
            );
            return response()->json($response, 400);

        }

    }

    public function createCommute(Request $request)
    {

        $origin = $request->origin;
        $destination = $request->destination;
        $startTime = $request->starttime;
        $endTime = $request->endtime;
        $type = $request->type;

        $commuteInfo = new CommuteInfo();
        $commuteInfo->origin = $request->origin;
        $commuteInfo->destination = $request->destination;
        $commuteInfo->startTime = $request->startTime;
        $commuteInfo->endTime = $request->endTime;
        $commuteInfo->type = $request->type;
        $commuteInfo->user_id = Auth::user()->id;
        //dd($user_id);
        $commuteInfo->save();


        if ($commuteInfo->save()) {
            $response = array(
                'message' => 'successfull'
            );
            return response()->json($response, 200);
        } else {
            $response = array(
                'message' => 'Commute Do not match'
            );
            return response()->json($response, 400);

        }
    }

    public function updateProfile(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $address = $request->address;
        $id = Auth::user()->id;

        $result = User::where('id', $id)
            ->update(['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
        // $id=Auth::user()->id;

        if ($result) {
            $response = array(
                'message' => 'Successful'
            );
            return response()->json($response, 200);
        } else {
            $response = array(
                'message' => 'Commute Do not match'
            );
            return response()->json($response, 400);
        }
    }

}

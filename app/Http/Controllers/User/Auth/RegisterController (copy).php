<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Mlm;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\Bonuses;
use App\Models\UserExtra;
use App\Models\BonusUserLogs;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
        $this->middleware('registration.status')->except('registrationNotAllowed');
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $position = session('position');
        $refUser = null;
        $joining = null;
        $pos = 1;
        if ($position) {
            $refUser = User::where('username', session('ref'))->first();
            if ($position == 'left')
                $pos = 1;
            else {
                $pos = 2;
            }
            $positioner = Mlm::getPositioner($refUser, $pos);
            $join_under = $positioner;
            $joining = $join_under->username;
        }


        $userId_gen = '2RS' . random_int(100000, 999999);
        while (1) {
            $user_count_exists = User::where('username', $userId_gen)->count();
            if ($user_count_exists > 0) {
                $userId_gen = '2RS' . random_int(100000, 999999);
            } else {
                break;
            }
        }


        return view($this->activeTemplate . 'user.auth.register', compact('pageTitle', 'mobileCode', 'countries', 'refUser', 'position', 'joining', 'pos', 'userId_gen'));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = gs();
        $passwordValidation = Password::min(6);
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));
        $validate = Validator::make($data, [
            'referral' => 'required|exists:users,username',
            'position' => 'required|in:1,2',
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required|regex:/^([0-9]*)$/',
            'password' => ['required', 'confirmed', $passwordValidation],
            'username' => 'required|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'mobile_code' => 'required|in:' . $mobileCodes,
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'agree' => $agree
        ]);


        return $validate;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        /*    if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        } */

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }


        $exist = User::where('mobile', $request->mobile_code . $request->mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

        $username_exist = User::where('username', $request->username)->first();
        if ($username_exist) {
            $notify[] = ['error', 'The user id already exists'];
            return back()->withNotify($notify)->withInput();
        }

        $email_exist = User::where('email', $request->email)->first();
        if ($email_exist) {
            $notify[] = ['error', 'The email id already exists'];
            return back()->withNotify($notify)->withInput();
        }


        if (empty($request->referral) || empty($request->place_id)) {
            $notify[] = ['error', 'Referral and Place Id is required'];
            return back()->withNotify($notify)->withInput();
        }

        if (empty($request->position)) {
            $notify[] = ['error', 'Position is required'];
            return back()->withNotify($notify)->withInput();
        }


        $place_id_user_exist = User::where('username', $request->place_id)->first();
        if (!$place_id_user_exist) {
            $notify[] = ['error', 'The place id user does not exists'];
            return back()->withNotify($notify)->withInput();
        }

        $referral_user_exist = User::where('username', $request->referral)->first();
        if (!$referral_user_exist) {
            $notify[] = ['error', 'The referral id user does not exists'];
            return back()->withNotify($notify)->withInput();
        }


        $place_id_user_exist_left = User::where('pos_id', $place_id_user_exist->id)

            ->where('position', 1)
            ->first();

        $place_id_user_exist_right = User::where('pos_id', $place_id_user_exist->id)

            ->where('position', 2)
            ->first();

        if (isset($place_id_user_exist_left->id) && isset($place_id_user_exist_right->id)) {
            $notify[] = ['error', 'The place id is not available'];
            return back()->withNotify($notify)->withInput();
        } else {
            if ($request->position == 1) {
                if (isset($place_id_user_exist_left->id)) {
                    $notify[] = ['error', 'The place id cannot be used anymore'];
                    return back()->withNotify($notify)->withInput();
                }
            } else {
                if ($request->position == 2) {
                    if (isset($place_id_user_exist_right->id)) {
                        $notify[] = ['error', 'The place id cannot be used anymore'];
                        return back()->withNotify($notify)->withInput();
                    }
                }
            }
        }


        if ($request->referral != $request->place_id) {
            // $users_left_members = Mlm::getAllActivePaidUserOfUserDirectIndirectLeft($referral_user_exist);
            $users_left_members = Mlm::getUserDownline($referral_user_exist, 'LEFT', 1, 0, 0);

            // $users_right_members = Mlm::getAllActivePaidUserOfUserDirectIndirectRight($referral_user_exist);
            $users_right_members = Mlm::getUserDownline($referral_user_exist, 'RIGHT', 1, 0, 0);;

            $is_place_in_chain = false;
            foreach ($users_left_members as $key => $users_left_member_item) {
                // echo  "<br/>".$users_left_member_item->username;
                if ($users_left_member_item->id == $place_id_user_exist->id) {
                    $is_place_in_chain = true;
                    break;
                }
            }
            if (!$is_place_in_chain) {
                foreach ($users_right_members as $key => $users_right_member_item) {
                    // echo  "<br/>".$users_left_member_item->username;
                    if ($users_right_member_item->id == $place_id_user_exist->id) {
                        $is_place_in_chain = true;
                        break;
                    }
                }
            }
            // die;
            if (!$is_place_in_chain) {

                $notify[] = ['error', 'You cannot use this place id'];
                return back()->withNotify($notify)->withInput();
            }
        }




        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $general = gs();

        $referUser = User::where('username', $data['referral'])->first();
        $bonus = Bonuses::where('status', 1)->orderBy('id', 'desc')->first();


        // dd($bonus);
        $position = $data['position'];
        if ($data['place_id']) {

            $placeUser = User::where('username', $data['place_id'])->first();
        } else {

            $placeUser = $referUser;
        }
        $positioner = Mlm::getPositioner($referUser, $position);

        //User Create
        $user = new User();
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        $user->username = trim($data['username']);
        $user->ref_by = $referUser->id;
        $user->pos_id = $placeUser->id;
        $user->position = $position;
        $user->country_code = $data['country_code'];
        $user->mobile = $data['mobile_code'] . $data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->kv = $general->kv ? Status::NO : Status::YES;
        $user->ev = $general->ev ? Status::NO : Status::YES;
        $user->sv = $general->sv ? Status::NO : Status::YES;
        $user->ts = 0;
        $user->tv = 1;


        $is_bonus_given = false;
        if ($bonus && isset($bonus->amount) && isset($bonus->id)) {
            $total_bonus_given_count = BonusUserLogs::where('bonus_id', $bonus->id)->count();
            if ($total_bonus_given_count >= $bonus->participants) {
                $bonus->status = 0;
                $bonus->save();
            } else {
                $is_bonus_given = true;
                $user->total_bonus = $bonus->amount;
            }
        }




        $user->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;




        if ($is_bonus_given && $bonus && isset($bonus->amount) && isset($bonus->id)) {
            BonusUserLogs::create([
                'user_table_id' => $user->id,
                'bonus_id' => $bonus->id,
            ]);
        }





        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();


        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();


        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile', $request->mobile)->exists();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    public function registered(Request $request, $user)
    {
        $userExtras = new UserExtra();
        $userExtras->user_id = $user->id;
        $userExtras->save();
        Mlm::updateFreeCount($user);
        return to_route('user.home');
    }
}

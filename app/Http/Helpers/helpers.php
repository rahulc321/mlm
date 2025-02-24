<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Notify\Notify;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

function paginateArrayOfObjects($items, $perPage = 5, $page = null)
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $total = count($items);
    $currentpage = $page;
    $offset = ($currentpage * $perPage) - $perPage;
    $itemstoshow = array_slice($items, $offset, $perPage);

    return new LengthAwarePaginator($itemstoshow, $total, $perPage);
}

function systemDetails()
{
    $system['name'] = 'mlmlab';
    $system['version'] = '2.2';
    $system['build_version'] = '4.3.6';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{

    $pgrs_rstl_payment_amount_round_off_limit = setting('pgrs_rstl_payment_amount_round_off_limit', 7);
    $amount = round($amount, $pgrs_rstl_payment_amount_round_off_limit);
    return $amount + 0;


    // $amount = round($amount, $length);
    // return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $decimal = setting('pgrs_rstl_payment_amount_round_off_limit', 7);
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = false, $avatar = false)
{
    $clean = '';
    if (@is_readable($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($avatar) {
        return asset('assets/images/default-member.png');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit.index';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}

function mlmPositions()
{
    return array(
        '1' => 'Left',
        '2' => 'Right',
    );
}

function return2sureRoundAmount($amount, $decimal = 7)
{
    $pgrs_rstl_payment_amount_round_off_limit = setting('pgrs_rstl_payment_amount_round_off_limit', 7);

    return round($amount, $pgrs_rstl_payment_amount_round_off_limit);
}
function generateLaonId($length = 10)
{
    $number = mt_rand(1, 9);
    $length=$length-1;
    do {
        for ($i = $length; $i--; $i > 0) {
            $number .= mt_rand(0, 9);
        }
    } while (!empty(DB::table('loan_approved')->where('loan_id', $number)->first(['id'])));

    return $number;
}
function generateLaonIdOld($length = 10)
{
    $number = '';

    do {
        for ($i = $length; $i--; $i > 0) {
            $number .= mt_rand(0, 9);
        }
    } while (!empty(DB::table('loan_approved')->where('loan_id', $number)->first(['id'])));

    return $number;
}

function checkUserHasEmiDefaulterInHisTeam($user)
{

    $all_user_reffered = DB::table('users')
        ->select('id')
        ->where('ref_by', $user->id)
        ->where('status', 1)
        ->get();



    $is_user_has_emi_defaulter_member = false;
    $members_user_not_paid_emi_in_team = [];
    $number_of_loan_users_has_helped_in_team = 0;
    $members_user_has_helped_in_team_for_loan = [];

    foreach ($all_user_reffered as  $all_user_reffered_item) {

        $loan_applications = DB::table('loan_applications')
            ->where('is_application_approved', 1)
            ->where('user_id', $all_user_reffered_item->id)
            ->get();

        foreach ($loan_applications as  $loan_applications_item) {
            if (isset($loan_applications_item->id)) {
                $loan_approved = DB::table('loan_approved')
                    // ->where('is_loan_closed', 0)
                    ->where('loan_application_id', $loan_applications_item->id)
                    ->get();

                $loan_taken_by_user = DB::table('users')
                    ->select('username')
                    // ->where('is_loan_closed', 0)
                    ->where('id', $loan_applications_item->user_id)
                    ->first();

                if (isset($loan_taken_by_user->username)) {
                    $members_user_has_helped_in_team_for_loan[] = $loan_taken_by_user->username;
                }

                foreach ($loan_approved as  $loan_approved_item) {

                    $number_of_loan_users_has_helped_in_team++;
                    $loan_repayments = DB::table('loan_repayments')
                        // ->where('is_emi_paid', 2)
                        ->where('is_active', 1)
                        ->where('loan_id', $loan_approved_item->id)
                        ->get();

                    foreach ($loan_repayments as  $loan_repayment_item) {
                        if ($loan_repayment_item->is_emi_paid == 2) {
                            $is_user_has_emi_defaulter_member = true;
                            if (isset($loan_taken_by_user->username)) {
                                $members_user_not_paid_emi_in_team[] = $loan_taken_by_user->username;
                            }
                        }
                    }




                    // $number_of_loan_users_has_helped_in_team++;
                    // $loan_repayments_count = DB::table('loan_repayments')
                    //     ->where('is_emi_paid', 2)
                    //     ->where('is_active', 1)
                    //     ->where('loan_id', $loan_approved_item->id)
                    //     ->count();
                    // if ($loan_repayments_count > 0) {
                    //     $is_user_has_emi_defaulter_member = true;
                    // }
                }
            }
        }
    }

    return [
        'members_user_has_helped_in_team_for_loan' => array_unique($members_user_has_helped_in_team_for_loan),
        'number_of_loan_users_has_helped_in_team' => $number_of_loan_users_has_helped_in_team,
        'is_user_has_emi_defaulter_member' => $is_user_has_emi_defaulter_member,
        'members_user_not_paid_emi_in_team' => array_unique($members_user_not_paid_emi_in_team),
    ];
    // if ($is_user_has_emi_defaulter_member) {
    //     if ($number_of_loan_users_has_helped_in_team >= 6) {
    //     } else {
    //         if ($request->method() == 'GET') {
    //             // echo ($user->loanRepaymentsPending);
    //             // echo ($user->anyLoanGoingOn);
    //             // dd($user);
    //             // file_put_contents(XXX . "/x.txt", "\n" . $request->route()->getName(), FILE_APPEND);
    //             // dd($request->route()->getName());
    //             if (!in_array($request->route()->getName(), [
    //                 'user.plan.bstl_payment',
    //                 'user.my.rstl_closing',
    //                 'user.my.rstl_emi_repayment',
    //                 'user.deposit.insert',
    //                 'user.deposit.update',
    //                 'user.deposit.manual.confirm',
    //                 'user.deposit.confirm',
    //                 'user.deposit.history',
    //             ])) {
    //                 $notify[] = ['error', 'Please ask your team member to pay his EMI'];
    //                 // return back()->withNotify($notify);
    //                 return to_route('user.plan.bstl_payment')->withNotify($notify);
    //             }
    //         }
    //     }
    // }
}

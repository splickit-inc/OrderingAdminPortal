<?php namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Model\AdmMerchantEmail;
use App\Model\AdmMerchantPhone;
use App\Service\Utility;
use DB;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function index()
    {
        $merchant_id = session('current_merchant_id');

        $admin_emails = AdmMerchantEmail::where('merchant_id', '=', $merchant_id)->get()->toArray();

        //Angular Form is Expecting Boolean T/F rather than our DB Y/N
        $admin_emails = array_map(function ($email) {
            $email['weekly'] = ($email['weekly'] == 'Y') ? true : false;
            $email['daily'] = ($email['daily'] == 'Y') ? true : false;
            $email['admin'] = ($email['admin'] == 'Y') ? true : false;
            return $email;
        }, $admin_emails);

        //Merchant Phone
        $admin_phones = AdmMerchantPhone::where('merchant_id', '=', $merchant_id)->get()->toArray();

        return ["admin_emails" => $admin_emails,
            "admin_phones" => $admin_phones];
    }

    public function createAdminEmail(Request $request, Utility $utility)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $new_admin_email = new AdmMerchantEmail();

        $new_admin_email->merchant_id = $merchant_id;

        $new_admin_email->email = $data['email'];

        $new_admin_email->name = $data['name'];

        $new_admin_email->admin = $utility->convertBooleanYN($data['admin']);

        $new_admin_email->daily = $utility->convertBooleanYN($data['daily']);

        $new_admin_email->weekly = $utility->convertBooleanYN($data['weekly']);

        $new_admin_email->save();

        $response = $new_admin_email->toArray();

        $response['admin'] = $utility->convertYNBoolean($data['admin']);

        $response['daily'] = $utility->convertYNBoolean($data['daily']);

        $response['weekly'] = $utility->convertYNBoolean($data['weekly']);

        return $response;
    }

    public function updateAdminEmail(Request $request, Utility $utility)
    {
        $merchant_id = session('current_merchant_id');
        $data = $request->all();

        /** @var AdmMerchantEmail $new_admin_email */
        $new_admin_email = AdmMerchantEmail::find($data['id']);
        $new_admin_email->merchant_id = $merchant_id;

        $new_admin_email->email = $data['email'];

        $new_admin_email->name = $data['name'];

        $new_admin_email->admin = $utility->convertBooleanYN($data['admin']);

        $new_admin_email->daily = $utility->convertBooleanYN($data['daily']);

        $new_admin_email->weekly = $utility->convertBooleanYN($data['weekly']);

        $new_admin_email->save();
        return response()->json($new_admin_email->toArray(), 200);
    }

    public function deleteAdminEmail($id)
    {
        AdmMerchantEmail::destroy($id);
    }

    public function createAdminPhone(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $new_admin_phone = new AdmMerchantPhone();

        $new_admin_phone->merchant_id = $merchant_id;

        $new_admin_phone->name = $data['name'];

        $new_admin_phone->title = $data['title'];

        $new_admin_phone->email = $request->email;

        $new_admin_phone->phone_no = preg_replace('~\D~', '', $data['phone_no']);;

        $new_admin_phone->save();

        return $new_admin_phone->toArray();
    }

    public function deleteAdminPhone($id)
    {
        AdmMerchantPhone::destroy($id);
    }

    public function updateAdminPhone(Request $request)
    {
        $merchant_id = session('current_merchant_id');

        $data = $request->all();

        $new_admin_phone = AdmMerchantPhone::find($data['id']);

        $new_admin_phone->merchant_id = $merchant_id;

        $new_admin_phone->name = $data['name'];

        $new_admin_phone->title = $data['title'];

        $new_admin_phone->email = $data['email'];

        $new_admin_phone->phone_no = preg_replace('~\D~', '', $data['phone_no']);;

        $new_admin_phone->save();

        return 1;
    }
}




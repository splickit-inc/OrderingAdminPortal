<?php

namespace App\Http\Controllers;

use App\Service\LeadService;
use Illuminate\Http\Request;
use App\Model\Lead;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller {

    /**
     * Show the resources
     *
     * @return \Illuminate\Http\Response
     */
    public function customers() {
        return view('customer');
    }

    /**
     * Display the specified resource.
     *
     * @param  string $guid
     * @return \Illuminate\Http\Response
     */
    public function show($guid) {
        $lead = Lead::where('guid', $guid)->first();
        if (is_null($lead)) {
            return response()->json(["errors" => "Not found", "status" => 404], 404);
        }
        return $lead;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceTypes() {
        return Lead::serviceTypes();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentTypes() {
        return Lead::paymentTypes();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $data = $request->all();
        $leadService = new LeadService();
        $createdLead = $leadService->createLead($data);
        if ($createdLead === false) {
            return $this->errorResponse($leadService->errors(), 422);
        }
        $emailRes = $leadService->sendResellerFormEmail($createdLead);
        if ($emailRes === false) {
            //Log if email couldn't be enqueued for some weird reason
            Log::error(serialize($leadService->errors()));
        }
        return response()->json($createdLead, 201);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $perPage = intval($request->input('per_page', 200));
        $search = $request->input('search');
        $searchParams = $request->input('search_params');
        if (is_null($search) || is_null($searchParams)) {
            $createdAtRange = $request->input('created_at');
            if (is_null($createdAtRange)) {
                return Lead::paginate($perPage);
            }
            return Lead::createdAtRange($createdAtRange)->paginate($perPage);
        }
        return Lead::search($search, explode(',', $searchParams))->paginate($perPage);
    }


}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Offer;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 50));
        $search = $request->input('search');
        $searchParams = $request->input('search_params');
        if (is_null($search) || is_null($searchParams)) {
            return Offer::paginate($perPage);
        }
        return Offer::search($search, explode(',', $searchParams))->paginate($perPage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $offer = Offer::create($request->all());
        return response()->json($offer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Offer $offer
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $offer)
    {
        return $offer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Offer $offer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Offer $offer)
    {
        $offer->update($request->all());

        return response()->json($offer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Offer $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->json(null, 204);
    }
}

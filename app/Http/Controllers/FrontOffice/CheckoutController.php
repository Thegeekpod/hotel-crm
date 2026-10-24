<?php

namespace App\Http\Controllers\FrontOffice;

use Illuminate\Http\Request;

class CheckoutController extends FolioController
{
    /**
     * Display the checkout master directory
     */
    public function index(Request $request)
    {
        $data = $this->getFolioIndexData($request);
        return view('frontoffice.checkout.index', $data);
    }
}

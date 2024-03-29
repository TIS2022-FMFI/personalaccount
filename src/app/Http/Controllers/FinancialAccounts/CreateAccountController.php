<?php

namespace App\Http\Controllers\FinancialAccounts;

use App\Http\Requests\FinancialAccounts\CreateAccountRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Manages creation of new financial accounts.
 */
class CreateAccountController
{

    /**
     * Handles the request to add a new financial account for the current user
     *
     * @param CreateAccountRequest $request
     * the HTTP request to create an operation
     * @return Application|ResponseFactory|Response
     * a response containing information about this operation's result
     */
    public function create(CreateAccountRequest $request)
    {
        $user = Auth::user();

        $account = $user->accounts()->create([
            'title' => $request->validated('title'),
            'sap_id' => $request->validated('sap_id')
        ]);

        if (! $account->exists)
            return response(trans('financial_accounts.create.failed'), 500);
        return response(trans('financial_accounts.create.success'), 201);
    }

}
